@extends('dashboard.layout.app')
@section('content')
@php
    // 1 = completed, 0 = pending, anything else = rejected
    $numBadge = fn ($s) => $s == 1 ? 'bg-emerald-500/15 text-emerald-400' : ($s == 0 ? 'bg-amber-500/15 text-amber-300' : 'bg-red-500/15 text-red-400');
    $numLabel = fn ($s) => $s == 1 ? 'Completed' : ($s == 0 ? 'Pending' : 'Rejected');
    $strBadge = fn ($s) => $s === 'completed' ? 'bg-emerald-500/15 text-emerald-400' : ($s === 'pending' ? 'bg-amber-500/15 text-amber-300' : 'bg-red-500/15 text-red-400');
@endphp

<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Wallet</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Transactions</h1>
            <p class="text-sm text-gray-400 mt-0.5">Deposits, withdrawals, transfers, trades and asset purchases in one place.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.deposit') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Deposit</a>
            <a href="{{ route('user.withdrawal') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Withdraw</a>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="border-b border-gray-700">
            <!-- Phones: select -->
            <div class="md:hidden p-4">
                <label for="mobileTabSelect" class="block text-sm font-medium text-gray-300 mb-1.5">Show</label>
                <select id="mobileTabSelect" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="deposits">Deposits</option>
                    <option value="withdrawals">Withdrawals</option>
                    <option value="transfers">Transfers</option>
                    <option value="trades">Trades</option>
                    <option value="holdings">Holdings</option>
                </select>
            </div>

            <!-- md+: tabs -->
            <nav class="hidden md:flex gap-6 px-6" aria-label="Tabs">
                <button type="button" id="depositsTab" class="tab-button inline-flex items-center gap-2 py-4 px-1 border-b-2 border-transparent text-gray-400 hover:text-gray-300 font-semibold text-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4v12M6 10l6 6 6-6M4 20h16"/></svg>
                    <span>Deposits</span>
                </button>
                <button type="button" id="withdrawalsTab" class="tab-button inline-flex items-center gap-2 py-4 px-1 border-b-2 border-transparent text-gray-400 hover:text-gray-300 font-semibold text-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 20V8M6 14l6-6 6 6M4 4h16"/></svg>
                    <span>Withdrawals</span>
                </button>
                <button type="button" id="transfersTab" class="tab-button inline-flex items-center gap-2 py-4 px-1 border-b-2 border-transparent text-gray-400 hover:text-gray-300 font-semibold text-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 7h13l-4-4M17 17H4l4 4"/></svg>
                    <span>Transfers</span>
                </button>
                <button type="button" id="tradesTab" class="tab-button inline-flex items-center gap-2 py-4 px-1 border-b-2 border-transparent text-gray-400 hover:text-gray-300 font-semibold text-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
                    <span>Trades</span>
                </button>
                <button type="button" id="holdingsTab" class="tab-button inline-flex items-center gap-2 py-4 px-1 border-b-2 border-transparent text-gray-400 hover:text-gray-300 font-semibold text-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4M3 17l9 4 9-4"/></svg>
                    <span>Holdings</span>
                </button>
            </nav>
        </div>

            <!-- deposits -->
            <div id="depositsContent" class="tab-content">
                @if(count($deposits ?? []))
                    <ul class="md:hidden divide-y divide-gray-700">
                        @foreach($deposits ?? [] as $deposit)
                        <li class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white truncate">{{ $deposit->payment_method ? $deposit->payment_method->crypto_display_name ?? 'N/A' : 'N/A' }}</div>
                                    <div class="mt-0.5 text-xs text-gray-400 truncate">{{ $deposit->created_at->format('M d, Y H:i') }} @if($deposit->reference) &middot; {{ $deposit->reference }}@endif</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-semibold font-mono text-white">{{ auth()->user()->formatAmount($deposit->amount) }}</div>
                                    <div class="mt-1"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $numBadge($deposit->status) }}">{{ $numLabel($deposit->status) }}</span></div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-900/50">
                                <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider"><th class="px-4 lg:px-6 py-3">Date</th><th class="px-4 lg:px-6 py-3">Amount</th><th class="px-4 lg:px-6 py-3">Payment method</th><th class="px-4 lg:px-6 py-3">Status</th><th class="px-4 lg:px-6 py-3">Reference</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($deposits ?? [] as $deposit)
                                <tr class="hover:bg-gray-700/40">
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-gray-300">{{ $deposit->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white font-mono">{{ auth()->user()->formatAmount($deposit->amount) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white">{{ $deposit->payment_method ? $deposit->payment_method->crypto_display_name ?? 'N/A' : 'N/A' }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap "><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $numBadge($deposit->status) }}">{{ $numLabel($deposit->status) }}</span></td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-gray-400">{{ $deposit->reference ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-4 py-12 text-center">
                        <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4v12M6 10l6 6 6-6M4 20h16"/></svg>
                        </div>
                        <div class="mt-3 text-white font-medium">No deposits yet</div>
                        <p class="mt-1 text-sm text-gray-400">Fund your wallet and your deposits will be listed here.</p>
                        <a href="{{ route('user.deposit') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Make a deposit</a>
                    </div>
                @endif
            </div>
            <!-- withdrawals -->
            <div id="withdrawalsContent" class="tab-content hidden">
                @if(count($withdrawals ?? []))
                    <ul class="md:hidden divide-y divide-gray-700">
                        @foreach($withdrawals ?? [] as $withdrawal)
                        <li class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white truncate">{{ ucfirst($withdrawal->payment_method ?? 'N/A') }}</div>
                                    <div class="mt-0.5 text-xs text-gray-400 truncate">{{ $withdrawal->created_at->format('M d, Y H:i') }} &middot; from {{ ucfirst(str_replace('_', ' ', $withdrawal->from_account ?? 'N/A')) }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-semibold font-mono text-white">{{ auth()->user()->formatAmount($withdrawal->amount) }}</div>
                                    <div class="mt-1"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $numBadge($withdrawal->status) }}">{{ $numLabel($withdrawal->status) }}</span></div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-900/50">
                                <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider"><th class="px-4 lg:px-6 py-3">Date</th><th class="px-4 lg:px-6 py-3">Amount</th><th class="px-4 lg:px-6 py-3">From account</th><th class="px-4 lg:px-6 py-3">Payment method</th><th class="px-4 lg:px-6 py-3">Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($withdrawals ?? [] as $withdrawal)
                                <tr class="hover:bg-gray-700/40">
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-gray-300">{{ $withdrawal->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white font-mono">{{ auth()->user()->formatAmount($withdrawal->amount) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white">{{ ucfirst(str_replace('_', ' ', $withdrawal->from_account ?? 'N/A')) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white">{{ ucfirst($withdrawal->payment_method ?? 'N/A') }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap "><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $numBadge($withdrawal->status) }}">{{ $numLabel($withdrawal->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-4 py-12 text-center">
                        <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 20V8M6 14l6-6 6 6M4 4h16"/></svg>
                        </div>
                        <div class="mt-3 text-white font-medium">No withdrawals yet</div>
                        <p class="mt-1 text-sm text-gray-400">Withdrawal requests you make will be listed here.</p>
                        <a href="{{ route('user.withdrawal') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Request a withdrawal</a>
                    </div>
                @endif
            </div>
            <!-- transfers -->
            <div id="transfersContent" class="tab-content hidden">
                @if(count($transfers ?? []))
                    <ul class="md:hidden divide-y divide-gray-700">
                        @foreach($transfers ?? [] as $transfer)
                        <li class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white truncate">{{ ucfirst(str_replace('_', ' ', $transfer->from_account ?? 'N/A')) }} &rarr; {{ ucfirst(str_replace('_', ' ', $transfer->to_account ?? 'N/A')) }}</div>
                                    <div class="mt-0.5 text-xs text-gray-400 truncate">{{ $transfer->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-semibold font-mono text-white">{{ money($transfer->amount, 2) }}</div>
                                    <div class="mt-1"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $strBadge($transfer->status) }}">{{ ucfirst($transfer->status ?? 'N/A') }}</span></div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-900/50">
                                <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider"><th class="px-4 lg:px-6 py-3">Date</th><th class="px-4 lg:px-6 py-3">Amount</th><th class="px-4 lg:px-6 py-3">From</th><th class="px-4 lg:px-6 py-3">To</th><th class="px-4 lg:px-6 py-3">Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($transfers ?? [] as $transfer)
                                <tr class="hover:bg-gray-700/40">
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-gray-300">{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($transfer->amount, 2) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white">{{ ucfirst(str_replace('_', ' ', $transfer->from_account ?? 'N/A')) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white">{{ ucfirst(str_replace('_', ' ', $transfer->to_account ?? 'N/A')) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap "><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $strBadge($transfer->status) }}">{{ ucfirst($transfer->status ?? 'N/A') }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-4 py-12 text-center">
                        <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 7h13l-4-4M17 17H4l4 4"/></svg>
                        </div>
                        <div class="mt-3 text-white font-medium">No transfers yet</div>
                        <p class="mt-1 text-sm text-gray-400">Moves between your wallet and trading balances will show here.</p>
                    </div>
                @endif
            </div>
            <!-- trades -->
            <div id="tradesContent" class="tab-content hidden">
                @if(count($trades ?? []))
                    <ul class="md:hidden divide-y divide-gray-700">
                        @foreach($trades ?? [] as $trade)
                        <li class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white truncate">{{ $trade->trade_pair->pair ?? 'N/A' }}</div>
                                    <div class="mt-0.5 text-xs text-gray-400 truncate">{{ $trade->created_at->format('M d, Y H:i') }} &middot; {{ money($trade->amount, 2) }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-semibold font-mono {{ ($trade->profit_loss ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ ($trade->profit_loss ?? 0) >= 0 ? '+' : '' }}{{ money($trade->profit_loss ?? 0, 2) }}</div>
                                    <div class="mt-1"><span class="inline-flex gap-1"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $trade->action_type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ ucfirst($trade->action_type ?? 'N/A') }}</span><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $trade->status === 'closed' ? 'bg-gray-700 text-gray-300' : 'bg-blue-500/15 text-blue-300' }}">{{ ucfirst($trade->status ?? 'N/A') }}</span></span></div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-900/50">
                                <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider"><th class="px-4 lg:px-6 py-3">Date</th><th class="px-4 lg:px-6 py-3">Pair</th><th class="px-4 lg:px-6 py-3">Type</th><th class="px-4 lg:px-6 py-3">Amount</th><th class="px-4 lg:px-6 py-3">P&amp;L</th><th class="px-4 lg:px-6 py-3">Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($trades ?? [] as $trade)
                                <tr class="hover:bg-gray-700/40">
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-gray-300">{{ $trade->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white">{{ $trade->trade_pair->pair ?? 'N/A' }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap "><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $trade->action_type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ ucfirst($trade->action_type ?? 'N/A') }}</span></td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($trade->amount, 2) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap font-mono {{ ($trade->profit_loss ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ ($trade->profit_loss ?? 0) >= 0 ? '+' : '' }}{{ money($trade->profit_loss ?? 0, 2) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap "><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $trade->status === 'closed' ? 'bg-gray-700 text-gray-300' : 'bg-blue-500/15 text-blue-300' }}">{{ ucfirst($trade->status ?? 'N/A') }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-4 py-12 text-center">
                        <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
                        </div>
                        <div class="mt-3 text-white font-medium">No trades yet</div>
                        <p class="mt-1 text-sm text-gray-400">Trades you place in the Trade Room will be listed here.</p>
                        <a href="{{ route('user.liveTrading.index') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Open the Trade Room</a>
                    </div>
                @endif
            </div>
            <!-- holdings -->
            <div id="holdingsContent" class="tab-content hidden">
                @if(count($holdingTransactions ?? []))
                    <ul class="md:hidden divide-y divide-gray-700">
                        @foreach($holdingTransactions ?? [] as $transaction)
                        <li class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white truncate">{{ $transaction->asset->symbol ?? 'N/A' }} &middot; {{ number_format($transaction->quantity, 8) }}</div>
                                    <div class="mt-0.5 text-xs text-gray-400 truncate">{{ $transaction->created_at->format('M d, Y H:i') }} &middot; at {{ money($transaction->price_per_unit, 8) }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-sm font-semibold font-mono text-white">{{ money($transaction->total_amount, 2) }}</div>
                                    <div class="mt-1"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $transaction->type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ ucfirst($transaction->type ?? 'N/A') }}</span></div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-900/50">
                                <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider"><th class="px-4 lg:px-6 py-3">Date</th><th class="px-4 lg:px-6 py-3">Asset</th><th class="px-4 lg:px-6 py-3">Type</th><th class="px-4 lg:px-6 py-3">Quantity</th><th class="px-4 lg:px-6 py-3">Price</th><th class="px-4 lg:px-6 py-3">Total</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($holdingTransactions ?? [] as $transaction)
                                <tr class="hover:bg-gray-700/40">
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-gray-300">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white">{{ $transaction->asset->symbol ?? 'N/A' }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap "><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $transaction->type === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ ucfirst($transaction->type ?? 'N/A') }}</span></td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white font-mono">{{ number_format($transaction->quantity, 8) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($transaction->price_per_unit, 8) }}</td>
                                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($transaction->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-4 py-12 text-center">
                        <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 12l9 4 9-4M3 17l9 4 9-4"/></svg>
                        </div>
                        <div class="mt-3 text-white font-medium">No asset purchases yet</div>
                        <p class="mt-1 text-sm text-gray-400">Buys and sells of crypto and stocks will show here.</p>
                        <a href="{{ route('user.holding.buy-assets') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Buy an asset</a>
                    </div>
                @endif
            </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = ['deposits', 'withdrawals', 'transfers', 'trades', 'holdings'];
    
    // Get the tab parameter from URL
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'deposits';
    
    // Function to activate a tab
    function activateTab(tabName) {
        // Update mobile dropdown
        const mobileSelect = document.getElementById('mobileTabSelect');
        if (mobileSelect) {
            mobileSelect.value = tabName;
        }
        
        // Update desktop tabs
        tabs.forEach(name => {
            const btn = document.getElementById(name + 'Tab');
            const content = document.getElementById(name + 'Content');
            if (btn) btn.classList.remove('active', 'border-blue-500', 'text-blue-400');
            if (btn) btn.classList.add('border-transparent', 'text-gray-400');
            if (content) content.classList.remove('active');
            if (content) content.classList.add('hidden');
        });
        
        const activeBtn = document.getElementById(tabName + 'Tab');
        const activeContent = document.getElementById(tabName + 'Content');
        if (activeBtn) {
            activeBtn.classList.add('active', 'border-blue-500', 'text-blue-400');
            activeBtn.classList.remove('border-transparent', 'text-gray-400');
        }
        if (activeContent) {
            activeContent.classList.add('active');
            activeContent.classList.remove('hidden');
        }
    }
    
    // Activate the tab from URL parameter
    activateTab(activeTab);
    
    // Add click event listeners for desktop tabs
    tabs.forEach(tabName => {
        const tabButton = document.getElementById(tabName + 'Tab');
        if (tabButton) {
            tabButton.addEventListener('click', function() {
                activateTab(tabName);
            });
        }
    });
    
    // Add change event listener for mobile dropdown
    const mobileSelect = document.getElementById('mobileTabSelect');
    if (mobileSelect) {
        mobileSelect.addEventListener('change', function() {
            activateTab(this.value);
        });
    }
});
</script>

@endsection
