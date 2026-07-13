<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CopySubscription;
use App\Models\Expert;
use App\Services\UserNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpertController extends Controller
{
    public function index()
    {
        $experts = Expert::withCount('subscriptions')->orderBy('name')->paginate(20);
        return Inertia::render('Admin/Experts/Index', ['experts' => $experts]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|string|max:500',
            'bio' => 'nullable|string',
            'win_rate' => 'nullable|numeric|min:0|max:100',
            'profit_share' => 'nullable|numeric|min:0|max:100',
            'total_volume' => 'nullable|numeric|min:0',
            'status' => 'in:verified,pro,institutional,top-tier',
            'is_active' => 'boolean',
        ]);

        Expert::create($data);
        return redirect()->route('admin.experts.index')->with('success', 'Expert created.');
    }

    public function update(Request $request, Expert $expert)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'avatar' => 'nullable|string|max:500',
            'bio' => 'nullable|string',
            'win_rate' => 'nullable|numeric|min:0|max:100',
            'profit_share' => 'nullable|numeric|min:0|max:100',
            'total_volume' => 'nullable|numeric|min:0',
            'status' => 'in:verified,pro,institutional,top-tier',
            'is_active' => 'boolean',
        ]);

        $expert->update($data);
        return redirect()->back()->with('success', 'Expert updated.');
    }

    public function destroy(Expert $expert)
    {
        $expert->delete();
        return redirect()->route('admin.experts.index')->with('success', 'Expert deleted.');
    }

    public function subscriptions(Expert $expert)
    {
        $subscriptions = $expert->subscriptions()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return Inertia::render('Admin/Experts/Subscriptions', [
            'expert' => $expert,
            'subscriptions' => $subscriptions,
        ]);
    }

    public function __construct(
        private UserNotificationService $notifications,
    ) {}

    public function cancelSubscription(CopySubscription $subscription)
    {
        if ($subscription->status !== 'active') {
            return redirect()->back()->with('error', 'Subscription is not active.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $user = $subscription->user;
        $this->notifications->sendExpertSubscriptionCancelled($user, $subscription, $subscription->expert?->name ?? 'Unknown');

        \Log::info("Admin cancelled subscription {$subscription->id} for user {$user->id}. No profits transferred.");

        return redirect()->back()->with('success', 'Subscription cancelled. No profits were transferred.');
    }
}
