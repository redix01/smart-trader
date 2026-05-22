<?php

namespace App\Http\Controllers;

use App\Services\CoinMarketCapService;
use App\Services\SwapService;
use App\Services\MarketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SwapController extends Controller
{
    public function __construct(
        private SwapService $swap,
        private MarketService $market,
        private CoinMarketCapService $coinMarketCap,
    ) {}

    public function index(Request $request)
    {
        $this->coinMarketCap->syncMarketPairs();

        return Inertia::render('Swap', [
            'pairs' => $this->swap->getSupportedPairs(),
            'history' => $this->swap->getUserSwaps($request->user()),
        ]);
    }

    public function quote(Request $request)
    {
        $this->coinMarketCap->syncMarketPairs();

        $data = $request->validate([
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $quote = $this->swap->getQuote($data['from'], $data['to'], (float) $data['amount']);

        if (!$quote) {
            return response()->json(['error' => 'Unsupported pair'], 422);
        }

        return response()->json($quote);
    }

    public function store(Request $request)
    {
        $this->coinMarketCap->syncMarketPairs();

        $data = $request->validate([
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $this->swap->executeSwap(
            $request->user(),
            $data['from'],
            $data['to'],
            (float) $data['amount']
        );

        return redirect()->route('swap')->with('success', 'Swap completed.');
    }
}
