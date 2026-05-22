<?php

namespace App\Providers;

use App\Models\Deposit;
use App\Models\Stake;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Policies\AdminPolicy;
use App\Policies\DepositPolicy;
use App\Policies\StakingPolicy;
use App\Policies\WalletPolicy;
use App\Policies\WithdrawalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::policy(Wallet::class, WalletPolicy::class);
        Gate::policy(Deposit::class, DepositPolicy::class);
        Gate::policy(Withdrawal::class, WithdrawalPolicy::class);
        Gate::policy(Stake::class, StakingPolicy::class);
        Gate::define('admin', [AdminPolicy::class, 'accessAdmin']);
        Gate::define('manage-users', [AdminPolicy::class, 'manageUsers']);
        Gate::define('manage-kyc', [AdminPolicy::class, 'manageKyc']);
        Gate::define('manage-settings', [AdminPolicy::class, 'manageSettings']);
    }
}
