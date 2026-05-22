<?php

namespace App\Http\Controllers;

use App\Models\MarketPair;
use App\Services\CoinMarketCapService;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private PortfolioService $portfolio,
        private CoinMarketCapService $coinMarketCap,
    ) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $this->coinMarketCap->syncMarketPairs();
        $summary = $this->portfolio->summary($user);
        $wallets = collect($summary['wallets']);
        $totalBalance = (float) $wallets->sum('balance');
        $totalLocked = (float) $wallets->sum('locked');
        $progress = $totalBalance > 0 ? (int) round(min(100, max(0, ($totalLocked / $totalBalance) * 100))) : 0;

        $cryptoCurrencies = ['BTC', 'ETH', 'SOL', 'BNB', 'XRP', 'ADA', 'DOT', 'MATIC', 'AVAX', 'LTC', 'USDT'];
        $primaryWallet = $wallets
            ->first(fn (array $wallet) => in_array(strtoupper((string) $wallet['currency']), $cryptoCurrencies, true))
            ?? $wallets->sortByDesc('balance')->first();

        $pair = MarketPair::where('is_active', true)
            ->where('quote_currency', 'USDT')
            ->orderBy('sort_order')
            ->first();

        $chartCurrency = strtoupper($primaryWallet['currency'] ?? $pair?->base_currency ?? 'BTC');

        if (in_array($chartCurrency, ['USD', 'USDT'], true) && $pair) {
            $chartCurrency = $pair->base_currency;
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
