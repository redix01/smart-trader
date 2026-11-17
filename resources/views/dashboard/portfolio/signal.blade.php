@extends('dashboard.layout.app')

@section('content')
@php
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
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Signals</h1>
            <p class="text-sm text-gray-400 mt-0.5">Your signal plans and how many signals you have left.</p>
        </div>
        <a href="{{ route('user.plans.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            New signal plan
        </a>
    </div>

    @include('dashboard.portfolio.partials.footer-menu')

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Signal investment</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white font-mono break-all">{{ money($totalSignalInvestment, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Active plans</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-emerald-400">{{ $activeSignalPlans }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Signals remaining</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white">{{ $totalSignalsRemaining }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Signals used</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white">{{ $totalSignalsUsed }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-white">Signal plans</h2>
        </div>
        @if($signalPlans->count() > 0)
            <ul class="md:hidden divide-y divide-gray-700">
                @foreach($signalPlans as $plan)
                    <li class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white truncate">{{ $plan->plan->name }}</div>
                                <span class="mt-1 inline-block px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusBadge($plan->status) }}">{{ ucfirst($plan->status) }}</span>
                            </div>
                            <div class="text-sm font-semibold text-white font-mono shrink-0">{{ money($plan->amount_paid, 2) }}</div>
                        </div>
                        <dl class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div class="rounded-lg bg-gray-900/50 px-3 py-2"><dt class="text-gray-400">Remaining</dt><dd class="mt-0.5 text-white">{{ $plan->signal_quantity_remaining ?? 'Unlimited' }}</dd></div>
                            <div class="rounded-lg bg-gray-900/50 px-3 py-2"><dt class="text-gray-400">Used today</dt><dd class="mt-0.5 text-white">{{ $plan->daily_signals_used ?? 0 }}</dd></div>
                        </dl>
                        <div class="mt-2 flex items-center justify-between gap-3 text-xs text-gray-400">
                            <span>{{ $plan->start_date ? $plan->start_date->format('M d, Y') : 'N/A' }} &rarr; {{ $plan->end_date ? $plan->end_date->format('M d, Y') : 'N/A' }}</span>
                            <a href="{{ route('user.plans.show', $plan) }}" class="inline-flex items-center min-h-[40px] text-sm font-semibold text-gold-400 hover:text-gold-300">Details</a>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900/50">
                        <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <th class="px-6 py-3">Plan</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Remaining</th>
                            <th class="px-6 py-3">Used today</th>
                            <th class="px-6 py-3">Start</th>
                            <th class="px-6 py-3">End</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($signalPlans as $plan)
                            <tr class="hover:bg-gray-700/40">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $plan->plan->name }}</div>
                                    <div class="text-xs text-gray-400 max-w-xs truncate">{{ $plan->plan->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($plan->amount_paid, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusBadge($plan->status) }}">{{ ucfirst($plan->status) }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-white">{{ $plan->signal_quantity_remaining ?? 'Unlimited' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-white">{{ $plan->daily_signals_used ?? 0 }}</td>
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" viewBox="0 0 24 24"><path d="M4 18h2M9 18V12M14 18V8M19 18V4"/></svg>
                </div>
                <div class="mt-3 text-white font-medium">No signal plans yet</div>
                <p class="mt-1 text-sm text-gray-400">Subscribe to receive trade signals from our analysts.</p>
                <a href="{{ route('user.plans.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Subscribe to a signal plan</a>
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Signal trading tips</h2>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <h3 class="text-sm font-semibold text-white">Analyse before you act</h3>
                <p class="mt-1 text-sm text-gray-400">Always analyze signals before executing trades. Consider market conditions and your risk tolerance.</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <h3 class="text-sm font-semibold text-white">Execute quickly</h3>
                <p class="mt-1 text-sm text-gray-400">Signals are time-sensitive. Execute trades quickly to capture the best entry points.</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <h3 class="text-sm font-semibold text-white">Manage risk</h3>
                <p class="mt-1 text-sm text-gray-400">Set stop-loss orders and never risk more than you can afford to lose on any single trade.</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <h3 class="text-sm font-semibold text-white">Diversify</h3>
                <p class="mt-1 text-sm text-gray-400">Don't put all your funds into signals. Maintain a balanced portfolio across different strategies.</p>
            </div>
        </div>
    </div>
</div>
@endsection
