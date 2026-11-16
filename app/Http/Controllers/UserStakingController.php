<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\UserStaking;
use Illuminate\Http\Request;
use App\Services\EarnService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserStakingController extends Controller
{
    /**
     * Display user's staking dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's active staking subscriptions
        $activeStakings = UserStaking::where('user_id', $user->id)
            ->active()
            ->with('plan')
            ->latest()
            ->paginate(10);
        
        // Get completed staking subscriptions
        $completedStakings = UserStaking::where('user_id', $user->id)
            ->completed()
            ->with('plan')
            ->latest()
            ->paginate(10);
        
        // Get available staking plans
        $stakingPlans = Plan::ofType('staking')->active()->ordered()->get();
        
        return view('dashboard.staking.index', compact('activeStakings', 'completedStakings', 'stakingPlans'));
    }

    /**
     * Show the form for creating a new staking subscription
     */
    public function create()
    {
        $user = Auth::user();
        $stakingPlans = Plan::ofType('staking')->active()->ordered()->get();
        
        return view('dashboard.staking.create', compact('stakingPlans', 'user'));
    }

    /**
     * Store a newly created staking subscription
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'amount_staked' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        // Verify it's a staking plan
        if ($plan->type !== 'staking') {
            return redirect()->back()
                ->with('error', 'Selected plan is not a staking plan.')
                ->withInput();
        }

        // Check if staking_currency is set
        if (!$plan->staking_currency) {
            return redirect()->back()
                ->with('error', 'This staking plan is not properly configured. Please contact support.')
                ->withInput();
        }

        // Check minimum amount
        if ($plan->minimum_amount && $request->amount_staked < $plan->minimum_amount) {
            return redirect()->back()
                ->with('error', 'Amount must be at least ' . number_format($plan->minimum_amount, 8) . ' ' . ($plan->staking_currency ?? 'USD'))
                ->withInput();
        }

        try {
            $staking = app(EarnService::class)->stake($user, $plan, (float) $request->amount_staked, $request->notes);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->with('error', collect($e->errors())->flatten()->first())->withInput();
        }

        return redirect()->route('user.staking.show', $staking)
            ->with('success', 'Staking started. '.money_from($staking->amount_staked, $staking->currency ?: 'USD').' was moved from your staking balance.');
    }

    /**
     * Display the specified staking subscription
     */
    public function show(UserStaking $staking)
    {
        $user = Auth::user();
        
        // Ensure user can only view their own staking
        if ($staking->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $staking->load('plan');
        
        return view('dashboard.staking.show', compact('staking'));
    }

    /**
     * Cancel the specified staking subscription
     */
    public function cancel(UserStaking $staking)
    {
        $user = Auth::user();
        
        // Ensure user can only cancel their own staking
        if ($staking->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$staking->isActive()) {
            return redirect()->back()
                ->with('error', 'This staking subscription is not active.');
        }

        // Check if there's a lock period
        if ($staking->plan->lock_period) {
            $lockEndDate = $staking->start_date->addDays($staking->plan->lock_period);
            if (now()->lt($lockEndDate)) {
                return redirect()->back()
                    ->with('error', 'Cannot cancel staking during lock period. Lock period ends on ' . $lockEndDate->format('M d, Y H:i'));
            }
        }

        try {
            $paid = app(EarnService::class)->cancelStake($staking);
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('user.staking.index')
            ->with('success', 'Staking ended. '.money($paid['principal'] + $paid['rewards']).' was returned to your main balance'
                .($paid['rewards'] > 0 ? ' (including '.money($paid['rewards']).' of rewards).' : '.'));
    }

    /**
     * Withdraw rewards from the specified staking subscription
     */
    public function withdraw(UserStaking $staking)
    {
        $user = Auth::user();
        
        // Ensure user can only withdraw from their own staking
        if ($staking->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $paid = app(EarnService::class)->withdrawStakingRewards($staking);
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->back()
            ->with('success', money($paid).' of rewards was added to your main balance.');
    }

    /**
     * Get staking statistics for user
     */
    public function statistics()
    {
        $user = Auth::user();
        
        // Calculate statistics
        $totalStakings = UserStaking::where('user_id', $user->id)->count();
        $activeStakings = UserStaking::where('user_id', $user->id)->active()->count();
        $totalStaked = UserStaking::where('user_id', $user->id)->active()->sum('amount_staked');
        $totalRewards = UserStaking::where('user_id', $user->id)->sum('total_rewards');
        
        // Get recent stakings
        $recentStakings = UserStaking::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('dashboard.staking.statistics', compact(
            'totalStakings',
            'activeStakings',
            'totalStaked',
            'totalRewards',
            'recentStakings'
        ));
    }
}
