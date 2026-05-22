<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StakingPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StakingPlanController extends Controller
{
    public function index()
    {
        $plans = StakingPlan::orderBy('sort_order')->paginate(20);
        return Inertia::render('Admin/StakingPlans/Index', ['plans' => $plans]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'icon' => 'nullable|string|max:500',
            'apy' => 'required|numeric|min:0|max:100',
            'payout_cycle' => 'in:daily,weekly,monthly',
            'duration_days' => 'required|integer|min:1',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        StakingPlan::create($data);
        return redirect()->route('admin.staking-plans.index')->with('success', 'Staking plan created.');
    }

    public function update(Request $request, StakingPlan $stakingPlan)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'currency' => 'sometimes|string|max:10',
            'icon' => 'nullable|string|max:500',
            'apy' => 'sometimes|numeric|min:0|max:100',
            'payout_cycle' => 'in:daily,weekly,monthly',
            'duration_days' => 'sometimes|integer|min:1',
            'min_amount' => 'sometimes|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $stakingPlan->update($data);
        return redirect()->back()->with('success', 'Staking plan updated.');
    }

    public function destroy(StakingPlan $stakingPlan)
    {
        $stakingPlan->delete();
        return redirect()->route('admin.staking-plans.index')->with('success', 'Staking plan deleted.');
    }
}
