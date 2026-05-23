<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MarketPair;
use App\Services\CoinMarketCapService;
use App\Services\FinnhubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_markets_page_shows_live_market_data(): void
    {
        $this->mock(CoinMarketCapService::class, function ($mock) {
            $mock->shouldReceive('syncMarketPairs')->andReturnNull();
        });
        $this->mock(FinnhubService::class, function ($mock) {
            $mock->shouldReceive('getTrackedStocks')->andReturn([
                [
                    'id' => 'stock:AAPL',
                    'name' => 'AAPL',
                    'symbol' => 'AAPL/USD',
                    'price' => '190.12',
                    'change' => '+1.0%',
                    'high' => '191.00',
                    'low' => '188.50',
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

        MarketPair::create([
            'base_currency' => 'BTC',
            'quote_currency' => 'USDT',
            'current_price' => 65000,
            'price_change_24h' => 2.4,
            'high_24h' => 66000,
            'low_24h' => 64000,
            'volume_24h' => 1250000000,
            'market_cap' => 1300000000000,
            'is_active' => true,
            'sort_order' => 1,
            'icon' => 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png',
        ]);

        $this->actingAs($this->createUser())
            ->get(route('markets'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Markets')
                ->has('cryptoMarkets', 1)
                ->has('stockMarkets', 1)
                ->where('overview.active_pairs', 1)
                ->where('overview.btc_dominance', '100.0%')
                ->etc()
            );
    }

    private function createUser(): User
    {
        return User::factory()->create();
    }
}
