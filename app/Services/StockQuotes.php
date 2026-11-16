<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Delayed stock quotes for the public site, from Yahoo Finance's public chart endpoint (no API key).
 *
 * Policy: read-only GETs, 4s timeout, no retries in-request (the page polls again in 30s).
 * Results are cached for 60s; the last good set is kept so the table never goes blank;
 * after a failure the provider is paused for 2 minutes instead of being hit on every request.
 */
class StockQuotes
{
    public const SYMBOLS = [
        'AAPL' => 'Apple',
        'NVDA' => 'Nvidia',
        'MSFT' => 'Microsoft',
        'TSLA' => 'Tesla',
        'AMZN' => 'Amazon',
        'SPY' => 'S&P 500 ETF',
    ];

    private const URL = 'https://query1.finance.yahoo.com/v8/finance/chart/%s';
    private const FRESH_KEY = 'stock_quotes.fresh';
    private const LAST_GOOD_KEY = 'stock_quotes.last_good';
    private const PAUSE_KEY = 'stock_quotes.paused';
    private const FRESH_SECONDS = 60;
    private const PAUSE_SECONDS = 120;

    /** Quotes for the page: fresh if possible, otherwise the last good set (may be empty). */
    public function get(): array
    {
        if ($fresh = Cache::get(self::FRESH_KEY)) {
            return $fresh;
        }
        if (! Cache::has(self::PAUSE_KEY)) {
            $quotes = $this->fetch();
            if ($quotes) {
                Cache::put(self::FRESH_KEY, $quotes, self::FRESH_SECONDS);
                Cache::forever(self::LAST_GOOD_KEY, $quotes);

                return $quotes;
            }
            Cache::put(self::PAUSE_KEY, true, self::PAUSE_SECONDS);
        }

        return Cache::get(self::LAST_GOOD_KEY, []);
    }

    /** Cached data only — never calls the provider. Safe to use while rendering a page. */
    public function cached(): array
    {
        return Cache::get(self::FRESH_KEY) ?? Cache::get(self::LAST_GOOD_KEY, []);
    }

    private function fetch(): array
    {
        try {
            $responses = Http::pool(fn (Pool $pool) => array_map(
                fn ($sym) => $pool->as($sym)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; quotes)'])
                    ->connectTimeout(2)->timeout(4)
                    ->get(sprintf(self::URL, $sym), ['interval' => '1d', 'range' => '1d']),
                array_keys(self::SYMBOLS)
            ));
        } catch (\Throwable $e) {
            Log::warning('Stock quotes request failed', ['error' => $e->getMessage()]);

            return [];
        }

        $quotes = [];
        foreach (self::SYMBOLS as $sym => $name) {
            $res = $responses[$sym] ?? null;
            if (! $res instanceof \Illuminate\Http\Client\Response || ! $res->successful()) {
                continue;
            }
            $meta = $res->json('chart.result.0.meta');
            $price = (float) ($meta['regularMarketPrice'] ?? 0);
            $prev = (float) ($meta['chartPreviousClose'] ?? $meta['previousClose'] ?? 0);
            if ($price <= 0 || $prev <= 0) {
                continue;
            }
            $quotes[] = [
                'symbol' => $sym,
                'name' => $name,
                'price' => round($price, 2),
                'change' => round($price - $prev, 2),
                'change_pct' => round(($price - $prev) / $prev * 100, 2),
                'volume' => (int) ($meta['regularMarketVolume'] ?? 0),
            ];
        }

        if (! $quotes) {
            Log::warning('Stock quotes provider returned no usable data');
        }

        return $quotes;
    }
}
