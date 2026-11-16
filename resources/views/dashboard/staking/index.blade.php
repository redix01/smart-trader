@extends('dashboard.layout.app')

@section('content')
@php
    $totalStaked = $activeStakings->sum('amount_staked');
    $totalRewards = $activeStakings->sum('total_rewards') + $completedStakings->sum('total_rewards');
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Earn</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Staking</h1>
            <p class="text-sm text-gray-400 mt-0.5">Lock funds in a staking plan and earn rewards on a schedule.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.staking.statistics') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                Statistics
            </a>
            <a href="{{ route('user.staking.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Start staking
            </a>
        </div>
    </div>

    <!-- Quick stats: one strip, three columns even on phones -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 grid grid-cols-3 divide-x divide-gray-700">
        <div class="p-3 sm:p-5 min-w-0">
            <div class="text-[11px] sm:text-xs text-gray-400 truncate">Active stakes</div>
            <div class="text-lg sm:text-2xl font-semibold text-white mt-1">{{ $activeStakings->count() }}</div>
        </div>
        <div class="p-3 sm:p-5 min-w-0">
            <div class="text-[11px] sm:text-xs text-gray-400 truncate">Total staked</div>
            <div class="text-lg sm:text-2xl font-semibold text-white font-mono mt-1 truncate">{{ money_from($totalStaked) }}</div>
        </div>
        <div class="p-3 sm:p-5 min-w-0">
            <div class="text-[11px] sm:text-xs text-gray-400 truncate">Total rewards</div>
            <div class="text-lg sm:text-2xl font-semibold text-emerald-400 font-mono mt-1 truncate">{{ money_from($totalRewards) }}</div>
        </div>
    </div>

    <!-- Available staking plans -->
    <section>
        <div class="flex items-end justify-between gap-3 mb-3">
            <div>
                <h2 class="text-base sm:text-lg font-semibold text-white">Available plans</h2>
                <p class="text-xs sm:text-sm text-gray-400 mt-0.5">Rewards accrue on your stake and are paid to your main balance.</p>
            </div>
        </div>
        @if($stakingPlans->count() > 0)
            @php $popular = $stakingPlans->count() >= 3 ? intdiv($stakingPlans->count(), 2) : ($stakingPlans->count() === 2 ? 1 : -1); @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                @foreach($stakingPlans as $plan)
                    @php $featured = $loop->index === $popular; @endphp
                    <div class="flex flex-col rounded-2xl border p-5 sm:p-6 {{ $featured ? 'pc-card pc-card--accent border-blue-500/40' : 'border-gray-700 bg-gray-800' }}">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-base font-semibold text-white">{{ $plan->name }}</h3>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($featured)
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-500 text-white">Popular</span>
                                @endif
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ $plan->staking_currency ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-400">Annual yield</div>
                            <div class="flex items-baseline gap-1.5 mt-0.5">
                                <span class="text-4xl font-bold text-white font-mono tracking-tight">{{ $plan->apy_rate ? number_format($plan->apy_rate, 2) : '0.00' }}%</span>
                                <span class="text-sm font-semibold {{ $featured ? 'text-blue-200' : 'text-gold-400' }}">APY</span>
                            </div>
                        </div>

                        <dl class="grid grid-cols-3 gap-2 mt-5 mb-5 rounded-xl p-3 text-sm {{ $featured ? 'bg-black/25' : 'bg-gray-900' }}">
                            <div class="min-w-0">
                                <dt class="text-[11px] text-gray-400">Minimum</dt>
                                <dd class="text-white font-mono font-semibold truncate mt-0.5">{{ $plan->minimum_amount ? money_from($plan->minimum_amount, $plan->staking_currency ?: 'USD') : 'None' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-[11px] text-gray-400">Lock</dt>
                                <dd class="text-white font-semibold truncate mt-0.5">{{ $plan->lock_period ? $plan->lock_period.' days' : 'Flexible' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-[11px] text-gray-400">Rewards</dt>
                                <dd class="text-white font-semibold truncate mt-0.5">{{ $plan->reward_frequency ? ucfirst($plan->reward_frequency) : 'N/A' }}</dd>
                            </div>
                        </dl>

                        <a href="{{ route('user.staking.create') }}?plan={{ $plan->id }}" class="mt-auto w-full inline-flex items-center justify-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            Stake now
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-gray-700 bg-gray-800 px-4 py-10 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <h3 class="text-white font-medium mt-3">No staking plans available</h3>
                <p class="text-sm text-gray-400 mt-1">Check back later for new staking opportunities.</p>
            </div>
        @endif
    </section>

    <!-- Active stakes -->
    <section>
        <div class="flex items-center justify-between gap-3 mb-3">
            <h2 class="text-base sm:text-lg font-semibold text-white">Active stakes</h2>
            <span class="text-xs text-gray-400">{{ $activeStakings->total() }} active</span>
        </div>
        @if($activeStakings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                @foreach($activeStakings as $staking)
                    @php $cur = $staking->currency ?: 'USD'; @endphp
                    <div class="flex flex-col rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-white font-semibold truncate">{{ $staking->plan->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $staking->formatted_apy_rate }} APY</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-500/15 text-emerald-400">Active</span>
                        </div>
                        <dl class="grid grid-cols-2 gap-px mt-4 rounded-xl overflow-hidden bg-gray-700/60 text-sm">
                            <div class="bg-gray-900 p-3 min-w-0"><dt class="text-[11px] text-gray-400">Staked</dt><dd class="text-white font-mono font-semibold truncate mt-0.5">{{ money_from($staking->amount_staked, $cur) }}</dd></div>
                            <div class="bg-gray-900 p-3 min-w-0"><dt class="text-[11px] text-gray-400">Current value</dt><dd class="text-white font-mono font-semibold truncate mt-0.5">{{ money_from($staking->current_value ?? $staking->calculateCurrentValue(), $cur) }}</dd></div>
                            <div class="bg-gray-900 p-3 min-w-0"><dt class="text-[11px] text-gray-400">Rewards paid</dt><dd class="text-emerald-400 font-mono font-semibold truncate mt-0.5">{{ money_from($staking->total_rewards, $cur) }}</dd></div>
                            <div class="bg-gray-900 p-3 min-w-0"><dt class="text-[11px] text-gray-400">Ends</dt><dd class="text-white font-semibold truncate mt-0.5">{{ $staking->end_date ? $staking->end_date->format('M d, Y') : 'Open-ended' }}</dd></div>
                        </dl>
                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <a href="{{ route('user.staking.show', $staking) }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                                View
                            </a>
                            <form method="POST" action="{{ route('user.staking.withdraw', $staking) }}">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                    Withdraw
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($activeStakings->hasPages())
                <div class="mt-4">{{ $activeStakings->links() }}</div>
            @endif
        @else
            <div class="rounded-2xl border border-gray-700 bg-gray-800 px-4 py-10 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <h3 class="text-white font-medium mt-3">No active stakes</h3>
                <p class="text-sm text-gray-400 mt-1">Choose a plan above to start earning staking rewards.</p>
                <a href="{{ route('user.staking.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                    Start your first stake
                </a>
            </div>
        @endif
    </section>

    <!-- Completed stakes -->
    <section>
        <h2 class="text-base sm:text-lg font-semibold text-white mb-3">Completed stakes</h2>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="divide-y divide-gray-700">
                @forelse($completedStakings as $staking)
                    @php $cur = $staking->currency ?: 'USD'; @endphp
                    <a href="{{ route('user.staking.show', $staking) }}" class="flex items-center gap-3 p-4 sm:px-6 hover:bg-gray-700/30">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <div class="text-white font-medium truncate">{{ $staking->plan->name }}</div>
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">Completed</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1">Ended {{ $staking->end_date ? $staking->end_date->format('M d, Y') : 'N/A' }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-sm text-white font-mono">{{ money_from($staking->amount_staked, $cur) }}</div>
                            <div class="text-xs text-emerald-400 font-mono">+{{ money_from($staking->total_rewards, $cur) }}</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center">
                        <div class="w-10 h-10 rounded-full bg-gray-700 grid place-items-center mx-auto">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">Finished stakes will appear here.</p>
                    </div>
                @endforelse
            </div>
            @if($completedStakings->hasPages())
                <div class="px-4 py-3 sm:px-6 border-t border-gray-700">
                    {{ $completedStakings->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
