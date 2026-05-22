<?php

namespace App\Http\Controllers;

use App\Services\PortfolioService;
use App\Services\MarketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(private PortfolioService $portfolio, private MarketService $market) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $this->market->syncCryptoPrices();
        $summary = $this->portfolio->summary($user);
        $wallets = collect($summary['wallets']);
        $totalBalance = (float) $wallets->sum('balance');
        $totalLocked = (float) $wallets->sum('locked');
        $progress = $totalBalance > 0 ? (int) round(min(100, max(0, ($totalLocked / $totalBalance) * 100))) : 0;

        $primaryWallet = $wallets->sortByDesc('balance')->first();
        $chartCurrency = strtoupper($primaryWallet['currency'] ?? 'BTC');
        $cryptoCurrencies = ['BTC', 'ETH', 'SOL', 'BNB', 'XRP', 'ADA', 'DOT', 'MATIC', 'AVAX', 'LTC'];

        if (in_array($chartCurrency, ['USD', 'USDT'], true)) {
            $chartCurrency = 'BTC';
        }

        $isCrypto = in_array($chartCurrency, $cryptoCurrencies, true);

        return Inertia::render('Dashboard', [
            'kpi' => $this->portfolio->dashboardKpi($user),
            'wallets' => $this->portfolio->balancesByCurrency($user),
            'kyc_status' => $user->kyc_level,
            'chart' => [
                'title' => $chartCurrency . ' / ' . ($isCrypto ? 'USDT' : 'USD'),
                'subtitle' => $chartCurrency . ($isCrypto ? ' spot market' : ' forex market'),
                'symbol' => $isCrypto ? 'BINANCE:' . $chartCurrency . 'USDT' : 'FX:' . $chartCurrency . 'USD',
                'progress' => $progress,
            ],
        ]);
    }
}
