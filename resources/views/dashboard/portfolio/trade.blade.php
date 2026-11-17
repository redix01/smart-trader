@extends('dashboard.layout.app')

@section('content')
@php
    $typeBadge = fn ($type) => match ($type) {
        'trading' => 'bg-blue-500/15 text-blue-300',
        'signal' => 'bg-emerald-500/15 text-emerald-400',
        'mining' => 'bg-amber-500/15 text-amber-300',
        'staking' => 'bg-sky-500/15 text-sky-300',
        default => 'bg-gray-700 text-gray-300',
    };
    $statusBadge = fn ($status) => match ($status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'expired' => 'bg-red-500/15 text-red-400',
        'cancelled' => 'bg-gray-700 text-gray-300',
        default => 'bg-amber-500/15 text-amber-300',
    };
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Portfolio</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Subscriptions</h1>
            <p class="text-sm text-gray-400 mt-0.5">Every plan you have subscribed to, with its status and dates.</p>
        </div>
        <a href="{{ route('user.plans.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            New plan
        </a>
    </div>

    @include('dashboard.portfolio.partials.footer-menu')

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total investment</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white font-mono break-all">{{ money($totalTradingInvestment, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Active plans</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-emerald-400">{{ $activeTradingPlans }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Expired plans</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white">{{ $expiredTradingPlans }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total plans</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white">{{ $tradingPlans->count() }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-white">All subscriptions</h2>
        </div>
        @if($tradingPlans->count() > 0)
            {{-- Phones: cards --}}
            <ul class="md:hidden divide-y divide-gray-700">
                @foreach($tradingPlans as $plan)
                    <li class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white truncate">{{ $plan->plan->name }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $typeBadge($plan->plan->type) }}">{{ ucfirst($plan->plan->type) }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusBadge($plan->status) }}">{{ ucfirst($plan->status) }}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-semibold text-white font-mono">{{ money($plan->amount_paid, 2) }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3 text-xs text-gray-400">
                            <span>{{ $plan->start_date ? $plan->start_date->format('M d, Y') : 'N/A' }} &rarr; {{ $plan->end_date ? $plan->end_date->format('M d, Y') : 'N/A' }}</span>
                            <a href="{{ route('user.plans.show', $plan) }}" class="inline-flex items-center min-h-[40px] text-sm font-semibold text-gold-400 hover:text-gold-300">Details</a>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- md+: table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900/50">
                        <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <th class="px-6 py-3">Plan</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Start</th>
                            <th class="px-6 py-3">End</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($tradingPlans as $plan)
                            <tr class="hover:bg-gray-700/40">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $plan->plan->name }}</div>
                                    <div class="text-xs text-gray-400 max-w-xs truncate">{{ $plan->plan->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $typeBadge($plan->plan->type) }}">{{ ucfirst($plan->plan->type) }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($plan->amount_paid, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusBadge($plan->status) }}">{{ ucfirst($plan->status) }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $plan->start_date ? $plan->start_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $plan->end_date ? $plan->end_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right"><a href="{{ route('user.plans.show', $plan) }}" class="font-semibold text-gold-400 hover:text-gold-300">View details</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-4 py-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/><path stroke-linecap="round" d="M8 9h8M8 13h8M8 17h5"/></svg>
                </div>
                <div class="mt-3 text-white font-medium">No subscriptions yet</div>
                <p class="mt-1 text-sm text-gray-400">Pick a plan to start trading with a managed strategy.</p>
                <a href="{{ route('user.plans.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Subscribe to a plan</a>
            </div>
        @endif
    </div>
</div>
@endsection
