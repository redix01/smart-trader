<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Server-side prices used to execute and value Trade Room orders (USD).
 *
 * Crypto: Binance public market-data endpoint (no key), batched, 3s timeout, cached 5s,
 * falling back to the stored Asset price. A failure pauses Binance calls for 60s.
 * Stocks: the stored Asset price (refreshed by `assets:update-prices` every 5 minutes).
 * A stored price older than STALE_AFTER_MINUTES is not used: orders are refused rather than
 * filled at an out-of-date price.
 */
class MarketPrice
{
    private const BINANCE = 'https://data-api.binance.vision/api/v3/ticker/price';
    private const PAUSE_KEY = 'market_price.binance_paused';
    public const STALE_AFTER_MINUTES = 30;

    /** @return float|null USD price, or null when the market is unknown. */
    public function usd(string $type, string $symbol): ?float
    {
        return $this->many([[$type, $symbol]])[$this->key($type, $symbol)] ?? null;
    }

    /**
     * @param  array<int, array{0:string,1:string}>  $markets  [type, symbol] pairs
     * @return array<string, float> keyed by "type:SYMBOL"
     */
    public function many(array $markets): array
    {
        $out = [];
        $crypto = [];
        foreach ($markets as [$type, $symbol]) {
            $symbol = strtoupper($symbol);
            if ($type === 'crypto') {
                $crypto[$symbol] = true;
            }
        }

        $live = $crypto ? $this->binance(array_keys($crypto)) : [];

        $stored = Asset::query()
            ->whereIn('symbol', collect($markets)->pluck(1)->map(fn ($s) => strtoupper($s))->unique()->all())
            ->where('updated_at', '>=', now()->subMinutes(self::STALE_AFTER_MINUTES))
            ->get(['type', 'symbol', 'current_price'])
            ->mapWithKeys(fn ($a) => [$this->key($a->type, $a->symbol) => (float) $a->current_price]);

        foreach ($markets as [$type, $symbol]) {
            $k = $this->key($type, $symbol);
            $price = $type === 'crypto' ? ($live[strtoupper($symbol)] ?? null) : null;
            $price = $price ?: ($stored[$k] ?? null);
            if ($price && $price > 0) {
                $out[$k] = (float) $price;
            }
        }

        return $out;
    }

    public function key(string $type, string $symbol): string
    {
        return $type.':'.strtoupper($symbol);
    }

    /** @return array<string, float> symbol => USD price */
    private function binance(array $symbols): array
    {
        sort($symbols);
        $cacheKey = 'market_price.binance.'.md5(implode(',', $symbols));
        if ($hit = Cache::get($cacheKey)) {
            return $hit;
        }
        if (Cache::has(self::PAUSE_KEY)) {
            return [];
        }

        try {
            $pairs = array_map(fn ($s) => $s.'USDT', $symbols);
            $res = Http::connectTimeout(3)->timeout(4)->acceptJson()
                ->get(self::BINANCE, ['symbols' => json_encode($pairs, JSON_UNESCAPED_SLASHES)]);

            if (! $res->successful()) {
                // Binance rejects the whole batch if one pair is unknown (400): retry pairs one by one, once.
                if ($res->status() === 400 && count($symbols) > 1) {
                    return $this->binanceEach($symbols, $cacheKey);
                }
                throw new \RuntimeException('HTTP '.$res->status());
            }

            $prices = collect($res->json() ?? [])
                ->filter(fn ($row) => isset($row['symbol'], $row['price']) && str_ends_with($row['symbol'], 'USDT'))
                ->mapWithKeys(fn ($row) => [substr($row['symbol'], 0, -4) => (float) $row['price']])
                ->all();
            Cache::put($cacheKey, $prices, 5);

            return $prices;
        } catch (\Throwable $e) {
            Log::warning('Binance price lookup failed; using stored prices', ['error' => $e->getMessage()]);
            Cache::put(self::PAUSE_KEY, true, 60);

            return [];
        }
    }

    private function binanceEach(array $symbols, string $cacheKey): array
    {
        $prices = [];
        foreach ($symbols as $s) {
            try {
                $r = Http::connectTimeout(3)->timeout(4)->acceptJson()->get(self::BINANCE, ['symbol' => $s.'USDT']);
                if ($r->successful() && ($p = (float) $r->json('price')) > 0) {
                    $prices[$s] = $p;
                }
            } catch (\Throwable $e) {
                break;
            }
        }
        Cache::put($cacheKey, $prices, 5);

        return $prices;
    }
}
