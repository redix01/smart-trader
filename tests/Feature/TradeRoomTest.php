<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\LiveTrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TradeRoomTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Http::fake(['data-api.binance.vision/*' => Http::response([['symbol' => 'BTCUSDT', 'price' => '60000']])]);
    }

    private function assets(): void
    {
        Asset::create(['symbol' => 'BTC', 'name' => 'Bitcoin', 'type' => 'crypto', 'current_price' => 60000, 'price_change_percentage_24h' => 1.5, 'is_active' => true]);
        Asset::create(['symbol' => 'AAPL', 'name' => 'Apple Inc.', 'type' => 'stock', 'current_price' => 200, 'price_change_percentage_24h' => -0.4, 'is_active' => true]);
    }

    private function trader(float $balance = 1000): User
    {
        $user = User::factory()->create();
        $user->forceFill(['trading_balance' => $balance, 'currency' => 'USD'])->save();

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
            ->assertSee('No open positions')
            ->assertSee('No waiting orders')
            ->assertSee('id="openMarkets"', false)
            ->assertSee('id="marketsBackdrop"', false)
            ->assertSee('id="ticketPanel"', false)
            ->assertSee('data-open-ticket="buy"', false)
            ->assertSee('data-open-ticket="sell"', false)
            ->assertSee('data-interval="60"', false);
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

    public function test_market_order_opens_a_position_and_limit_order_can_be_cancelled(): void
    {
        $this->assets();
        $user = $this->trader(1000);

        // Market order: fills now, shows under Positions with a Close button.
        $this->actingAs($user)->postJson(route('user.liveTrading.store'), [
            'asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'market', 'side' => 'buy', 'amount' => 250, 'leverage' => 10,
        ])->assertOk()->assertJsonPath('trade.status', 'open');
        $this->assertEquals(750, (float) $user->fresh()->trading_balance);
        $position = LiveTrade::firstOrFail();

        // Limit order below the market: waits, shows under Orders with Cancel.
        $this->actingAs($user)->postJson(route('user.liveTrading.store'), [
            'asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'limit', 'side' => 'buy', 'price' => 50000, 'quantity' => 0.01, 'leverage' => 1,
        ])->assertOk()->assertJsonPath('trade.status', 'pending');
        $order = LiveTrade::where('status', 'pending')->firstOrFail();
        $this->assertEquals(250, (float) $user->fresh()->trading_balance);   // 750 - 500 margin

        $html = $this->actingAs($user)->get(route('user.liveTrading.index'))->getContent();
        $this->assertStringContainsString('data-close-id="'.$position->id.'"', $html);
        $this->assertStringContainsString('data-cancel-id="'.$order->id.'"', $html);
        // Regression: the cancel/close hooks live only on their buttons (a container-level hook made every click ask to cancel).
        $this->assertSame(1, substr_count($html, 'data-cancel-id="'.$order->id.'"'));
        $this->assertStringNotContainsString('data-cancel=', $html);
        $this->assertStringContainsString("all('button[data-cancel-id]')", $html);
        // No browser dialogs: confirmations use the modal helper.
        $this->assertStringContainsString('uiConfirm(', $html);
        $this->assertDoesNotMatchRegularExpression('/[^.\w]confirm\(/', $html);

        $this->actingAs($user)->postJson("/user/live-trading/{$order->id}/cancel")->assertOk()->assertJson(['success' => true]);
        $this->assertEquals(750, (float) $user->fresh()->trading_balance);
    }

    public function test_order_larger_than_balance_is_rejected(): void
    {
        $this->assets();
        $user = $this->trader(100);

        $this->actingAs($user)->postJson(route('user.liveTrading.store'), [
            'asset_type' => 'crypto', 'symbol' => 'BTC', 'order_type' => 'market', 'side' => 'buy', 'amount' => 500,
        ])->assertStatus(422)->assertJson(['success' => false]);

        $this->assertEquals(100, (float) $user->fresh()->trading_balance);
    }

    public function test_sidebar_links_to_the_trade_room(): void
    {
        $this->actingAs($this->trader())->get(route('user.dashboard'))->assertOk()
            ->assertSee('<span>Trade Room</span>', false)
            ->assertSee(route('user.liveTrading.index'), false);
    }

    public function test_history_and_overview_pages_render(): void
    {
        $user = $this->trader();
        $this->actingAs($user)->get(route('user.liveTrading.history'))->assertOk();
        $this->actingAs($user)->get(route('user.overview.index'))->assertOk();
    }
}
