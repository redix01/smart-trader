<?php

namespace App\Http\Controllers;

use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(private PortfolioService $portfolio) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Dashboard', [
            'kpi' => $this->portfolio->dashboardKpi($user),
            'wallets' => $this->portfolio->balancesByCurrency($user),
            'kyc_status' => $user->kyc_level,
        ]);
    }
}
