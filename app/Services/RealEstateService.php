<?php

namespace App\Services;

use App\Models\PropertyInvestment;
use App\Models\PropertyProject;
use App\Models\User;
use Illuminate\Support\Collection;

class RealEstateService
{
    public function __construct(private UserNotificationService $notifications) {}

    public function getActiveProjects(): Collection
    {
        return PropertyProject::where('is_active', true)
            ->get()
            ->map(fn (PropertyProject $p) => [
                'id' => $p->id,
                'title' => $p->title,
                'region' => $p->region,
                'description' => $p->description,
                'min' => '$' . number_format((float) $p->min_investment, 0),
                'roi' => number_format((float) $p->target_roi, 1) . '%',
                'strategy' => $p->strategy,
                'status' => $p->status,
                'image' => $p->image,
            ]);
    }

    public function getUserInvestments(User $user): Collection
    {
        return $user->propertyInvestments()
            ->with('project')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (PropertyInvestment $i) => [
                'id' => $i->id,
                'project_title' => $i->project?->title,
                'amount' => number_format((float) $i->amount, 2),
                'expected_return' => number_format((float) $i->expected_return, 2),
                'payout_received' => number_format((float) $i->payout_received, 2),
                'status' => $i->status,
                'date' => $i->created_at->format('Y-m-d'),
            ]);
    }

    public function invest(User $user, int $projectId, float $amount): PropertyInvestment
    {
        $project = PropertyProject::findOrFail($projectId);
        $expectedReturn = $amount * ((float) $project->target_roi / 100);

        $investment = PropertyInvestment::create([
            'user_id' => $user->id,
            'property_project_id' => $project->id,
            'amount' => $amount,
            'expected_return' => $expectedReturn,
            'status' => 'active',
            'disclosure_signed' => false,
        ]);

        $this->notifications->sendPropertyInvestmentCreated($user, $investment, $project->title);

        return $investment;
    }
}
