<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\TradePair;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user's trades
        $trades = Trade::whereUserId($user->id)->latest()->get();
        $openTrades = $trades->filter(function($trade) {
            return $trade->status === 'open';
        });
        $closedTrades = $trades->filter(function($trade) {
            return $trade->status === 'closed';
        });
        
        // Get user's active subscriptions/plans
        $activePlans = $user->activeUserPlans()->with('plan')->get();
        $tradingPlans = $activePlans->filter(function($plan) {
            return $plan->plan && $plan->plan->type === 'trading';
        })->count();
        $signalPlans = $activePlans->filter(function($plan) {
            return $plan->plan && $plan->plan->type === 'signal';
        })->count();
        $stakingPlans = $activePlans->filter(function($plan) {
            return $plan->plan && $plan->plan->type === 'staking';
        })->count();
        $miningPlans = $activePlans->filter(function($plan) {
            return $plan->plan && $plan->plan->type === 'mining';
        })->count();
        
        // Get user's holdings data
        $holdings = $user->holdings()->with('asset')->get();
        $totalHoldingsValue = $holdings->sum('current_value');
        
        // Get bot trading data
        $botTradings = $user->botTradings()->get();
        $activeBots = $botTradings->filter(function($bot) {
            return $bot->status === 'active';
        })->count();
        $totalBotProfit = $botTradings->sum('total_profit');
        
        // Calculate trading performance metrics
        $totalTrades = $trades->count();
        $winningTrades = $closedTrades->filter(function($trade) {
            return $trade->profit_loss > 0;
        })->count();
        $winRate = $totalTrades > 0 ? ($winningTrades / $totalTrades) * 100 : 0;
        $avgProfit = $closedTrades->count() > 0 ? $closedTrades->avg('profit_loss') : 0;
        
        // Get recent transactions
        $recentTransactions = $user->holdingTransactions()
            ->with('asset')
            ->latest()
            ->take(5)
            ->get();
        
        // Get copy trading data
        $copyTrades = $user->copiedTrades()->get();
        $activeCopyTrades = $copyTrades->filter(function($copy) {
            return $copy->status == 1;
        })->count();
        
        $dashboardData = [
            'user' => $user,
            'trades' => $trades,
            'openTrades' => $openTrades,
            'closedTrades' => $closedTrades,
            'totalTrades' => $totalTrades,
            'winningTrades' => $winningTrades,
            'winRate' => $winRate,
            'avgProfit' => $avgProfit,
            'activePlans' => $activePlans,
            'tradingPlans' => $tradingPlans,
            'signalPlans' => $signalPlans,
            'stakingPlans' => $stakingPlans,
            'miningPlans' => $miningPlans,
            'totalPlans' => $activePlans->count(),
            'holdings' => $holdings,
            'totalHoldingsValue' => $totalHoldingsValue,
            'botTradings' => $botTradings,
            'activeBots' => $activeBots,
            'totalBotProfit' => $totalBotProfit,
            'recentTransactions' => $recentTransactions,
            'copyTrades' => $copyTrades,
            'activeCopyTrades' => $activeCopyTrades,
        ];
        
        return view('dashboard.index', $dashboardData);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    public function referrals()
    {
        $user = Auth::user()->load(['referredUsers', 'referralRecords.referredUser']);

        $referralLink = route('register', ['ref' => $user->referral_code]);
        $totalReferrals = $user->referredUsers->count();
        $totalEarnings = $user->referralRecords->where('status', 'paid')->sum('amount');
        $pendingEarnings = $user->referralRecords->where('status', 'pending')->sum('amount');

        return view('dashboard.referrals', compact(
            'user',
            'referralLink',
            'totalReferrals',
            'totalEarnings',
            'pendingEarnings'
        ));
    }

    public function updateProfile(Request $request, $id)
    {
        $validated = $request->validate([
           'name' => 'nullable|string|max:255',
           'phone' => 'nullable|string|max:20',
           'telegram' => 'nullable|string|max:255',
           'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $user = User::findOrFail($id);
        
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar')->store('files', 'public');
            $validated['avatar'] = $avatarPath;
        }
        
        $user->update($validated);
        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('status', 'Password updated successfully!');
    }

    public function loading()
    {
        return view('dashboard.loading');
    }
}
