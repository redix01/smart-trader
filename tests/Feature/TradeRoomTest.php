<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\LiveTrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeRoomTest extends TestCase
{
    use RefreshDatabase;

    private function assets(): void
    {
        Asset::create(['symbol' => 'BTC', 'name' => 'Bitcoin', 'type' => 'crypto', 'current_price' => 60000, 'price_change_percentage_24h' => 1.5, 'is_active' => true]);
        Asset::create(['symbol' => 'AAPL', 'name' => 'Apple Inc.', 'type' => 'stock', 'current_price' => 200, 'price_change_percentage_24h' => -0.4, 'is_active' => true]);
    }

    private function trader(float $balance = 1000): User
    {
        $user = User::factory()->create();
        $user->forceFill(['trading_balance' => $balance])->save();

        return $user;
    }

    public function test_terminal_shows_markets_chart_ticket_and_orders(): void
    {
        $this->assets();

        $this->actingAs($this->trader())->get(route('user.liveTrading.index'))->assertOk()
            ->assertSee('id="marketList"', false)
            ->assertSee('data-symbol="BTC"', false)
            ->assertSee('data-symbol="AAPL"', false)
            ->assertSee('id="tvChart"', false)
            ->assertSee('id="orderForm"', false)
            ->assertSee('No open orders');
    }

    public function test_old_trade_link_opens_the_terminal_on_that_market(): void
    {
        $this->assets();
        $user = $this->trader();

        $this->actingAs($user)->get('/user/live-trading/trade?asset_type=stock&symbol=AAPL')
            ->assertRedirect(route('user.liveTrading.index', ['asset_type' => 'stock', 'symbol' => 'AAPL']));

        $html = $this->actingAs($user)->get(route('user.liveTrading.index', ['asset_type' => 'stock', 'symbol' => 'AAPL']))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/class="tr-mkt is-on"\s+data-type="stock" data-symbol="AAPL"/', $html);
    }

    public function test_market_order_debits_and_cancel_refunds_the_trading_balance(): void
    {
        $this->assets();
        $user = $this->trader(1000);

        $this->actingAs($user)->postJson(route('user.liveTrading.store'), [
            'asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'market', 'side' => 'buy', 'amount' => 250, 'leverage' => 10,
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertEquals(750, (float) $user->fresh()->trading_balance);
        $order = LiveTrade::firstOrFail();
        $this->assertSame('pending', $order->status);

        $this->actingAs($user)->get(route('user.liveTrading.index'))->assertSee('data-cancel="'.$order->id.'"', false);

        $this->actingAs($user)->postJson("/user/live-trading/{$order->id}/cancel")->assertOk()->assertJson(['success' => true]);
        $this->assertEquals(1000, (float) $user->fresh()->trading_balance);
    }

    public function test_order_larger_than_balance_is_rejected(): void
    {
        $this->assets();
        $user = $this->trader(100);

        $this->actingAs($user)->postJson(route('user.liveTrading.store'), [
            'asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'market', 'side' => 'buy', 'amount' => 500,
        ])->assertStatus(400)->assertJson(['success' => false]);

        $this->assertEquals(100, (float) $user->fresh()->trading_balance);
    }

    public function test_sidebar_groups_put_wallet_after_overview(): void
    {
        $html = $this->actingAs($this->trader())->get(route('user.dashboard'))->assertOk()->getContent();
        $pos = fn ($t) => strpos($html, '<span>'.$t.'</span>');
        $this->assertTrue($pos('Overview') < $pos('Wallet') && $pos('Wallet') < $pos('Trade') && $pos('Trade') < $pos('Account'));
        foreach (['Deposit', 'Withdraw', 'Trade Room', 'Copy trading', 'Verification', 'Transactions'] as $link) {
            $this->assertStringContainsString('<span>'.$link.'</span>', $html);
        }
    }
}
