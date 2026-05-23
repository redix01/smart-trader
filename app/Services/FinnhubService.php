<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FinnhubService
{
    private const STOCK_SYMBOLS = [
        'AAPL', 'MSFT', 'NVDA', 'AMZN', 'GOOGL', 'META', 'TSLA', 'NFLX', 'AMD', 'INTC',
        'JPM', 'V', 'MA', 'WMT', 'COST', 'KO', 'PEP', 'DIS', 'NKE', 'BA',
    ];

    public function getTrackedStocks(): array
    {
        $token = config('services.finnhub.key');

        if (! $token) {
            return [];
        }

        return Cache::remember('finnhub.tracked-stocks.v1', now()->addMinutes(5), function () use ($token) {
            $stocks = [];

            foreach (self::STOCK_SYMBOLS as $index => $symbol) {
                $quote = $this->quote($symbol, $token);

                if (! $quote) {
                    continue;
                }

                $currentPrice = (float) data_get($quote, 'c', 0);
                $previousClose = (float) data_get($quote, 'pc', 0);
                $changeValue = (float) data_get($quote, 'd', $currentPrice - $previousClose);
                $changePercent = $previousClose > 0
                    ? ($changeValue / $previousClose) * 100
                    : (float) data_get($quote, 'dp', 0);

                $stocks[] = [
                    'id' => 'stock:' . $symbol,
                    'name' => $symbol,
                    'symbol' => $symbol . '/USD',
                    'price' => number_format($currentPrice, 2, '.', ''),
                    'change' => $changePercent >= 0
                        ? '+' . number_format($changePercent, 1) . '%'
                        : number_format($changePercent, 1) . '%',
                    'high' => number_format((float) data_get($quote, 'h', $currentPrice), 2, '.', ''),
                    'low' => number_format((float) data_get($quote, 'l', $currentPrice), 2, '.', ''),
                    'volume' => 'N/A',
                    'cap' => 'N/A',
                    'icon' => $this->tickerIcon($symbol),
                    'up' => $changePercent >= 0,
                    'market_type' => 'stocks',
                    'favoriteable' => false,
                    'trade_asset' => $symbol,
                    'sort_order' => $index + 1,
                ];
            }

            return $stocks;
        });
    }

    private function quote(string $symbol, string $token): ?array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'X-Finnhub-Token' => $token,
        ])->timeout(10)->retry(2, 250)->get('https://finnhub.io/api/v1/quote', [
            'symbol' => $symbol,
            'token' => $token,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        return (is_array($data) && (float) data_get($data, 'c', 0) > 0) ? $data : null;
    }

    private function tickerIcon(string $symbol): string
    {
        $label = strtoupper(substr($symbol, 0, 4));
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <rect width="64" height="64" rx="16" fill="#111827"/>
  <text x="32" y="38" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="20" font-weight="700" fill="#ffffff">{$label}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
