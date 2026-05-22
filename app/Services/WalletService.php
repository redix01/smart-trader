<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function getOrCreateWallet(User $user, string $currency): Wallet
    {
        return $user->wallets()->firstOrCreate(
            ['currency' => strtoupper($currency)],
            [
                'label' => strtoupper($currency),
                'balance' => 0,
                'locked_balance' => 0,
                'is_active' => true,
            ]
        );
    }

    public function debit(User $user, string $currency, float $amount, string $errorKey = 'amount'): Wallet
    {
        $wallet = $user->wallets()
            ->where('currency', strtoupper($currency))
            ->lockForUpdate()
            ->first();

        if (! $wallet || $wallet->available_balance < $amount) {
            throw ValidationException::withMessages([
                $errorKey => 'Insufficient ' . strtoupper($currency) . ' balance.',
            ]);
        }

        $wallet->decrement('balance', $amount);

        return $wallet->fresh();
    }

    public function credit(User $user, string $currency, float $amount): Wallet
    {
        $wallet = $this->getOrCreateWallet($user, $currency);
        $wallet->increment('balance', $amount);

        return $wallet->fresh();
    }
}
