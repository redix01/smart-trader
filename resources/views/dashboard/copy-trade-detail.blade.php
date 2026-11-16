@extends('dashboard.layout.app')

@section('content')
@php
    $totalTrades = $wins + $losses;
    $winRate = $totalTrades > 0 ? round(($wins / $totalTrades) * 100, 1) : 0;
    $barPercent = min(100, max(0, $winRate));
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Copy trading</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Copy trade details</h1>
            <p class="text-sm text-gray-400 mt-0.5">How your copy of {{ $trader->name }} is performing.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.copyTrading.index', ['tab' => 'history']) }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold {{ $copiedTrade->status == 1 ? '' : 'col-span-2' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back
            </a>
            @if($copiedTrade->status == 1)
            <form action="{{ route('user.copyTrading.stop', $copiedTrade->id) }}" method="POST" class="flex">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold"
                        data-confirm="Are you sure you want to stop copying this trader?">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Stop copying</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Trader -->
    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <div class="flex items-center gap-4">
            <div class="relative shrink-0">
                <img src="{{ $trader->avatar_url }}" alt="{{ $trader->name }}"
                     class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border border-gray-600"
                     onerror="this.src='{{ asset('img/trader.jpg') }}'">
                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-emerald-500 rounded-full border-2 border-gray-800"></span>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-lg sm:text-xl font-semibold text-white truncate">{{ $trader->name }}</h2>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-500/15 text-emerald-400">{{ $trader->win_rate }}% win rate</span>
                    <span class="text-xs text-gray-400">{{ $tradeCount }} total trades</span>
                </div>
            </div>
            @if($copiedTrade->status == 1)
                <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-500/15 text-emerald-400">Active</span>
            @else
                <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">Inactive</span>
            @endif
        </div>
    </section>

    <!-- Key numbers -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Investment</div>
            <div class="text-xl font-semibold text-white mt-1 font-mono truncate">{{ money($copiedTrade->amount, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Profit / loss</div>
            <div class="text-xl font-semibold mt-1 font-mono truncate {{ $pnl >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ money($pnl, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">ROI</div>
            <div class="text-xl font-semibold mt-1 {{ $roi >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ number_format($roi, 2) }}%</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Trades</div>
            <div class="text-xl font-semibold text-white mt-1">{{ $tradeCount }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 col-span-2 lg:col-span-1">
            <div class="text-xs text-gray-400">Started</div>
            <div class="text-xl font-semibold text-white mt-1">{{ $copiedTrade->created_at->diffForHumans() }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Performance -->
        <section class="lg:col-span-2 rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h3 class="text-base font-semibold text-white">Performance</h3>
            <div class="mt-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-400">Win rate</span>
                    <span class="font-semibold text-white">{{ $winRate }}%</span>
                </div>
                <div class="mt-2 h-2 rounded-full bg-gray-700 overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $barPercent }}%"></div>
                </div>
            </div>
            <dl class="mt-4 grid grid-cols-3 gap-3 text-center">
                <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3">
                    <dt class="text-xs text-gray-400">Total</dt>
                    <dd class="text-lg font-semibold text-white mt-0.5">{{ $tradeCount }}</dd>
                </div>
                <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3">
                    <dt class="text-xs text-gray-400">Wins</dt>
                    <dd class="text-lg font-semibold text-emerald-400 mt-0.5">{{ $wins }}</dd>
                </div>
                <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3">
                    <dt class="text-xs text-gray-400">Losses</dt>
                    <dd class="text-lg font-semibold text-red-400 mt-0.5">{{ $losses }}</dd>
                </div>
            </dl>
        </section>

        <!-- Timeline -->
        <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h3 class="text-base font-semibold text-white">Timeline</h3>
            <ol class="mt-4 space-y-4">
                <li class="flex items-start gap-3">
                    <span class="mt-1.5 w-2.5 h-2.5 bg-emerald-500 rounded-full shrink-0"></span>
                    <div>
                        <div class="text-sm text-white font-medium">Copy trade started</div>
                        <div class="text-xs text-gray-400">{{ $copiedTrade->created_at->format('M d, Y \a\t g:i A') }}</div>
                    </div>
                </li>
                @if($copiedTrade->stopped_at)
                <li class="flex items-start gap-3">
                    <span class="mt-1.5 w-2.5 h-2.5 bg-red-500 rounded-full shrink-0"></span>
                    <div>
                        <div class="text-sm text-white font-medium">Copy trade stopped</div>
                        <div class="text-xs text-gray-400">{{ $copiedTrade->stopped_at->format('M d, Y \a\t g:i A') }}</div>
                    </div>
                </li>
                @endif
            </ol>
            <p class="mt-5 text-xs text-gray-500">
                @if($copiedTrade->status == 1)
                    This copy is active and mirrors {{ $trader->name }}'s trades.
                @else
                    This copy has been stopped.
                @endif
            </p>
        </section>
    </div>
</div>
@endsection
