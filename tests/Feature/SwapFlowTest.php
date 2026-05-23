<?php

namespace Tests\Feature;

use App\Models\MarketPair;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Services\CoinMarketCapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SwapFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_swap_quote_uses_admin_configured_fee(): void
    {
        $this->mock(CoinMarketCapService::class, function ($mock) {
            $mock->shouldReceive('syncMarketPairs')->andReturnNull();
        });

        PlatformSetting::create([
            'key' => 'swap_fee',
            'value' => '2.5',
            'group' => 'Fees',
            'type' => 'number',
        ]);

        $user = User::factory()->create([
            'kyc_level' => 'verified',
        ]);

        MarketPair::create([
            'base_currency' => 'BTC',
            'quote_currency' => 'USDT',
            'current_price' => 50000,
            'price_change_24h' => 0,
            'high_24h' => 50000,
            'low_24h' => 50000,
            'volume_24h' => 0,
            'market_cap' => 0,
            'is_active' => true,
            'sort_order' => 1,
            'icon' => 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png',
        ]);

        $this->actingAs($user)
            ->postJson(route('swap.quote'), [
                'from' => 'USD',
                'to' => 'BTC',
                'amount' => 1000,
            ])
            ->assertOk()
            ->assertJsonPath('fee_percent', 2.5)
            ->assertJsonPath('fee', 0.0005)
            ->assertJsonPath('to_amount', 0.0195);
    }
}
