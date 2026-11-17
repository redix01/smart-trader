@extends('dashboard.layout.app')

@section('content')
@php
    $planBadge = match (true) {
        $userPlan->isActive() => ['Active', 'bg-emerald-500/15 text-emerald-400'],
        $userPlan->isCancelled() => ['Cancelled', 'bg-amber-500/15 text-amber-300'],
        $userPlan->isExpired() => ['Expired', 'bg-red-500/15 text-red-400'],
        default => [ucfirst($userPlan->status), 'bg-gray-700 text-gray-300'],
    };
    $signalBadge = fn ($status) => match ($status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'completed' => 'bg-blue-500/15 text-blue-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        default => 'bg-gray-700 text-gray-300',
    };
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Plans</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">{{ $userPlan->plan->name }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">Details of your {{ $userPlan->plan->type }} plan subscription.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.plans.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>Back
            </a>
            @if($userPlan->isActive())
                <form method="POST" action="{{ route('user.plans.cancel', $userPlan) }}" class="flex-1 sm:flex-none" data-confirm="Are you sure you want to cancel this plan?">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/10 text-sm font-semibold">
                        Cancel plan
                    </button>
                </form>
            @elseif($userPlan->isCancelled())
                <form method="POST" action="{{ route('user.plans.reactivate', $userPlan) }}" class="flex-1 sm:flex-none">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        Reactivate plan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Key figures -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 grid grid-cols-2 lg:grid-cols-4 overflow-hidden">
        <div class="p-4 sm:p-5 min-w-0">
            <div class="text-xs text-gray-400">Status</div>
            <div class="mt-1.5"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $planBadge[1] }}">{{ $planBadge[0] }}</span></div>
        </div>
        <div class="p-4 sm:p-5 min-w-0 border-l border-gray-700">
            <div class="text-xs text-gray-400">Amount paid</div>
            <div class="text-base sm:text-xl font-semibold text-white font-mono mt-1 break-words">{{ money_from($userPlan->amount_paid, $userPlan->currency ?: 'USD') }}</div>
        </div>
        <div class="p-4 sm:p-5 min-w-0 border-t lg:border-t-0 lg:border-l border-gray-700">
            <div class="text-xs text-gray-400">Ends</div>
            <div class="text-base sm:text-xl font-semibold text-white mt-1">{{ $userPlan->end_date ? $userPlan->end_date->format('M d, Y') : 'N/A' }}</div>
        </div>
        <div class="p-4 sm:p-5 min-w-0 border-t border-l lg:border-t-0 border-gray-700">
            <div class="text-xs text-gray-400">Remaining</div>
            <div class="text-base sm:text-xl font-semibold text-white mt-1">
                @if($userPlan->isActive() && $userPlan->end_date)
                    {{ $userPlan->remaining_days }} days
                @else
                    —
                @endif
            </div>
        </div>
    </div>

    <!-- Plan Details Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Plan information</h2>
            <dl class="mt-4 divide-y divide-gray-700 text-sm">
                <div class="flex justify-between gap-4 py-2.5"><dt class="text-gray-400">Plan</dt><dd class="text-white font-medium text-right">{{ $userPlan->plan->name }}</dd></div>
                <div class="flex justify-between gap-4 py-2.5"><dt class="text-gray-400">Type</dt><dd class="text-white text-right">{{ ucfirst($userPlan->plan->type) }}</dd></div>
                @if($userPlan->plan->description)
                    <div class="py-2.5"><dt class="text-gray-400">Description</dt><dd class="text-gray-300 mt-1">{{ $userPlan->plan->description }}</dd></div>
                @endif
            </dl>
            @if($userPlan->plan->type === 'trading')
                <h3 class="text-sm font-semibold text-white mt-4">Trading features</h3>
                <ul class="mt-2 space-y-1.5 text-sm text-gray-300">
                    @if($userPlan->plan->pairs)
                        <li class="flex gap-2"><span class="text-gold-400">•</span>{{ $userPlan->plan->pairs }} trading pairs</li>
                    @endif
                    @if($userPlan->plan->leverage)
                        <li class="flex gap-2"><span class="text-gold-400">•</span>Leverage up to 1:{{ number_format($userPlan->plan->leverage) }}</li>
                    @endif
                    @if($userPlan->plan->spreads)
                        <li class="flex gap-2"><span class="text-gold-400">•</span>Spreads from {{ $userPlan->plan->spreads }} pips</li>
                    @endif
                    @if($userPlan->plan->swap_fees)
                        <li class="flex gap-2"><span class="text-gold-400">•</span>Swap fees: {{ $userPlan->plan->swap_fees }}%</li>
                    @endif
                </ul>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Subscription details</h2>
            <dl class="mt-4 divide-y divide-gray-700 text-sm">
                <div class="flex justify-between gap-4 py-2.5"><dt class="text-gray-400">Amount paid</dt><dd class="text-white font-mono text-right">{{ money_from($userPlan->amount_paid, $userPlan->currency ?: 'USD') }}</dd></div>
                <div class="flex justify-between gap-4 py-2.5"><dt class="text-gray-400">Currency</dt><dd class="text-white text-right">{{ $userPlan->currency }}</dd></div>
                <div class="flex justify-between gap-4 py-2.5"><dt class="text-gray-400">Start date</dt><dd class="text-white text-right">{{ $userPlan->start_date ? $userPlan->start_date->format('M d, Y H:i') : 'N/A' }}</dd></div>
                <div class="flex justify-between gap-4 py-2.5"><dt class="text-gray-400">End date</dt><dd class="text-white text-right">{{ $userPlan->end_date ? $userPlan->end_date->format('M d, Y H:i') : 'N/A' }}</dd></div>
                @if($userPlan->notes)
                    <div class="py-2.5"><dt class="text-gray-400">Notes</dt><dd class="text-gray-300 mt-1">{{ $userPlan->notes }}</dd></div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Signals for Signal Plans -->
    @if($userPlan->plan->type === 'signal' && $signals->count() > 0)
        <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
                <h2 class="text-base font-semibold text-white">Signal history</h2>
            </div>

            <!-- Phones: cards -->
            <ul class="md:hidden divide-y divide-gray-700">
                @foreach($signals as $signal)
                    <li>
                        <a href="{{ route('user.signals.show', $signal) }}" class="block p-4 hover:bg-gray-700/40">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white truncate">{{ $signal->title }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $signal->symbol }} · {{ $signal->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <div class="flex gap-1.5 shrink-0">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $signal->type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ ucfirst($signal->type) }}</span>
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $signalBadge($signal->status) }}">{{ ucfirst($signal->status) }}</span>
                                </div>
                            </div>
                            <dl class="grid grid-cols-3 gap-2 mt-3 text-sm">
                                <div><dt class="text-xs text-gray-400">Entry</dt><dd class="text-white font-mono">{{ number_format($signal->entry_price, 2) }}</dd></div>
                                <div><dt class="text-xs text-gray-400">Target</dt><dd class="text-white font-mono">{{ number_format($signal->take_profit, 2) }}</dd></div>
                                <div><dt class="text-xs text-gray-400">Stop loss</dt><dd class="text-white font-mono">{{ number_format($signal->stop_loss, 2) }}</dd></div>
                            </dl>
                        </a>
                    </li>
                @endforeach
            </ul>

            <!-- Desktop: table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900/50">
                        <tr>
                            @foreach(['Signal', 'Pair', 'Type', 'Entry price', 'Target', 'Stop loss', 'Status', 'Date', ''] as $col)
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($signals as $signal)
                            <tr class="hover:bg-gray-700/40">
                                <td class="px-4 py-3 font-medium text-white">{{ $signal->title }}</td>
                                <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $signal->symbol }}</td>
                                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $signal->type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ ucfirst($signal->type) }}</span></td>
                                <td class="px-4 py-3 text-gray-300 font-mono">{{ number_format($signal->entry_price, 2) }}</td>
                                <td class="px-4 py-3 text-gray-300 font-mono">{{ number_format($signal->take_profit, 2) }}</td>
                                <td class="px-4 py-3 text-gray-300 font-mono">{{ number_format($signal->stop_loss, 2) }}</td>
                                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $signalBadge($signal->status) }}">{{ ucfirst($signal->status) }}</span></td>
                                <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $signal->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('user.signals.show', $signal) }}" class="text-gold-400 hover:text-gold-300 font-semibold">View details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($userPlan->plan->type === 'signal')
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-8 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h3 class="text-white font-medium mt-3">No signals yet</h3>
            <p class="text-sm text-gray-400 mt-1">No signals have been published for this plan yet. They'll appear here as soon as they are.</p>
        </div>
    @endif
</div>
@endsection
