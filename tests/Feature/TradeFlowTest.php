<?php

namespace Tests\Feature;

use App\Models\MarketPair;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\Wallet;
use App\Services\FinnhubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_execute_crypto_trade_using_usd_balance(): void
    {
        PlatformSetting::create([
            'key' => 'trading_fee',
            'value' => '1.5',
            'group' => 'Fees',
            'type' => 'number',
        ]);

        $user = User::factory()->create([
            'kyc_level' => 'verified',
        ]);

        $pair = MarketPair::create([
            'base_currency' => 'BTC',
            'quote_currency' => 'USDT',
            'current_price' => 50000,
            'price_change_24h' => 0,
            'volume_24h' => 0,
            'high_24h' => 50000,
            'low_24h' => 50000,
            'market_cap' => 0,
            'is_active' => true,
            'sort_order' => 1,
            'icon' => 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png',
        ]);

        Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 1000,
        ]);

        Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'BTC',
            'balance' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('trades.store'), [
                'market_type' => 'crypto',
                'pair_id' => $pair->id,
                'pair' => 'BTC/USDT',
                'side' => 'buy',
                'type' => 'Market',
                'amount' => 0.01,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'pair' => 'BTC/USDT',
            'side' => 'buy',
            'status' => 'completed',
            'fee' => 7.5,
        ]);

        $this->assertEquals('492.50000000', $user->wallets()->where('currency', 'USD')->firstOrFail()->balance);
        $this->assertEquals('0.01000000', $user->wallets()->where('currency', 'BTC')->firstOrFail()->balance);
    }

    public function test_user_can_execute_stock_trade_using_usd_balance(): void
    {
        $this->mock(FinnhubService::class, function ($mock) {
            $mock->shouldReceive('getTrackedStocks')->andReturn([
                [
                    'id' => 'stock:AAPL',
                    'name' => 'AAPL',
                    'symbol' => 'AAPL/USD',
                    'price' => '182.50',
                    'change' => '+1.2%',
                    'high' => '184.00',
                    'low' => '180.00',
                    'volume' => 'N/A',
                    'cap' => 'N/A',
                    'icon' => 'data:image/svg+xml;base64,abc',
                    'up' => true,
                    'market_type' => 'stocks',
                    'favoriteable' => false,
                    'trade_asset' => 'AAPL',
                    'sort_order' => 1,
                ],
            ]);
        });

        PlatformSetting::create([
            'key' => 'trading_fee',
            'value' => '2.0',
            'group' => 'Fees',
            'type' => 'number',
        ]);

        $user = User::factory()->create([
            'kyc_level' => 'verified',
        ]);

        Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 1000,
        ]);

        Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'AAPL',
            'balance' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('trades.store'), [
                'market_type' => 'stocks',
                'pair' => 'AAPL/USD',
                'side' => 'buy',
                'type' => 'Market',
                'amount' => 1,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'pair' => 'AAPL/USD',
            'side' => 'buy',
            'status' => 'completed',
            'fee' => 3.65,
        ]);

        $this->assertEquals('813.85000000', $user->wallets()->where('currency', 'USD')->firstOrFail()->balance);
        $this->assertEquals('1.00000000', $user->wallets()->where('currency', 'AAPL')->firstOrFail()->balance);
    }

    public function test_user_can_execute_forex_trade_with_static_pair_id(): void
    {
        $user = User::factory()->create([
            'kyc_level' => 'verified',
        ]);

        Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 1000,
        ]);

        $this->actingAs($user)
            ->post(route('trades.store'), [
                'market_type' => 'forex',
                'pair_id' => 201,
                'pair' => 'EUR/USD',
                'side' => 'buy',
                'type' => 'Market',
                'amount' => 100,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'pair' => 'EUR/USD',
            'side' => 'buy',
            'status' => 'completed',
        ]);

        $this->assertEquals('891.44155000', $user->wallets()->where('currency', 'USD')->firstOrFail()->balance);
        $this->assertEquals('100.00000000', $user->wallets()->where('currency', 'EUR')->firstOrFail()->balance);
    }
}
