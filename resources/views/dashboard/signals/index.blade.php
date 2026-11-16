@extends('dashboard.layout.app')

@section('content')
@php
    // Instrument prices (not money): forex/sub-unit quotes need more than 2 decimals
    $px = fn ($v) => $v === null || (float) $v == 0 ? '—' : number_format((float) $v, abs((float) $v) >= 10 ? 2 : (abs((float) $v) >= 1 ? 4 : 6));
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

    {{-- Summary --}}
    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5">
        <dl class="grid grid-cols-3 divide-x divide-gray-700">
            <div class="pr-3 min-w-0">
                <dt class="text-xs text-gray-400">Active</dt>
                <dd class="text-xl sm:text-2xl font-semibold text-white mt-1 tabular-nums">{{ $activeSignals->total() }}</dd>
            </div>
            <div class="px-3 min-w-0">
                <dt class="text-xs text-gray-400">Completed</dt>
                <dd class="text-xl sm:text-2xl font-semibold text-white mt-1 tabular-nums">{{ $completedSignals->total() }}</dd>
            </div>
            <div class="pl-3 min-w-0">
                <dt class="text-xs text-gray-400">Signal plans</dt>
                <dd class="text-xl sm:text-2xl font-semibold text-white mt-1 tabular-nums">{{ $userSignalPlans->count() }}</dd>
            </div>
        </dl>
    </section>

    {{-- Active signals --}}
    <section class="space-y-3">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-white">Active signals</h2>
                <p class="text-sm text-gray-400 mt-0.5">Open trade ideas you can act on now.</p>
            </div>
            <span class="text-xs text-gray-400 tabular-nums">{{ $activeSignals->total() }} open</span>
        </div>

        @if($activeSignals->count())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($activeSignals as $signal)
                    @php
                        $isBuy = $signal->type === 'buy';
                        $entry = (float) $signal->entry_price;
                        $tp = (float) $signal->take_profit;
                        $sl = (float) $signal->stop_loss;
                        $reward = ($tp && $entry) ? abs($tp - $entry) : 0;
                        $risk = ($sl && $entry) ? abs($entry - $sl) : 0;
                        $rr = $risk > 0 ? $reward / $risk : null;
                        $rewardPct = ($reward + $risk) > 0 ? round($reward / ($reward + $risk) * 100) : 50;
                        $movePct = ($entry > 0 && $tp) ? ($tp - $entry) / $entry * 100 : null;
                    @endphp
                    <a href="{{ route('user.signals.show', $signal) }}" class="pc-card rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5 flex flex-col">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 shrink-0 rounded-xl grid place-items-center {{ $isBuy ? 'bg-emerald-500/15 text-emerald-300' : 'bg-red-500/15 text-red-300' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">@if($isBuy)<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>@else<path d="M3 7l6 6 4-4 8 8M15 17h6v-6"/>@endif</svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-semibold text-white leading-tight truncate">{{ $signal->symbol }}</h3>
                                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $signal->title }}</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $typeBadge($signal->type) }}">{{ strtoupper($signal->type) }}</span>
                        </div>

                        <div class="mt-5 flex items-end justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-xs text-gray-400">Entry price</div>
                                <div class="text-3xl font-bold text-white tracking-tight leading-none mt-1.5 tabular-nums truncate">{{ $px($signal->entry_price) }}</div>
                            </div>
                            @if($movePct !== null)
                                <div class="text-right shrink-0">
                                    <div class="text-xs text-gray-400">To target</div>
                                    <div class="text-sm font-semibold mt-1 tabular-nums {{ ($isBuy ? $movePct >= 0 : $movePct <= 0) ? 'text-emerald-400' : 'text-red-400' }}">{{ $movePct >= 0 ? '+' : '' }}{{ number_format($movePct, 2) }}%</div>
                                </div>
                            @endif
                        </div>
                        <div class="mt-3 flex h-1.5 rounded-full overflow-hidden gap-0.5" role="img" aria-label="Risk to reward">
                            <span class="bg-red-500/80" style="width: {{ 100 - $rewardPct }}%"></span>
                            <span class="bg-emerald-500" style="width: {{ $rewardPct }}%"></span>
                        </div>

                        <dl class="mt-4 grid grid-cols-3 gap-2">
                            <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                                <dt class="text-[11px] text-gray-400">Take profit</dt>
                                <dd class="text-sm font-semibold text-emerald-400 mt-0.5 tabular-nums truncate">{{ $px($signal->take_profit) }}</dd>
                            </div>
                            <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                                <dt class="text-[11px] text-gray-400">Stop loss</dt>
                                <dd class="text-sm font-semibold text-red-400 mt-0.5 tabular-nums truncate">{{ $px($signal->stop_loss) }}</dd>
                            </div>
                            <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                                <dt class="text-[11px] text-gray-400">Risk : reward</dt>
                                <dd class="text-sm font-semibold text-white mt-0.5 tabular-nums truncate">{{ $rr !== null ? '1 : '.number_format($rr, 1) : '—' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-auto pt-4">
                            <p class="text-xs text-gray-500 mb-3">{{ optional($signal->plan)->name }}@if($signal->expires_at) · expires {{ $signal->expires_at->diffForHumans() }}@endif</p>
                            <span class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                View signal
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
            @if($activeSignals->hasPages())
                <div>{{ $activeSignals->links() }}</div>
            @endif
        @else
            <div class="rounded-2xl border border-gray-700 bg-gray-800 px-4 py-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-gray-700 grid place-items-center text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
                </div>
                @if($userSignalPlans->isEmpty())
                    <p class="text-white font-medium mt-3">Subscribe to a signal plan to start receiving trade ideas.</p>
                    <a href="{{ route('user.signal-subscriptions.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Subscribe to a signal plan</a>
                @else
                    <p class="text-white font-medium mt-3">No active signals right now, new ones from your plans appear here.</p>
                    <a href="{{ route('user.liveTrading.index') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Open the Trade Room</a>
                @endif
            </div>
        @endif
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <section class="lg:col-span-2 rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-white">Completed signals</h2>
                <span class="text-xs text-gray-400 tabular-nums">{{ $completedSignals->total() }}</span>
            </div>
            <ul class="divide-y divide-gray-700">
                @forelse($completedSignals as $signal)
                    <li>
                        <a href="{{ route('user.signals.show', $signal) }}" class="p-4 sm:px-6 flex items-center gap-3 hover:bg-white/[.03]">
                            <span class="w-9 h-9 shrink-0 rounded-lg bg-gray-900 border border-gray-700 grid place-items-center text-[10px] font-bold text-gray-200">{{ \Illuminate\Support\Str::limit($signal->symbol, 4, '') }}</span>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm text-white font-medium truncate">{{ $signal->title }}</div>
                                <div class="mt-0.5 text-xs text-gray-400 tabular-nums">{{ $signal->symbol }} · entry {{ $px($signal->entry_price) }}</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $typeBadge($signal->type) }}">{{ strtoupper($signal->type) }}</span>
                            <svg class="w-4 h-4 shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg>
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-10 text-center">
                        <div class="w-12 h-12 mx-auto rounded-full bg-gray-700 grid place-items-center text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>
                        </div>
                        <p class="text-sm text-gray-400 mt-3">Closed signals and their results are listed here.</p>
                    </li>
                @endforelse
            </ul>
            @if($completedSignals->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-700">{{ $completedSignals->links() }}</div>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-white">Your plans</h2>
                <a href="{{ route('user.signal-subscriptions.create') }}" class="text-sm font-semibold text-gold-400 hover:text-gold-300">Browse</a>
            </div>
            <ul class="divide-y divide-gray-700">
                @forelse($userSignalPlans as $subscription)
                    <li class="px-4 sm:px-6 py-3.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm text-white font-medium truncate">{{ $subscription->plan->name }}</div>
                            <div class="text-gray-400 text-xs">Ends {{ optional($subscription->end_date)->diffForHumans() }}</div>
                        </div>
                        <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $subscription->isActive() ? 'bg-emerald-500/15 text-emerald-400' : 'bg-gray-700 text-gray-300' }}">{{ ucfirst($subscription->status) }}</span>
                    </li>
                @empty
                    <li class="px-4 py-10 text-center">
                        <div class="w-12 h-12 mx-auto rounded-full bg-gray-700 grid place-items-center text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
                        </div>
                        <p class="text-sm text-gray-400 mt-3">You have no signal plan yet.</p>
                        <a href="{{ route('user.signal-subscriptions.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Browse signal plans</a>
                    </li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
@endsection
