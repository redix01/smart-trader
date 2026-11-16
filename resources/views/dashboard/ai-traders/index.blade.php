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
    <section id="plans" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        @forelse($plans as $plan)
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 flex flex-col hover:border-gold-500 transition-colors">
            <div class="flex items-start justify-between gap-3">
                <h3 class="text-base font-semibold text-white">{{ $plan->name }}</h3>
                <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ $plan->number_of_traders }} AI trader{{ $plan->number_of_traders > 1 ? 's' : '' }}</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-white mt-3">{{ $plan->formatted_price }}</div>
            @if($plan->description)
                <p class="text-sm text-gray-400 mt-2">{{ $plan->description }}</p>
            @endif

            <div class="mt-4 rounded-xl border border-gray-700 bg-gray-900/50 p-3 flex items-center justify-between gap-3">
                <span class="text-xs text-gray-400">Minimum investment</span>
                <span class="font-semibold text-white font-mono">{{ money_from($plan->investment_amount, 'USD', 0) }}</span>
            </div>

            <div class="mt-4">
                <div class="text-xs text-gray-400 mb-2">Markets covered</div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($plan->stocks_trading as $stock)
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold font-mono bg-gray-700 text-gray-300">{{ $stock }}</span>
                    @endforeach
                </div>
            </div>

            @if($plan->features)
            <ul class="mt-4 space-y-1.5">
                @foreach($plan->features as $feature)
                <li class="text-sm text-gray-300 flex items-start gap-2">
                    <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>
            @endif

            <div class="mt-auto pt-5">
                <a href="{{ route('user.aiTraders.plan', $plan) }}"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                    View AI traders
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl border border-gray-700 bg-gray-800 px-4 py-12 text-center">
            <p class="text-white font-medium">No AI trader plans available</p>
            <p class="text-sm text-gray-400 mt-1">Plans will appear here once they are published.</p>
        </div>
        @endforelse
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
                <span class="w-7 h-7 rounded-full bg-blue-600/20 text-gold-400 grid place-items-center text-xs font-bold font-mono">{{ $i + 1 }}</span>
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
