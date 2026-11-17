@extends('dashboard.layout.app')

@section('content')
@php
    $badge = fn ($status) => match ($status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'completed' => 'bg-blue-500/15 text-blue-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        'suspended' => 'bg-amber-500/15 text-amber-300',
        default => 'bg-gray-700 text-gray-300',
    };
    $stats = [
        ['Total mining', $totalMinings, 'text-white'],
        ['Active mining', $activeMinings, 'text-white'],
        ['Total invested', money($totalInvested, 2), 'text-white font-mono'],
        ['Total mined', money_from($totalMined), 'text-emerald-400 font-mono'],
    ];
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Mining</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Mining statistics</h1>
            <p class="text-sm text-gray-400 mt-0.5">Track your mining performance and earnings.</p>
        </div>
        <a href="{{ route('user.mining.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg border border-gray-700 bg-gray-800 text-sm font-semibold text-gray-200 hover:text-white hover:border-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>Mining
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Recent Mining Activity -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
                <h2 class="text-base font-semibold text-white">Recent activity</h2>
            </div>
            <div class="divide-y divide-gray-700">
                @forelse($recentMinings as $mining)
                    <a href="{{ route('user.mining.show', $mining) }}" class="block p-4 sm:px-6 hover:bg-gray-700/40">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-white font-medium truncate">{{ $mining->plan->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">Started {{ $mining->start_date->format('M d, Y') }}</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $badge($mining->status) }}">{{ ucfirst($mining->status) }}</span>
                        </div>
                        <dl class="grid grid-cols-2 gap-2 mt-3 text-sm">
                            <div class="min-w-0"><dt class="text-xs text-gray-400">Invested</dt><dd class="text-white font-mono truncate">{{ money_from($mining->amount_invested, $mining->currency ?: 'USD') }}</dd></div>
                            <div class="min-w-0"><dt class="text-xs text-gray-400">Mined</dt><dd class="text-emerald-400 font-mono truncate">{{ money_from($mining->total_mined, $mining->currency ?: 'USD') }}</dd></div>
                        </dl>
                    </a>
                @empty
                    <div class="px-4 py-10 text-center">
                        <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 9h6v6H9z"/></svg>
                        </div>
                        <h3 class="text-white font-medium mt-3">No mining activity yet</h3>
                        <p class="text-sm text-gray-400 mt-1">Start a mining plan and its activity will show here.</p>
                        <a href="{{ route('user.mining.create') }}" class="mt-4 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            Start mining
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Mining performance</h2>
            <div class="text-center py-10">
                <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
                </div>
                <h3 class="text-white font-medium mt-3">Performance chart coming soon</h3>
                <p class="text-sm text-gray-400 mt-1">We're working on detailed analytics.</p>
            </div>
        </div>
    </div>
</div>
@endsection
