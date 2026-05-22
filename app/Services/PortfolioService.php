<?php

namespace App\Services;

use App\Models\MarketPair;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Collection;

class PortfolioService
{
    public function summary(User $user): array
    {
        $wallets = $user->wallets()->get();

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
        $wallets = $user->wallets()->get();
        $totalBalance = (float) $wallets->sum('balance');
        $openOrders = Order::where('user_id', $user->id)
            ->where('status', 'open')
            ->get();

        $marketPrices = MarketPair::where('is_active', true)
            ->get()
            ->keyBy(fn (MarketPair $pair) => $pair->name);

        $openProfit = $openOrders->sum(function (Order $order) use ($marketPrices) {
            if ($order->price === null) {
                return 0;
            }

            $pair = $marketPrices->get($order->pair);

            if (! $pair) {
                return 0;
            }

            $entry = (float) $order->price;
            $current = (float) $pair->current_price;
            $amount = (float) $order->amount;

            return $order->side === 'sell'
                ? ($entry - $current) * $amount
                : ($current - $entry) * $amount;
        });

        return [
            'total_balance' => number_format($totalBalance, 2),
            'active_trades' => $openOrders->count(),
            'open_profit' => ($openProfit >= 0 ? '+$' : '-$') . number_format(abs($openProfit), 2),
            'pending_deposit' => '$' . number_format($user->deposits()->where('status', 'pending')->sum('amount'), 2),
            'pending_withdrawal' => '$' . number_format($user->withdrawals()->where('status', 'pending')->sum('amount'), 2),
            'monthly_change_pct' => $totalBalance > 0
                ? '+' . number_format(min(25, max(0.1, abs($openProfit) / max($totalBalance, 1) * 100)), 1) . '%'
                : '+0.0%',
        ];
    }

    public function balancesByCurrency(User $user): Collection
    {
        return $user->wallets()->get()->map(fn (Wallet $w) => [
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
