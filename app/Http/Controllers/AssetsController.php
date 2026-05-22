<?php

namespace App\Http\Controllers;

use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetsController extends Controller
{
    public function __construct(private PortfolioService $portfolio) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Assets', [
            'assets' => $this->portfolio->balancesByCurrency($user),
            'summary' => $this->portfolio->summary($user),
        ]);
    }
}
