@extends('dashboard.layout.app')

@section('title', $plan->name . ' - AI Stock Traders')

@section('content')
@php
    $isSubscribed = auth()->user()->hasActiveAiTraderSubscription($plan->id);
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">AI traders</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">{{ $plan->name }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $plan->description ?: 'Subscribe to this plan, then activate the AI traders you want.' }}</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.aiTraders.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                All plans
            </a>
        </div>
    </div>

    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5">
        <dl class="grid grid-cols-3 divide-x divide-gray-700">
            <div class="pr-3 min-w-0">
                <dt class="text-xs text-gray-400">Per month</dt>
                <dd class="text-base sm:text-2xl font-semibold text-white mt-1 truncate tabular-nums">{{ $plan->formatted_price }}</dd>
            </div>
            <div class="px-3 min-w-0">
                <dt class="text-xs text-gray-400">AI traders</dt>
                <dd class="text-base sm:text-2xl font-semibold text-white mt-1 tabular-nums">{{ $plan->number_of_traders }}</dd>
            </div>
            <div class="pl-3 min-w-0">
                <dt class="text-xs text-gray-400">Min. invest</dt>
                <dd class="text-base sm:text-2xl font-semibold text-white mt-1 truncate tabular-nums">{{ money_from($plan->investment_amount, 'USD', 2) }}</dd>
            </div>
        </dl>
    </section>

    <!-- Subscription -->
    @if($isSubscribed)
        @php $activeSub = auth()->user()->activeAiTraderSubscriptions()->where('ai_trader_plan_id', $plan->id)->first(); @endphp
        <section class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 sm:p-5 flex items-start gap-3">
            <span class="w-10 h-10 shrink-0 rounded-full bg-emerald-500/15 text-emerald-400 grid place-items-center"><i class="fas fa-check"></i></span>
            <div>
                <h2 class="text-base font-semibold text-white">You're subscribed</h2>
                <p class="text-sm text-gray-300 mt-0.5">Activate any AI trader below to start automated trading.@if(optional($activeSub)->expires_at) Renews or expires {{ $activeSub->expires_at->format('M d, Y') }}.@endif</p>
            </div>
        </section>
    @else
        <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-white">Subscribe to {{ $plan->name }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">Unlock every AI trader in this plan and start automated trading.</p>
            </div>
            <button onclick="subscribeToPlan({{ $plan->id }})"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                Subscribe for {{ $plan->formatted_price }}/month
            </button>
        </section>
    @endif

    <!-- AI Traders -->
    <section id="traders" class="space-y-3">
        <div>
            <h2 class="text-base font-semibold text-white">Available AI traders</h2>
            <p class="text-sm text-gray-400 mt-0.5">Each trader has its own strategy, stock list and risk limits.</p>
        </div>

        @if($traders->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($traders as $trader)
            @php
                $activated = auth()->user()->hasActivatedAiTrader($trader->id);
                $winPct = min(100, max(0, (float) $trader->win_rate));
                $perfUp = $trader->current_performance >= 0;
                $stocks = collect($trader->stocks_to_trade ?? []);
            @endphp
            <div class="pc-card rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5 flex flex-col">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 shrink-0 rounded-xl bg-blue-500/15 text-blue-300 grid place-items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="5" y="8" width="14" height="12" rx="2"/><path d="M12 4v4M9 14h.01M15 14h.01"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-semibold text-white leading-tight truncate">{{ $trader->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ ucfirst($trader->trading_strategy) }} · {{ $trader->ai_model }}</p>
                    </div>
                    @if($activated)
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 pc-pulse"></span>Active</span>
                    @else
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-700 text-gray-300">{{ ucfirst($trader->ai_confidence) }}</span>
                    @endif
                </div>

                <div class="mt-5">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <div class="text-xs text-gray-400">Performance</div>
                            <div class="text-3xl font-bold tracking-tight leading-none mt-1.5 tabular-nums {{ $perfUp ? 'text-emerald-400' : 'text-red-400' }}">{{ $trader->formatted_performance }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-400">Win rate</div>
                            <div class="text-sm font-semibold text-white mt-1 tabular-nums">{{ $trader->formatted_win_rate }}</div>
                        </div>
                    </div>
                    <div class="mt-3 h-1.5 rounded-full bg-gray-700/70 overflow-hidden" role="img" aria-label="Win rate {{ $trader->formatted_win_rate }}">
                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $winPct }}%"></div>
                    </div>
                </div>

                <dl class="mt-4 grid grid-cols-3 gap-2">
                    <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                        <dt class="text-[11px] text-gray-400">Trades</dt>
                        <dd class="text-sm font-semibold text-white mt-0.5 tabular-nums truncate">{{ number_format($trader->total_trades) }}</dd>
                    </div>
                    <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                        <dt class="text-[11px] text-gray-400">Stop loss</dt>
                        <dd class="text-sm font-semibold text-red-400 mt-0.5 tabular-nums truncate">{{ $trader->stop_loss_percentage }}%</dd>
                    </div>
                    <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                        <dt class="text-[11px] text-gray-400">Take profit</dt>
                        <dd class="text-sm font-semibold text-emerald-400 mt-0.5 tabular-nums truncate">{{ $trader->take_profit_percentage }}%</dd>
                    </div>
                </dl>

                <div class="mt-3 space-y-1 text-xs">
                    <p class="truncate text-gray-300 font-medium" title="{{ $stocks->implode(', ') }}">{{ $stocks->take(5)->implode(' · ') }}@if($stocks->count() > 5)<span class="text-gray-500"> +{{ $stocks->count() - 5 }}</span>@endif</p>
                    <p class="text-gray-500">Up to {{ $trader->max_positions }} positions, {{ $trader->position_size_percentage }}% each</p>
                </div>

                <div class="mt-auto pt-4 space-y-2">
                    @if($activated)
                        <a href="{{ route('user.aiTraders.trader', $trader) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 text-emerald-300 text-sm font-semibold">
                            <i class="fas fa-check-circle"></i>Activated · view performance
                        </a>
                    @elseif($isSubscribed)
                        <button onclick="showActivationModal({{ $trader->id }}, '{{ $trader->name }}', {{ $trader->aiTraderPlan ? $trader->aiTraderPlan->investment_amount : 0 }})"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            <i class="fas fa-play"></i>Activate trader
                        </button>
                    @else
                        <span class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-700/60 text-gray-400 text-sm font-semibold" title="Subscribe to this plan to activate">
                            <i class="fas fa-lock"></i>Subscribe to activate
                        </span>
                    @endif
                    @unless($activated)
                    <a href="{{ route('user.aiTraders.trader', $trader) }}"
                       class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 text-sm font-semibold text-gold-400 hover:text-gold-300">
                        View details
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    @endunless
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="rounded-2xl border border-gray-700 bg-gray-800 px-4 py-12 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-gray-700 grid place-items-center text-gray-300"><i class="fas fa-robot"></i></div>
            <h3 class="text-white font-medium mt-3">No AI traders in this plan yet</h3>
            <p class="text-sm text-gray-400 mt-1">Check back soon, or look at the other plans.</p>
            <a href="{{ route('user.aiTraders.index') }}" class="mt-4 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">See all plans</a>
        </div>
        @endif
    </section>

    @if($plan->features)
    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Plan features</h2>
        <ul class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($plan->features as $feature)
            <li class="flex items-start gap-2 text-sm text-gray-300">
                <svg class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                {{ $feature }}
            </li>
            @endforeach
        </ul>
    </section>
    @endif
</div>

<!-- Activation Modal -->
<div id="activationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-end sm:items-center justify-center sm:p-4">
    <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between gap-3 px-4 sm:px-6 py-4 border-b border-gray-700">
            <h3 class="text-base font-semibold text-white">Activate AI trader</h3>
            <button onclick="closeActivationModal()" class="w-10 h-10 -mr-2 grid place-items-center rounded-lg text-gray-400 hover:text-white hover:bg-gray-700" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 sm:p-6 space-y-4">
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <div class="font-semibold text-white" id="traderName">Trader Name</div>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-400">Minimum investment</span>
                        <span class="font-semibold text-white" id="minInvestment">{{ currency_symbol() }}0</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-400">Your trading balance</span>
                        <span class="font-semibold text-emerald-400" id="userBalance">{{ currency_symbol() }}0</span>
                    </div>
                </div>
            </div>

            <div>
                <label for="investmentAmount" class="block text-sm font-medium text-gray-300 mb-1.5">Investment amount</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">{{ currency_symbol() }}</span>
                    <input type="number"
                           id="investmentAmount"
                           inputmode="decimal"
                           class="w-full pl-9 pr-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter amount"
                           min="0"
                           step="0.01">
                </div>
                <div class="mt-2 text-sm text-gray-400" id="amountError"></div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="closeActivationModal()"
                        class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                    Cancel
                </button>
                <button onclick="confirmActivation()"
                        id="confirmActivationBtn"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-play"></i>Activate
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Get user balance on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get user balance from a data attribute or make an API call
    // For now, we'll use a placeholder - in real implementation, get from user data
    currentUserBalance = {{ auth()->user()->trading_balance ?? 0 }};
    
    // Add input validation for investment amount
    const investmentInput = document.getElementById('investmentAmount');
    if (investmentInput) {
        investmentInput.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            validateInvestmentAmount(amount);
        });
    }
});

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

// Global variables for activation modal
let currentTraderId = null;
let currentMinInvestment = 0;
let currentUserBalance = 0;

// Show activation modal
function showActivationModal(traderId, traderName, minInvestment) {
    currentTraderId = traderId;
    currentMinInvestment = minInvestment;
    
    // Update modal content
    document.getElementById('traderName').textContent = traderName;
    document.getElementById('minInvestment').textContent = CURRENCY_SYMBOL + minInvestment.toLocaleString();
    document.getElementById('userBalance').textContent = CURRENCY_SYMBOL + currentUserBalance.toLocaleString();
    document.getElementById('investmentAmount').value = minInvestment;
    document.getElementById('amountError').textContent = '';
    
    // Show modal
    document.getElementById('activationModal').classList.remove('hidden');
    
    // Focus on input
    setTimeout(() => {
        document.getElementById('investmentAmount').focus();
    }, 100);
}

// Close activation modal
function closeActivationModal() {
    document.getElementById('activationModal').classList.add('hidden');
    currentTraderId = null;
    currentMinInvestment = 0;
}

// Validate investment amount
function validateInvestmentAmount(amount) {
    const errorElement = document.getElementById('amountError');
    const confirmBtn = document.getElementById('confirmActivationBtn');
    
    if (amount < currentMinInvestment) {
        errorElement.textContent = `Minimum investment required: ${CURRENCY_SYMBOL}${currentMinInvestment.toLocaleString()}`;
        errorElement.className = 'mt-2 text-sm text-red-400';
        confirmBtn.disabled = true;
        return false;
    } else if (amount > currentUserBalance) {
        errorElement.textContent = `Insufficient balance. Available: ${CURRENCY_SYMBOL}${currentUserBalance.toLocaleString()}`;
        errorElement.className = 'mt-2 text-sm text-red-400';
        confirmBtn.disabled = true;
        return false;
    } else {
        errorElement.textContent = '';
        errorElement.className = 'mt-2 text-sm text-gray-400';
        confirmBtn.disabled = false;
        return true;
    }
}

// Confirm activation
function confirmActivation() {
    const amount = parseFloat(document.getElementById('investmentAmount').value);
    
    if (!validateInvestmentAmount(amount)) {
        return;
    }
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Confirm Activation',
            text: `Are you sure you want to activate this AI Trader with ${CURRENCY_SYMBOL}${amount.toLocaleString()}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0b1020',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Activate!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                activateTrader(currentTraderId, amount);
            }
        });
    } else {
        uiConfirm(`Are you sure you want to activate this AI Trader with ${CURRENCY_SYMBOL}${amount.toLocaleString()}?`, { title: 'Confirm Activation', confirmText: 'Yes, Activate!', tone: 'info' }).then(function (ok) {
            if (ok) activateTrader(currentTraderId, amount);
        });
    }
}

// Activate AI Trader function
function activateTrader(traderId, investmentAmount) {
    // Show loading state
    const confirmBtn = document.getElementById('confirmActivationBtn');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Activating...';
    confirmBtn.disabled = true;
    
    // Make API call to activate trader
    fetch(`/user/ai-traders/trader/${traderId}/activate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            investment_amount: investmentAmount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'AI Trader Activated!',
                    text: data.message || 'Your AI Trader has been successfully activated and is now trading.',
                    confirmButtonColor: '#0b1020'
                });
            } else {
                uiAlert('AI Trader Activated! ' + (data.message || 'Your AI Trader has been successfully activated and is now trading.'), { tone: 'success', title: 'Done' });
            }
            
            // Close modal and reload page
            closeActivationModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to activate AI Trader');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Activation Failed',
                text: error.message || 'There was an error activating the AI Trader. Please try again.',
                confirmButtonColor: '#0b1020'
            });
        } else {
            uiAlert('Activation Failed: ' + (error.message || 'There was an error activating the AI Trader. Please try again.'), { tone: 'danger', title: 'Something went wrong' });
        }
        
        // Restore original button state
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
    });
}

// Subscribe to AI Trader Plan function
async function subscribeToPlan(planId) {
    if (await uiConfirm('Are you sure you want to subscribe to this AI Trader Plan? This will enable you to activate AI Traders from this plan.', { tone: 'info' })) {
        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Subscribing...';
        button.disabled = true;
        
        // Make API call to subscribe to plan
        fetch(`/user/ai-traders/plan/${planId}/subscribe`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Subscription Successful!',
                        text: data.message || 'You have successfully subscribed to this AI Trader Plan.',
                        confirmButtonColor: '#0b1020'
                    });
                } else {
                    uiAlert('Subscription Successful! ' + (data.message || 'You have successfully subscribed to this AI Trader Plan.'), { tone: 'success', title: 'Done' });
                }
                
                // Reload the page to show activate buttons
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Failed to subscribe to AI Trader Plan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Subscription Failed',
                    text: error.message || 'There was an error subscribing to the AI Trader Plan. Please try again.',
                    confirmButtonColor: '#0b1020'
                });
            } else {
                uiAlert('Subscription Failed: ' + (error.message || 'There was an error subscribing to the AI Trader Plan. Please try again.'), { tone: 'danger', title: 'Something went wrong' });
            }
            
            // Restore original button state
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}
</script>
@endpush
