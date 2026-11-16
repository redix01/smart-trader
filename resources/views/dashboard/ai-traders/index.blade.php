@extends('dashboard.layout.app')

@section('title', 'AI Stock Traders - Advanced AI-Powered Trading')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Automation</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">AI Traders</h1>
            <p class="text-sm text-gray-400 mt-0.5">Choose a plan, then pick the AI traders that manage a stock portfolio for you.</p>
        </div>
    </div>

    <!-- Plans -->
    <section id="plans" class="space-y-3">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-white">AI trader plans</h2>
                <p class="text-sm text-gray-400 mt-0.5">Monthly subscription, billed from your balance.</p>
            </div>
            @if($plans->count())<span class="text-xs text-gray-400 tabular-nums">{{ $plans->count() }} plans</span>@endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($plans as $plan)
        @php
            $stocks = collect($plan->stocks_trading ?? []);
            $features = collect($plan->features ?? []);
        @endphp
        <div class="pc-card rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5 flex flex-col">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 shrink-0 rounded-xl bg-blue-500/15 text-blue-300 grid place-items-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="5" y="8" width="14" height="12" rx="2"/><path d="M12 4v4M9 14h.01M15 14h.01"/></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-semibold text-white leading-tight truncate">{{ $plan->name }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5 truncate" title="{{ $stocks->implode(', ') }}">{{ $stocks->take(4)->implode(' · ') }}@if($stocks->count() > 4) +{{ $stocks->count() - 4 }}@endif</p>
                </div>
                <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-500/15 text-blue-200">{{ $plan->number_of_traders }} AI trader{{ $plan->number_of_traders > 1 ? 's' : '' }}</span>
            </div>

            <div class="mt-5 flex items-baseline gap-1.5">
                <span class="text-3xl font-bold text-white tracking-tight tabular-nums">{{ $plan->formatted_price }}</span>
                <span class="text-sm text-gray-400">/ month</span>
            </div>
            @if($plan->description)
                <p class="text-sm text-gray-400 mt-2 line-clamp-2">{{ $plan->description }}</p>
            @endif

            <dl class="mt-4 grid grid-cols-2 gap-2">
                <div class="rounded-xl bg-gray-900 px-3 py-2.5 min-w-0">
                    <dt class="text-[11px] text-gray-400">Minimum investment</dt>
                    <dd class="text-sm font-semibold text-white mt-0.5 tabular-nums truncate">{{ money_from($plan->investment_amount, 'USD', 2) }}</dd>
                </div>
                <div class="rounded-xl bg-gray-900 px-3 py-2.5 min-w-0">
                    <dt class="text-[11px] text-gray-400">Stocks covered</dt>
                    <dd class="text-sm font-semibold text-white mt-0.5 tabular-nums">{{ $stocks->count() }}</dd>
                </div>
            </dl>

            @if($features->isNotEmpty())
            <ul class="mt-4 space-y-2">
                @foreach($features as $feature)
                <li class="text-sm text-gray-300 flex items-start gap-2">
                    <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>
            @endif

            <div class="mt-auto pt-5">
                <a href="{{ route('user.aiTraders.plan', $plan) }}"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                    View AI traders
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl border border-gray-700 bg-gray-800 px-4 py-12 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-gray-700 grid place-items-center text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="5" y="8" width="14" height="12" rx="2"/><path d="M12 4v4M9 14h.01M15 14h.01"/></svg>
            </div>
            <p class="text-white font-medium mt-3">No AI trader plans are published yet.</p>
            <a href="{{ route('user.copyTrading.index') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Try copy trading</a>
        </div>
        @endforelse
        </div>
    </section>

    <!-- How it works -->
    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">How AI trading works</h2>
        <ol class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach([
                ['AI analysis', 'AI models analyse market data, news sentiment and technical indicators in real time.'],
                ['Smart execution', 'AI traders place trades automatically within their strategy and risk limits.'],
                ['Monitor results', 'Track each trader’s performance and see how your portfolio is doing.'],
            ] as $i => [$title, $text])
            <li class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <span class="w-7 h-7 rounded-full bg-blue-600/20 text-gold-400 grid place-items-center text-xs font-bold tabular-nums">{{ $i + 1 }}</span>
                <h3 class="text-sm font-semibold text-white mt-3">{{ $title }}</h3>
                <p class="text-sm text-gray-400 mt-1">{{ $text }}</p>
            </li>
            @endforeach
        </ol>
        <p class="mt-4 text-xs text-gray-500">Built-in stop-loss, take-profit and position sizing on every trader. Past performance does not guarantee future results.</p>
    </section>
</div>
@endsection

@push('scripts')
<script>
// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endpush
