@extends('dashboard.layout.app')

@section('content')
@php
    $copyStatus = function ($trade) {
        if ($trade->status == 0 && $trade->stopped_at) return ['Stopped', 'bg-red-500/15 text-red-400'];
        if ($trade->status == 0) return ['Pending', 'bg-amber-500/15 text-amber-300'];
        return ['Active', 'bg-emerald-500/15 text-emerald-400'];
    };
    $signedMoney = fn ($v) => ((float) $v < 0 ? '-' : '') . money(abs((float) $v), 2);
@endphp
<div class="space-y-4 sm:space-y-6">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Automation</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Copy Trading</h1>
            <p class="text-sm text-gray-400 mt-0.5">Pick an experienced trader and mirror their trades automatically.</p>
        </div>
    </div>

    <!-- Copy Trading Modal (bottom sheet on phones) -->
    <div id="copyTradingModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-end sm:items-center justify-center sm:p-4">
        <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-gray-700">
                <h3 id="modalTitle" class="text-base font-semibold text-white">Copy Trading</h3>
                <button id="closeModal" class="w-10 h-10 -mr-2 grid place-items-center rounded-lg text-gray-400 hover:text-white hover:bg-gray-700" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 sm:p-6">
                <div id="modalContent" class="flex items-start gap-3">
                    <div id="modalIcon" class="flex-shrink-0"></div>
                    <div class="flex-1">
                        <p id="modalMessage" class="text-sm text-gray-300"></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end px-4 sm:px-6 py-4 border-t border-gray-700">
                <button id="modalCloseBtn" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <nav class="inline-flex w-full sm:w-auto p-1 rounded-xl border border-gray-700 bg-gray-800">
        <button id="tradersTab" class="tab-button active flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-semibold bg-gray-700 text-white">
            Available traders
        </button>
        <button id="historyTab" class="tab-button flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-semibold text-gray-400 hover:text-white">
            My copies
            @if(count($copiedTrades) > 0)<span class="ml-1 text-xs font-mono text-gray-400">{{ count($copiedTrades) }}</span>@endif
        </button>
    </nav>

    <!-- Available Traders Section -->
    <div id="tradersSection" class="tab-content space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                       id="traderSearch"
                       placeholder="Search traders by name..."
                       class="w-full pl-10 pr-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div id="traderCount" class="text-sm text-gray-400 sm:text-right">{{ count($traders) }} traders available</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($traders as $trader)
            @php
                $barPercent = min(100, max(0, (int)($trader->win_rate ?? 0)));
                $tradesTotal = (int)($trader->win ?? 0) + (int)($trader->loss ?? 0);
                $isStopped = in_array($trader->id, $stoppedCopyTrades);
            @endphp
            <div class="trader-card pc-card rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5 flex flex-col">
                <div class="flex items-center gap-3">
                    <div class="relative shrink-0">
                        <img src="{{ $trader->avatar_url }}" alt="{{ $trader->name }}"
                             class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-500/30"
                             onerror="this.src='{{ asset('img/trader.jpg') }}'">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-gray-800"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-semibold text-white truncate leading-tight">{{ $trader->name }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Professional trader</p>
                    </div>
                    @if($isStopped)
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-700 text-gray-300">Stopped</span>
                    @else
                        <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 pc-pulse"></span>Trading
                        </span>
                    @endif
                </div>

                <div class="mt-5">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <div class="text-xs text-gray-400">Win rate</div>
                            <div class="text-3xl font-bold text-white tracking-tight leading-none mt-1.5 tabular-nums">{{ $trader->win_rate }}<span class="text-lg text-gray-400 font-semibold">%</span></div>
                        </div>
                        <div class="text-right text-xs text-gray-400 pb-0.5 tabular-nums">
                            <span class="text-emerald-400 font-semibold">{{ number_format($trader->win ?? 0) }}W</span>
                            <span class="text-gray-600 mx-0.5">/</span>
                            <span class="text-red-400 font-semibold">{{ number_format($trader->loss ?? 0) }}L</span>
                        </div>
                    </div>
                    <div class="mt-3 h-1.5 rounded-full bg-gray-700/70 overflow-hidden" role="img" aria-label="Win rate {{ $trader->win_rate }}%">
                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $barPercent }}%"></div>
                    </div>
                </div>

                <dl class="mt-4 grid grid-cols-3 gap-2">
                    <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                        <dt class="text-[11px] text-gray-400">Trades</dt>
                        <dd class="text-sm font-semibold text-white mt-0.5 tabular-nums truncate">{{ number_format($tradesTotal) }}</dd>
                    </div>
                    <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                        <dt class="text-[11px] text-gray-400">Profit share</dt>
                        <dd class="text-sm font-semibold text-white mt-0.5 tabular-nums truncate">{{ number_format($trader->profit_share, 1) }}%</dd>
                    </div>
                    <div class="rounded-xl bg-gray-900 px-2.5 py-2 min-w-0">
                        <dt class="text-[11px] text-gray-400">Min. invest</dt>
                        <dd class="text-sm font-semibold text-white mt-0.5 tabular-nums truncate">{{ money_from($trader->amount, 'USD', 2) }}</dd>
                    </div>
                </dl>

                <div class="mt-auto pt-4">
                    @if($isStopped)
                        <button disabled
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-700 text-gray-400 text-sm font-semibold cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                            <span>Stopped</span>
                        </button>
                        <p class="text-xs text-gray-500 mt-2 text-center">You stopped copying this trader.</p>
                    @else
                        <form action="{{ route('user.copyTrading.store') }}" method="POST" class="copy-trade-form" data-trader="{{ $trader->id }}">
                            @csrf
                            <input type="hidden" name="trader_id" value="{{ $trader->id }}">
                            <input type="hidden" name="amount" value="{{ $trader->amount }}">

                            <button type="submit"
                                    class="copy-trade-btn w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                <svg class="copy-trade-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="copy-trade-text">Copy trader</span>
                                <svg class="copy-trade-spinner hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="copy-trade-loading hidden">Processing...</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full rounded-2xl border border-gray-700 bg-gray-800 px-4 py-12 text-center">
                <div class="w-12 h-12 bg-gray-700 rounded-full grid place-items-center mx-auto text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M16 4.5a3.5 3.5 0 010 7M22 20a7 7 0 00-4-6.3"/></svg>
                </div>
                <p class="text-white font-medium mt-3">No traders available yet, new traders are listed here as they join.</p>
                <a href="{{ route('user.liveTrading.index') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Trade yourself meanwhile</a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- My Copied Trades Section -->
    <div id="historySection" class="tab-content hidden">
    <section class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-700 flex items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-white">My copied trades</h2>
            <span class="text-xs font-mono text-gray-400">{{ count($copiedTrades) }}</span>
        </div>

        @if(count($copiedTrades) > 0)
        <ul class="md:hidden divide-y divide-gray-700">
            @foreach($copiedTrades as $trade)
                @php [$stLabel, $stClass] = $copyStatus($trade); @endphp
                <li>
                    <a href="{{ route('user.copyTrading.detail', $trade->id) }}" class="block p-4 hover:bg-gray-700/40">
                        <div class="flex items-center gap-3">
                            <img src="{{ $trade->copy_trader?->avatar_url ?? asset('img/trader.jpg') }}" alt=""
                                 class="w-9 h-9 rounded-full object-cover" onerror="this.src='{{ asset('img/trader.jpg') }}'">
                            <div class="min-w-0 flex-1">
                                <div class="text-white font-medium truncate">{{ $trade->copy_trader?->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-400">Started {{ date('M d, Y', strtotime($trade->created_at)) }}</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $stClass }}">{{ $stLabel }}</span>
                        </div>
                        <dl class="mt-3 grid grid-cols-3 gap-2 text-sm">
                            <div><dt class="text-xs text-gray-400">Amount</dt><dd class="text-white font-medium">{{ money($trade->amount, 2) }}</dd></div>
                            <div><dt class="text-xs text-gray-400">W / L</dt><dd><span class="text-emerald-400">{{ $trade->win ?? 0 }}</span> / <span class="text-red-400">{{ $trade->loss ?? 0 }}</span></dd></div>
                            <div class="text-right"><dt class="text-xs text-gray-400">PnL</dt><dd class="font-medium {{ ($trade->pnl ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $signedMoney($trade->pnl ?? 0) }}</dd></div>
                        </dl>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-900/50 border-b border-gray-700">
                    <tr>
                        <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400">Trader</th>
                        <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400">Amount</th>
                        <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400">Trades</th>
                        <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400">Win/Loss</th>
                        <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400">PnL</th>
                        <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400">Started</th>
                        <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400">Stopped</th>
                        <th class="text-right py-3 px-4 text-xs font-medium uppercase tracking-wider text-gray-400"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($copiedTrades as $trade)
                    @php [$stLabel, $stClass] = $copyStatus($trade); @endphp
                    <tr class="hover:bg-gray-700/40">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $trade->copy_trader?->avatar_url ?? asset('img/trader.jpg') }}" alt=""
                                     class="w-8 h-8 rounded-full object-cover"
                                     onerror="this.src='{{ asset('img/trader.jpg') }}'">
                                <span class="text-white">{{ $trade->copy_trader?->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-white">{{ money($trade->amount, 2) }}</td>
                        <td class="py-3 px-4 text-white">{{ $trade->trade_count ?? 0 }}</td>
                        <td class="py-3 px-4">
                            <span class="text-emerald-400">{{ $trade->win ?? 0 }}</span> /
                            <span class="text-red-400">{{ $trade->loss ?? 0 }}</span>
                        </td>
                        <td class="py-3 px-4 {{ ($trade->pnl ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $signedMoney($trade->pnl ?? 0) }}</td>
                        <td class="py-3 px-4 text-gray-400 whitespace-nowrap">{{ date('M d, Y', strtotime($trade->created_at)) }}</td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $stClass }}">{{ $stLabel }}</span></td>
                        <td class="py-3 px-4 text-gray-400 whitespace-nowrap">{{ $trade->stopped_at ? date('M d, Y H:i', strtotime($trade->stopped_at)) : '—' }}</td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('user.copyTrading.detail', $trade->id) }}" class="text-gold-400 hover:text-gold-300 font-semibold whitespace-nowrap">Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center px-4 py-12">
            <div class="w-12 h-12 bg-gray-700 rounded-full grid place-items-center mx-auto text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h3 class="text-white font-medium mt-3">No copied trades yet</h3>
            <p class="text-sm text-gray-400 mt-1">Choose a trader on the Available traders tab to start copying.</p>
        </div>
        @endif
    </section>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Tab functionality
        const tradersTab = document.getElementById('tradersTab');
        const historyTab = document.getElementById('historyTab');
        const tradersSection = document.getElementById('tradersSection');
        const historySection = document.getElementById('historySection');

        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'bg-gray-700', 'text-white');
                button.classList.add('text-gray-400');
            });

            // Show selected tab content
            if (tabName === 'traders') {
                tradersSection.classList.remove('hidden');
                tradersTab.classList.add('active', 'bg-gray-700', 'text-white');
                tradersTab.classList.remove('text-gray-400');
            } else if (tabName === 'history') {
                historySection.classList.remove('hidden');
                historyTab.classList.add('active', 'bg-gray-700', 'text-white');
                historyTab.classList.remove('text-gray-400');
            }
        }

        // Add click event listeners to tabs
        tradersTab.addEventListener('click', () => {
            showTab('traders');
            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('tab', 'traders');
            window.history.pushState({}, '', url);
        });
        historyTab.addEventListener('click', () => {
            showTab('history');
            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('tab', 'history');
            window.history.pushState({}, '', url);
        });

        // Check URL parameters for initial tab
        const urlParams = new URLSearchParams(window.location.search);
        const initialTab = urlParams.get('tab');
        if (initialTab === 'history') {
            showTab('history');
        } else {
            showTab('traders'); // Default to traders tab
        }


        // Modal functionality
        const modal = document.getElementById('copyTradingModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalIcon = document.getElementById('modalIcon');
        const closeModal = document.getElementById('closeModal');
        const modalCloseBtn = document.getElementById('modalCloseBtn');

        function showModal(type, message) {
            modalTitle.textContent = 'Copy Trading';
            modalMessage.textContent = message;
            
            // Clear previous icon
            modalIcon.innerHTML = '';
            
            if (type === 'success') {
                modalTitle.textContent = 'Success!';
                modalIcon.innerHTML = `
                    <div class="w-12 h-12 bg-emerald-500/15 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                `;
            } else if (type === 'warning') {
                modalTitle.textContent = 'Already Copying';
                modalIcon.innerHTML = `
                    <div class="w-12 h-12 bg-amber-500/15 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                `;
            } else if (type === 'error') {
                modalTitle.textContent = 'Error';
                modalIcon.innerHTML = `
                    <div class="w-12 h-12 bg-red-500/15 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                `;
            }
            
            modal.classList.remove('hidden');
        }

        function hideModal() {
            modal.classList.add('hidden');
        }

        // Close modal events
        closeModal.addEventListener('click', hideModal);
        modalCloseBtn.addEventListener('click', hideModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal();
            }
        });

        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                hideModal();
            }
        });

        // Function to reset copy trade button state
        function resetCopyTradeButton(form) {
            const submitBtn = form.querySelector('.copy-trade-btn');
            const icon = form.querySelector('.copy-trade-icon');
            const text = form.querySelector('.copy-trade-text');
            const spinner = form.querySelector('.copy-trade-spinner');
            const loadingText = form.querySelector('.copy-trade-loading');
            
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            icon.classList.remove('hidden');
            text.classList.remove('hidden');
            spinner.classList.add('hidden');
            loadingText.classList.add('hidden');
        }

        // Copy trade form submission with loading state
        const copyTradeForms = document.querySelectorAll('.copy-trade-form');
        
        copyTradeForms.forEach(function(form, index){
            
            form.addEventListener('submit', function(e){
                e.preventDefault(); // Prevent default form submission
                
                const traderId = form.getAttribute('data-trader');
                const amount = form.querySelector('input[name="amount"]').value;
                const submitBtn = form.querySelector('.copy-trade-btn');
                const icon = form.querySelector('.copy-trade-icon');
                const text = form.querySelector('.copy-trade-text');
                const spinner = form.querySelector('.copy-trade-spinner');
                const loadingText = form.querySelector('.copy-trade-loading');
                
                
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                icon.classList.add('hidden');
                text.classList.add('hidden');
                spinner.classList.remove('hidden');
                loadingText.classList.remove('hidden');
                
                // Prepare form data
                const formData = new FormData(form);
                
                // Submit via AJAX
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button state
                    resetCopyTradeButton(form);
                    
                    // Show appropriate modal
                    if (data.success) {
                        showModal('success', data.message);
                        // Refresh the page after 2 seconds to update the UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else if (data.warning) {
                        showModal('warning', data.message);
                    } else if (data.error) {
                        showModal('error', data.message);
                    } else {
                        showModal('error', 'An unexpected error occurred. Please try again.');
                    }
                })
                .catch(error => {
                    resetCopyTradeButton(form);
                    showModal('error', 'An error occurred while processing your request. Please try again.');
                });
            });
        });

        // Trader Search Functionality
        const searchInput = document.getElementById('traderSearch');
        const traderCards = document.querySelectorAll('.trader-card');
        const traderCountElement = document.getElementById('traderCount');

        if (searchInput && traderCards.length > 0) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                traderCards.forEach(function(card) {
                    const traderNameElement = card.querySelector('h3');
                    if (traderNameElement) {
                        const traderName = traderNameElement.textContent.toLowerCase();
                        
                        if (searchTerm === '' || traderName.includes(searchTerm)) {
                            card.style.display = '';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    }
                });

                // Update trader count
                if (traderCountElement) {
                    traderCountElement.textContent = `${visibleCount} traders available`;
                }

                // Show "no results" message if no traders match
                const noResultsMessage = document.getElementById('noResultsMessage');
                if (visibleCount === 0 && searchTerm !== '') {
                    if (!noResultsMessage) {
                        const gridContainer = traderCards[0].parentElement;
                        const messageDiv = document.createElement('div');
                        messageDiv.id = 'noResultsMessage';
                        messageDiv.className = 'col-span-full text-center py-12';
                        messageDiv.innerHTML = `
                            <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <p class="text-white font-medium">No traders found</p>
                            <p class="text-sm text-gray-400 mt-1">Try a different name.</p>
                        `;
                        gridContainer.appendChild(messageDiv);
                    }
                } else if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            });

            // Clear search functionality
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    this.dispatchEvent(new Event('input'));
                }
            });
        }
    });
</script>
@endpush

@endsection
