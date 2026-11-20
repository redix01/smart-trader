<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\LiveTrade;
use App\Models\User;
use App\Services\TradeEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TradeEngineTest extends TestCase
{
    use RefreshDatabase;

    private float $btc = 100;
    private bool $binanceDown = false;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Http::fake(fn () => $this->binanceDown
            ? Http::response('down', 503)
            : Http::response([['symbol' => 'BTCUSDT', 'price' => (string) $this->btc]]));
        Asset::create(['symbol' => 'BTC', 'name' => 'Bitcoin', 'type' => 'crypto', 'current_price' => 50, 'is_active' => true]);
    }

    private function price(float $p): void
    {
        $this->btc = $p;
        Cache::flush(); // drop the 5s price cache
    }

    private function trader(float $balance = 1000): User
    {
        $u = User::factory()->create();
        $u->forceFill(['trading_balance' => $balance, 'currency' => 'USD'])->save();

        return $u->fresh();
    }

    private function engine(): TradeEngine
    {
        return app(TradeEngine::class);
    }

    private function market(User $u, string $side, float $amount, float $lev = 1, array $extra = []): LiveTrade
    {
        return $this->engine()->place($u, ['asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'market', 'side' => $side, 'amount' => $amount, 'leverage' => $lev] + $extra);
    }

    public function test_market_order_fills_immediately_and_opens_a_position(): void
    {
        $u = $this->trader(1000);
        $t = $this->market($u, 'buy', 200, 5);

        $this->assertSame(LiveTrade::STATUS_OPEN, $t->status);
        $this->assertEquals(100, (float) $t->entry_price);
        $this->assertEquals(10, (float) $t->quantity);           // 200 margin × 5x / 100
        $this->assertNotNull($t->filled_at);
        $this->assertEquals(800, (float) $u->fresh()->trading_balance);
    }

    public function test_limit_order_waits_until_the_market_reaches_it(): void
    {
        $u = $this->trader(1000);
        $t = $this->engine()->place($u, ['asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'limit', 'side' => 'buy', 'price' => 90, 'quantity' => 2, 'leverage' => 2]);

        $this->assertSame(LiveTrade::STATUS_PENDING, $t->status);
        $this->assertEquals(910, (float) $u->fresh()->trading_balance);   // margin 90 × 2 / 2

        $this->price(95);
        $this->engine()->match();
        $this->assertSame(LiveTrade::STATUS_PENDING, $t->fresh()->status);

        $this->price(89);
        $this->engine()->match();
        $this->assertSame(LiveTrade::STATUS_OPEN, $t->fresh()->status);
        $this->assertEquals(90, (float) $t->fresh()->entry_price);        // fills at the limit
    }

    public function test_marketable_limit_fills_at_the_better_price(): void
    {
        $t = $this->engine()->place($this->trader(), ['asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'limit', 'side' => 'buy', 'price' => 110, 'quantity' => 1]);

        $this->assertSame(LiveTrade::STATUS_OPEN, $t->status);
        $this->assertEquals(100, (float) $t->entry_price);
    }

    public function test_closing_a_long_pays_margin_plus_profit_once(): void
    {
        $u = $this->trader(1000);
        $t = $this->market($u, 'buy', 200, 5);   // 10 BTC at 100

        $this->price(110);
        $closed = $this->engine()->close($t);
        $this->assertSame(LiveTrade::STATUS_CLOSED, $closed->status);
        $this->assertEquals(100, (float) $closed->realized_pnl);
        $this->assertEquals(1100, (float) $u->fresh()->trading_balance); // 800 + 200 + 100

        $this->assertNull($this->engine()->close($t));                   // second close does nothing
        $this->assertEquals(1100, (float) $u->fresh()->trading_balance);
    }

    public function test_short_position_profits_when_price_falls(): void
    {
        $u = $this->trader(1000);
        $t = $this->market($u, 'sell', 100, 2);   // short 2 BTC at 100

        $this->price(90);
        $closed = $this->engine()->close($t);
        $this->assertEquals(20, (float) $closed->realized_pnl);
        $this->assertEquals(1020, (float) $u->fresh()->trading_balance);
    }

    public function test_take_profit_and_stop_loss_close_automatically(): void
    {
        $u = $this->trader(1000);
        $tp = $this->market($u, 'buy', 100, 1, ['take_profit' => 120, 'stop_loss' => 80]);

        $this->price(121);
        $this->engine()->match();
        $tp->refresh();
        $this->assertSame('take_profit', $tp->close_reason);
        $this->assertEquals(120, (float) $tp->exit_price);

        $this->price(100);
        $sl = $this->market($u, 'buy', 100, 1, ['stop_loss' => 90]);
        $this->price(85);
        $this->engine()->match();
        $this->assertSame('stop_loss', $sl->fresh()->close_reason);
        $this->assertEquals(-10, (float) $sl->fresh()->realized_pnl);
    }

    public function test_position_is_liquidated_when_loss_reaches_the_margin(): void
    {
        $u = $this->trader(1000);
        $t = $this->market($u, 'buy', 100, 10);   // 10 BTC on 100 margin: -10 % wipes it out

        $this->price(89);
        $this->engine()->match();
        $t->refresh();
        $this->assertSame(LiveTrade::STATUS_LIQUIDATED, $t->status);
        $this->assertEquals(-100, (float) $t->realized_pnl);             // never more than the margin
        $this->assertEquals(900, (float) $u->fresh()->trading_balance);
    }

    public function test_targets_on_the_wrong_side_are_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->market($this->trader(), 'buy', 100, 1, ['take_profit' => 90]);
    }

    public function test_insufficient_balance_is_rejected_without_charging(): void
    {
        $u = $this->trader(50);
        try {
            $this->market($u, 'buy', 100);
            $this->fail('expected rejection');
        } catch (ValidationException $e) {
            $this->assertEquals(50, (float) $u->fresh()->trading_balance);
            $this->assertSame(0, LiveTrade::count());
        }
    }

    public function test_cancel_refunds_only_pending_orders(): void
    {
        $u = $this->trader(1000);
        $pending = $this->engine()->place($u, ['asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'limit', 'side' => 'buy', 'price' => 50, 'quantity' => 2]);
        $open = $this->market($u, 'buy', 100);

        $this->assertTrue($this->engine()->cancel($pending));
        $this->assertFalse($this->engine()->cancel($pending));            // already cancelled
        $this->assertFalse($this->engine()->cancel($open));               // positions are closed, not cancelled
        $this->assertEquals(900, (float) $u->fresh()->trading_balance);
    }

    public function test_matcher_command_is_idempotent(): void
    {
        $u = $this->trader(1000);
        $this->market($u, 'buy', 100, 1, ['take_profit' => 110]);
        $this->price(115);

        Artisan::call('trades:match');
        $after = (float) $u->fresh()->trading_balance;
        Artisan::call('trades:match');

        $this->assertEquals(1010, $after);
        $this->assertEquals($after, (float) $u->fresh()->trading_balance);
    }

    public function test_falls_back_to_stored_price_when_binance_is_down(): void
    {
        $this->binanceDown = true;
        Cache::flush();
        $t = $this->market($this->trader(), 'buy', 100);

        $this->assertEquals(50, (float) $t->entry_price);                // Asset.current_price
    }

    public function test_stale_stored_price_is_not_used_when_binance_is_down(): void
    {
        $this->binanceDown = true;
        Cache::flush();
        Asset::where('symbol', 'BTC')->update(['updated_at' => now()->subHours(2)]);

        $this->expectException(ValidationException::class);
        $this->market($this->trader(), 'buy', 100);
    }

    public function test_endpoints_place_close_and_cancel(): void
    {
        $u = $this->trader(1000);
        $this->actingAs($u)->postJson(route('user.liveTrading.store'), ['asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'market', 'side' => 'buy', 'amount' => 100])
            ->assertOk()->assertJson(['success' => true])->assertJsonPath('trade.status', 'open');
        $id = LiveTrade::first()->id;

        $this->price(120);
        $this->actingAs($u)->postJson("/user/live-trading/{$id}/close")->assertOk()->assertJson(['success' => true]);
        $this->actingAs($u)->postJson("/user/live-trading/{$id}/close")->assertStatus(400);
        $this->assertEquals(1020, (float) $u->fresh()->trading_balance);

        $other = $this->trader();
        $this->actingAs($other)->postJson("/user/live-trading/{$id}/close")->assertStatus(403);
    }
}
