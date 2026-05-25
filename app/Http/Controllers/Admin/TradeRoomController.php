<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketPair;
use App\Models\User;
use App\Services\CoinMarketCapService;
use App\Services\TradeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TradeRoomController extends Controller
{
    public function __construct(
        private CoinMarketCapService $coinMarketCap,
        private TradeService $trades,
    ) {}

    public function index(Request $request)
    {
        $this->coinMarketCap->syncMarketPairs();

        $users = User::where('account_tier', '!=', 'admin')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
        $selectedUser = $request->integer('user_id')
            ? User::find($request->integer('user_id'))
            : $users->first();

        return Inertia::render('Admin/TradeRoom/Index', [
            'users' => $users,
            'selectedUser' => $selectedUser,
            'pairs' => $this->cryptoPairs(),
            'stockPairs' => collect($this->trades->getStaticPairs('stocks')),
            'forexPairs' => collect($this->trades->getStaticPairs('forex')),
            'defaultPair' => $this->cryptoPairs()->first(),
            'defaultMarketType' => 'crypto',
            'balances' => $selectedUser ? $this->balances($selectedUser) : [],
            'history' => $selectedUser ? $this->trades->getUserTradeHistory($selectedUser, 10) : [],
        ]);
    }

    public function store(Request $request)
    {
        $this->coinMarketCap->syncMarketPairs();

        $data = $request->validate($this->tradeRules($request));

        $user = User::findOrFail($data['user_id']);

        $this->trades->placeOrder(
            $user,
            $data['market_type'],
            isset($data['pair_id']) ? (int) $data['pair_id'] : null,
            $data['pair'],
            $data['side'],
            $data['type'],
            (float) $data['amount'],
            isset($data['price']) ? (float) $data['price'] : null,
        );

        return redirect()->route('admin.trade-room.index', ['user_id' => $user->id])
            ->with('success', 'Trade placed for '.$user->name.'.');
    }

    private function tradeRules(Request $request): array
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'market_type' => 'required|in:crypto,stocks,forex',
            'pair' => 'required|string|max:32',
            'side' => 'required|in:buy,sell',
            'type' => 'required|in:Market,Limit',
            'amount' => 'required|numeric|min:0.00000001',
            'price' => 'nullable|numeric|min:0.00000001',
        ];

        $rules['pair_id'] = $request->input('market_type') === 'crypto'
            ? 'required|exists:market_pairs,id'
            : 'nullable|integer';

        return $rules;
    }

    private function cryptoPairs()
    {
        return MarketPair::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('base_currency')
            ->get()
            ->map(fn (MarketPair $pair) => [
                'id' => $pair->id,
                'name' => $pair->name,
                'price' => number_format((float) $pair->current_price, 2),
                'change' => (float) $pair->price_change_24h >= 0
                    ? '+'.number_format((float) $pair->price_change_24h, 1).'%'
                    : number_format((float) $pair->price_change_24h, 1).'%',
                'up' => (float) $pair->price_change_24h >= 0,
                'icon' => $pair->icon,
            ]);
    }

    private function balances(User $user)
    {
        return $user->wallets()->get()->map(fn ($wallet) => [
            'label' => $wallet->currency,
            'value' => $wallet->balance,
            'color' => match ($wallet->currency) {
                'BTC' => 'text-orange-500',
                'ETH' => 'text-blue-500',
                'USDT', 'USD' => 'text-emerald-500',
                default => 'text-zinc-400',
            },
        ]);
    }
}
