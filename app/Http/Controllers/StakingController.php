<?php

namespace App\Http\Controllers;

use App\Services\StakingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StakingController extends Controller
{
    public function __construct(private StakingService $staking) {}

    public function index(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Staking', [
            'plans' => $this->staking->getActivePlans(),
            'userStakes' => $this->staking->getUserStakes($user),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:staking_plans,id',
            'amount' => 'required|numeric|min:0.001',
        ]);

        $this->staking->createStake($request->user(), (int) $data['plan_id'], (float) $data['amount']);

        return redirect()->route('stakes')->with('success', 'Stake created successfully.');
    }
}
