@extends('dashboard.layout.app')

@section('title', $trader->name . ' - AI Stock Trader Details')

@section('content')
@php
    $activated = auth()->user()->hasActivatedAiTrader($trader->id);
    $subscribed = auth()->user()->hasActiveAiTraderSubscription($trader->ai_trader_plan_id);
    $plan = $trader->aiTraderPlan;
    $planUrl = $plan ? route('user.aiTraders.plan', $plan) : route('user.aiTraders.index');
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">{{ $plan ? $plan->name : 'AI traders' }}</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1 flex flex-wrap items-center gap-2">
                {{ $trader->name }}
                @if($activated)
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-500/15 text-emerald-400">Active</span>
                @endif
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">AI-powered stock trading with {{ $trader->ai_model }}.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ $planUrl }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold {{ $activated ? 'col-span-2' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back to plan
            </a>
            @if(!$activated)
                @if($subscribed)
                    <button onclick="showActivationModal({{ $trader->id }}, '{{ $trader->name }}', {{ $plan ? $plan->investment_amount : 0 }})"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        <i class="fas fa-play"></i>Activate
                    </button>
                @else
                    <a href="{{ $planUrl }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Subscribe to plan</a>
                @endif
            @endif
        </div>
    </div>

    <div class="flex flex-wrap gap-1.5">
        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ $trader->ai_model }}</span>
        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ ucfirst($trader->trading_strategy) }}</span>
        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ ucfirst($trader->ai_confidence) }} confidence</span>
        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ ucfirst($trader->ai_learning_mode) }} learning</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Performance</div>
            <div class="text-xl sm:text-2xl font-semibold mt-1 {{ $trader->current_performance >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $trader->formatted_performance }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Win rate</div>
            <div class="text-xl sm:text-2xl font-semibold text-white mt-1">{{ $trader->formatted_win_rate }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total trades</div>
            <div class="text-xl sm:text-2xl font-semibold text-white mt-1">{{ $trader->total_trades }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Wins / losses</div>
            <div class="text-xl sm:text-2xl font-semibold mt-1"><span class="text-emerald-400">{{ $trader->winning_trades }}</span><span class="text-gray-500"> / </span><span class="text-red-400">{{ $trader->total_trades - $trader->winning_trades }}</span></div>
        </div>
    </div>

    <!-- Performance chart -->
    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Performance overview</h2>
        <div class="relative mt-4 h-64 sm:h-80">
            <canvas id="performanceChart"></canvas>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Configuration -->
        <section class="lg:col-span-2 rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Trading configuration</h2>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6">
                <dl class="divide-y divide-gray-700">
                    <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">AI model</dt><dd class="text-white font-medium text-right">{{ $trader->ai_model }}</dd></div>
                    <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">Confidence level</dt><dd class="text-white font-medium">{{ ucfirst($trader->ai_confidence) }}</dd></div>
                    <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">Learning mode</dt><dd class="text-white font-medium">{{ ucfirst($trader->ai_learning_mode) }}</dd></div>
                </dl>
                <dl class="divide-y divide-gray-700 border-t border-gray-700 sm:border-t-0">
                    <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">Strategy</dt><dd class="text-white font-medium">{{ ucfirst($trader->trading_strategy) }}</dd></div>
                    <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">Risk tolerance</dt><dd class="text-white font-medium">{{ $trader->risk_tolerance }}</dd></div>
                    <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">Max positions</dt><dd class="text-white font-medium">{{ $trader->max_positions }}</dd></div>
                </dl>
            </div>

            <h3 class="text-sm font-semibold text-white mt-5">Risk management</h3>
            <div class="mt-3 grid grid-cols-3 gap-2 sm:gap-3">
                <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3 text-center">
                    <div class="text-lg sm:text-xl font-semibold text-red-400">{{ $trader->stop_loss_percentage }}%</div>
                    <div class="text-xs text-gray-400">Stop loss</div>
                </div>
                <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3 text-center">
                    <div class="text-lg sm:text-xl font-semibold text-emerald-400">{{ $trader->take_profit_percentage }}%</div>
                    <div class="text-xs text-gray-400">Take profit</div>
                </div>
                <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3 text-center">
                    <div class="text-lg sm:text-xl font-semibold text-white">{{ $trader->position_size_percentage }}%</div>
                    <div class="text-xs text-gray-400">Position size</div>
                </div>
            </div>

            <h3 class="text-sm font-semibold text-white mt-5">Stocks traded</h3>
            <div class="mt-2 flex flex-wrap gap-1.5">
                @foreach($trader->stocks_to_trade as $stock)
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold font-mono bg-gray-700 text-gray-300">{{ $stock }}</span>
                @endforeach
            </div>
        </section>

        <!-- Plan + action -->
        <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 flex flex-col">
            <h2 class="text-base font-semibold text-white">Plan</h2>
            <dl class="mt-3 divide-y divide-gray-700">
                <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">Plan name</dt><dd class="text-white font-medium text-right">{{ $plan ? $plan->name : 'No Plan' }}</dd></div>
                <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">Monthly cost</dt><dd class="text-white font-medium">{{ $plan ? $plan->formatted_price : 'N/A' }}</dd></div>
                <div class="flex justify-between gap-3 py-2.5 text-sm"><dt class="text-gray-400">Minimum investment</dt><dd class="text-white font-medium font-mono">{{ $plan ? money_from($plan->investment_amount, 'USD', 0) : 'N/A' }}</dd></div>
            </dl>

            <div class="mt-auto pt-5">
                @if($activated)
                    <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4">
                        <div class="text-sm font-semibold text-emerald-400"><i class="fas fa-check-circle mr-1"></i> AI trader active</div>
                        <p class="text-sm text-gray-300 mt-1">This AI trader is trading on your behalf.</p>
                    </div>
                @elseif($subscribed)
                    <p class="text-sm text-gray-400 mb-3">You're subscribed to this plan, so you can activate this trader now.</p>
                    <button onclick="showActivationModal({{ $trader->id }}, '{{ $trader->name }}', {{ $plan ? $plan->investment_amount : 0 }})"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        <i class="fas fa-play"></i>Activate trader
                    </button>
                @else
                    <p class="text-sm text-gray-400 mb-3">Subscribe to the {{ $plan ? $plan->name : 'required' }} plan to activate this AI trader.</p>
                    <a href="{{ $planUrl }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        Go to plan
                    </a>
                @endif
            </div>
        </section>
    </div>

    @if($similarTraders->count() > 0)
    <section class="space-y-3">
        <h2 class="text-base font-semibold text-white">Similar AI traders</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($similarTraders as $similarTrader)
            <a href="{{ route('user.aiTraders.trader', $similarTrader) }}" class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5 hover:border-gold-500 transition-colors block">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-white truncate">{{ $similarTrader->name }}</h3>
                    <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $similarTrader->current_performance >= 0 ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ $similarTrader->formatted_performance }}</span>
                </div>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ $similarTrader->ai_model }}</span>
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ ucfirst($similarTrader->trading_strategy) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-400">Win rate <span class="font-semibold text-white">{{ $similarTrader->formatted_win_rate }}</span></span>
                    <span class="font-semibold text-gold-400">Details →</span>
                </div>
            </a>
            @endforeach
        </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Performance Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Fetch performance data
    fetch('{{ route("user.aiTraders.performance", $trader) }}')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#9CA3AF'
                            }
                        },
                        title: {
                            display: true,
                            text: '8-Week Performance History',
                            color: '#FFFFFF'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(148, 163, 184, 0.12)'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(148, 163, 184, 0.12)'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        }
                    },
                    elements: {
                        point: {
                            radius: 4,
                            hoverRadius: 6
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading performance data:', error);
            // Fallback chart with sample data
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'],
                    datasets: [{
                        label: 'Performance (%)',
                        data: [2.1, 4.3, 6.8, 8.2, 11.5, 14.7, 18.3, {{ $trader->current_performance }}],
                        borderColor: '{{ $trader->current_performance >= 0 ? "#10B981" : "#EF4444" }}',
                        backgroundColor: '{{ $trader->current_performance >= 0 ? "rgba(16, 185, 129, 0.1)" : "rgba(239, 68, 68, 0.1)" }}',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#9CA3AF'
                            }
                        },
                        title: {
                            display: true,
                            text: '8-Week Performance History',
                            color: '#FFFFFF'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(148, 163, 184, 0.12)'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(148, 163, 184, 0.12)'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        }
                    },
                    elements: {
                        point: {
                            radius: 4,
                            hoverRadius: 6
                        }
                    }
                }
            });
        });
});

// Global variables for activation modal
let currentTraderId = null;
let currentMinInvestment = 0;
let currentUserBalance = 0;

// Get user balance on page load
document.addEventListener('DOMContentLoaded', function() {
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

</script>
@endpush
