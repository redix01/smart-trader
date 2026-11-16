@extends('dashboard.layout.app')

@section('content')
@php
    $statusBadge = fn ($status) => match ($status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'pending' => 'bg-amber-500/15 text-amber-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        default => 'bg-gray-700 text-gray-300',
    };
    $avgSuccessRate = $signalSubscriptions->where('plan.success_rate', '>', 0)->avg('plan.success_rate');
    $stats = [
        ['Active subscriptions', $signalSubscriptions->where('status', 'active')->count()],
        ['Signals remaining', $signalSubscriptions->sum('signal_quantity_remaining')],
        ['Total spent', auth()->user()->formatAmount($signalSubscriptions->sum('amount_paid'))],
        ['Avg. success rate', ($avgSuccessRate ? number_format($avgSuccessRate, 1) : '0').'%'],
    ];
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Signals</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Signal subscriptions</h1>
            <p class="text-sm text-gray-400 mt-0.5">Track your signal plans, remaining signals and renewals.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.plan.signal') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Browse plans</a>
            <a href="{{ route('user.signal-subscriptions.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Subscribe</a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach($stats as [$label, $value])
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5">
                <div class="text-xs text-gray-400">{{ $label }}</div>
                <div class="text-xl sm:text-2xl font-semibold text-white mt-1 truncate">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    @if($signalSubscriptions->count() > 0)
        <section class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
                <h2 class="text-base font-semibold text-white">Your subscriptions</h2>
            </div>

            {{-- Phones: cards --}}
            <ul class="md:hidden divide-y divide-gray-700">
                @foreach($signalSubscriptions as $subscription)
                    <li class="p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-white font-medium truncate">{{ $subscription->plan->name }}</div>
                                <div class="text-xs text-gray-400">{{ ucfirst($subscription->plan->signal_market_type ?? 'N/A') }}</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusBadge($subscription->status) }}">{{ ucfirst($subscription->status) }}</span>
                        </div>
                        <dl class="grid grid-cols-2 gap-3 text-sm">
                            <div><dt class="text-xs text-gray-400">Signals left</dt><dd class="text-white font-medium">{{ $subscription->signal_quantity_remaining ?? 0 }} <span class="text-gray-400">/ {{ $subscription->plan->signal_quantity ?? 0 }}</span></dd></div>
                            <div><dt class="text-xs text-gray-400">Paid</dt><dd class="text-white font-medium">{{ money($subscription->amount_paid, 2) }}</dd></div>
                            <div class="col-span-2"><dt class="text-xs text-gray-400">Period</dt><dd class="text-white">{{ $subscription->start_date ? $subscription->start_date->format('M d') : 'N/A' }} – {{ $subscription->end_date ? $subscription->end_date->format('M d') : 'N/A' }}
                                @if($subscription->end_date)<span class="text-xs text-gray-400">· {{ $subscription->end_date->isFuture() ? $subscription->end_date->diffForHumans().' left' : 'Expired '.$subscription->end_date->diffForHumans() }}</span>@endif
                            </dd></div>
                        </dl>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('user.signal-subscriptions.show', $subscription) }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Details</a>
                            @if($subscription->status === 'active')
                                <a href="{{ route('user.signals.index') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">View signals</a>
                            @endif
                            @if($subscription->status === 'cancelled' || $subscription->status === 'expired')
                                <form action="{{ route('user.signal-subscriptions.renew', $subscription) }}" method="POST" class="flex-1 flex">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Renew</button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- md+: table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900/50 border-b border-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Signals</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($signalSubscriptions as $subscription)
                            <tr class="hover:bg-gray-700/40">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $subscription->plan->name }}</div>
                                    <div class="text-xs text-gray-400">{{ ucfirst($subscription->plan->signal_market_type ?? 'N/A') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusBadge($subscription->status) }}">{{ ucfirst($subscription->status) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-white font-medium">{{ $subscription->signal_quantity_remaining ?? 0 }}</span>
                                    <span class="text-gray-400">/ {{ $subscription->plan->signal_quantity ?? 0 }}</span>
                                    <div class="text-xs text-gray-400">Used today: {{ $subscription->daily_signals_used ?? 0 }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-white">
                                        {{ $subscription->start_date ? $subscription->start_date->format('M d') : 'N/A' }} –
                                        {{ $subscription->end_date ? $subscription->end_date->format('M d') : 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        @if($subscription->end_date && $subscription->end_date->isFuture())
                                            {{ $subscription->end_date->diffForHumans() }} left
                                        @elseif($subscription->end_date && $subscription->end_date->isPast())
                                            Expired {{ $subscription->end_date->diffForHumans() }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium">{{ money($subscription->amount_paid, 2) }}</div>
                                    <div class="text-xs text-gray-400">{{ $subscription->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap justify-end gap-3 font-semibold">
                                        <a href="{{ route('user.signal-subscriptions.show', $subscription) }}" class="text-gold-400 hover:text-gold-300">Details</a>
                                        @if($subscription->status === 'active')
                                            <a href="{{ route('user.signals.index') }}" class="text-emerald-400 hover:text-emerald-300">Signals</a>
                                        @endif
                                        @if($subscription->status === 'cancelled' || $subscription->status === 'expired')
                                            <form action="{{ route('user.signal-subscriptions.renew', $subscription) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-amber-300 hover:text-amber-200">Renew</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <section class="rounded-2xl border border-gray-700 bg-gray-800 px-4 py-12 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-gray-700 grid place-items-center text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
            </div>
            <h3 class="text-white font-medium mt-3">No signal subscriptions yet</h3>
            <p class="text-sm text-gray-400 mt-1">Subscribe to a signal plan to start receiving trade ideas.</p>
            <a href="{{ route('user.signal-subscriptions.create') }}" class="mt-5 w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Choose a signal plan</a>
        </section>
    @endif
</div>
@endsection
