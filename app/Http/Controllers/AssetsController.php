<?php

namespace App\Http\Controllers;

use App\Services\DepositService;
use App\Services\PortfolioService;
use App\Services\CoinMarketCapService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetsController extends Controller
{
    public function __construct(
        private PortfolioService $portfolio,
        private DepositService $deposits,
        private CoinMarketCapService $coinMarketCap,
    ) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $this->coinMarketCap->syncMarketPairs();
        $approvedDeposits = $this->deposits->getUserDeposits($user)
            ->filter(fn (array $deposit) => $deposit['status'] === 'approved')
            ->values();

        return Inertia::render('Assets', [
            'assets' => $this->portfolio->balancesWithMarketData($user),
            'deposits' => $approvedDeposits,
            'summary' => $this->portfolio->summary($user),
        ]);
    }
}
