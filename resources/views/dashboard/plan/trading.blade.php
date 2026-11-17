@extends('dashboard.layout.app')

@section('content')
@php
    $check = '<svg class="w-4 h-4 text-gold-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>';
    $inputCls = 'w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500';
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Plans</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Trading plans</h1>
            <p class="text-sm text-gray-400 mt-0.5">Pick a plan that matches how much you want to trade with.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="px-3 py-2 rounded-lg border border-gray-700 bg-gray-800">
                <div class="text-[10px] uppercase tracking-wider text-gray-400">Trading balance</div>
                <div class="font-mono text-sm font-semibold text-white">{{ money($user->trading_balance ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Trading Plans Grid -->
    @if($tradingPlans->count() > 0)
        @php $popular = $tradingPlans->count() >= 3 ? intdiv($tradingPlans->count(), 2) : ($tradingPlans->count() === 2 ? 1 : -1); @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
            @foreach($tradingPlans as $plan)
                @php $featured = $loop->index === $popular; $planCur = $plan->currency ?: 'USD'; @endphp
                <div class="flex flex-col rounded-2xl border p-5 sm:p-6 {{ $featured ? 'pc-card pc-card--accent border-blue-500/40' : 'border-gray-700 bg-gray-800' }}">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-base font-semibold text-white">{{ $plan->name }}</h3>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($featured)
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-500 text-white">Popular</span>
                            @endif
                            @if($plan->duration)
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300 whitespace-nowrap">{{ $plan->duration }} days</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-xs text-gray-400">Funding from</div>
                        <div class="text-3xl sm:text-4xl font-bold text-white font-mono tracking-tight mt-0.5 truncate">{{ money_from($plan->min_funding, $planCur) }}</div>
                        <div class="text-sm text-gray-400 mt-0.5">
                            @if($plan->hasUnlimitedFunding())
                                Up to unlimited
                            @elseif($plan->max_funding && $plan->max_funding > $plan->min_funding)
                                Up to {{ money_from($plan->max_funding, $planCur) }}
                            @endif
                        </div>
                    </div>

                    <ul class="space-y-2 mt-5 mb-5 rounded-xl p-4 text-sm text-gray-200 {{ $featured ? 'bg-black/25' : 'bg-gray-900' }}">
                        @if($plan->pairs)
                            <li class="flex items-start gap-2">{!! $check !!}<span>{{ \Illuminate\Support\Str::contains(strtolower($plan->pairs), 'pair') ? $plan->pairs : $plan->pairs.' pairs' }}</span></li>
                        @endif
                        @if($plan->leverage)
                            <li class="flex items-start gap-2">{!! $check !!}<span>Leverage up to 1:{{ number_format($plan->leverage) }}</span></li>
                        @endif
                        @if($plan->spreads)
                            <li class="flex items-start gap-2">{!! $check !!}<span>Spreads from {{ $plan->spreads }} pips</span></li>
                        @endif
                        @if($plan->swap_fees)
                            <li class="flex items-start gap-2">{!! $check !!}<span>Swap fees: {{ $plan->swap_fees }}%</span></li>
                        @endif
                        @if($plan->max_lot_size)
                            <li class="flex items-start gap-2">{!! $check !!}<span>Max lot size: {{ $plan->max_lot_size }}</span></li>
                        @endif
                        @if($plan->features)
                            <li class="flex items-start gap-2">{!! $check !!}<span>{{ $plan->features }}</span></li>
                        @endif
                    </ul>

                    <button type="button"
                        class="mt-auto w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold"
                        onclick="subscribeToPlan({{ $plan->id }}, @js($plan->name), {{ $plan->min_funding }}, @js($plan->currency))"
                    >
                        Subscribe
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Plans Available -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-8 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
            </div>
            <h3 class="text-white font-medium mt-3">No trading plans available</h3>
            <p class="text-sm text-gray-400 mt-1">There are no active trading plans right now. Check back later or contact support.</p>
        </div>
    @endif

    <!-- Additional Information -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Included with every plan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-4">
            @foreach([
                ['Professional trading tools', 'Advanced charting, technical analysis and real-time market data.'],
                ['Risk management', 'Built-in stop-loss, take-profit and position sizing to protect your capital.'],
                ['24/7 support', 'Round-the-clock help with any trading question.'],
                ['Educational resources', 'Trading guides, webinars and market analysis to build your skills.'],
            ] as [$title, $text])
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600/15 text-gold-400 grid place-items-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-white">{{ $title }}</h3>
                        <p class="text-sm text-gray-400 mt-0.5">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Subscribe Modal -->
<div id="subscribeModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4" onclick="if (event.target === this) closeSubscribeModal()">
        <div class="w-full max-w-lg rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="flex justify-between items-center px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Subscribe to plan</h3>
                <button type="button" onclick="closeSubscribeModal()" class="w-10 h-10 -mr-2 grid place-items-center text-gray-400 hover:text-white" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="subscribeForm" method="POST" action="" class="p-4 sm:p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="planName" class="block text-sm font-medium text-gray-300 mb-1.5">Plan</label>
                        <input type="text" id="planName" readonly class="{{ $inputCls }} text-gray-300">
                    </div>

                    <div id="planFundingRange" class="px-4 py-3 rounded-xl border border-gray-700 bg-gray-900/50 hidden">
                        <div class="text-sm text-gray-300">
                            <span class="text-gray-400">Funding range:</span> <span id="fundingRangeText" class="font-mono text-white"></span>
                        </div>
                    </div>

                    <div>
                        <label for="amountPaid" class="block text-sm font-medium text-gray-300 mb-1.5">Amount to pay</label>
                        <input type="number" id="amountPaid" name="amount_paid" step="0.01" required inputmode="decimal" class="{{ $inputCls }} font-mono" oninput="checkBalance()" onchange="checkBalance()" placeholder="Enter amount within plan limits">
                        <p class="text-xs text-gray-500 mt-1">Must be within the plan's funding range.</p>
                    </div>

                    <!-- Balance Check Section -->
                    <div id="balanceCheckSection" class="hidden">
                        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30">
                            <div class="text-sm text-red-400 mb-3">
                                <strong>Insufficient trading balance</strong><br>
                                Your trading balance: <strong>{{ money($user->trading_balance ?? 0, 2) }}</strong><br>
                                Amount needed: <strong>{{ currency_symbol() }}<span id="amountNeeded"></span></strong>
                            </div>
                            <button type="button" onclick="openFundAccountModal()" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                Fund your trading account
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-300 mb-1.5">Currency</label>
                        <select name="currency" id="currency" required class="{{ $inputCls }}">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="JPY">JPY</option>
                            <option value="CAD">CAD</option>
                            <option value="AUD">AUD</option>
                            <option value="CHF">CHF</option>
                            <option value="CNY">CNY</option>
                            <option value="INR">INR</option>
                            <option value="BRL">BRL</option>
                        </select>
                    </div>

                    <div>
                        <label for="subscribeNotes" class="block text-sm font-medium text-gray-300 mb-1.5">Notes <span class="text-gray-500">(optional)</span></label>
                        <textarea id="subscribeNotes" name="notes" rows="3" class="{{ $inputCls }}" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeSubscribeModal()" class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                        Cancel
                    </button>
                    <button type="submit" id="subscribeButton" class="flex-[2] inline-flex items-center justify-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                        Subscribe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fund Trading Modal -->
<div id="fundTradingModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4" onclick="if (event.target === this) closeFundTradingModal()">
        <div class="w-full max-w-lg rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="flex justify-between items-center px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Fund your trading account</h3>
                <button type="button" onclick="closeFundTradingModal()" class="w-10 h-10 -mr-2 grid place-items-center text-gray-400 hover:text-white" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-4 sm:p-6">
                <div class="mb-4 grid grid-cols-2 gap-2">
                    <div class="rounded-xl border border-gray-700 bg-gray-900/50 px-3 py-2.5">
                        <div class="text-xs text-gray-400">Trading balance</div>
                        <div class="text-sm font-semibold text-white font-mono">{{ money($user->trading_balance ?? 0, 2) }}</div>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-gray-900/50 px-3 py-2.5">
                        <div class="text-xs text-gray-400">To deposit</div>
                        <div class="text-sm font-semibold text-white font-mono">{{ currency_symbol() }}<span id="fundAmountDisplay"></span></div>
                    </div>
                </div>

                <form id="fundTradingForm" method="POST" action="{{ route('user.payment') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="fundAmount" class="block text-sm font-medium text-gray-300 mb-1.5">Deposit amount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-mono">{{ currency_symbol() }}</span>
                                <input type="number" id="fundAmount" name="amount" step="0.01" required inputmode="decimal" class="{{ $inputCls }} pl-8 font-mono" placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label for="fundWalletType" class="block text-sm font-medium text-gray-300 mb-1.5">Wallet</label>
                            <select id="fundWalletType" name="wallet_type" required class="{{ $inputCls }}">
                                <option value="trading">Trading Balance</option>
                                <option value="holding">Holding Balance</option>
                                <option value="staking">Staking Balance</option>
                            </select>
                        </div>

                        <div>
                            <label for="fundPaymentMethod" class="block text-sm font-medium text-gray-300 mb-1.5">Payment method</label>
                            <select id="fundPaymentMethod" name="payment_method_id" required class="{{ $inputCls }}">
                                @foreach(\App\Models\PaymentMethod::where('is_active', true)->get() as $method)
                                    <option value="{{ $method->id }}">{{ $method->crypto_display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fundProof" class="block text-sm font-medium text-gray-300 mb-1.5">Proof of payment</label>
                            <input type="file" id="fundProof" name="proof" required accept="image/*,.pdf" class="{{ $inputCls }} text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-gray-700 file:text-white">
                            <p class="text-xs text-gray-500 mt-1">Upload a screenshot or PDF of your payment.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeFundTradingModal()" class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="flex-[2] inline-flex items-center justify-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            Submit deposit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const userTradingBalance = {{ $user->trading_balance ?? 0 }};
    
    // Plan max funding data for validation
    window.planMaxFunding = {
        @foreach($tradingPlans as $plan)
            {{ $plan->id }}: {{ $plan->max_funding ? $plan->max_funding : 'null' }},
        @endforeach
    };
    
    function subscribeToPlan(planId, planName, planPrice, planCurrency) {
        // Set modal values
        document.getElementById('planName').value = planName;
        document.getElementById('amountPaid').value = planPrice;
        document.getElementById('currency').value = planCurrency;
        
        // Set form action
        document.getElementById('subscribeForm').action = `/user/plans/subscribe/${planId}`;
        
        // Store plan data for validation
        window.currentPlanData = {
            id: planId,
            name: planName,
            min_funding: planPrice,
            max_funding: window.planMaxFunding[planId] || null,
            currency: planCurrency
        };
        
        // Show funding range
        const fundingRangeDiv = document.getElementById('planFundingRange');
        const fundingRangeText = document.getElementById('fundingRangeText');
        if (fundingRangeDiv && fundingRangeText) {
            const maxFunding = window.planMaxFunding[planId];
            if (maxFunding && maxFunding > planPrice) {
                fundingRangeText.textContent = `${planCurrency} ${planPrice.toFixed(2)} - ${planCurrency} ${maxFunding.toFixed(2)}`;
            } else {
                fundingRangeText.textContent = `${planCurrency} ${planPrice.toFixed(2)} - Unlimited`;
            }
            fundingRangeDiv.classList.remove('hidden');
        }
        
        // Check balance immediately when modal opens
        checkBalance();
        
        // Show modal
        document.getElementById('subscribeModal').classList.remove('hidden');
    }
    
    function checkBalance() {
        const amountInput = document.getElementById('amountPaid');
        const balanceSection = document.getElementById('balanceCheckSection');
        const amountNeededSpan = document.getElementById('amountNeeded');
        
        if (!amountInput || !balanceSection) return;
        
        const enteredAmount = parseFloat(amountInput.value) || 0;
        
        // Get plan details from the modal
        const planName = document.getElementById('planName').value;
        const planId = document.getElementById('subscribeForm').action.split('/').pop();
        
        // Find the plan data (we'll need to pass this from the subscribeToPlan function)
        const currentPlan = window.currentPlanData;
        
        if (currentPlan) {
            // Check if amount is within min/max funding range
            if (enteredAmount < currentPlan.min_funding) {
                // Show min funding error
                showFundingError(`Amount must be at least ${currentPlan.currency} ${currentPlan.min_funding.toFixed(2)}`);
                return;
            }
            
            if (currentPlan.max_funding && enteredAmount > currentPlan.max_funding) {
                // Show max funding error
                showFundingError(`Amount cannot exceed ${currentPlan.currency} ${currentPlan.max_funding.toFixed(2)}`);
                return;
            }
        }
        
        // Clear any funding errors
        hideFundingError();
        
        if (enteredAmount > userTradingBalance) {
            const amountNeeded = enteredAmount - userTradingBalance;
            amountNeededSpan.textContent = amountNeeded.toFixed(2);
            balanceSection.classList.remove('hidden');
        } else {
            balanceSection.classList.add('hidden');
        }
    }
    
    function showFundingError(message) {
        let errorSection = document.getElementById('fundingErrorSection');
        if (!errorSection) {
            errorSection = document.createElement('div');
            errorSection.id = 'fundingErrorSection';
            errorSection.className = 'p-4 bg-red-900/20 border border-red-500/30 rounded-lg';
            errorSection.innerHTML = `
                <div class="text-sm text-red-400">
                    <strong>Invalid Amount:</strong> ${message}
                </div>
            `;
            
            // Insert after the amount input field
            const amountInput = document.getElementById('amountPaid');
            amountInput.parentNode.insertBefore(errorSection, amountInput.nextSibling);
        } else {
            errorSection.innerHTML = `
                <div class="text-sm text-red-400">
                    <strong>Invalid Amount:</strong> ${message}
                </div>
            `;
            errorSection.classList.remove('hidden');
        }
        
        // Disable subscribe button when there are validation errors
        const subscribeButton = document.getElementById('subscribeButton');
        if (subscribeButton) {
            subscribeButton.disabled = true;
        }
    }
    
    function hideFundingError() {
        const errorSection = document.getElementById('fundingErrorSection');
        if (errorSection) {
            errorSection.classList.add('hidden');
        }
        
        // Enable subscribe button when validation errors are cleared
        const subscribeButton = document.getElementById('subscribeButton');
        if (subscribeButton) {
            subscribeButton.disabled = false;
        }
    }
    
    function openFundAccountModal() {
        // Close subscribe modal
        closeSubscribeModal();
        
        // Open fund trading modal with the amount needed
        const amountInput = document.getElementById('amountPaid');
        const enteredAmount = parseFloat(amountInput.value) || 0;
        const amountNeeded = Math.max(0, enteredAmount - userTradingBalance);
        
        // Set fund modal values
        document.getElementById('fundAmount').value = amountNeeded.toFixed(2);
        document.getElementById('fundAmountDisplay').textContent = amountNeeded.toFixed(2);
        
        // Show fund modal
        document.getElementById('fundTradingModal').classList.remove('hidden');
    }
    
    function closeSubscribeModal() {
        document.getElementById('subscribeModal').classList.add('hidden');
        // Hide balance check section when modal closes
        const balanceSection = document.getElementById('balanceCheckSection');
        if (balanceSection) {
            balanceSection.classList.add('hidden');
        }
        // Clear plan data and hide funding errors
        window.currentPlanData = null;
        hideFundingError();
        
        // Hide funding range
        const fundingRangeDiv = document.getElementById('planFundingRange');
        if (fundingRangeDiv) {
            fundingRangeDiv.classList.add('hidden');
        }
    }
    
    function closeFundTradingModal() {
        document.getElementById('fundTradingModal').classList.add('hidden');
    }
    
    // Close modals when clicking outside
    document.getElementById('subscribeModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSubscribeModal();
        }
    });
    
    document.getElementById('fundTradingModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFundTradingModal();
        }
    });
</script>
@endsection
