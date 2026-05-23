<?php

namespace App\Services;

use App\Models\MarketPair;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CoinMarketCapService
{
    private const CACHE_KEY = 'coinmarketcap.crypto.quotes.v1';
    private const LISTINGS_CACHE_KEY = 'coinmarketcap.crypto.listings.latest.v1';

    public function syncMarketPairs(int $limit = 100): void
    {
        $listings = $this->latestListings($limit);

        if (! empty($listings)) {
            foreach ($listings as $index => $listing) {
                $price = data_get($listing, 'quote.USD.price');

                if ($price === null) {
                    continue;
                }

                MarketPair::updateOrCreate(
                    [
                        'base_currency' => (string) data_get($listing, 'symbol'),
                        'quote_currency' => 'USDT',
                    ],
                    [
                        'current_price' => (float) $price,
                        'price_change_24h' => (float) data_get($listing, 'quote.USD.percent_change_24h', 0),
                        'volume_24h' => (float) data_get($listing, 'quote.USD.volume_24h', 0),
                        'high_24h' => (float) data_get($listing, 'quote.USD.high_24h', $price),
                        'low_24h' => (float) data_get($listing, 'quote.USD.low_24h', $price),
                        'market_cap' => (float) data_get($listing, 'quote.USD.market_cap', 0),
                        'icon' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/' . data_get($listing, 'id') . '.png',
                        'is_active' => true,
                        'sort_order' => $index + 1,
                        'updated_at' => now(),
                    ]
                );
            }

            return;
        }

        $symbols = MarketPair::where('is_active', true)
            ->where('quote_currency', 'USDT')
            ->limit($limit)
            ->pluck('base_currency')
            ->unique()
            ->values()
            ->all();

        if (empty($symbols)) {
            return;
        }

        $quotes = $this->latestQuotes($symbols);

        foreach ($quotes as $symbol => $quote) {
            $price = data_get($quote, 'quote.USD.price');

            if ($price === null) {
                continue;
            }

            MarketPair::where('base_currency', $symbol)
                ->where('quote_currency', 'USDT')
                ->update([
                    'current_price' => (float) $price,
                    'price_change_24h' => (float) data_get($quote, 'quote.USD.percent_change_24h', 0),
                    'volume_24h' => (float) data_get($quote, 'quote.USD.volume_24h', 0),
                    'high_24h' => (float) data_get($quote, 'quote.USD.high_24h', $price),
                    'low_24h' => (float) data_get($quote, 'quote.USD.low_24h', $price),
                    'market_cap' => (float) data_get($quote, 'quote.USD.market_cap', 0),
                    'updated_at' => now(),
                ]);
        }
    }

    public function latestQuotes(array $symbols): array
    {
        $key = config('services.coinmarketcap.key');

        if (! $key) {
            return [];
        }

        $cacheKey = self::CACHE_KEY . ':' . md5(implode(',', $symbols));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($key, $symbols) {
            $response = Http::withHeaders([
                'X-CMC_PRO_API_KEY' => $key,
                'Accept' => 'application/json',
            ])->timeout(10)->retry(2, 250)->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
                'symbol' => implode(',', $symbols),
                'convert' => 'USD',
            ]);

            if (! $response->successful()) {
                return [];
            }

            return $response->json('data', []);
        });
    }

    public function latestListings(int $limit = 100): array
    {
        $key = config('services.coinmarketcap.key');

        if (! $key) {
            return [];
        }

        return Cache::remember(self::LISTINGS_CACHE_KEY . ':' . $limit, now()->addMinutes(5), function () use ($key, $limit) {
            $response = Http::withHeaders([
                'X-CMC_PRO_API_KEY' => $key,
                'Accept' => 'application/json',
            ])->timeout(10)->retry(2, 250)->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest', [
                'start' => 1,
                'limit' => $limit,
                'convert' => 'USD',
            ]);

            if (! $response->successful()) {
                return [];
            }

            return $response->json('data', []);
        });
    }
}
