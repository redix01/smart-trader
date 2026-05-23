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

        $user = $request->user();
        $asset = strtoupper((string) $request->string('asset'));
        $defaultPair = $pairs->first();

        if ($asset !== '') {
            $defaultPair = $pairs->firstWhere('name', $asset . '/USDT')
                ?? $pairs->firstWhere('name', $asset . '/USD')
                ?? $pairs->first(fn (array $pair) => str_starts_with($pair['name'], $asset . '/'))
                ?? $pairs->first(fn (array $pair) => str_ends_with($pair['name'], '/' . $asset))
                ?? $defaultPair;
        }

        return Inertia::render('Trades', [
            'pairs' => $pairs,
            'defaultPair' => $defaultPair,
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
            'pair_id' => 'required|exists:market_pairs,id',
            'side' => 'required|in:buy,sell',
            'type' => 'required|in:Market,Limit',
            'amount' => 'required|numeric|min:0.00000001',
            'price' => 'nullable|numeric|min:0.00000001',
        ]);

        $this->trades->placeOrder(
            $request->user(),
            (int) $data['pair_id'],
            $data['side'],
            $data['type'],
            (float) $data['amount'],
            isset($data['price']) ? (float) $data['price'] : null,
        );

        return redirect()->route('trades', ['asset' => explode('/', (string) MarketPair::findOrFail($data['pair_id'])->name)[0]])
            ->with('success', 'Trade executed successfully.');
    }
}
