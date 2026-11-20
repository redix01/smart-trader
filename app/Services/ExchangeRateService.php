<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * USD-based exchange rates (ECB reference rates via frankfurter.dev).
 *
 * Rates are refreshed on a schedule (fx:refresh), stored in `exchange_rates` and
 * cached for reads. If the API is down the last stored rate keeps being used, so
 * conversion never silently turns into 1:1 for a currency that has a rate.
 */
class ExchangeRateService
{
    public const BASE = 'USD';
    public const SUPPORTED = ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 'CNY', 'INR', 'BRL'];
    private const CACHE_KEY = 'fx:usd-rates';
    private const CACHE_TTL = 3600;        // seconds
    private const STALE_AFTER_HOURS = 48;

    /** @return array<string,float> currency => units per 1 USD */
    public function rates(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $rates = ExchangeRate::where('base', self::BASE)->pluck('rate', 'currency')->map(fn ($r) => (float) $r)->all();
            $rates[self::BASE] = 1.0;

            return $rates;
        });
    }

    /** Units of $to per 1 unit of $from. Unknown currencies are treated like USD (symbol falls back to $ too). */
    public function rate(string $from, string $to): float
    {
        $from = $this->normalize($from);
        $to = $this->normalize($to);

        if ($from === $to) {
            return 1.0;
        }

        $rates = $this->rates();
        $usdToFrom = $rates[$from] ?? 1.0;
        $usdToTo = $rates[$to] ?? 1.0;

        if ($usdToFrom <= 0) {
            return 1.0;
        }

        return $usdToTo / $usdToFrom;
    }

    /** Accept ISO codes and the currency symbols older plan records were saved with ('$', '€', '£', '¥'). */
    public function normalize(?string $currency): string
    {
        $currency = trim((string) $currency);

        return match ($currency) {
            '', '$' => 'USD',
            '€' => 'EUR',
            '£' => 'GBP',
            '¥' => 'JPY',
            default => strtoupper($currency),
        };
    }

    public function convert(float|int|string|null $amount, string $from, string $to): float
    {
        return (float) $amount * $this->rate($from, $to);
    }

    /** Pull fresh rates and store them. Returns the number of currencies updated (0 on failure). */
    public function refresh(): int
    {
        $symbols = implode(',', array_diff(self::SUPPORTED, [self::BASE]));

        try {
            $response = Http::timeout(15)->retry(2, 500)->get('https://api.frankfurter.dev/v1/latest', [
                'base' => self::BASE,
                'symbols' => $symbols,
            ]);
        } catch (\Throwable $e) {
            Log::error('Exchange rate refresh failed: '.$e->getMessage());

            return 0;
        }

        $rates = $response->successful() ? ($response->json('rates') ?? []) : [];
        if (! $rates) {
            Log::error('Exchange rate refresh returned no rates', ['status' => $response->status()]);

            return 0;
        }

        $count = 0;
        foreach ($rates as $currency => $rate) {
            if (! in_array($currency, self::SUPPORTED, true) || ! is_numeric($rate) || $rate <= 0) {
                continue;
            }
            ExchangeRate::updateOrCreate(
                ['base' => self::BASE, 'currency' => $currency],
                ['rate' => $rate, 'source' => 'frankfurter', 'fetched_at' => now()]
            );
            $count++;
        }

        Cache::forget(self::CACHE_KEY);

        return $count;
    }

    public function isStale(): bool
    {
        $latest = ExchangeRate::where('base', self::BASE)->max('fetched_at');

        return ! $latest || now()->diffInHours($latest, true) > self::STALE_AFTER_HOURS;
    }
}
