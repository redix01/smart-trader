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
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Active plans</h1>
            <p class="text-sm text-gray-400 mt-0.5">Plans that are running now, grouped by type.</p>
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

    @if($activePlans->count() > 0)
        @foreach($activePlans as $planType => $plans)
            <section>
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h2 class="text-base sm:text-lg font-semibold text-white">{{ ucfirst($planType) }} plans</h2>
                    <span class="text-xs text-gray-400">{{ $plans->count() }} active</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($plans as $userPlan)
                        <div class="flex flex-col rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-white font-semibold truncate">{{ $userPlan->plan->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $userPlan->plan->description ?: ucfirst($planType).' plan' }}</div>
                                </div>
                                <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $badge('active') }}">Active</span>
                            </div>
                            <dl class="grid grid-cols-2 gap-px mt-4 rounded-xl overflow-hidden bg-gray-700/60 text-sm">
                                <div class="bg-gray-900 p-3 min-w-0"><dt class="text-[11px] text-gray-400">Amount paid</dt><dd class="text-white font-mono font-semibold truncate mt-0.5">{{ money_from($userPlan->amount_paid, $userPlan->currency ?: 'USD') }}</dd></div>
                                <div class="bg-gray-900 p-3 min-w-0"><dt class="text-[11px] text-gray-400">Type</dt><dd class="text-white font-semibold truncate mt-0.5">{{ ucfirst($userPlan->plan->type) }}</dd></div>
                                <div class="bg-gray-900 p-3 min-w-0"><dt class="text-[11px] text-gray-400">Started</dt><dd class="text-white font-semibold truncate mt-0.5">{{ $userPlan->start_date ? $userPlan->start_date->format('M d, Y') : 'N/A' }}</dd></div>
                                <div class="bg-gray-900 p-3 min-w-0"><dt class="text-[11px] text-gray-400">Ends</dt><dd class="font-semibold truncate mt-0.5 {{ $userPlan->isExpiringSoon() ? 'text-amber-300' : 'text-white' }}">{{ $userPlan->end_date ? $userPlan->end_date->format('M d, Y') : 'N/A' }}{{ $userPlan->isExpiringSoon() ? ' · soon' : '' }}</dd></div>
                            </dl>
                            <div class="grid grid-cols-2 gap-2 mt-4">
                                <a href="{{ route('user.plans.show', $userPlan) }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white">View</a>
                                <form method="POST" action="{{ route('user.plans.cancel', $userPlan) }}">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold border border-red-500/40 text-red-400 hover:bg-red-500/10" data-confirm="Are you sure you want to cancel this plan?">Cancel</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    @else
        <div class="rounded-2xl border border-gray-700 bg-gray-800">
            <div class="px-4 py-12 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-white font-medium mt-3">No active plans</h3>
                <p class="text-sm text-gray-400 mt-1">You don't have any running plans. Subscribe to one to get started.</p>
                <a href="{{ route('user.plan.trading') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Browse plans</a>
            </div>
        </div>
    @endif
</div>
@endsection
