@extends('dashboard.layout.app')

@section('content')
@php
    $typeBadge = fn ($type) => $type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : ($type === 'sell' ? 'bg-red-500/15 text-red-400' : 'bg-gray-700 text-gray-300');
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Signals</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">My Signals</h1>
            <p class="text-sm text-gray-400 mt-0.5">Trade ideas from your active signal plans.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.signal-subscriptions.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">My subscriptions</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <section class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-white">Active signals</h2>
                    <span class="text-xs font-mono text-gray-400">{{ $activeSignals->total() }}</span>
                </div>
                <ul class="divide-y divide-gray-700">
                    @forelse($activeSignals as $signal)
                        <li class="p-4 sm:px-6 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-white font-medium truncate">{{ $signal->title }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                                    <span class="px-2 py-0.5 rounded-md font-semibold {{ $typeBadge($signal->type) }}">{{ strtoupper($signal->type) }}</span>
                                    <span class="font-mono">{{ $signal->symbol }}</span>
                                    <span>· {{ optional($signal->expires_at)->diffForHumans() ?? 'No expiry' }}</span>
                                </div>
                            </div>
                            <a href="{{ route('user.signals.show', $signal) }}" class="shrink-0 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">View</a>
                        </li>
                    @empty
                        <li class="p-8 text-center">
                            <div class="w-12 h-12 mx-auto rounded-full bg-gray-700 grid place-items-center text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
                            </div>
                            <p class="text-white font-medium mt-3">No active signals right now</p>
                            <p class="text-sm text-gray-400 mt-1">New signals from your plans will appear here.</p>
                            @if($userSignalPlans->isEmpty())
                                <a href="{{ route('user.signal-subscriptions.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Subscribe to a signal plan</a>
                            @endif
                        </li>
                    @endforelse
                </ul>
                @if($activeSignals->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-gray-700">{{ $activeSignals->links() }}</div>
                @endif
            </section>

            <section class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-white">Completed signals</h2>
                    <span class="text-xs font-mono text-gray-400">{{ $completedSignals->total() }}</span>
                </div>
                <ul class="divide-y divide-gray-700">
                    @forelse($completedSignals as $signal)
                        <li class="p-4 sm:px-6 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-white font-medium truncate">{{ $signal->title }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                                    <span class="px-2 py-0.5 rounded-md font-semibold {{ $typeBadge($signal->type) }}">{{ strtoupper($signal->type) }}</span>
                                    <span class="font-mono">{{ $signal->symbol }}</span>
                                    <span class="px-2 py-0.5 rounded-md font-semibold bg-gray-700 text-gray-300">Completed</span>
                                </div>
                            </div>
                            <a href="{{ route('user.signals.show', $signal) }}" class="shrink-0 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Details</a>
                        </li>
                    @empty
                        <li class="p-6 text-center text-sm text-gray-400">No completed signals yet.</li>
                    @endforelse
                </ul>
                @if($completedSignals->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-gray-700">{{ $completedSignals->links() }}</div>
                @endif
            </section>
        </div>

        <aside>
            <section class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
                    <h2 class="text-base font-semibold text-white">Your plans</h2>
                </div>
                <ul class="p-4 space-y-3">
                    @forelse($userSignalPlans as $subscription)
                        <li class="flex items-center justify-between gap-3 rounded-xl border border-gray-700 bg-gray-900/50 p-3">
                            <div class="min-w-0">
                                <div class="text-white font-medium truncate">{{ $subscription->plan->name }}</div>
                                <div class="text-gray-400 text-xs">Ends {{ optional($subscription->end_date)->diffForHumans() }}</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $subscription->isActive() ? 'bg-emerald-500/15 text-emerald-400' : 'bg-gray-700 text-gray-300' }}">{{ ucfirst($subscription->status) }}</span>
                        </li>
                    @empty
                        <li class="text-center py-2">
                            <p class="text-sm text-gray-400">No active signal plans.</p>
                            <a href="{{ route('user.signal-subscriptions.create') }}" class="mt-2 inline-flex text-sm font-semibold text-gold-400 hover:text-gold-300">Browse signal plans</a>
                        </li>
                    @endforelse
                </ul>
            </section>
        </aside>
    </div>
</div>
@endsection
