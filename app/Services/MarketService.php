<?php

namespace App\Services;

use App\Models\MarketPair;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Support\Collection;

class MarketService
{
    public function __construct(
        private CoinMarketCapService $coinMarketCap,
        private FinnhubService $finnhub,
    ) {}

    public function syncCryptoPrices(): void
    {
        $this->coinMarketCap->syncMarketPairs();
    }

    public function getAllPairs(): Collection
    {
        $this->syncCryptoPrices();

        return MarketPair::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('base_currency')
            ->get()
            ->map(fn (MarketPair $p) => $this->formatPair($p));
    }

    public function getStocks(): Collection
    {
        return collect($this->finnhub->getTrackedStocks());
    }

    public function getTopGainers(int $limit = 4): Collection
    {
        $this->syncCryptoPrices();

        return MarketPair::where('is_active', true)
            ->where('price_change_24h', '>', 0)
            ->orderByDesc('price_change_24h')
            ->limit($limit)
            ->get()
            ->map(fn (MarketPair $p) => $this->formatPair($p));
    }

    public function getOverview(): array
    {
        $this->syncCryptoPrices();

        $pairs = MarketPair::where('is_active', true);
        $totalMarketCap = (float) $pairs->sum('market_cap');
        $btcMarketCap = (float) MarketPair::where('is_active', true)
            ->where('base_currency', 'BTC')
            ->where('quote_currency', 'USDT')
            ->value('market_cap') ?: 0;

        return [
            'total_market_cap' => '$' . number_format($totalMarketCap, 2),
            'total_volume_24h' => '$' . number_format((float) $pairs->sum('volume_24h'), 2),
            'active_pairs' => $pairs->count(),
            'btc_dominance' => $totalMarketCap > 0
                ? number_format(($btcMarketCap / $totalMarketCap) * 100, 1) . '%'
                : '0.0%',
        ];
    }

    public function getUserFavorites(User $user): array
    {
        return Watchlist::where('user_id', $user->id)
            ->with('marketPair')
            ->get()
            ->pluck('market_pair_id')
            ->toArray();
    }

    public function toggleFavorite(User $user, int $marketPairId): bool
    {
        $existing = Watchlist::where('user_id', $user->id)
            ->where('market_pair_id', $marketPairId)
            ->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        Watchlist::create([
            'user_id' => $user->id,
            'market_pair_id' => $marketPairId,
        ]);

        return true;
    }

    private function formatPair(MarketPair $p): array
    {
        return [
            'id' => $p->id,
            'name' => $p->base_currency,
            'symbol' => $p->name,
            'price' => number_format((float) $p->current_price, 2),
            'change' => (float) $p->price_change_24h >= 0
                ? '+' . number_format((float) $p->price_change_24h, 1) . '%'
                : number_format((float) $p->price_change_24h, 1) . '%',
            'high' => number_format((float) $p->high_24h, 2),
            'low' => number_format((float) $p->low_24h, 2),
            'volume' => $this->formatVolume((float) $p->volume_24h),
            'cap' => $this->formatVolume((float) $p->market_cap),
            'icon' => $p->icon,
            'up' => (float) $p->price_change_24h >= 0,
            'market_type' => 'crypto',
            'favoriteable' => true,
            'trade_asset' => $p->base_currency,
        ];
    }

    private function formatVolume(float $value): string
    {
        if ($value >= 1_000_000_000) {
            return number_format($value / 1_000_000_000, 1) . 'B';
        }
        if ($value >= 1_000_000) {
            return number_format($value / 1_000_000, 1) . 'M';
        }
        return number_format($value, 0);
    }
}
