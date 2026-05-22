<?php

namespace App\Services;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Collection;

class WithdrawalService
{
    public function createWithdrawal(User $user, array $data): Withdrawal
    {
        $amount = (float) $data['amount'];
        $fee = $amount * 0.01;
        $netAmount = $amount - $fee;

        return Withdrawal::create([
            'user_id' => $user->id,
            'method' => $data['method'],
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $netAmount,
            'currency' => $data['currency'] ?? 'USD',
            'destination_details' => $data['destination'] ?? [],
            'status' => 'pending',
        ]);
    }

    public function getUserWithdrawals(User $user): Collection
    {
        return $user->withdrawals()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Withdrawal $w) => [
                'id' => $w->id,
                'method' => $w->method,
                'amount' => number_format((float) $w->amount, 2),
                'fee' => number_format((float) $w->fee, 2),
                'net' => number_format((float) $w->net_amount, 2),
                'status' => $w->status,
                'date' => $w->created_at->format('Y-m-d H:i'),
            ]);
    }

    public function getUserBalance(User $user): float
    {
        return (float) $user->wallets()
            ->where('currency', 'USD')
            ->value('balance') ?? 0;
    }
}
