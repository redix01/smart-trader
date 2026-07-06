<?php

namespace App\Services;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawalService
{
    public function __construct(
        private WalletService $wallets,
        private UserNotificationService $notifications,
        private PlatformSettingsService $settings,
    ) {}

    public function createWithdrawal(User $user, array $data): Withdrawal
    {
        $withdrawal = DB::transaction(function () use ($user, $data) {
            $currency = strtoupper((string) ($data['currency'] ?? 'USD'));
            $amount = (float) $data['amount'];
            $feePercent = $this->settings->getPercent('withdrawal_fee', 1.0);
            $fee = $amount * ($feePercent / 100);
            $wallet = $user->wallets()
                ->where('currency', $currency)
                ->lockForUpdate()
                ->first();

            if (! $wallet || $wallet->available_balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient ' . $currency . ' balance.',
                ]);
            }

            $wallet->decrement('balance', $amount);

            return Withdrawal::create([
                'user_id' => $user->id,
                'method' => $data['method'],
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $amount - $fee,
                'currency' => $currency,
                'destination_details' => $data['destination'] ?? [],
                'status' => 'pending',
            ]);
        });

        $this->notifications->sendWithdrawalSubmitted($user, $withdrawal);

        return $withdrawal;
    }

    public function getUserWithdrawals(User $user): Collection
    {
        return $user->withdrawals()
            ->orderByDesc('created_at')
            ->get()
            ->toBase()
            ->map(fn (Withdrawal $w) => [
                'id' => $w->id,
                'method' => $w->method,
                'amount' => number_format((float) $w->amount, 2),
                'fee' => number_format((float) $w->fee, 2),
                'net' => number_format((float) $w->net_amount, 2),
                'currency' => $w->currency,
                'status' => $w->status,
                'date' => $w->created_at->format('Y-m-d H:i'),
            ]);
    }

    public function getUserBalance(User $user, string $currency = 'USD'): float
    {
        return (float) $user->wallets()
            ->where('currency', strtoupper($currency))
            ->value('balance') ?? 0;
    }

    public function getUserWallets(User $user): array
    {
        return $user->wallets()
            ->orderBy('currency')
            ->get()
            ->map(fn ($wallet) => [
                'symbol' => $wallet->currency,
                'balance' => (float) $wallet->balance,
                'available' => (float) $wallet->available_balance,
            ])
            ->values()
            ->all();
    }
}
