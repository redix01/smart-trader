<?php

namespace App\Services;

use App\Models\MiningPlan;
use App\Models\MiningSubscription;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class MiningService
{
    public function getActivePlans(): Collection
    {
        return MiningPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->toBase()
            ->map(fn (MiningPlan $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'icon' => $p->icon,
                'min' => (float) $p->min_amount,
                'max' => (float) ($p->max_amount ?? 0),
                'roi' => number_format((float) $p->roi_percent, 0) . '%',
                'roi_percent' => (float) $p->roi_percent,
                'duration' => $p->duration_days . ' DAYS',
                'duration_days' => $p->duration_days,
                'color' => $this->getColor($p->icon ?? $p->name),
            ]);
    }

    public function getUserSubscriptions(User $user): Collection
    {
        return $user->miningSubscriptions()
            ->with('miningPlan')
            ->orderByDesc('created_at')
            ->get()
            ->toBase()
            ->map(fn (MiningSubscription $s) => [
                'id' => $s->id,
                'plan_name' => $s->miningPlan?->name,
                'amount' => (float) $s->amount,
                'earned' => (float) $s->earned_so_far,
                'status' => $s->status,
                'start_date' => $s->start_date->format('Y-m-d'),
                'end_date' => $s->end_date?->format('Y-m-d'),
            ]);
    }

    public function createSubscription(User $user, int $planId, float $amount): MiningSubscription
    {
        $plan = MiningPlan::findOrFail($planId);

        if ($amount < (float) $plan->min_amount) {
            throw ValidationException::withMessages([
                'amount' => 'Minimum subscription amount is $' . number_format((float) $plan->min_amount, 2),
            ]);
        }

        if ($plan->max_amount && $amount > (float) $plan->max_amount) {
            throw ValidationException::withMessages([
                'amount' => 'Maximum subscription amount is $' . number_format((float) $plan->max_amount, 2),
            ]);
        }

        if (!$plan->is_active) {
            throw ValidationException::withMessages([
                'plan_id' => 'This mining plan is no longer available.',
            ]);
        }

        return MiningSubscription::create([
            'user_id' => $user->id,
            'mining_plan_id' => $plan->id,
            'amount' => $amount,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_days),
        ]);
    }

    private function getColor(string $key): string
    {
        return match (strtolower($key)) {
            'starter', 'bronze' => 'from-blue-600/20 to-blue-600/5',
            'lite', 'silver' => 'from-zinc-400/20 to-zinc-400/5',
            'classic', 'gold' => 'from-yellow-600/20 to-yellow-600/5',
            'pro', 'platinum' => 'from-emerald-600/20 to-emerald-600/5',
            'enterprise', 'diamond' => 'from-indigo-600/20 to-indigo-600/5',
            default => 'from-blue-600/20 to-blue-600/5',
        };
    }
}
