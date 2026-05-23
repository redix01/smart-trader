<?php

namespace App\Services;

use App\Models\Stake;
use App\Models\StakingPlan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StakingService
{
    public function __construct(private WalletService $wallets) {}

    public function getActivePlans(): Collection
    {
        return StakingPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->toBase()
            ->map(fn (StakingPlan $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'symbol' => $p->currency,
                'icon' => $p->icon,
                'icon_bg' => $this->getColor($p->currency),
                'min' => $this->formatAmount($p->min_amount, $p->currency),
                'max' => $p->max_amount ? $this->formatAmount($p->max_amount, $p->currency) : 'Unlimited',
                'cycle' => ucfirst($p->payout_cycle),
                'apy' => number_format((float) $p->apy, 1) . '%',
                'duration_days' => $p->duration_days,
            ]);
    }

    public function getUserStakes(User $user): Collection
    {
        return $user->stakes()
            ->with('stakingPlan')
            ->orderByDesc('created_at')
            ->get()
            ->toBase()
            ->map(fn (Stake $s) => [
                'id' => $s->id,
                'plan_name' => $s->stakingPlan?->name,
                'amount' => number_format((float) $s->amount, 6),
                'rewards' => number_format((float) $s->accrued_rewards, 6),
                'status' => $s->status,
                'start_date' => $s->start_date->format('Y-m-d'),
                'end_date' => $s->end_date?->format('Y-m-d'),
            ]);
    }

    public function createStake(User $user, int $planId, float $amount): Stake
    {
        return DB::transaction(function () use ($user, $planId, $amount) {
            $plan = StakingPlan::findOrFail($planId);

            if ($amount < (float) $plan->min_amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Minimum staking amount is ' . $this->formatAmount((float) $plan->min_amount, $plan->currency) . ' ' . strtoupper($plan->currency),
                ]);
            }

            if ($plan->max_amount && $amount > (float) $plan->max_amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Maximum staking amount is ' . $this->formatAmount((float) $plan->max_amount, $plan->currency) . ' ' . strtoupper($plan->currency),
                ]);
            }

            $this->wallets->debit($user, $plan->currency, $amount);

            return Stake::create([
                'user_id' => $user->id,
                'staking_plan_id' => $plan->id,
                'amount' => $amount,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration_days),
            ]);
        });
    }

    private function getColor(string $currency): string
    {
        return match (strtoupper($currency)) {
            'BTC' => 'bg-orange-600',
            'ETH' => 'bg-blue-600',
            'SOL' => 'bg-cyan-600',
            'AVAX' => 'bg-rose-600',
            'MATIC', 'POL' => 'bg-purple-600',
            'USDT' => 'bg-emerald-600',
            default => 'bg-blue-600',
        };
    }

    private function formatAmount(float $value, string $currency): string
    {
        if (in_array(strtoupper($currency), ['BTC', 'ETH', 'SOL'])) {
            return number_format($value, 4);
        }
        return number_format($value, 0);
    }
}
