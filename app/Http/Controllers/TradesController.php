<?php

namespace App\Http\Controllers;

use App\Models\MarketPair;
use App\Services\CoinMarketCapService;
use App\Services\MarketService;
use App\Services\TradeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TradesController extends Controller
{
    public function __construct(
        private MarketService $market,
        private CoinMarketCapService $coinMarketCap,
        private TradeService $trades,
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
        $stockPairs = collect($this->trades->getStaticPairs('stocks'));
        $forexPairs = collect($this->trades->getStaticPairs('forex'));

        $user = $request->user();
        $asset = strtoupper((string) $request->string('asset'));
        $defaultPair = $pairs->first();
        $defaultMarketType = 'crypto';

        if ($asset !== '') {
            $defaultPair = $pairs->firstWhere('name', $asset . '/USDT')
                ?? $pairs->firstWhere('name', $asset . '/USD')
                ?? $pairs->first(fn (array $pair) => str_starts_with($pair['name'], $asset . '/'))
                ?? $pairs->first(fn (array $pair) => str_ends_with($pair['name'], '/' . $asset));

            if (! $defaultPair) {
                $defaultPair = $stockPairs->firstWhere('name', $asset . '/USD')
                    ?? $forexPairs->firstWhere('name', $asset . '/USD')
                    ?? $forexPairs->firstWhere('name', 'USD/' . $asset)
                    ?? $stockPairs->first(fn (array $pair) => str_starts_with($pair['name'], $asset . '/'))
                    ?? $forexPairs->first(fn (array $pair) => str_starts_with($pair['name'], $asset . '/'))
                    ?? $forexPairs->first(fn (array $pair) => str_ends_with($pair['name'], '/' . $asset))
                    ?? $pairs->first();
            }

            if ($defaultPair && $stockPairs->contains(fn (array $pair) => $pair['name'] === $defaultPair['name'])) {
                $defaultMarketType = 'stocks';
            } elseif ($defaultPair && $forexPairs->contains(fn (array $pair) => $pair['name'] === $defaultPair['name'])) {
                $defaultMarketType = 'forex';
            }
        }

        return Inertia::render('Trades', [
            'pairs' => $pairs,
            'stockPairs' => $stockPairs,
            'forexPairs' => $forexPairs,
            'defaultPair' => $defaultPair,
            'defaultMarketType' => $defaultMarketType,
            'balances' => $user->wallets()->get()->map(fn ($w) => [
                'label' => $w->currency,
                'value' => $w->balance,
                'color' => match ($w->currency) {
                    'BTC' => 'text-orange-500',
                    'ETH' => 'text-blue-500',
                    'USDT' => 'text-emerald-500',
                    default => 'text-zinc-400',
                },
            ]),
            'history' => $this->trades->getUserTradeHistory($user),
        ]);
    }

    public function store(Request $request)
    {
        $this->coinMarketCap->syncMarketPairs();

        $data = $request->validate([
            'market_type' => 'required|in:crypto,stocks,forex',
            'pair_id' => 'nullable|exists:market_pairs,id',
            'pair' => 'required|string|max:32',
            'side' => 'required|in:buy,sell',
            'type' => 'required|in:Market,Limit',
            'amount' => 'required|numeric|min:0.00000001',
            'price' => 'nullable|numeric|min:0.00000001',
        ]);

        $this->trades->placeOrder(
            $request->user(),
            $data['market_type'],
            isset($data['pair_id']) ? (int) $data['pair_id'] : null,
            $data['pair'],
            $data['side'],
            $data['type'],
            (float) $data['amount'],
            isset($data['price']) ? (float) $data['price'] : null,
        );

        return redirect()->route('trades', ['asset' => explode('/', $data['pair'])[0]])
            ->with('success', 'Trade executed successfully.');
    }
}
