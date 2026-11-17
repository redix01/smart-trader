@extends('dashboard.layout.app')

@section('content')
@php
    $u = auth()->user();
    $badge = 'px-2 py-0.5 rounded-md text-xs font-semibold ';
    // Withdrawal: 1 = successful, 2 = declined, else pending. Transfer: string status.
    $wdStatus = fn ($s) => $s == 1 ? ['Successful', 'bg-emerald-500/15 text-emerald-400'] : ($s == 2 ? ['Declined', 'bg-red-500/15 text-red-400'] : ['Pending', 'bg-amber-500/15 text-amber-300']);
    $trStatus = fn ($s) => match ($s) {
        'completed' => ['Completed', 'bg-emerald-500/15 text-emerald-400'],
        'pending' => ['Pending', 'bg-amber-500/15 text-amber-300'],
        'failed' => ['Failed', 'bg-red-500/15 text-red-400'],
        default => ['Unknown', 'bg-gray-700 text-gray-300'],
    };
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Wallet</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Withdraw &amp; transfer</h1>
            <p class="text-sm text-gray-400 mt-0.5">Cash out to crypto, bank or PayPal, or move funds between your balances.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="flex items-start gap-2 p-4 rounded-xl border border-red-500/40 bg-red-500/10 text-sm text-red-300">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="flex items-start gap-2 p-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 text-sm text-emerald-300">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Balances -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Main balance</div>
            <div class="mt-1 text-lg sm:text-xl font-semibold text-white font-mono break-all">{{ $u->formatAmount($u->balance) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Trading balance</div>
            <div class="mt-1 text-lg sm:text-xl font-semibold text-white font-mono break-all">{{ $u->formatAmount($u->trading_balance ?? 0) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Holding balance</div>
            <div class="mt-1 text-lg sm:text-xl font-semibold text-white font-mono break-all">{{ $u->formatAmount($u->holding_balance ?? 0) }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Not withdrawable</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Mining balance</div>
            <div class="mt-1 text-lg sm:text-xl font-semibold text-white font-mono break-all">{{ $u->formatAmount($u->mining_balance ?? 0) }}</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="border-b border-gray-700">
            <nav class="-mb-px grid grid-cols-3 sm:flex sm:gap-8 px-2 sm:px-6" aria-label="Tabs">
                <button type="button" id="withdrawTab" class="tab-button inline-flex items-center justify-center gap-2 border-b-2 border-blue-500 text-blue-400 py-3 sm:py-4 px-1 text-sm font-semibold whitespace-nowrap min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 4v12M6 10l6 6 6-6M4 20h16"/></svg>
                    <span>Withdraw</span>
                </button>
                <button type="button" id="transferTab" class="tab-button inline-flex items-center justify-center gap-2 border-b-2 border-transparent text-gray-400 hover:text-gray-300 py-3 sm:py-4 px-1 text-sm font-semibold whitespace-nowrap min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 7h13l-4-4M17 17H4l4 4"/></svg>
                    <span>Transfer</span>
                </button>
                <button type="button" id="historyTab" class="tab-button inline-flex items-center justify-center gap-2 border-b-2 border-transparent text-gray-400 hover:text-gray-300 py-3 sm:py-4 px-1 text-sm font-semibold whitespace-nowrap min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    <span>History</span>
                </button>
            </nav>
        </div>

        <div class="p-4 sm:p-6">
            <!-- Withdraw Tab -->
            <div id="withdrawContent" class="tab-content">
                <div class="max-w-lg mx-auto">
                    <h3 class="text-base font-semibold text-white">Withdraw funds</h3>
                    <p class="text-sm text-gray-400 mt-0.5 mb-4">Requests are reviewed by our team before they are paid out.</p>
                    <form action="{{ route('user.withdrawalStore') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="withdrawFromAccount" class="block text-sm font-medium text-gray-300 mb-1.5">From account</label>
                            <select id="withdrawFromAccount" name="from_account" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select account</option>
                                <option value="balance" data-balance="{{ auth()->user()->balance }}">Main Balance ({{ money(auth()->user()->balance, 2) }})</option>
                                <option value="trading_balance" data-balance="{{ auth()->user()->trading_balance ?? 0 }}">Trading Balance ({{ money(auth()->user()->trading_balance ?? 0, 2) }})</option>
                                <option value="mining_balance" data-balance="{{ auth()->user()->mining_balance ?? 0 }}">Mining Balance ({{ money(auth()->user()->mining_balance ?? 0, 2) }})</option>
                            </select>
                        </div>

                        <div>
                            <label for="withdrawalMethod" class="block text-sm font-medium text-gray-300 mb-1.5">Payment method</label>
                            <select id="withdrawalMethod" name="payment_method" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select payment method</option>
                                <option value="crypto">Cryptocurrency</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>

                        <!-- Crypto Fields -->
                        <div id="cryptoFields" class="space-y-4 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Cryptocurrency</label>
                                <select name="wallet" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="BTC">Bitcoin (BTC)</option>
                                    <option value="ETH">Ethereum (ETH)</option>
                                    <option value="SOL">Solana (SOL)</option>
                                    <option value="BNB">Binance Coin (BNB)</option>
                                    <option value="USDT">Tether (USDT)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Wallet address</label>
                                <input type="text" name="address" autocomplete="off" spellcheck="false" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm" placeholder="Paste your wallet address">
                                <p class="text-xs text-gray-500 mt-1">Double-check the address and network. Crypto sent to a wrong address cannot be recovered.</p>
                            </div>
                        </div>

                        <!-- Bank Fields -->
                        <div id="bankFields" class="space-y-4 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Bank name</label>
                                <input type="text" name="bank_name" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Chase Bank">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Account name</label>
                                    <input type="text" name="acct_name" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Account holder name">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Account number</label>
                                    <input type="text" name="acct_number" inputmode="numeric" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Account number">
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Fields -->
                        <div id="paypalFields" class="space-y-4 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">PayPal email</label>
                                <input type="email" name="paypal" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="your@email.com">
                            </div>
                        </div>

                        <div>
                            <label for="withdrawAmount" class="block text-sm font-medium text-gray-300 mb-1.5">Amount</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm pointer-events-none">{{ currency_symbol() }}</span>
                                <input type="number" id="withdrawAmount" name="amount" step="0.01" min="0.01" inputmode="decimal"
                                       class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 pl-8"
                                       placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Available: {{ currency_symbol() }}<span id="withdrawAvailableAmount">0.00</span></p>
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            Request withdrawal
                        </button>
                    </form>
                </div>
            </div>

            <!-- Transfer Funds Tab -->
            <div id="transferContent" class="tab-content hidden">
                <div class="max-w-lg mx-auto">
                    <h3 class="text-base font-semibold text-white">Transfer between accounts</h3>
                    <p class="text-sm text-gray-400 mt-0.5 mb-4">Move money between your own balances.</p>
                    <div class="rounded-xl border border-blue-500/30 bg-blue-500/10 p-3 mb-4">
                        <p class="text-sm text-blue-200">
                            <strong>Note:</strong> Holding balance represents your portfolio value and cannot be transferred or withdrawn.
                            To access these funds, you must sell your assets first.
                        </p>
                    </div>
                    <form id="transferForm" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="fromAccount" class="block text-sm font-medium text-gray-300 mb-1.5">From</label>
                                <select id="fromAccount" name="from_account" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select account</option>
                                <option value="balance" data-balance="{{ auth()->user()->balance }}">Main Balance ({{ money(auth()->user()->balance, 2) }})</option>
                                <option value="trading_balance" data-balance="{{ auth()->user()->trading_balance ?? 0 }}">Trading Balance ({{ money(auth()->user()->trading_balance ?? 0, 2) }})</option>
                                <option value="mining_balance" data-balance="{{ auth()->user()->mining_balance ?? 0 }}">Mining Balance ({{ money(auth()->user()->mining_balance ?? 0, 2) }})</option>
                                </select>
                            </div>

                            <div>
                                <label for="toAccount" class="block text-sm font-medium text-gray-300 mb-1.5">To</label>
                                <select id="toAccount" name="to_account" class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select account</option>
                                <option value="balance" data-balance="{{ auth()->user()->balance }}">Main Balance ({{ money(auth()->user()->balance, 2) }})</option>
                                <option value="trading_balance" data-balance="{{ auth()->user()->trading_balance ?? 0 }}">Trading Balance ({{ money(auth()->user()->trading_balance ?? 0, 2) }})</option>
                                <option value="mining_balance" data-balance="{{ auth()->user()->mining_balance ?? 0 }}">Mining Balance ({{ money(auth()->user()->mining_balance ?? 0, 2) }})</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="transferAmount" class="block text-sm font-medium text-gray-300 mb-1.5">Amount</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm pointer-events-none">{{ currency_symbol() }}</span>
                                <input type="number" id="transferAmount" name="amount" step="0.01" min="0.01" inputmode="decimal"
                                       class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 pl-8"
                                       placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Available: {{ currency_symbol() }}<span id="availableAmount">0.00</span></p>
                        </div>

                        <button type="submit" id="transferSubmitBtn" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            <span id="transferBtnText">Transfer funds</span>
                            <span id="transferBtnSpinner" class="hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- History Tab -->
            <div id="historyContent" class="tab-content hidden">
                @php
                    $allTransactions = collect();
                    foreach ($withdrawals as $withdrawal) {
                        $withdrawal->transaction_type = 'withdrawal';
                        $allTransactions->push($withdrawal);
                    }
                    foreach ($transfers as $transfer) {
                        $transfer->transaction_type = 'transfer';
                        $allTransactions->push($transfer);
                    }
                    $allTransactions = $allTransactions->sortByDesc('created_at')->values();

                    $detailFor = function ($t) {
                        if ($t->transaction_type !== 'withdrawal') {
                            return str_replace('_', ' ', ucfirst($t->from_account)).' → '.str_replace('_', ' ', ucfirst($t->to_account));
                        }
                        $extra = match ($t->payment_method) {
                            'crypto' => $t->wallet ?? '—',
                            'bank' => (json_decode($t->bank, true)['bank_name'] ?? ''),
                            'paypal' => $t->paypal ?? '—',
                            default => $t->address ?? '—',
                        };
                        return ucfirst($t->payment_method).' - '.$extra;
                    };
                    $statusFor = fn ($t) => $t->transaction_type === 'withdrawal' ? $wdStatus($t->status) : $trStatus($t->status);
                @endphp

                @if($allTransactions->count())
                    <ul class="md:hidden -mx-4 -my-4 divide-y divide-gray-700">
                        @foreach($allTransactions as $transaction)
                            @php [$sl, $sc] = $statusFor($transaction); @endphp
                            <li class="p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="{{ $badge }} {{ $transaction->transaction_type === 'withdrawal' ? 'bg-red-500/15 text-red-400' : 'bg-blue-500/15 text-blue-300' }}">{{ $transaction->transaction_type === 'withdrawal' ? 'Withdrawal' : 'Transfer' }}</span>
                                            <span class="text-xs text-gray-400">{{ $transaction->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-300 break-all">{{ $detailFor($transaction) }}</div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="text-sm font-semibold text-white font-mono">{{ $u->formatAmount($transaction->amount) }}</div>
                                        <span class="mt-1 inline-block {{ $badge }} {{ $sc }}">{{ $sl }}</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="hidden md:block overflow-x-auto -mx-6 -my-6">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-900/50">
                                <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    <th class="px-6 py-3">#</th>
                                    <th class="px-6 py-3">Type</th>
                                    <th class="px-6 py-3">Amount</th>
                                    <th class="px-6 py-3">Method / details</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($allTransactions as $index => $transaction)
                                    @php [$sl, $sc] = $statusFor($transaction); @endphp
                                    <tr class="hover:bg-gray-700/40">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap"><span class="{{ $badge }} {{ $transaction->transaction_type === 'withdrawal' ? 'bg-red-500/15 text-red-400' : 'bg-blue-500/15 text-blue-300' }}">{{ $transaction->transaction_type === 'withdrawal' ? 'Withdrawal' : 'Transfer' }}</span></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ $u->formatAmount($transaction->amount) }}</td>
                                        <td class="px-6 py-4 text-gray-300 max-w-xs truncate">{{ $detailFor($transaction) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap"><span class="{{ $badge }} {{ $sc }}">{{ $sl }}</span></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $transaction->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-8 text-center">
                        <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </div>
                        <div class="mt-3 text-white font-medium">No withdrawals or transfers yet</div>
                        <p class="mt-1 text-sm text-gray-400">Requests you make from the Withdraw or Transfer tab will be listed here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Custom Modal -->
<div id="customModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-end sm:items-center justify-center sm:p-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 p-4 sm:p-6 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-3">
            <h3 id="modalTitle" class="text-lg font-semibold text-white">Notice</h3>
            <button type="button" id="closeModal" class="w-10 h-10 -mr-2 grid place-items-center rounded-lg text-gray-400 hover:text-white hover:bg-gray-700" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-6">
            <p id="modalMessage" class="text-sm text-gray-300"></p>
        </div>
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3">
            <button type="button" id="modalCancelBtn" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                Cancel
            </button>
            <button type="button" id="modalConfirmBtn" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                OK
            </button>
        </div>
    </div>
</div>

<script>
// Tab functionality
document.getElementById('withdrawTab').addEventListener('click', () => switchTab('withdraw'));
document.getElementById('transferTab').addEventListener('click', () => switchTab('transfer'));
document.getElementById('historyTab').addEventListener('click', () => switchTab('history'));

function switchTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-400');
        btn.classList.add('border-transparent', 'text-gray-400');
    });
    
    if (tab === 'withdraw') {
        document.getElementById('withdrawTab').classList.add('border-blue-500', 'text-blue-400');
        document.getElementById('withdrawTab').classList.remove('border-transparent', 'text-gray-400');
    } else if (tab === 'transfer') {
        document.getElementById('transferTab').classList.add('border-blue-500', 'text-blue-400');
        document.getElementById('transferTab').classList.remove('border-transparent', 'text-gray-400');
    } else if (tab === 'history') {
        document.getElementById('historyTab').classList.add('border-blue-500', 'text-blue-400');
        document.getElementById('historyTab').classList.remove('border-transparent', 'text-gray-400');
    }
    
    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    if (tab === 'withdraw') {
        document.getElementById('withdrawContent').classList.remove('hidden');
    } else if (tab === 'transfer') {
        document.getElementById('transferContent').classList.remove('hidden');
    } else if (tab === 'history') {
        document.getElementById('historyContent').classList.remove('hidden');
    }
}

// Transfer form functionality
document.getElementById('fromAccount').addEventListener('change', updateAvailableAmount);
document.getElementById('withdrawFromAccount').addEventListener('change', updateWithdrawAvailableAmount);

function updateAvailableAmount() {
    const select = document.getElementById('fromAccount');
    const option = select.options[select.selectedIndex];
    const balance = option.getAttribute('data-balance') || 0;
    document.getElementById('availableAmount').textContent = parseFloat(balance).toFixed(2);
}

function updateWithdrawAvailableAmount() {
    const select = document.getElementById('withdrawFromAccount');
    const option = select.options[select.selectedIndex];
    const balance = option.getAttribute('data-balance') || 0;
    document.getElementById('withdrawAvailableAmount').textContent = parseFloat(balance).toFixed(2);
}

// Withdrawal method toggle
document.getElementById('withdrawalMethod').addEventListener('change', function() {
    const method = this.value;
    
    // Hide all fields
    document.getElementById('cryptoFields').classList.add('hidden');
    document.getElementById('bankFields').classList.add('hidden');
    document.getElementById('paypalFields').classList.add('hidden');
    
    // Show relevant fields
    if (method === 'crypto') {
        document.getElementById('cryptoFields').classList.remove('hidden');
    } else if (method === 'bank') {
        document.getElementById('bankFields').classList.remove('hidden');
    } else if (method === 'paypal') {
        document.getElementById('paypalFields').classList.remove('hidden');
    }
});

// Transfer form submission
document.getElementById('transferForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Set button to processing state
    setButtonProcessing(true, 'transfer');
    
    const formData = new FormData(this);
    
    fetch('/user/transfer-funds', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        setButtonProcessing(false, 'transfer');
        
        if (data.success) {
            showModal('Success', 'Transfer completed successfully!');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showModal('Error', 'Error: ' + data.message);
        }
    })
    .catch(error => {
        // Reset button state
        setButtonProcessing(false, 'transfer');
        
        console.error('Error:', error);
        showModal('Error', 'An error occurred during transfer');
    });
});

// Modal functionality
function showModal(title, message, showCancel = false) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('modalCancelBtn').style.display = showCancel ? 'block' : 'none';
    document.getElementById('customModal').classList.remove('hidden');
}

function hideModal() {
    document.getElementById('customModal').classList.add('hidden');
}

// Modal event listeners
document.getElementById('closeModal').addEventListener('click', hideModal);
document.getElementById('modalConfirmBtn').addEventListener('click', hideModal);
document.getElementById('modalCancelBtn').addEventListener('click', hideModal);

// Close modal when clicking outside
document.getElementById('customModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideModal();
    }
});

// Button processing state
function setButtonProcessing(isProcessing, buttonType = 'withdraw') {
    const button = document.getElementById(buttonType + 'SubmitBtn');
    const buttonText = document.getElementById(buttonType + 'BtnText');
    const buttonSpinner = document.getElementById(buttonType + 'BtnSpinner');
    
    if (isProcessing) {
        button.disabled = true;
        button.classList.add('opacity-50', 'cursor-not-allowed');
        if (buttonType === 'withdraw') {
            button.classList.remove('hover:bg-red-700');
        } else {
            button.classList.remove('hover:bg-blue-700');
        }
        buttonText.classList.add('hidden');
        buttonSpinner.classList.remove('hidden');
    } else {
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        if (buttonType === 'withdraw') {
            button.classList.add('hover:bg-red-700');
        } else {
            button.classList.add('hover:bg-blue-700');
        }
        buttonText.classList.remove('hidden');
        buttonSpinner.classList.add('hidden');
    }
}

// Check if user is still authenticated
function checkAuthentication() {
    return fetch('{{ route("user.debug.simple") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (response.redirected || response.url.includes('login')) {
            throw new Error('Session expired');
        }
        return response.ok;
    });
}

// Withdrawal form submission
// The page's form has no #withdrawForm (it posts normally); guard so the rest of this script still runs.
document.getElementById('withdrawForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Set button to processing state
    setButtonProcessing(true, 'withdraw');
    
    // Check authentication first
    checkAuthentication()
    .then(isAuthenticated => {
        if (!isAuthenticated) {
            throw new Error('Session expired. Please refresh the page and log in again.');
        }
        return processWithdrawal();
    })
    .catch(error => {
        setButtonProcessing(false, 'withdraw');
        if (error.message.includes('Session expired')) {
            showModal('Session Expired', 'Your session has expired. Please refresh the page and log in again.');
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showModal('Error', error.message);
        }
    });
});

function processWithdrawal() {
    const form = document.getElementById('withdrawForm');
    const formData = new FormData(form);
    
    // Use Laravel route helper to get the correct URL
    const withdrawalUrl = '{{ route("user.withdrawalStore") }}';
    console.log('Making withdrawal request to:', withdrawalUrl);
    console.log('Form data entries:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    
    // Debug CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    console.log('CSRF Token:', csrfToken);
    console.log('CSRF Token length:', csrfToken.length);
    
    return fetch(withdrawalUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            // Don't set Content-Type for FormData - let browser set it with boundary
        },
        body: formData
    })
    .then(async response => {
        // Reset button state
        setButtonProcessing(false, 'withdraw');
        
        // Debug: Log response details
        console.log('Response received:');
        console.log('Status:', response.status, response.statusText);
        console.log('Headers:', Object.fromEntries(response.headers.entries()));
        console.log('URL:', response.url);
        console.log('Redirected:', response.redirected);
        
        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // If not JSON, likely an error page or redirect
            const text = await response.text();
            console.error('Non-JSON response received:', text.substring(0, 200));
            throw new Error('Server returned an invalid response. Please try again or contact support.');
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showModal('Success', 'Withdrawal request submitted successfully!');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showModal('Error', 'Error: ' + (data.message || 'Unknown error occurred'));
        }
    })
    .catch(error => {
        // Reset button state if not already reset
        setButtonProcessing(false, 'withdraw');
        
        console.error('Withdrawal error:', error);
        
        // Show user-friendly error message
        let errorMessage = 'An error occurred during withdrawal request';
        if (error.message.includes('HTTP error! status: 403')) {
            errorMessage = 'Access denied. Your session may have expired or there was a security token issue. Please refresh the page and try again.';
        } else if (error.message.includes('HTTP error! status: 422')) {
            errorMessage = 'Please check your form data and try again';
        } else if (error.message.includes('HTTP error! status: 401')) {
            errorMessage = 'Your session has expired. Please refresh the page and try again';
        } else if (error.message.includes('HTTP error! status: 500')) {
            errorMessage = 'Server error occurred. Please try again later';
        } else if (error.message.includes('invalid response')) {
            errorMessage = error.message;
        }
        
                        showModal('Error', errorMessage);
    });
}

// Simple form validation
document.addEventListener('DOMContentLoaded', function() {
    const withdrawForm = document.querySelector('form[action*="withdrawalStore"]');
    if (withdrawForm) {
        withdrawForm.addEventListener('submit', function(e) {
            const val = (name) => (this.querySelector('[name="' + name + '"]') || {}).value || '';
            const fromAccount = val('from_account');
            // payment_method is a <select> here (radio buttons on older markup): support both.
            const pmField = this.querySelector('[name="payment_method"]');
            const paymentMethod = pmField && pmField.type === 'radio'
                ? (this.querySelector('[name="payment_method"]:checked') || {}).value
                : val('payment_method');
            const amount = val('amount');
            
            if (!fromAccount) {
                uiAlert('Please select an account to withdraw from', { title: 'Check your details' });
                e.preventDefault();
                return;
            }
            
            if (!paymentMethod) {
                uiAlert('Please select a payment method', { title: 'Check your details' });
                e.preventDefault();
                return;
            }
            
            if (!amount || amount <= 0) {
                uiAlert('Please enter a valid withdrawal amount', { title: 'Check your details' });
                e.preventDefault();
                return;
            }
            
            // Additional validation based on payment method
            if (paymentMethod === 'crypto') {
                const wallet = val('wallet');
                const address = val('address');
                if (!wallet || !address) {
                    uiAlert('Please fill in wallet type and address for crypto withdrawal', { title: 'Check your details' });
                    e.preventDefault();
                    return;
                }
            } else if (paymentMethod === 'bank') {
                const bankName = val('bank_name');
                const acctName = val('acct_name');
                const acctNumber = val('acct_number');
                if (!bankName || !acctName || !acctNumber) {
                    uiAlert('Please fill in all bank details', { title: 'Check your details' });
                    e.preventDefault();
                    return;
                }
            } else if (paymentMethod === 'paypal') {
                const paypal = val('paypal');
                if (!paypal || !paypal.includes('@')) {
                    uiAlert('Please enter a valid PayPal email address', { title: 'Check your details' });
                    e.preventDefault();
                    return;
                }
            }
        });
    }
});

</script>
@endsection
