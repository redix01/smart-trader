<?php

namespace App\Services;

use App\Models\CopySubscription;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Support\Collection;

class ExpertService
{
    public function __construct(private UserNotificationService $notifications) {}

    public function getActiveExperts(): Collection
    {
        return Expert::where('is_active', true)
            ->get()
            ->map(fn (Expert $e) => [
                'id' => $e->id,
                'name' => $e->name,
                'avatar' => $e->avatar,
                'win_rate' => number_format((float) $e->win_rate, 0) . '%',
                'profit_share' => number_format((float) $e->profit_share, 0) . '%',
                'status' => $e->status,
                'total_volume' => $this->formatVolume((float) $e->total_volume),
            ]);
    }

    public function subscribe(User $user, int $expertId, float $amount): CopySubscription
    {
        $expert = Expert::findOrFail($expertId);

        $subscription = CopySubscription::create([
            'user_id' => $user->id,
            'expert_id' => $expertId,
            'allocation_amount' => $amount,
            'current_value' => $amount,
            'status' => 'active',
        ]);

        $this->notifications->sendExpertSubscriptionCreated($user, $subscription, $expert->name);

        return $subscription;
    }

    public function getUserSubscriptions(User $user): Collection
    {
        return $user->copySubscriptions()
            ->with('expert')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (CopySubscription $s) => [
                'id' => $s->id,
                'expert_name' => $s->expert?->name,
                'allocated' => number_format((float) $s->allocation_amount, 2),
                'current_value' => number_format((float) $s->current_value, 2),
                'status' => $s->status,
                'date' => $s->created_at->format('Y-m-d'),
            ]);
    }

    private function formatVolume(float $value): string
    {
        if ($value >= 1_000_000) {
            return number_format($value / 1_000_000, 1) . 'M';
        }
        return number_format($value, 0);
    }
}
