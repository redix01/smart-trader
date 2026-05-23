<?php

namespace App\Http\Controllers;

use App\Services\CoinMarketCapService;
use App\Services\MarketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketsController extends Controller
{
    public function __construct(
        private MarketService $market,
        private CoinMarketCapService $coinMarketCap,
    ) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $this->coinMarketCap->syncMarketPairs();

        return Inertia::render('Markets', [
            'cryptoMarkets' => $this->market->getAllPairs(),
            'stockMarkets' => $this->market->getStocks(),
            'topGainers' => $this->market->getTopGainers(),
            'overview' => $this->market->getOverview(),
            'favorites' => $this->market->getUserFavorites($user),
        ]);
    }

    public function toggleFavorite(Request $request, int $marketPairId)
    {
        $this->market->toggleFavorite($request->user(), $marketPairId);

        return redirect()->route('markets');
    }
}
