<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Collection;

class PortfolioService
{
    public function summary(User $user): array
    {
        $wallets = $user->wallets;

        return [
            'total_balance' => $this->formatBalance($wallets->sum('balance')),
            'total_locked' => $this->formatBalance($wallets->sum('locked_balance')),
            'wallet_count' => $wallets->count(),
            'wallets' => $wallets->map(fn (Wallet $w) => [
                'currency' => $w->currency,
                'balance' => $w->balance,
                'locked' => $w->locked_balance,
                'available' => $w->available_balance,
            ]),
        ];
    }

    public function dashboardKpi(User $user): array
    {
        $wallets = $user->wallets;
        $totalBalance = (float) $wallets->sum('balance');

        return [
            'total_balance' => number_format($totalBalance, 2),
            'active_trades' => rand(1, 20),
            'open_profit' => '+$' . number_format(abs($totalBalance * 0.03), 2),
            'pending_deposit' => '$' . number_format($user->deposits()->where('status', 'pending')->sum('amount'), 2),
            'pending_withdrawal' => '$' . number_format($user->withdrawals()->where('status', 'pending')->sum('amount'), 2),
            'monthly_change_pct' => '+' . number_format(abs($totalBalance * 0.001), 1) . '%',
        ];
    }

    public function balancesByCurrency(User $user): Collection
    {
        return $user->wallets->map(fn (Wallet $w) => [
            'symbol' => $w->currency,
            'balance' => $w->balance,
            'locked' => $w->locked_balance,
            'available' => $w->available_balance,
        ]);
    }

    private function formatBalance(float $value): string
    {
        return number_format($value, 2);
    }
}
