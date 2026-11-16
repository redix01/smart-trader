@extends('dashboard.layout.app')

@section('content')
@php
    $badge = fn ($status) => match ($status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'expired' => 'bg-red-500/15 text-red-400',
        'cancelled' => 'bg-amber-500/15 text-amber-300',
        default => 'bg-gray-700 text-gray-300',
    };
    $tabs = [
        ['label' => 'All', 'route' => 'user.plans.index'],
        ['label' => 'Active', 'route' => 'user.plans.my-plans'],
        ['label' => 'History', 'route' => 'user.plans.history'],
    ];
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Plans</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Plan history</h1>
            <p class="text-sm text-gray-400 mt-0.5">Past and current subscriptions, newest first.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.plan.trading') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Subscribe to a plan
            </a>
        </div>
    </div>

    <!-- Section tabs -->
    <nav class="grid grid-cols-3 gap-1 p-1 rounded-lg bg-gray-800 border border-gray-700 sm:w-80" aria-label="Plan views">
        @foreach($tabs as $tab)
            <a href="{{ route($tab['route']) }}" class="text-center py-2 rounded-md text-sm font-semibold {{ request()->routeIs($tab['route']) ? 'bg-gray-700 text-white' : 'text-gray-400 hover:text-white' }}">{{ $tab['label'] }}</a>
        @endforeach
    </nav>

    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-white">Plan history</h2>
            <span class="text-xs text-gray-400">{{ $planHistory->total() }} total</span>
        </div>

        @if($planHistory->count() > 0)
            <!-- Phones: cards -->
            <ul class="md:hidden divide-y divide-gray-700">
                @foreach($planHistory as $userPlan)
                    <li class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white">{{ $userPlan->plan->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ ucfirst($userPlan->plan->type) }} plan</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $badge($userPlan->status) }}">{{ ucfirst($userPlan->status) }}</span>
                        </div>
                        <dl class="grid grid-cols-2 gap-x-3 gap-y-2 mt-3 text-sm">
                            <div><dt class="text-xs text-gray-400">Amount paid</dt><dd class="text-white font-mono">{{ $userPlan->formatted_amount_paid }}</dd></div>
                            <div><dt class="text-xs text-gray-400">Ends</dt><dd class="text-gray-300">{{ $userPlan->end_date ? $userPlan->end_date->format('M d, Y') : 'N/A' }}@if($userPlan->isExpiringSoon()) <span class="text-xs text-amber-300">· soon</span>@endif</dd></div>
                            <div><dt class="text-xs text-gray-400">Started</dt><dd class="text-gray-300">{{ $userPlan->start_date ? $userPlan->start_date->format('M d, Y') : 'N/A' }}</dd></div>
                        </dl>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <a href="{{ route('user.plans.show', $userPlan) }}" class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-sm font-semibold bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white">View</a>
                            @if($userPlan->isActive())
                            <form method="POST" action="{{ route('user.plans.cancel', $userPlan) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-sm font-semibold border border-red-500/40 text-red-400 hover:bg-red-500/10" data-confirm="Are you sure you want to cancel this plan?">Cancel</button>
                            </form>
                            @elseif($userPlan->isCancelled())
                            <form method="POST" action="{{ route('user.plans.reactivate', $userPlan) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-sm font-semibold border border-emerald-500/40 text-emerald-400 hover:bg-emerald-500/10">Reactivate</button>
                            </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Desktop: table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount paid</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Start date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">End date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($planHistory as $userPlan)
                            <tr class="hover:bg-gray-700/40">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white whitespace-nowrap">{{ $userPlan->plan->name }}</div>
                                    <div class="text-xs text-gray-400 max-w-xs truncate">{{ $userPlan->plan->description }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ ucfirst($userPlan->plan->type) }}</span></td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $badge($userPlan->status) }}">{{ ucfirst($userPlan->status) }}</span></td>
                                <td class="px-4 py-3 whitespace-nowrap text-white font-mono">{{ $userPlan->formatted_amount_paid }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-300">{{ $userPlan->start_date ? $userPlan->start_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-300">
                                    {{ $userPlan->end_date ? $userPlan->end_date->format('M d, Y') : 'N/A' }}
                                    @if($userPlan->isExpiringSoon())
                                        <span class="ml-1 text-xs text-amber-300">(expiring soon)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('user.plans.show', $userPlan) }}" class="text-gold-400 hover:text-gold-300 font-semibold">View</a>
                                        @if($userPlan->isActive())
                                        <form method="POST" action="{{ route('user.plans.cancel', $userPlan) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-400 hover:text-red-300 font-semibold" data-confirm="Are you sure you want to cancel this plan?">Cancel</button>
                                        </form>
                                        @elseif($userPlan->isCancelled())
                                        <form method="POST" action="{{ route('user.plans.reactivate', $userPlan) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-emerald-400 hover:text-emerald-300 font-semibold">Reactivate</button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($planHistory->hasPages())
                <div class="px-4 py-3 sm:px-6 border-t border-gray-700">
                    {{ $planHistory->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-12 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-white font-medium mt-3">No plan history</h3>
                <p class="text-sm text-gray-400 mt-1">You haven't subscribed to any plans yet.</p>
                <a href="{{ route('user.plan.trading') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Browse plans</a>
            </div>
        @endif
    </div>
</div>
@endsection
