<?php

namespace Tests\Unit;

use App\Models\MarketPair;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Services\PortfolioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_kpi_uses_open_orders_for_trade_count_and_profit(): void
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 1000,
        ]);

        MarketPair::create([
            'base_currency' => 'BTC',
            'quote_currency' => 'USDT',
            'current_price' => 110,
            'price_change_24h' => 0,
            'volume_24h' => 0,
            'high_24h' => 110,
            'low_24h' => 100,
            'market_cap' => 0,
            'icon' => 'btc.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Order::create([
            'user_id' => $user->id,
            'type' => 'market',
            'side' => 'buy',
            'pair' => 'BTC/USDT',
            'price' => 100,
            'amount' => 2,
            'filled' => 0,
            'total' => 200,
            'fee' => 0,
            'status' => 'open',
        ]);

        $kpi = app(PortfolioService::class)->dashboardKpi($user);

        $this->assertSame(1, $kpi['active_trades']);
        $this->assertSame('+$20.00', $kpi['open_profit']);
    }
}
