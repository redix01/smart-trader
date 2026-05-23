<?php

namespace Tests\Feature;

use App\Models\MarketPair;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_execute_crypto_trade_from_wallet_balances(): void
    {
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
            'currency' => 'USDT',
            'balance' => 1000,
        ]);

        Wallet::factory()->create([
            'user_id' => $user->id,
            'currency' => 'BTC',
            'balance' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('trades.store'), [
                'pair_id' => $pair->id,
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
        ]);

        $this->assertEquals('499.50000000', $user->wallets()->where('currency', 'USDT')->firstOrFail()->balance);
        $this->assertEquals('0.01000000', $user->wallets()->where('currency', 'BTC')->firstOrFail()->balance);
    }
}
