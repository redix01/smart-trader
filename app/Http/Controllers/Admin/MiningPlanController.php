<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MiningPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MiningPlanController extends Controller
{
    public function index()
    {
        $plans = MiningPlan::orderBy('min_amount')->paginate(20);
        return Inertia::render('Admin/MiningPlans/Index', ['plans' => $plans]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'roi_percent' => 'required|numeric|min:0|max:1000',
            'duration_days' => 'required|integer|min:1',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        MiningPlan::create($data);
        return redirect()->route('admin.mining-plans.index')->with('success', 'Mining plan created.');
    }

    public function update(Request $request, MiningPlan $miningPlan)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'icon' => 'nullable|string|max:100',
            'roi_percent' => 'sometimes|numeric|min:0|max:1000',
            'duration_days' => 'sometimes|integer|min:1',
            'min_amount' => 'sometimes|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $miningPlan->update($data);
        return redirect()->back()->with('success', 'Mining plan updated.');
    }

    public function destroy(MiningPlan $miningPlan)
    {
        $miningPlan->delete();
        return redirect()->route('admin.mining-plans.index')->with('success', 'Mining plan deleted.');
    }
}
