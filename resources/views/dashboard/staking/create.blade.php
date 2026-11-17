@extends('dashboard.layout.app')

@section('content')
@php
    $inputCls = 'w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500';
    $check = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>';
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Earn</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Start staking</h1>
            <p class="text-sm text-gray-400 mt-0.5">Choose a plan and the amount you want to stake.</p>
        </div>
        <a href="{{ route('user.staking.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg border border-gray-700 bg-gray-800 text-sm font-semibold text-gray-200 hover:text-white hover:border-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>Staking
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Staking Form -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Staking setup</h2>

            <form method="POST" action="{{ route('user.staking.store') }}" class="space-y-5 mt-4">
                @csrf

                <!-- Plan Selection -->
                <div>
                    <label for="plan_id" class="block text-sm font-medium text-gray-300 mb-1.5">Staking plan <span class="text-red-400">*</span></label>
                    <select id="plan_id" name="plan_id" required class="{{ $inputCls }}">
                        <option value="">Choose a staking plan</option>
                        @foreach($stakingPlans as $plan)
                            <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - {{ $plan->staking_currency ?? 'N/A' }} ({{ number_format($plan->apy_rate, 2) }}% APY)
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount to Stake -->
                <div>
                    <label for="amount_staked" class="block text-sm font-medium text-gray-300 mb-1.5">Amount to stake <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="number" id="amount_staked" name="amount_staked" step="0.01" min="0" required inputmode="decimal"
                               class="{{ $inputCls }} pr-28 font-mono"
                               placeholder="0.00">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-400 text-sm" id="currency-display">Select plan first</span>
                        </div>
                    </div>
                    @error('amount_staked')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-300 mb-1.5">Notes <span class="text-gray-500">(optional)</span></label>
                    <textarea id="notes" name="notes" rows="3"
                              class="{{ $inputCls }}"
                              placeholder="Add any notes about this staking subscription"></textarea>
                    @error('notes')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                    Start staking
                </button>
            </form>
        </div>

        <!-- Plan Details -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Selected Plan Info -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-white">Plan details</h3>
                <div id="plan-details" class="mt-3">
                    <div class="text-center py-6">
                        <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm text-gray-400 mt-3">Select a staking plan to view its details.</p>
                    </div>
                </div>
            </div>

            <!-- Staking Benefits -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-white">Why stake</h3>
                <ul class="space-y-3 mt-4">
                    @foreach([
                        ['Passive income', 'Earn rewards just by holding your crypto.'],
                        ['Compound growth', 'Reinvest rewards for exponential growth.'],
                        ['Network security', 'Help secure blockchain networks.'],
                        ['Flexible terms', 'Choose from various lock periods and APY rates.'],
                    ] as [$title, $text])
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600/15 text-gold-400 grid place-items-center shrink-0">{!! $check !!}</span>
                            <div>
                                <h4 class="text-sm font-medium text-white">{{ $title }}</h4>
                                <p class="text-sm text-gray-400">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Risk Warning -->
            <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-300 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <div>
                    <h4 class="text-sm font-semibold text-amber-300">Risk warning</h4>
                    <p class="text-sm text-amber-200/80 mt-1">
                        Staking involves risks including potential loss of funds. APY rates are not guaranteed and may vary.
                        Always do your own research before staking.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan_id');
    const amountInput = document.getElementById('amount_staked');
    const currencyDisplay = document.getElementById('currency-display');
    const planDetails = document.getElementById('plan-details');

    const stakingPlans = @json($stakingPlans);

    const row = (label, value, cls = 'text-white') => `
        <div class="flex justify-between gap-4 py-2.5">
            <dt class="text-gray-400">${label}</dt>
            <dd class="${cls} text-right">${value}</dd>
        </div>`;

    function updatePlanDetails(planId) {
        const plan = stakingPlans.find(p => p.id == planId);

        if (plan) {
            currencyDisplay.textContent = plan.staking_currency || 'N/A';

            planDetails.innerHTML = `
                <dl class="divide-y divide-gray-700 text-sm">
                    ${row('Plan', plan.name, 'text-white font-medium')}
                    ${row('Currency', plan.staking_currency || 'N/A')}
                    ${plan.apy_rate ? row('APY', parseFloat(plan.apy_rate).toFixed(2) + '%', 'text-emerald-400 font-semibold font-mono') : ''}
                    ${plan.minimum_amount ? row('Minimum', parseFloat(plan.minimum_amount).toFixed(2) + ' ' + (plan.staking_currency || 'USD'), 'text-white font-mono') : ''}
                    ${plan.lock_period ? row('Lock period', plan.lock_period + ' days') : ''}
                    ${plan.reward_frequency ? row('Rewards', plan.reward_frequency) : ''}
                    ${plan.staking_duration ? row('Duration', plan.staking_duration + ' days') : ''}
                </dl>
            `;
        } else {
            currencyDisplay.textContent = 'Select plan first';
            planDetails.innerHTML = `
                <div class="text-center py-6">
                    <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-sm text-gray-400 mt-3">Select a staking plan to view its details.</p>
                </div>
            `;
        }
    }

    planSelect.addEventListener('change', function() {
        updatePlanDetails(this.value);
    });

    // Initialize with selected plan if any
    if (planSelect.value) {
        updatePlanDetails(planSelect.value);
    }
});
</script>
@endsection
