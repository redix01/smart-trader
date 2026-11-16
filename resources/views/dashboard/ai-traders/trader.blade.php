@extends('dashboard.layout.app')

@section('title', $trader->name . ' - AI Stock Trader Details')

@section('content')
<!-- Trader Header -->
<section class="bg-gray-800 border border-gray-700 rounded-2xl text-white py-16 relative overflow-hidden">
        
    <div class="w-full px-4 relative z-10">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center mb-4">
                <a href="{{ route('user.aiTraders.plan', $trader->aiTraderPlan) }}" class="text-gray-300 hover:text-blue-300 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <span class="text-gray-300">{{ $trader->aiTraderPlan ? $trader->aiTraderPlan->name : 'No Plan' }}</span>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <div class="flex items-center gap-4 mb-4">
                        <h1 class="text-4xl font-bold">{{ $trader->name }}</h1>
                        @if(auth()->user()->hasActivatedAiTrader($trader->id))
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @endif
                    </div>
                    <p class="text-xl text-gray-300 mb-6">Advanced AI-powered stock trading with {{ $trader->ai_model }}</p>
                    
                    <div class="flex flex-wrap gap-3 mb-6">
                        <span class="bg-blue-50 text-blue-400 px-3 py-1 rounded-full text-sm border border-gray-700">{{ $trader->ai_model }}</span>
                        <span class="bg-blue-600/20 text-blue-400 px-3 py-1 rounded-full text-sm border border-blue-600/30">{{ ucfirst($trader->trading_strategy) }}</span>
                        <span class="bg-green-600/20 text-green-400 px-3 py-1 rounded-full text-sm border border-green-600/30">{{ ucfirst($trader->ai_confidence) }} Confidence</span>
                        <span class="bg-orange-600/20 text-orange-400 px-3 py-1 rounded-full text-sm border border-orange-600/30">{{ ucfirst($trader->ai_learning_mode) }} Learning</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 backdrop-blur-sm rounded-lg p-4 text-center border border-gray-700">
                        <div class="text-3xl font-bold {{ $trader->current_performance >= 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $trader->formatted_performance }}
                        </div>
                        <div class="text-gray-300 text-sm">Current Performance</div>
                    </div>
                    <div class="bg-blue-50 backdrop-blur-sm rounded-lg p-4 text-center border border-gray-700">
                        <div class="text-3xl font-bold text-blue-400">{{ $trader->formatted_win_rate }}</div>
                        <div class="text-gray-300 text-sm">Win Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Performance Chart -->
<section class="py-16 bg-gray-800">
    <div class="w-full px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-white mb-8">Performance Overview</h2>
            <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
                <canvas id="performanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</section>

<!-- Trader Details -->
<section class="py-16 bg-gray-900">
    <div class="w-full px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Details -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-700">
                        <h3 class="text-2xl font-bold text-white mb-6">Trading Configuration</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-white mb-3">AI Settings</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">AI Model:</span>
                                        <span class="font-medium text-blue-400">{{ $trader->ai_model }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Confidence Level:</span>
                                        <span class="font-medium text-blue-400">{{ ucfirst($trader->ai_confidence) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Learning Mode:</span>
                                        <span class="font-medium text-blue-400">{{ ucfirst($trader->ai_learning_mode) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-semibold text-white mb-3">Trading Strategy</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Strategy:</span>
                                        <span class="font-medium text-blue-400">{{ ucfirst($trader->trading_strategy) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Risk Tolerance:</span>
                                        <span class="font-medium text-blue-400">{{ $trader->risk_tolerance }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Max Positions:</span>
                                        <span class="font-medium text-blue-400">{{ $trader->max_positions }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <h4 class="font-semibold text-white mb-3">Stocks Trading</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($trader->stocks_to_trade as $stock)
                                <span class="bg-blue-50 text-blue-400 px-3 py-1 rounded-full text-sm font-medium border border-gray-700">{{ $stock }}</span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <h4 class="font-semibold text-white mb-3">Risk Management</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-red-400">{{ $trader->stop_loss_percentage }}%</div>
                                    <div class="text-sm text-red-300">Stop Loss</div>
                                </div>
                                <div class="bg-green-500/20 border border-green-500/30 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-400">{{ $trader->take_profit_percentage }}%</div>
                                    <div class="text-sm text-green-300">Take Profit</div>
                                </div>
                                <div class="bg-blue-50 border border-gray-700 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-400">{{ $trader->position_size_percentage }}%</div>
                                    <div class="text-sm text-blue-400">Position Size</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats & Actions -->
                <div>
                    <!-- Trading Statistics -->
                    <div class="bg-gray-800 rounded-xl shadow-lg p-6 mb-6 border border-gray-700">
                        <h3 class="text-xl font-bold text-white mb-4">Trading Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Total Trades:</span>
                                <span class="font-semibold text-blue-400">{{ $trader->total_trades }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Winning Trades:</span>
                                <span class="font-semibold text-green-400">{{ $trader->winning_trades }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Losing Trades:</span>
                                <span class="font-semibold text-red-400">{{ $trader->total_trades - $trader->winning_trades }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Win Rate:</span>
                                <span class="font-semibold text-blue-400">{{ $trader->formatted_win_rate }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Current Performance:</span>
                                <span class="font-semibold {{ $trader->current_performance >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $trader->formatted_performance }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Plan Info -->
                    <div class="bg-gray-800 rounded-xl shadow-lg p-6 mb-6 border border-gray-700">
                        <h3 class="text-xl font-bold text-white mb-4">Plan Information</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-gray-400">Plan Name</div>
                                <div class="font-semibold text-white">{{ $trader->aiTraderPlan ? $trader->aiTraderPlan->name : 'No Plan' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-400">Monthly Cost</div>
                                <div class="font-semibold text-2xl text-blue-400">{{ $trader->aiTraderPlan ? $trader->aiTraderPlan->formatted_price : 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-400">Minimum Investment</div>
                                <div class="font-semibold text-white">{{ currency_symbol() }}{{ $trader->aiTraderPlan ? number_format($trader->aiTraderPlan->investment_amount, 0) : 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white text-center">
                        @if(auth()->user()->hasActivatedAiTrader($trader->id))
                            <h3 class="text-xl font-bold mb-2">AI Trader Active</h3>
                            <p class="text-white/80 mb-4">This AI trader is currently active and trading on your behalf.</p>
                            <div class="flex gap-2">
                                <div class="flex-1 bg-green-600/20 text-green-700 py-3 px-4 rounded-lg font-semibold border border-green-600/30">
                                    <i class="fas fa-check-circle mr-2"></i>Activated
                                </div>
                                <a href="{{ route('user.aiTraders.plan', $trader->aiTraderPlan) }}" 
                                   class="bg-gray-800 text-blue-400 py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300 border border-gray-700 hover:border-blue-300 flex items-center justify-center">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        @elseif(auth()->user()->hasActiveAiTraderSubscription($trader->ai_trader_plan_id))
                            <h3 class="text-xl font-bold mb-2">Ready to Activate?</h3>
                            <p class="text-white/80 mb-4">Start using this AI trader to optimize your stock portfolio.</p>
                            <div class="flex gap-2">
                                <button onclick="showActivationModal({{ $trader->id }}, '{{ $trader->name }}', {{ $trader->aiTraderPlan ? $trader->aiTraderPlan->investment_amount : 0 }})" 
                                        class="flex-1 bg-gray-800 text-blue-400 py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300 border border-gray-700 hover:border-blue-300">
                                    <i class="fas fa-play mr-2"></i>Activate Trader
                                </button>
                                <a href="{{ route('user.aiTraders.plan', $trader->aiTraderPlan) }}" 
                                   class="bg-gray-800 text-blue-400 py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300 border border-gray-700 hover:border-blue-300 flex items-center justify-center">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        @else
                            <h3 class="text-xl font-bold mb-2">Subscription Required</h3>
                            <p class="text-white/80 mb-4">You need to subscribe to the {{ $trader->aiTraderPlan ? $trader->aiTraderPlan->name : 'required' }} plan to activate this AI trader.</p>
                            <div class="flex gap-2">
                                <a href="{{ route('user.aiTraders.plan', $trader->aiTraderPlan) }}" 
                                   class="flex-1 bg-gray-800 text-blue-400 py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300 border border-gray-700 hover:border-blue-300 text-center">
                                    <i class="fas fa-credit-card mr-2"></i>Go to Plan
                                </a>
                                <a href="{{ route('user.aiTraders.plan', $trader->aiTraderPlan) }}" 
                                   class="bg-gray-800 text-blue-400 py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300 border border-gray-700 hover:border-blue-300 flex items-center justify-center">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Similar Traders -->
@if($similarTraders->count() > 0)
<section class="py-16 bg-gray-800">
    <div class="w-full px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-white mb-8">Similar AI Traders</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($similarTraders as $similarTrader)
                <div class="bg-gray-800 rounded-xl p-6 hover:shadow-lg transition-shadow duration-300 border border-gray-700 hover:border-blue-300">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-white">{{ $similarTrader->name }}</h3>
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ $similarTrader->formatted_performance }}
                        </span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400 mb-3">
                        <span class="bg-blue-50 text-blue-400 px-2 py-1 rounded text-xs mr-2 border border-gray-700">{{ $similarTrader->ai_model }}</span>
                        <span class="bg-blue-600/20 text-blue-400 px-2 py-1 rounded text-xs border border-blue-600/30">{{ ucfirst($similarTrader->trading_strategy) }}</span>
                    </div>
                    <div class="text-sm text-gray-400 mb-4">
                        Win Rate: <span class="font-semibold text-blue-400">{{ $similarTrader->formatted_win_rate }}</span>
                    </div>
                    <a href="{{ route('user.aiTraders.trader', $similarTrader) }}" 
                       class="text-blue-400 hover:text-blue-300 font-medium text-sm">
                        View Details →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Activation Modal -->
<div id="activationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-xl shadow-2xl max-w-md w-full border border-gray-700">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Activate AI Trader</h3>
                <button onclick="closeActivationModal()" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="bg-blue-50 border border-gray-700 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-robot text-blue-400 mr-3"></i>
                        <span class="font-semibold text-white" id="traderName">Trader Name</span>
                    </div>
                    <div class="text-sm text-gray-300">
                        <div class="flex justify-between">
                            <span>Minimum Investment:</span>
                            <span class="font-semibold text-blue-400" id="minInvestment">{{ currency_symbol() }}0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Your Trading Balance:</span>
                            <span class="font-semibold text-green-400" id="userBalance">{{ currency_symbol() }}0</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="investmentAmount" class="block text-sm font-medium text-gray-300 mb-2">
                        Investment Amount
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">$</span>
                        <input type="number" 
                               id="investmentAmount" 
                               class="w-full pl-8 pr-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-blue-300 focus:ring-1 focus:ring-blue-300 transition-colors"
                               placeholder="Enter amount"
                               min="0"
                               step="0.01">
                    </div>
                    <div class="mt-2 text-sm text-gray-400" id="amountError"></div>
                </div>
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeActivationModal()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
                <button onclick="confirmActivation()" 
                        id="confirmActivationBtn"
                        class="flex-1 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white py-3 px-4 rounded-lg font-semibold transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-play mr-2"></i>Activate Trader
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                                color: 'rgba(47, 230, 222, 0.1)'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(47, 230, 222, 0.1)'
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
                                color: 'rgba(47, 230, 222, 0.1)'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(47, 230, 222, 0.1)'
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
