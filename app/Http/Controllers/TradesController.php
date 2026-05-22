<?php

namespace App\Http\Controllers;

use App\Models\MarketPair;
use App\Services\CoinMarketCapService;
use App\Services\MarketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TradesController extends Controller
{
    public function __construct(
        private MarketService $market,
        private CoinMarketCapService $coinMarketCap,
    ) {}

    public function index(Request $request)
    {
        $this->coinMarketCap->syncMarketPairs();

        $pairs = MarketPair::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('base_currency')
            ->get()
            ->map(fn (MarketPair $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => number_format((float) $p->current_price, 2),
                'change' => (float) $p->price_change_24h >= 0
                    ? '+' . number_format((float) $p->price_change_24h, 1) . '%'
                    : number_format((float) $p->price_change_24h, 1) . '%',
                'up' => (float) $p->price_change_24h >= 0,
                'icon' => $p->icon,
            ]);

        $user = $request->user();

        return Inertia::render('Trades', [
            'pairs' => $pairs,
            'defaultPair' => $pairs->first(),
            'balances' => $user->wallets->map(fn ($w) => [
                'label' => $w->currency,
                'value' => $w->balance,
                'color' => match ($w->currency) {
                    'BTC' => 'text-orange-500',
                    'ETH' => 'text-blue-500',
                    'USDT' => 'text-emerald-500',
                    default => 'text-zinc-400',
                },
            ]),
        ]);
    }
}
