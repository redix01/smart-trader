<?php

namespace App\Http\Controllers;

use App\Services\DepositService;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetsController extends Controller
{
    public function __construct(
        private PortfolioService $portfolio,
        private DepositService $deposits,
    ) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $approvedDeposits = $this->deposits->getUserDeposits($user)
            ->filter(fn (array $deposit) => $deposit['status'] === 'approved')
            ->values();

        return Inertia::render('Assets', [
            'assets' => $this->portfolio->balancesByCurrency($user),
            'deposits' => $approvedDeposits,
            'summary' => $this->portfolio->summary($user),
        ]);
    }
}
