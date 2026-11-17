@extends('dashboard.layout.app')

@section('content')
@php
    $badge = fn ($status) => match ($status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'completed' => 'bg-blue-500/15 text-blue-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        default => 'bg-gray-700 text-gray-300',
    };
    $stats = [
        ['Total stakes', $totalStakings, 'text-white'],
        ['Active stakes', $activeStakings, 'text-white'],
        ['Total staked', money_from($totalStaked), 'text-white font-mono'],
        ['Total rewards', money_from($totalRewards), 'text-emerald-400 font-mono'],
    ];
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Staking</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Staking statistics</h1>
            <p class="text-sm text-gray-400 mt-0.5">Track your staking performance and earnings.</p>
        </div>
        <a href="{{ route('user.staking.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg border border-gray-700 bg-gray-800 text-sm font-semibold text-gray-200 hover:text-white hover:border-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>Staking
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 grid grid-cols-2 lg:grid-cols-4 overflow-hidden">
        @foreach($stats as $i => [$label, $value, $cls])
            <div class="p-4 sm:p-5 min-w-0 border-gray-700 {{ $i % 2 ? 'border-l' : '' }} {{ $i >= 2 ? 'border-t lg:border-t-0 lg:border-l' : '' }}">
                <div class="text-xs text-gray-400">{{ $label }}</div>
                <div class="text-lg sm:text-2xl font-semibold mt-1 truncate {{ $cls }}">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <!-- Recent Stakings -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-white">Recent stakes</h2>
        </div>
        <div class="divide-y divide-gray-700">
            @forelse($recentStakings as $staking)
                <div class="p-4 sm:px-6 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="text-white font-medium truncate">{{ $staking->plan->name }}</div>
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $badge($staking->status) }}">{{ ucfirst($staking->status) }}</span>
                        </div>
                        <div class="text-sm text-gray-400 mt-1">
                            <span class="font-mono text-gray-300">{{ money_from($staking->amount_staked, $staking->currency ?: 'USD') }}</span>
                            · {{ $staking->formatted_apy_rate }} APY · {{ $staking->start_date->format('M d, Y') }}
                        </div>
                    </div>
                    <a href="{{ route('user.staking.show', $staking) }}" class="shrink-0 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                        View
                    </a>
                </div>
            @empty
                <div class="px-4 py-10 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-white font-medium mt-3">No stakes yet</h3>
                    <p class="text-sm text-gray-400 mt-1">Your staking performance will show here once you start.</p>
                    <a href="{{ route('user.staking.create') }}" class="mt-4 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        Start your first stake
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
