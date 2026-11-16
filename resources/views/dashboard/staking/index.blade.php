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

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Active stakes</div>
            <div class="text-xl sm:text-2xl font-semibold text-white mt-1">{{ $activeStakings->count() }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total staked</div>
            <div class="text-xl sm:text-2xl font-semibold text-white font-mono mt-1 truncate">{{ number_format($totalStaked, 8) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total rewards</div>
            <div class="text-xl sm:text-2xl font-semibold text-emerald-400 font-mono mt-1 truncate">{{ number_format($totalRewards, 8) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6 min-w-0">
            <!-- Available Staking Plans -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-white">Available plans</h2>
                @if($stakingPlans->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-4">
                        @foreach($stakingPlans as $plan)
                            <div class="flex flex-col rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-white font-semibold">{{ $plan->name }}</h3>
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ $plan->staking_currency ?? 'N/A' }}</span>
                                </div>
                                @if($plan->apy_rate)
                                    <div class="mt-2">
                                        <span class="text-2xl font-bold text-white font-mono">{{ number_format($plan->apy_rate, 2) }}%</span>
                                        <span class="text-sm text-gray-400">APY</span>
                                    </div>
                                @endif

                                <dl class="space-y-1.5 mt-3 mb-4 text-sm">
                                    @if($plan->minimum_amount)
                                        <div class="flex justify-between gap-3">
                                            <dt class="text-gray-400">Minimum</dt>
                                            <dd class="text-white font-mono text-right">{{ number_format($plan->minimum_amount, 2) }} {{ $plan->staking_currency ?? 'USD' }}</dd>
                                        </div>
                                    @endif
                                    @if($plan->lock_period)
                                        <div class="flex justify-between gap-3">
                                            <dt class="text-gray-400">Lock period</dt>
                                            <dd class="text-white text-right">{{ $plan->lock_period }} days</dd>
                                        </div>
                                    @endif
                                    @if($plan->reward_frequency)
                                        <div class="flex justify-between gap-3">
                                            <dt class="text-gray-400">Rewards</dt>
                                            <dd class="text-white text-right">{{ ucfirst($plan->reward_frequency) }}</dd>
                                        </div>
                                    @endif
                                </dl>

                                <div class="mt-auto flex gap-2">
                                    <a href="{{ route('user.staking.create') }}?plan={{ $plan->id }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                        Stake now
                                    </a>
                                    @if($plan->features)
                                        <button type="button" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                                            Details
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                        </div>
                        <h3 class="text-white font-medium mt-3">No staking plans available</h3>
                        <p class="text-sm text-gray-400 mt-1">Check back later for new staking opportunities.</p>
                    </div>
                @endif
            </div>

            <!-- Active Staking Subscriptions -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
                    <h2 class="text-base font-semibold text-white">Active stakes</h2>
                </div>
                <div class="divide-y divide-gray-700">
                    @forelse($activeStakings as $staking)
                        <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="text-white font-medium truncate">{{ $staking->plan->name }}</div>
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-500/15 text-emerald-400">Active</span>
                                </div>
                                <dl class="grid grid-cols-2 gap-x-4 gap-y-1 mt-2 text-sm">
                                    <div><dt class="inline text-gray-400">Staked:</dt> <dd class="inline text-white font-mono">{{ $staking->formatted_amount_staked }}</dd></div>
                                    <div><dt class="inline text-gray-400">APY:</dt> <dd class="inline text-white">{{ $staking->formatted_apy_rate }}</dd></div>
                                    <div><dt class="inline text-gray-400">Value:</dt> <dd class="inline text-white font-mono">{{ $staking->formatted_current_value }}</dd></div>
                                    @if($staking->end_date)
                                        <div><dt class="inline text-gray-400">Ends:</dt> <dd class="inline text-white">{{ $staking->end_date->format('M d, Y') }}</dd></div>
                                    @endif
                                </dl>
                            </div>
                            <div class="grid grid-cols-2 sm:flex gap-2 shrink-0">
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
                    @empty
                        <div class="px-4 py-10 text-center">
                            <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                            </div>
                            <h3 class="text-white font-medium mt-3">No active stakes</h3>
                            <p class="text-sm text-gray-400 mt-1">Choose a plan above to start earning staking rewards.</p>
                            <a href="{{ route('user.staking.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                Start your first stake
                            </a>
                        </div>
                    @endforelse
                </div>
                @if($activeStakings->hasPages())
                    <div class="px-4 py-3 sm:px-6 border-t border-gray-700">
                        {{ $activeStakings->links() }}
                    </div>
                @endif
            </div>

            <!-- Completed Staking -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
                    <h2 class="text-base font-semibold text-white">Completed stakes</h2>
                </div>
                <div class="divide-y divide-gray-700">
                    @forelse($completedStakings as $staking)
                        <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="text-white font-medium truncate">{{ $staking->plan->name }}</div>
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">Completed</span>
                                </div>
                                <dl class="grid grid-cols-2 gap-x-4 gap-y-1 mt-2 text-sm">
                                    <div><dt class="inline text-gray-400">Staked:</dt> <dd class="inline text-white font-mono">{{ $staking->formatted_amount_staked }}</dd></div>
                                    <div><dt class="inline text-gray-400">Rewards:</dt> <dd class="inline text-emerald-400 font-mono">{{ $staking->formatted_total_rewards }}</dd></div>
                                    <div><dt class="inline text-gray-400">Ended:</dt> <dd class="inline text-white">{{ $staking->end_date ? $staking->end_date->format('M d, Y') : 'N/A' }}</dd></div>
                                </dl>
                            </div>
                            <a href="{{ route('user.staking.show', $staking) }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                                Details
                            </a>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-gray-400">
                            Finished stakes will appear here.
                        </div>
                    @endforelse
                </div>
                @if($completedStakings->hasPages())
                    <div class="px-4 py-3 sm:px-6 border-t border-gray-700">
                        {{ $completedStakings->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Popular Staking Platforms -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-white">Popular platforms</h2>
                <p class="text-xs text-gray-500 mt-0.5">Typical APY elsewhere, for comparison.</p>
                <div class="space-y-2 mt-4">
                    @foreach([['Binance', 'Up to 12% APY'], ['Coinbase', 'Up to 5% APY'], ['Kraken', 'Up to 23% APY']] as [$name, $apy])
                        <a href="#" class="flex items-center justify-between rounded-xl border border-gray-700 bg-gray-900/50 px-4 py-3 hover:border-gray-600">
                            <div>
                                <div class="text-white text-sm font-medium">{{ $name }}</div>
                                <div class="text-gray-400 text-xs">{{ $apy }}</div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
