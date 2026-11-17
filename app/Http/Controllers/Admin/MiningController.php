<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserMining;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MiningController extends Controller
{
    /**
     * Display a listing of all user mining activities.
     */
    public function index()
    {
        $miningActivities = UserMining::with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_mining' => UserMining::count(),
            'active_mining' => UserMining::where('status', 'active')->count(),
            'completed_mining' => UserMining::where('status', 'completed')->count(),
            'total_invested' => UserMining::sum('amount_invested'),
            'total_mined' => UserMining::sum('total_mined'),
            'total_value' => UserMining::sum('current_value'),
        ];

        return view('admin.mining.index', compact('miningActivities', 'stats'));
    }


    /**
     * Update mining stats and PnL.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'total_mined' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'status' => 'required|in:active,completed,cancelled,suspended',
            'notes' => 'nullable|string|max:1000',
        ]);

        $mining = UserMining::findOrFail($id);

        if ((float) $request->total_mined < (float) $mining->total_withdrawn) {
            return redirect()->back()->with('error', 'Total mined cannot be lower than what the user has already withdrawn ('.number_format((float) $mining->total_withdrawn, 2).').');
        }

        // total_mined is what the user can withdraw from their Mining page (total_mined - total_withdrawn,
        // paid to the main balance by EarnService). Money moves only when they withdraw, so nothing is
        // credited here - crediting here as well would pay the same earnings twice.
        $mining->update([
            'total_mined' => $request->total_mined,
            'current_value' => $request->current_value,
            'status' => $request->status,
            'notes' => $request->notes,
            'last_mining_date' => now(),
        ]);

        return redirect()->route('admin.mining.index')
            ->with('success', 'Mining stats updated. The user can withdraw '.number_format(max(0, (float) $request->total_mined - (float) $mining->total_withdrawn), 2).' of mined earnings from their Mining page.');
    }

    /**
     * Suspend a mining activity.
     */
    public function suspend($id)
    {
        $mining = UserMining::findOrFail($id);
        $mining->update(['status' => 'suspended']);

        // Send notification to user
        $mining->user->createNotification(
            'mining_suspended',
            'Mining Suspended',
            "Your mining activity with {$mining->plan->name} has been suspended by admin.",
            [
                'mining_id' => $mining->id,
                'plan_name' => $mining->plan->name,
                'amount_invested' => $mining->amount_invested
            ]
        );

        return redirect()->back()->with('success', 'Mining activity suspended successfully!');
    }

    /**
     * Resume a mining activity.
     */
    public function resume($id)
    {
        $mining = UserMining::findOrFail($id);
        $mining->update(['status' => 'active']);

        // Send notification to user
        $mining->user->createNotification(
            'mining_resumed',
            'Mining Resumed',
            "Your mining activity with {$mining->plan->name} has been resumed by admin.",
            [
                'mining_id' => $mining->id,
                'plan_name' => $mining->plan->name,
                'amount_invested' => $mining->amount_invested
            ]
        );

        return redirect()->back()->with('success', 'Mining activity resumed successfully!');
    }

    /**
     * Cancel a mining activity.
     */
    public function cancel($id)
    {
        $mining = UserMining::with(['user', 'plan'])->findOrFail($id);

        $paid = app(\App\Services\EarnService::class)->cancelMining($mining);
        if ($paid === null) {
            return redirect()->back()->with('error', 'Only active or suspended mining contracts can be cancelled.');
        }

        $total = $paid['principal'] + $paid['mined'];
        $mining->user?->createNotification(
            'mining_cancelled',
            'Mining Cancelled',
            "Your mining activity with " . ($mining->plan->name ?? 'your plan') . " has been cancelled by admin. " .
            money($total, 2, $mining->user) . " (invested amount plus unpaid mined earnings) has been returned to your main balance.",
            [
                'mining_id' => $mining->id,
                'plan_name' => $mining->plan->name ?? null,
                'amount_invested' => $mining->amount_invested,
                'value_transferred' => $total,
            ]
        );

        return redirect()->back()->with('success', 'Mining activity cancelled. '.number_format($total, 2).' returned to the user\'s main balance.');
    }

    /**
     * Delete a mining activity.
     */
    public function destroy($id)
    {
        $mining = UserMining::findOrFail($id);

        // A live contract still holds the user's money: pay it back (principal + unpaid earnings) before deleting.
        app(\App\Services\EarnService::class)->cancelMining($mining);

        $mining->delete();

        return redirect()->back()->with('success', 'Mining activity deleted successfully!');
    }

    /**
     * Show mining statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_mining' => UserMining::count(),
            'active_mining' => UserMining::where('status', 'active')->count(),
            'completed_mining' => UserMining::where('status', 'completed')->count(),
            'cancelled_mining' => UserMining::where('status', 'cancelled')->count(),
            'suspended_mining' => UserMining::where('status', 'suspended')->count(),
            'total_invested' => UserMining::sum('amount_invested'),
            'total_mined' => UserMining::sum('total_mined'),
            'total_value' => UserMining::sum('current_value'),
        ];

        // Monthly mining data
        $dateFormat = DB::getDriverName() === 'sqlite' 
            ? 'strftime("%Y-%m", created_at)' 
            : 'DATE_FORMAT(created_at, "%Y-%m")';
            
        $monthlyData = UserMining::select(
                DB::raw("{$dateFormat} as month"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount_invested) as total_invested'),
                DB::raw('SUM(total_mined) as total_mined')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top mining plans
        $topPlans = UserMining::select('plan_id')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount_invested) as total_invested')
            ->with('plan:id,name')
            ->groupBy('plan_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.mining.statistics', compact('stats', 'monthlyData', 'topPlans'));
    }
}
