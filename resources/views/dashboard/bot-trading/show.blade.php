@extends('dashboard.layout.app')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Bot trading</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1 break-words">{{ $bot->name }}</h1>
            <p class="text-sm text-gray-400 mt-0.5"><span class="font-mono">{{ $bot->base_asset }}/{{ $bot->quote_asset }}</span> · {{ ucfirst(str_replace('_', ' ', $bot->strategy)) }} strategy</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.botTrading.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Back</span>
            </a>
            <a href="{{ route('user.botTrading.edit', $bot) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                Edit bot
            </a>
        </div>
    </div>

    <!-- Bot Status and Controls -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Bot Status Card -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <h3 class="text-base font-semibold text-white">Bot Status</h3>
                <div class="flex items-center space-x-2">
                    @if($bot->started_at && $bot->status === 'active')
                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">
                            @php
                                $duration = $bot->started_at->diff(now());
                                if ($duration->days > 0) {
                                    echo $duration->days . 'd ' . $duration->h . 'h';
                                } elseif ($duration->h > 0) {
                                    echo $duration->h . 'h ' . $duration->i . 'm';
                                } else {
                                    echo $duration->i . 'm';
                                }
                            @endphp
                        </span>
                    @endif
                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold 
                        @if($bot->status === 'active') bg-emerald-500/15 text-emerald-400
                        @elseif($bot->status === 'paused') bg-amber-500/15 text-amber-300
                        @else bg-red-500/15 text-red-400 @endif">
                        {{ ucfirst($bot->status) }}
                    </span>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Created:</span>
                    <span class="text-white">{{ $bot->created_at->format('M d, Y H:i') }}</span>
                </div>
                @if($bot->started_at)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Started:</span>
                    <span class="text-white">{{ $bot->started_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Duration:</span>
                    <span class="text-white">
                        @php
                            $duration = $bot->started_at->diff(now());
                            if ($duration->days > 0) {
                                echo $duration->days . 'd ' . $duration->h . 'h';
                            } elseif ($duration->h > 0) {
                                echo $duration->h . 'h ' . $duration->i . 'm';
                            } else {
                                echo $duration->i . 'm';
                            }
                        @endphp
                    </span>
                </div>
                @endif
                @if($bot->stopped_at)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Stopped:</span>
                    <span class="text-white">{{ $bot->stopped_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
                @if($bot->last_trade_at)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Last Trade:</span>
                    <span class="text-white">{{ $bot->last_trade_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
            </div>

            <!-- Bot Controls -->
            <div class="mt-6 space-y-2">
                @if($bot->status === 'active')
                    <button onclick="pauseBot({{ $bot->id }})" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-amber-500/40 text-amber-300 hover:bg-amber-500/10 text-sm font-semibold">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pause Bot
                    </button>
                    <button onclick="stopBot({{ $bot->id }})" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/10 text-sm font-semibold">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Stop Bot
                    </button>
                @elseif($bot->status === 'paused')
                    <button onclick="resumeBot({{ $bot->id }})" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Resume Bot
                    </button>
                    <button onclick="stopBot({{ $bot->id }})" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/10 text-sm font-semibold">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Stop Bot
                    </button>
                @endif
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h3 class="text-base font-semibold text-white mb-4">Performance</h3>
            <div class="space-y-4">
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Total Profit:</span>
                    <span class="font-semibold {{ $bot->total_profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ money($bot->total_profit, 2) }}
                    </span>
                </div>
                @if($bot->total_invested > 0)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Current Yield:</span>
                    <span class="font-semibold {{ ($bot->total_profit / $bot->total_invested * 100) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ number_format($bot->total_profit / $bot->total_invested * 100, 2) }}%
                    </span>
                </div>
                @endif
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Total Trades:</span>
                    <span class="text-white">{{ $bot->total_trades }}</span>
                </div>
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Success Rate:</span>
                    <span class="text-white">{{ $bot->success_rate ? number_format($bot->success_rate, 1) : 0 }}%</span>
                </div>
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Current Investment:</span>
                    <span class="text-white">{{ money($bot->total_invested, 2) }}</span>
                </div>
                @if($bot->target_yield_percentage)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Target Yield:</span>
                    <span class="text-blue-400">{{ $bot->target_yield_percentage }}%</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Bot Configuration -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h3 class="text-base font-semibold text-white mb-4">Configuration</h3>
            <div class="space-y-3">
                <!-- Investment Settings -->
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Max Investment:</span>
                    <span class="text-white">{{ money($bot->max_investment, 2) }}</span>
                </div>
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Min Trade:</span>
                    <span class="text-white">{{ money($bot->min_trade_amount, 2) }}</span>
                </div>
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Max Trade:</span>
                    <span class="text-white">{{ money($bot->max_trade_amount, 2) }}</span>
                </div>
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Max Open Trades:</span>
                    <span class="text-white">{{ $bot->max_open_trades }}</span>
                </div>
                
                <!-- Phase 1: Risk Management -->
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Leverage:</span>
                    <span class="text-white">{{ $bot->leverage }}x</span>
                </div>
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Trade Duration:</span>
                    <span class="text-white">
                        @switch($bot->trade_duration)
                            @case('1h')
                                1 Hour
                                @break
                            @case('4h')
                                4 Hours
                                @break
                            @case('24h')
                                24 Hours
                                @break
                            @case('1w')
                                1 Week
                                @break
                            @case('1m')
                                1 Month
                                @break
                            @default
                                {{ $bot->trade_duration }}
                        @endswitch
                    </span>
                </div>
                @if($bot->target_yield_percentage)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Target Yield:</span>
                    <span class="text-emerald-400">{{ $bot->target_yield_percentage }}%</span>
                </div>
                @endif
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Auto Close:</span>
                    <span class="text-white">{{ $bot->auto_close ? 'Yes' : 'No' }}</span>
                </div>
                
                <!-- Risk Management -->
                @if($bot->stop_loss_percentage)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Stop Loss:</span>
                    <span class="text-red-400">{{ $bot->stop_loss_percentage }}%</span>
                </div>
                @endif
                @if($bot->take_profit_percentage)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Take Profit:</span>
                    <span class="text-emerald-400">{{ $bot->take_profit_percentage }}%</span>
                </div>
                @endif
                @if($bot->daily_loss_limit)
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Daily Loss Limit:</span>
                    <span class="text-red-400">{{ money($bot->daily_loss_limit, 2) }}</span>
                </div>
                @endif
                
                <!-- Trading Settings -->
                <div class="flex justify-between gap-3 text-sm">
                    <span class="text-gray-400">Trade 24/7:</span>
                    <span class="text-white">{{ $bot->trading_24_7 ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Market Info -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h3 class="text-base font-semibold text-white mb-4">Current Market</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="flex justify-between items-center gap-3 text-sm">
                <span class="text-gray-400">Current Price:</span>
                <span class="text-white font-medium">
                    @php
                        $asset = \App\Models\Asset::where('symbol', $bot->base_asset)->first();
                        $currentPrice = $asset ? $asset->current_price : 0;
                    @endphp
                    {{ money_from($currentPrice, 'USD', 2) }}
                </span>
            </div>
            <div class="flex justify-between items-center gap-3 text-sm">
                <span class="text-gray-400">24h Change:</span>
                <span class="font-medium {{ $asset && $asset->price_change_24h >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                    @if($asset)
                        {{ $asset->price_change_24h >= 0 ? '+' : '' }}{{ number_format($asset->price_change_24h, 2) }}%
                    @else
                        -
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Recent Trades -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-white">Recent Trades</h3>
                <button onclick="refreshTrades()" class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full text-sm">
                <thead class="bg-gray-900/50 border-b border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Trade ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Profit/Loss</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($recentTrades as $trade)
                    <tr class="hover:bg-gray-700/40">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $trade->trade_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $trade->type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">
                                {{ strtoupper($trade->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                            {{ number_format($trade->base_amount, 6) }} {{ $trade->base_asset }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                            {{ money($trade->price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $trade->profit_loss >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            @if($trade->profit_loss !== null)
                                {{ money($trade->profit_loss, 2) }}
                                @if($trade->profit_loss_percentage)
                                    ({{ number_format($trade->profit_loss_percentage, 2) }}%)
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold 
                                @if($trade->status === 'executed') bg-emerald-500/15 text-emerald-400
                                @elseif($trade->status === 'pending') bg-amber-500/15 text-amber-300
                                @elseif($trade->status === 'cancelled') bg-red-500/15 text-red-400
                                @else bg-gray-700 text-gray-300 @endif">
                                {{ ucfirst($trade->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            {{ $trade->created_at->format('M d, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            No trades found for this bot yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Success/Error Modals -->
<div id="successModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4">
        <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-emerald-500/15 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2" id="successTitle">Success!</h3>
                <p class="text-gray-400 mb-6" id="successMessage">Operation completed successfully.</p>
                <button id="closeSuccessModal" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div id="errorModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4">
        <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-red-500/15 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-white mb-2" id="errorTitle">Error!</h3>
                <p class="text-gray-400 mb-6" id="errorMessage">An error occurred. Please try again.</p>
                <button id="closeErrorModal" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successModal = document.getElementById('successModal');
    const errorModal = document.getElementById('errorModal');
    const closeSuccessModal = document.getElementById('closeSuccessModal');
    const closeErrorModal = document.getElementById('closeErrorModal');

    // Close modals when clicking backdrop
    [successModal, errorModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });

    // Close success/error modals
    [closeSuccessModal, closeErrorModal].forEach(btn => {
        btn.addEventListener('click', () => {
            successModal.classList.add('hidden');
            errorModal.classList.add('hidden');
        });
    });

    // Bot control functions
    window.pauseBot = async (botId) => {
        if (!(await uiConfirm('Are you sure you want to pause this bot? It can be resumed later.', { tone: 'info' }))) return;

        try {
            const response = await fetch(`/user/bot-trading/${botId}/pause`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                showSuccessModal('Bot Paused!', result.message);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showErrorModal('Pause Failed', result.message);
            }
        } catch (error) {
            showErrorModal('Error', 'Failed to pause bot');
        }
    };

    window.resumeBot = async (botId) => {
        if (!(await uiConfirm('Are you sure you want to resume this bot?', { tone: 'info' }))) return;

        try {
            const response = await fetch(`/user/bot-trading/${botId}/resume`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                showSuccessModal('Bot Resumed!', result.message);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showErrorModal('Resume Failed', result.message);
            }
        } catch (error) {
            showErrorModal('Error', 'Failed to resume bot');
        }
    };

    window.stopBot = async (botId) => {
        if (!(await uiConfirm('Are you sure you want to PERMANENTLY STOP this bot? This action cannot be undone and all profits will be transferred to your trading balance.'))) return;

        try {
            const response = await fetch(`/user/bot-trading/${botId}/stop`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                const message = result.message + (result.profit_transferred ? ' Profits have been transferred to your trading balance.' : '');
                showSuccessModal('Bot Stopped!', message);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showErrorModal('Stop Failed', result.message);
            }
        } catch (error) {
            showErrorModal('Error', 'Failed to stop bot');
        }
    };

    function showSuccessModal(title, message) {
        document.getElementById('successTitle').textContent = title;
        document.getElementById('successMessage').textContent = message;
        successModal.classList.remove('hidden');
    }

    function showErrorModal(title, message) {
        document.getElementById('errorTitle').textContent = title;
        document.getElementById('errorMessage').textContent = message;
        errorModal.classList.remove('hidden');
    }

    // Refresh trades function
    window.refreshTrades = function() {
        // Show loading state
        const refreshBtn = document.querySelector('button[onclick="refreshTrades()"]');
        const originalContent = refreshBtn.innerHTML;
        refreshBtn.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refreshing...
        `;
        refreshBtn.disabled = true;

        // Reload the page to get fresh data
        setTimeout(() => {
            window.location.reload();
        }, 500);
    };
});
</script>
@endpush
