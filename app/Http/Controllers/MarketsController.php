<?php

namespace App\Http\Controllers;

use App\Services\MarketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketsController extends Controller
{
    public function __construct(private MarketService $market) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Markets', [
            'markets' => $this->market->getAllPairs(),
            'topGainers' => $this->market->getTopGainers(),
            'overview' => $this->market->getOverview(),
            'favorites' => $this->market->getUserFavorites($user),
        ]);
    }
}
