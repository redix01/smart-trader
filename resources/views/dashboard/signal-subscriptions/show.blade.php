@extends('dashboard.layout.app')

@section('content')
@php
    $statusClass = match ($subscription->status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'pending' => 'bg-amber-500/15 text-amber-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        default => 'bg-gray-700 text-gray-300',
    };
    $strength = (int) ($subscription->plan->signal_strength ?? 0);
    $sections = [
        'Plan' => [
            'Plan name' => $subscription->plan->name,
            'Plan type' => ucfirst($subscription->plan->type),
            'Market type' => ucfirst($subscription->plan->signal_market_type ?? 'N/A'),
        ],
        'Subscription' => [
            'Start date' => $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A',
            'End date' => $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A',
            'Amount paid' => money($subscription->amount_paid, 2),
        ],
        'Signal usage' => [
            'Total signals' => $subscription->plan->signal_quantity ?? 'N/A',
            'Remaining signals' => $subscription->signal_quantity_remaining ?? 'N/A',
            'Used today' => $subscription->daily_signals_used ?? 0,
            'Success rate' => number_format($subscription->plan->success_rate ?? 0, 1).'%',
        ],
    ];
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Signal subscription</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1 flex flex-wrap items-center gap-2">
                {{ $subscription->plan->name }}
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusClass }}">{{ ucfirst($subscription->status) }}</span>
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">Plan details, remaining signals and renewal options.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.signal-subscriptions.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Back</a>
            @if($subscription->status === 'active')
                <a href="{{ route('user.signals.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">View signals</a>
            @endif
            @if($subscription->status === 'cancelled' || $subscription->status === 'expired')
                <form action="{{ route('user.signal-subscriptions.renew', $subscription) }}" method="POST" class="flex">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Renew</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        @foreach($sections as $title => $rows)
            <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-white">{{ $title }}</h2>
                <dl class="mt-3 divide-y divide-gray-700">
                    @foreach($rows as $label => $value)
                        <div class="flex items-center justify-between gap-3 py-2.5 text-sm">
                            <dt class="text-gray-400">{{ $label }}</dt>
                            <dd class="text-white font-medium text-right">{{ $value }}</dd>
                        </div>
                    @endforeach
                    @if($loop->first)
                        <div class="flex items-center justify-between gap-3 py-2.5 text-sm">
                            <dt class="text-gray-400">Signal strength</dt>
                            <dd class="text-amber-300">{{ str_repeat('★', min($strength, 5)) }}{{ str_repeat('☆', max(0, 5 - $strength)) }} <span class="text-white">({{ $strength }}/5)</span></dd>
                        </div>
                    @endif
                </dl>
            </section>
        @endforeach
    </div>

    @if($subscription->plan->signal_features && is_array($subscription->plan->signal_features))
        <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Plan features</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($subscription->plan->signal_features as $feature)
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ $feature }}</span>
                @endforeach
            </div>
        </section>
    @endif

    @if($subscription->status === 'active')
        <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-white">Cancel subscription</h2>
                <p class="text-sm text-gray-400 mt-0.5">You will stop receiving new signals from this plan.</p>
            </div>
            <form action="{{ route('user.signal-subscriptions.cancel', $subscription) }}" method="POST" data-confirm="Are you sure you want to cancel this subscription?">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/10 text-sm font-semibold">Cancel subscription</button>
            </form>
        </section>
    @endif
</div>
@endsection
