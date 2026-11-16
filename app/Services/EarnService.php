<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserMining;
use App\Models\UserStaking;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Money movements for staking and mining.
 *
 *  - A stake is paid from staking_balance; a mining contract from mining_balance.
 *  - Rewards, mined amounts and returned principal are paid to the main balance (withdrawable).
 *  - Plan amounts are in the plan currency and converted into the user's currency.
 *
 * Every payout locks the subscription and user rows and pays only what is still owed
 * (staking: accrued − total_rewards; mining: total_mined − total_withdrawn), so a double
 * click or a retried request can never pay twice.
 */
class EarnService
{
    public function stake(User $user, Plan $plan, float $amount, ?string $notes = null): UserStaking
    {
        return DB::transaction(function () use ($user, $plan, $amount, $notes) {
            $locked = User::whereKey($user->getKey())->lockForUpdate()->first();
            $cost = round($locked->convertFrom($amount, $plan->staking_currency ?: 'USD'), 8);
            $this->debit($locked, 'staking_balance', $cost, 'staking');

            return UserStaking::create([
                'user_id' => $locked->id,
                'plan_id' => $plan->id,
                'amount_staked' => $amount,
                'currency' => $plan->staking_currency,
                'apy_rate' => $plan->apy_rate,
                'start_date' => now(),
                'end_date' => $plan->staking_duration ? now()->addDays($plan->staking_duration) : null,
                'status' => UserStaking::STATUS_ACTIVE,
                'total_rewards' => 0,
                'notes' => $notes,
            ]);
        });
    }

    /** Rewards earned so far that have not been paid out yet (plan currency). Accrues per full day and stops at end_date. */
    public function unpaidRewards(UserStaking $staking): float
    {
        if (! $staking->apy_rate || ! $staking->start_date) {
            return 0.0;
        }
        $until = $staking->end_date && $staking->end_date->isPast() ? $staking->end_date : now();
        $days = max(0, (int) floor($staking->start_date->diffInDays($until))); // rewards accrue per full day
        $accrued = (float) $staking->amount_staked * ((float) $staking->apy_rate / 100 / 365) * $days;

        return max(0.0, round($accrued - (float) $staking->total_rewards, 8));
    }

    /** Pay unpaid rewards to the main balance. Returns the amount paid (user's currency). */
    public function withdrawStakingRewards(UserStaking $staking): float
    {
        return DB::transaction(function () use ($staking) {
            $s = UserStaking::whereKey($staking->getKey())->lockForUpdate()->first();
            if (! $s || ! $s->isActive()) {
                throw ValidationException::withMessages(['staking' => 'This staking subscription is not active.']);
            }
            $paid = $this->payRewards($s);
            if ($paid <= 0) {
                throw ValidationException::withMessages(['staking' => 'No rewards available for withdrawal yet.']);
            }

            return $paid;
        });
    }

    /**
     * End a stake: pay unpaid rewards and return the principal to the main balance.
     *
     * @return array{principal: float, rewards: float} amounts in the user's currency
     */
    public function cancelStake(UserStaking $staking): array
    {
        return DB::transaction(function () use ($staking) {
            $s = UserStaking::whereKey($staking->getKey())->lockForUpdate()->first();
            if (! $s || ! $s->isActive()) {
                throw ValidationException::withMessages(['staking' => 'This staking subscription is not active.']);
            }
            $rewards = $this->payRewards($s);

            $user = User::whereKey($s->user_id)->lockForUpdate()->first();
            $principal = round($user->convertFrom((float) $s->amount_staked, $s->currency ?: 'USD'), 8);
            $user->increment('balance', $principal);

            $s->update([
                'status' => UserStaking::STATUS_CANCELLED,
                'end_date' => now(),
                'current_value' => $s->amount_staked,
            ]);

            return ['principal' => $principal, 'rewards' => $rewards];
        });
    }

    public function startMining(User $user, Plan $plan, float $amount, ?string $notes = null): UserMining
    {
        return DB::transaction(function () use ($user, $plan, $amount, $notes) {
            $locked = User::whereKey($user->getKey())->lockForUpdate()->first();
            $cost = round($locked->convertFrom($amount, $plan->currency ?: 'USD'), 8);
            $this->debit($locked, 'mining_balance', $cost, 'mining');

            return UserMining::create([
                'user_id' => $locked->id,
                'plan_id' => $plan->id,
                'amount_invested' => $amount,
                'currency' => $plan->currency,
                'hashrate' => $plan->hashrate,
                'equipment' => $plan->equipment,
                'downtime' => $plan->downtime,
                'electricity_costs' => $plan->electricity_costs,
                'start_date' => now(),
                'end_date' => $plan->mining_duration ? now()->addDays($plan->mining_duration) : null,
                'status' => UserMining::STATUS_ACTIVE,
                'notes' => $notes,
            ]);
        });
    }

    /** Mined amount not yet paid out (plan currency). */
    public function unpaidMined(UserMining $mining): float
    {
        return max(0.0, round((float) $mining->total_mined - (float) $mining->total_withdrawn, 8));
    }

    /** Pay the unpaid mined amount to the main balance. Returns the amount paid (user's currency). */
    public function withdrawMined(UserMining $mining): float
    {
        return DB::transaction(function () use ($mining) {
            $m = UserMining::whereKey($mining->getKey())->lockForUpdate()->first();
            $owed = $m ? $this->unpaidMined($m) : 0.0;
            if ($owed <= 0) {
                throw ValidationException::withMessages(['mining' => 'No mined earnings available for withdrawal yet.']);
            }

            $user = User::whereKey($m->user_id)->lockForUpdate()->first();
            $paid = round($user->convertFrom($owed, $m->currency ?: 'USD'), 8);
            $user->increment('balance', $paid);
            $m->update([
                'total_withdrawn' => round((float) $m->total_withdrawn + $owed, 8),
                'last_mining_date' => now(),
            ]);

            return $paid;
        });
    }

    /** Caller holds the staking row lock. */
    private function payRewards(UserStaking $s): float
    {
        $owed = $this->unpaidRewards($s);
        if ($owed <= 0) {
            return 0.0;
        }
        $user = User::whereKey($s->user_id)->lockForUpdate()->first();
        $paid = round($user->convertFrom($owed, $s->currency ?: 'USD'), 8);
        $user->increment('balance', $paid);
        $total = round((float) $s->total_rewards + $owed, 8);
        $s->update([
            'total_rewards' => $total,
            'last_reward_date' => now(),
            'current_value' => round((float) $s->amount_staked + $total, 8),
        ]);

        return $paid;
    }

    private function debit(User $locked, string $field, float $cost, string $label): void
    {
        if ($cost <= 0) {
            throw ValidationException::withMessages(['amount' => 'Enter an amount greater than zero.']);
        }
        if ($cost > (float) $locked->{$field}) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient '.$label.' balance. This needs '.money($cost, 2, $locked)
                    .' and you have '.money((float) $locked->{$field}, 2, $locked).'. Deposit or transfer funds first.',
            ]);
        }
        $locked->decrement($field, $cost);
    }
}
