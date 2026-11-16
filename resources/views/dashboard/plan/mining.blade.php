@extends('dashboard.layout.app')

@section('content')
@php
    $check = '<svg class="w-4 h-4 text-gold-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>';
    $plans = [
        ['name' => 'Enterprise', 'price' => money(15000), 'caption' => 'Setup fee', 'features' => ['1000 TH/s hash rate', '24/7 monitoring', 'Multiple cryptocurrencies', 'Dedicated support']],
        ['name' => 'Professional', 'price' => money(7500), 'caption' => 'Setup fee', 'features' => ['500 TH/s hash rate', '24/7 monitoring', 'Bitcoin & Ethereum']],
        ['name' => 'Starter', 'price' => money(2500), 'caption' => 'Setup fee', 'features' => ['100 TH/s hash rate', 'Basic monitoring', 'Bitcoin mining']],
    ];
    $benefits = [
        ['High-performance hardware', 'Latest ASIC miners and GPU rigs for maximum efficiency and profitability.'],
        ['Secure data centers', 'Enterprise-grade facilities with redundant power and cooling systems.'],
        ['Real-time monitoring', '24/7 monitoring and maintenance to ensure optimal mining performance.'],
        ['Instant payouts', 'Daily payouts directly to your wallet with a transparent fee structure.'],
    ];
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Plans</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Mining plans</h1>
            <p class="text-sm text-gray-400 mt-0.5">Rent hash power and receive daily mining payouts.</p>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        @foreach($plans as $plan)
            <div class="bg-gray-800 flex flex-col rounded-2xl border border-gray-700 p-4 sm:p-6 hover:border-blue-500/60 transition-colors">
                <h3 class="text-base font-semibold text-white">{{ $plan['name'] }}</h3>
                <div class="mt-3">
                    <div class="text-2xl sm:text-3xl font-bold text-white font-mono">{{ $plan['price'] }}</div>
                    <div class="text-sm text-gray-400 mt-0.5">{{ $plan['caption'] }}</div>
                </div>
                <ul class="space-y-2 mt-4 mb-5 text-sm text-gray-300">
                    @foreach($plan['features'] as $feature)
                        <li class="flex items-start gap-2">{!! $check !!}<span>{{ $feature }}</span></li>
                    @endforeach
                </ul>
                <button type="button" class="mt-auto w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">START MINING</button>
            </div>
        @endforeach
    </div>

    <!-- Additional Information -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Mining service benefits</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-4">
            @foreach($benefits as [$title, $text])
                <div class="flex items-start gap-3 rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600/15 text-gold-400 grid place-items-center shrink-0">{!! $check !!}</div>
                    <div>
                        <h3 class="text-sm font-semibold text-white">{{ $title }}</h3>
                        <p class="text-sm text-gray-400 mt-0.5">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    // Click handlers for the START MINING buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('button').forEach(button => {
            if (button.textContent.trim() === 'START MINING') {
                button.addEventListener('click', function() {
                    const planName = this.closest('.bg-gray-800').querySelector('h3').textContent;
                    uiAlert(`You selected the ${planName} Mining plan. Redirecting to setup page...`);
                });
            }
        });
    });
</script>
@endsection
