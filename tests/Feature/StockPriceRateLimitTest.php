<?php

namespace Tests\Feature;

use App\Services\AssetPriceService;
use Database\Seeders\AssetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StockPriceRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_update_stops_on_the_first_finnhub_429_and_runs_once_per_window(): void
    {
        config(['services.finnhub.api_key' => 'test-key']);
        $this->seed(AssetSeeder::class);
        Http::fake(['finnhub.io/*' => Http::response(['error' => 'Too many requests.'], 429)]);

        $service = new AssetPriceService();
        $service->updateStockPrices();
        Http::assertSentCount(1);

        // A second pass inside the two-minute window makes no calls at all.
        $service->updateStockPrices();
        Http::assertSentCount(1);
    }

    public function test_stock_update_saves_prices_when_finnhub_answers(): void
    {
        config(['services.finnhub.api_key' => 'test-key']);
        $this->seed(AssetSeeder::class);
        Http::fake(['finnhub.io/*' => Http::response(['c' => 123.45, 'dp' => 1.5])]);

        (new AssetPriceService())->updateStockPrices();

        $this->assertDatabaseHas('assets', ['type' => 'stock', 'current_price' => 123.45]);
    }
}
