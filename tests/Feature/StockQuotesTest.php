<?php

namespace Tests\Feature;

use App\Services\StockQuotes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StockQuotesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Cache::flush();
    }

    private function chart(float $price, float $prev): array
    {
        return ['chart' => ['result' => [['meta' => ['regularMarketPrice' => $price, 'chartPreviousClose' => $prev, 'regularMarketVolume' => 1000]]]]];
    }

    public function test_endpoint_returns_quotes_with_change_percent(): void
    {
        Http::fake(['query1.finance.yahoo.com/*' => Http::response($this->chart(110, 100))]);

        $this->getJson(route('market.stocks'))
            ->assertOk()
            ->assertJsonCount(count(StockQuotes::SYMBOLS), 'quotes')
            ->assertJsonPath('quotes.0.symbol', 'AAPL')
            ->assertJsonPath('quotes.0.price', 110)
            ->assertJsonPath('quotes.0.change_pct', 10);
    }

    public function test_results_are_cached_between_requests(): void
    {
        Http::fake(['query1.finance.yahoo.com/*' => Http::response($this->chart(110, 100))]);

        app(StockQuotes::class)->get();
        app(StockQuotes::class)->get();

        Http::assertSentCount(count(StockQuotes::SYMBOLS));
    }

    public function test_provider_failure_serves_last_good_data_and_pauses_calls(): void
    {
        Http::fake(['query1.finance.yahoo.com/*' => Http::response($this->chart(110, 100))]);
        $first = app(StockQuotes::class)->get();

        // Fresh cache expires; provider starts failing.
        Cache::forget('stock_quotes.fresh');
        Http::fake(['query1.finance.yahoo.com/*' => Http::response('down', 503)]);
        $this->assertSame($first, app(StockQuotes::class)->get());

        // Paused: a second miss does not call the provider again.
        $sent = count(Http::recorded());
        app(StockQuotes::class)->get();
        $this->assertSame($sent, count(Http::recorded()));
    }

    public function test_malformed_body_yields_empty_list_not_an_error(): void
    {
        Http::fake(['query1.finance.yahoo.com/*' => Http::response(['chart' => ['result' => null]])]);

        $this->getJson(route('market.stocks'))->assertOk()->assertJsonPath('quotes', []);
    }
}
