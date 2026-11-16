<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\UserMining;
use Illuminate\Http\Request;
use App\Services\EarnService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserMiningController extends Controller
{
    /**
     * Display a listing of user's mining subscriptions
     */
    public function index()
    {
        $user = Auth::user();
        
        $miningPlans = Plan::ofType('mining')->active()->ordered()->get();
        $activeMinings = UserMining::where('user_id', $user->id)
            ->active()
            ->with('plan')
            ->latest()
            ->get();
            
        $completedMinings = UserMining::where('user_id', $user->id)
            ->completed()
            ->with('plan')
            ->latest()
            ->get();
            
        $cancelledMinings = UserMining::where('user_id', $user->id)
            ->cancelled()
            ->with('plan')
            ->latest()
            ->get();

        return view('dashboard.mining.index', compact(
            'miningPlans',
            'activeMinings',
            'completedMinings',
            'cancelledMinings'
        ));
    }

    /**
     * Show the form for creating a new mining subscription
     */
    public function create()
    {
        $user = Auth::user();
        $miningPlans = Plan::ofType('mining')->active()->ordered()->get();
        
        return view('dashboard.mining.create', compact('miningPlans', 'user'));
    }

    /**
     * Store a newly created mining subscription
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'amount_invested' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        // Verify it's a mining plan
        if ($plan->type !== 'mining') {
            return redirect()->back()
                ->with('error', 'Selected plan is not a mining plan.')
                ->withInput();
        }

        // Check minimum funding
        if ($plan->min_funding && $request->amount_invested < $plan->min_funding) {
            return redirect()->back()
                ->with('error', 'Amount must be at least ' . number_format($plan->min_funding, 2) . ' ' . $plan->currency)
                ->withInput();
        }

        // Check maximum funding
        if ($plan->max_funding && $request->amount_invested > $plan->max_funding) {
            return redirect()->back()
                ->with('error', 'Amount cannot exceed ' . number_format($plan->max_funding, 2) . ' ' . $plan->currency)
                ->withInput();
        }

        try {
            $mining = app(EarnService::class)->startMining($user, $plan, (float) $request->amount_invested, $request->notes);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->with('error', collect($e->errors())->flatten()->first())->withInput();
        }

        return redirect()->route('user.mining.show', $mining)
            ->with('success', 'Mining contract started. '.money_from($mining->amount_invested, $mining->currency ?: 'USD').' was moved from your mining balance.');
    }

    /**
     * Display the specified mining subscription
     */
    public function show(UserMining $mining)
    {
        $user = Auth::user();
        
        // Ensure user can only view their own mining
        if ($mining->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $mining->load('plan');
        
        return view('dashboard.mining.show', compact('mining'));
    }

    /**
     * Cancel a mining subscription
     */
    public function cancel(UserMining $mining)
    {
        $user = Auth::user();
        
        // Ensure user can only cancel their own mining
        if ($mining->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$mining->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This mining subscription cannot be cancelled.');
        }

        DB::beginTransaction();
        try {
            $mining->update([
                'status' => UserMining::STATUS_CANCELLED,
            ]);

            // Here you would typically refund the remaining balance
            // For now, we'll just update the status

            DB::commit();

            return redirect()->route('user.mining.index')
                ->with('success', 'Mining subscription cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to cancel mining subscription. Error: ' . $e->getMessage());
        }
    }

    /**
     * Suspend a mining subscription
     */
    public function suspend(UserMining $mining)
    {
        $user = Auth::user();
        
        // Ensure user can only suspend their own mining
        if ($mining->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$mining->canBeSuspended()) {
            return redirect()->back()
                ->with('error', 'This mining subscription cannot be suspended.');
        }

        DB::beginTransaction();
        try {
            $mining->update([
                'status' => UserMining::STATUS_SUSPENDED,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Mining subscription suspended successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to suspend mining subscription. Error: ' . $e->getMessage());
        }
    }

    /**
     * Resume a suspended mining subscription
     */
    public function resume(UserMining $mining)
    {
        $user = Auth::user();
        
        // Ensure user can only resume their own mining
        if ($mining->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$mining->canBeResumed()) {
            return redirect()->back()
                ->with('error', 'This mining subscription cannot be resumed.');
        }

        DB::beginTransaction();
        try {
            $mining->update([
                'status' => UserMining::STATUS_ACTIVE,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Mining subscription resumed successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to resume mining subscription. Error: ' . $e->getMessage());
        }
    }

    /**
     * Withdraw mined cryptocurrency
     */
    public function withdraw(UserMining $mining)
    {
        $user = Auth::user();
        
        // Ensure user can only withdraw from their own mining
        if ($mining->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $paid = app(EarnService::class)->withdrawMined($mining);
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->back()
            ->with('success', money($paid).' of mined earnings was added to your main balance.');
    }

    /**
     * Display mining statistics
     */
    public function statistics()
    {
        $user = Auth::user();
        
        $totalMinings = UserMining::where('user_id', $user->id)->count();
        $activeMinings = UserMining::where('user_id', $user->id)->active()->count();
        $totalInvested = UserMining::where('user_id', $user->id)->sum('amount_invested');
        $totalMined = UserMining::where('user_id', $user->id)->sum('total_mined');
        
        $recentMinings = UserMining::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.mining.statistics', compact(
            'totalMinings',
            'activeMinings',
            'totalInvested',
            'totalMined',
            'recentMinings'
        ));
    }
}
