@extends('dashboard.layout.app')

@section('content')
@php
    $walletBadge = [
        'balance' => ['Main balance', 'bg-gray-700 text-gray-300'],
        'trading' => ['Trading balance', 'bg-blue-500/15 text-blue-300'],
        'holding' => ['Holding balance', 'bg-emerald-500/15 text-emerald-400'],
        'staking' => ['Staking balance', 'bg-sky-500/15 text-sky-300'],
    ];
    $statusBadge = fn ($s) => $s == 0 ? ['Pending', 'bg-amber-500/15 text-amber-300'] : ($s == 1 ? ['Approved', 'bg-emerald-500/15 text-emerald-400'] : ['Rejected', 'bg-red-500/15 text-red-400']);
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Wallet</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Deposit</h1>
            <p class="text-sm text-gray-400 mt-0.5">Send crypto to our wallet, upload your proof of payment and we credit your account once it is confirmed.</p>
        </div>
        <button type="button" id="newDepositBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            <span>New deposit</span>
        </button>
    </div>

    <!-- Page-level feedback: the deposit modal is hidden after a redirect, so
         success/error messages must live here to be visible to the user. -->
    <div id="deposit-page-feedback" class="space-y-3 empty:hidden">
    @if(session('success'))
        <div class="p-4 rounded-xl border border-emerald-500/40 bg-emerald-500/10 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl border border-red-500/40 bg-red-500/10 text-sm text-red-300">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl border border-red-500/40 bg-red-500/10 text-sm text-red-300">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($wallets->isEmpty())
        <div class="p-4 rounded-xl border border-amber-500/40 bg-amber-500/10 text-sm text-amber-300">
            No payment methods are available right now, so deposits cannot be submitted. Please contact support.
        </div>
    @endif
    </div>

    <!-- Balances -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Main balance</div>
            <div class="mt-1 text-xl sm:text-2xl font-semibold text-white font-mono break-all">{{ money($user->balance ?? 0, 2) }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Available to trade or move</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Holding balance</div>
            <div class="mt-1 text-xl sm:text-2xl font-semibold text-white font-mono break-all">{{ money($user->holding_balance ?? 0, 2) }}</div>
            <div class="text-xs text-gray-500 mt-0.5">For buying crypto and stocks</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Staking balance</div>
            <div class="mt-1 text-xl sm:text-2xl font-semibold text-white font-mono break-all">{{ money($user->staking_balance ?? 0, 2) }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Locked in staking pools</div>
        </div>
    </div>

    <!-- Deposit history -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-white">Deposit history</h2>
        </div>
        @if($deposits->count())
            <ul class="md:hidden divide-y divide-gray-700">
                @foreach($deposits as $deposit)
                    @php [$sLabel, $sClass] = $statusBadge($deposit->status); $w = $walletBadge[$deposit->wallet_type] ?? null; @endphp
                    <li class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white font-mono">{{ money($deposit->amount, 2) }}</div>
                                <div class="mt-0.5 text-xs text-gray-400 truncate">{{ optional($deposit->payment_method)->crypto_display_name ?? 'N/A' }} &middot; {{ $deposit->created_at ? $deposit->created_at->format('M d, Y H:i') : 'N/A' }}</div>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $sClass }}">{{ $sLabel }}</span>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                            @if($w)<span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $w[1] }}">{{ $w[0] }}</span>@else<span class="text-xs text-gray-500">N/A</span>@endif
                            <div class="flex items-center gap-4">
                                @if($deposit->proof)
                                    <button type="button" class="min-h-[40px] text-sm font-semibold text-gold-400 hover:text-gold-300" onclick="viewProof('{{ $deposit->id }}')">View proof</button>
                                @endif
                                @if($deposit->status == 0)
                                    <button type="button" class="min-h-[40px] text-sm font-semibold text-red-400 hover:text-red-300" onclick="cancelDeposit('{{ $deposit->id }}')">Cancel</button>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900/50">
                        <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Wallet</th>
                            <th class="px-6 py-3">Payment method</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($deposits as $deposit)
                            @php [$sLabel, $sClass] = $statusBadge($deposit->status); $w = $walletBadge[$deposit->wallet_type] ?? null; @endphp
                            <tr class="hover:bg-gray-700/40">
                                <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($deposit->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">@if($w)<span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $w[1] }}">{{ $w[0] }}</span>@else<span class="text-gray-500">N/A</span>@endif</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ optional($deposit->payment_method)->crypto_display_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $sClass }}">{{ $sLabel }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $deposit->created_at ? $deposit->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($deposit->proof)
                                        <button type="button" class="font-semibold text-gold-400 hover:text-gold-300 mr-4" onclick="viewProof('{{ $deposit->id }}')">View proof</button>
                                    @endif
                                    @if($deposit->status == 0)
                                        <button type="button" class="font-semibold text-red-400 hover:text-red-300" onclick="cancelDeposit('{{ $deposit->id }}')">Cancel</button>
                                    @endif
                                </td>
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
                <p class="mt-1 text-sm text-gray-400">Make your first deposit to start trading.</p>
                <button type="button" onclick="document.getElementById('newDepositBtn').click()" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">New deposit</button>
            </div>
        @endif
    </div>
</div>

    <!-- New Deposit Modal -->
    <div id="depositModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="depositModalTitle">
        <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4">
            <div class="w-full max-w-lg rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-4 sm:px-6 py-3 border-b border-gray-700">
                    <h3 id="depositModalTitle" class="text-lg font-semibold text-white">New deposit</h3>
                    <button type="button" id="closeModal" class="w-10 h-10 -mr-2 grid place-items-center rounded-lg text-gray-400 hover:text-white hover:bg-gray-700" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="depositForm" action="{{ route('user.payment') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-4">
                    @csrf

                    <!-- Amount Input -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-300 mb-1.5">Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-sm">{{ currency_symbol() }}</span>
                            </div>
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   step="0.01"
                                   min="0"
                                   required
                                   inputmode="decimal"
                                   value="{{ old('amount') }}"
                                   class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 pl-8 pr-14"
                                   placeholder="0.00">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-sm">{{ auth()->user()->currency ?? 'USD' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Selection -->
                    <div>
                        <label for="wallet_type" class="block text-sm font-medium text-gray-300 mb-1.5">Credit to</label>
                        <select id="wallet_type"
                                name="wallet_type"
                                required
                                class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Choose a wallet</option>
                            <option value="balance" {{ old('wallet_type') == 'balance' ? 'selected' : '' }}>Main Balance</option>
                            <option value="trading" {{ old('wallet_type') == 'trading' ? 'selected' : '' }}>Trading Balance</option>
                            <option value="holding" {{ old('wallet_type') == 'holding' ? 'selected' : '' }}>Holding Balance</option>
                            <option value="staking" {{ old('wallet_type') == 'staking' ? 'selected' : '' }}>Staking Balance</option>
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method_id" class="block text-sm font-medium text-gray-300 mb-1.5">Payment method</label>
                        <select id="payment_method_id"
                                name="payment_method_id"
                                required
                                class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select payment method</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}"
                                        data-address="{{ $wallet->address ?? '' }}"
                                        {{ old('payment_method_id') == $wallet->id ? 'selected' : '' }}>{{ $wallet->crypto_display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Wallet Address Display -->
                    <div id="walletAddressSection" class="hidden">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Send payment to</label>
                        <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3 sm:p-4">
                            <div class="flex items-start gap-3">
                                <div id="qrCode" class="w-20 h-20 shrink-0 bg-white rounded-lg p-1 flex items-center justify-center">
                                    <div id="qrCodeDiv" class="w-full h-full flex items-center justify-center"></div>
                                </div>
                                <div class="min-w-0 flex-1 space-y-2">
                                    <input type="text" id="walletAddress" readonly class="w-full px-3 py-2 rounded-lg bg-gray-900/60 border border-gray-600 text-white text-xs sm:text-sm font-mono truncate" />
                                    <button type="button" id="copyAddressBtn" class="w-full inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-blue-400 hover:text-blue-300 text-sm font-semibold">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        Copy Address
                                    </button>
                                </div>
                            </div>

                            <!-- Countdown Timer -->
                            <div class="mt-3 pt-3 border-t border-gray-700">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-300">Payment expires in</span>
                                    <div id="countdownTimer" class="text-sm font-mono font-bold text-amber-300">
                                        <span id="minutes">15</span>:<span id="seconds">00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    <div>
                        <label for="proof" class="block text-sm font-medium text-gray-300 mb-1.5">Proof of payment</label>
                        <label for="proof" class="flex flex-col items-center justify-center gap-1 px-3 py-4 border-2 border-gray-600 border-dashed rounded-xl bg-gray-900/40 hover:bg-gray-900/70 cursor-pointer transition-colors text-center">
                            <svg class="h-6 w-6 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-sm font-semibold text-gold-400">Tap to upload a screenshot or receipt</span>
                            <span class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</span>
                            <input id="proof" name="proof" type="file" class="sr-only" required>
                        </label>
                        <div id="filePreview" class="mt-2 text-sm text-gray-400 break-all hidden"></div>
                    </div>

                    <!-- Submit Button -->
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <button type="button"
                                id="cancelBtn"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                            Cancel
                        </button>
                        <button type="submit"
                                id="depositSubmitBtn"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            <span id="depositBtnText">Submit deposit</span>
                            <span id="depositBtnSpinner" class="hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('depositModal');
        const newDepositBtn = document.getElementById('newDepositBtn');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const depositForm = document.getElementById('depositForm');
        const fileInput = document.getElementById('proof');
        const filePreview = document.getElementById('filePreview');

        // Open modal
        newDepositBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });

        // Close modal functions
        function closeModalFunc() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            depositForm.reset();
            filePreview.classList.add('hidden');
            
            // Stop countdown timer when modal is closed
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        }

        closeModal.addEventListener('click', closeModalFunc);
        cancelBtn.addEventListener('click', closeModalFunc);

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModalFunc();
            }
        });

        // File upload preview
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                filePreview.textContent = `Selected: ${file.name}`;
                filePreview.classList.remove('hidden');
            } else {
                filePreview.classList.add('hidden');
            }
        });

        // Wallet address and QR code functionality
        const paymentMethodSelect = document.getElementById('payment_method_id');
        const walletAddressSection = document.getElementById('walletAddressSection');
        const walletAddressInput = document.getElementById('walletAddress');
        const qrCodeDiv = document.getElementById('qrCode');

        const copyAddressBtn = document.getElementById('copyAddressBtn');
        
        // Debug: Check if copy button is found
        console.log('Copy button found:', copyAddressBtn);
        if (!copyAddressBtn) {
            console.error('Copy button not found!');
        }

        // Countdown timer variables
        let countdownInterval;
        let timeLeft = 15 * 60; // 15 minutes in seconds

        // Start countdown timer
        function startCountdown() {
            // Clear any existing timer
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            // Reset time to 15 minutes
            timeLeft = 15 * 60;
            
            // Update display immediately
            updateCountdownDisplay();
            
            // Start the countdown
            countdownInterval = setInterval(() => {
                timeLeft--;
                updateCountdownDisplay();
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    // Handle timer expiration
                    document.getElementById('countdownTimer').innerHTML = '<span class="text-red-400">EXPIRED</span>';
                }
            }, 1000);
        }

        // Update countdown display
        function updateCountdownDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }

        // Handle payment method selection
        paymentMethodSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const walletAddress = selectedOption.getAttribute('data-address');
            
            console.log('Payment method changed:', {
                selectedOption: selectedOption.value,
                walletAddress: walletAddress,
                hasAddress: walletAddress && walletAddress.trim() !== ''
            });
            
            if (walletAddress && walletAddress.trim() !== '') {
                // Show wallet address section
                walletAddressSection.classList.remove('hidden');
                walletAddressInput.value = walletAddress;
                
                // Generate QR code using Laravel
                generateQRCode(walletAddress);
                
                // Start countdown timer
                startCountdown();
            } else {
                // Hide wallet address section
                walletAddressSection.classList.add('hidden');
                walletAddressInput.value = '';
                qrCodeDiv.innerHTML = '';
                
                // Stop countdown timer
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
            }
        });

        // Generate QR code using Laravel backend
        function generateQRCode(address) {
            // Show loading state
            qrCodeDiv.innerHTML = '<div class="w-full h-full bg-gray-300 rounded flex items-center justify-center text-xs text-gray-600">Generating...</div>';
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('CSRF Token:', csrfToken);
            
            // Make AJAX request to generate QR code
            fetch('{{ route("user.generate.qrcode") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    address: address
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('QR Code response:', data);
                if (data.success) {
                    // Display QR code as SVG div
                    qrCodeDiv.innerHTML = data.qr_code_svg;
                    console.log('QR Code generated successfully as SVG');
                    console.log('QR Code SVG length:', data.qr_code_svg.length);
                    console.log('QR Code SVG preview:', data.qr_code_svg.substring(0, 100));
                } else {
                    qrCodeDiv.innerHTML = '<div class="w-full h-full bg-gray-300 rounded flex items-center justify-center text-xs text-gray-600">QR Error</div>';
                }
            })
            .catch(error => {
                console.error('QR Code generation failed:', error);
                qrCodeDiv.innerHTML = '<div class="w-full h-full bg-gray-300 rounded flex items-center justify-center text-xs text-gray-600">QR Error</div>';
            });
        }

        // Copy address functionality - using event delegation as fallback
        if (copyAddressBtn) {
            console.log('Adding click event listener to copy button');
            copyAddressBtn.addEventListener('click', function() {
                console.log('Copy button clicked!');
                const address = walletAddressInput.value;
                console.log('Copy button clicked, address:', address);
            
            if (address && address.trim() !== '') {
                // Modern clipboard API
                const copyToClipboard = async (text) => {
                    try {
                        // Try modern clipboard API first
                        if (navigator.clipboard && window.isSecureContext) {
                            console.log('Using modern clipboard API');
                            await navigator.clipboard.writeText(text);
                            return true;
                        } else {
                            // Fallback for older browsers or non-secure contexts
                            console.log('Using fallback copy method');
                            const textArea = document.createElement('textarea');
                            textArea.value = text;
                            textArea.style.position = 'fixed';
                            textArea.style.left = '-999999px';
                            textArea.style.top = '-999999px';
                            textArea.style.opacity = '0';
                            document.body.appendChild(textArea);
                            textArea.focus();
                            textArea.select();
                            
                            try {
                                const result = document.execCommand('copy');
                                document.body.removeChild(textArea);
                                return result;
                            } catch (err) {
                                document.body.removeChild(textArea);
                                throw err;
                            }
                        }
                    } catch (err) {
                        console.error('Copy failed:', err);
                        return false;
                    }
                };

                copyToClipboard(address).then(success => {
                    console.log('Copy result:', success);
                    if (success) {
                        // Show success feedback
                        const originalText = copyAddressBtn.innerHTML;
                        copyAddressBtn.innerHTML = `
                            <svg class="w-4 h-4 inline mr-1 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Copied!
                        `;
                        copyAddressBtn.classList.remove('text-blue-400', 'hover:text-blue-300');
                        copyAddressBtn.classList.add('text-green-400');
                        
                        setTimeout(() => {
                            copyAddressBtn.innerHTML = originalText;
                            copyAddressBtn.classList.remove('text-green-400');
                            copyAddressBtn.classList.add('text-blue-400', 'hover:text-blue-300');
                        }, 2000);
                    } else {
                        // Show error feedback
                        const originalText = copyAddressBtn.innerHTML;
                        copyAddressBtn.innerHTML = `
                            <svg class="w-4 h-4 inline mr-1 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Failed!
                        `;
                        copyAddressBtn.classList.remove('text-blue-400', 'hover:text-blue-300');
                        copyAddressBtn.classList.add('text-red-400');
                        
                        setTimeout(() => {
                            copyAddressBtn.innerHTML = originalText;
                            copyAddressBtn.classList.remove('text-red-400');
                            copyAddressBtn.classList.add('text-blue-400', 'hover:text-blue-300');
                        }, 2000);
                    }
                });
            } else {
                console.log('No address to copy');
                uiAlert('No wallet address available to copy', { title: 'Nothing to copy' });
            }
        });
        } else {
            console.log('Copy button not found, using event delegation');
        }

        // Event delegation fallback for copy button (in case button is not found initially)
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'copyAddressBtn') {
                console.log('Copy button clicked via event delegation!');
                const address = walletAddressInput.value;
                console.log('Copy button clicked, address:', address);
                
                if (address && address.trim() !== '') {
                    // Modern clipboard API
                    const copyToClipboard = async (text) => {
                        try {
                            // Try modern clipboard API first
                            if (navigator.clipboard && window.isSecureContext) {
                                console.log('Using modern clipboard API');
                                await navigator.clipboard.writeText(text);
                                return true;
                            } else {
                                // Fallback for older browsers or non-secure contexts
                                console.log('Using fallback copy method');
                                const textArea = document.createElement('textarea');
                                textArea.value = text;
                                textArea.style.position = 'fixed';
                                textArea.style.left = '-999999px';
                                textArea.style.top = '-999999px';
                                textArea.style.opacity = '0';
                                document.body.appendChild(textArea);
                                textArea.focus();
                                textArea.select();
                                
                                try {
                                    const result = document.execCommand('copy');
                                    document.body.removeChild(textArea);
                                    return result;
                                } catch (err) {
                                    document.body.removeChild(textArea);
                                    throw err;
                                }
                            }
                        } catch (err) {
                            console.error('Copy failed:', err);
                            return false;
                        }
                    };

                    copyToClipboard(address).then(success => {
                        console.log('Copy result:', success);
                        if (success) {
                            // Show success feedback
                            const originalText = e.target.innerHTML;
                            e.target.innerHTML = `
                                <svg class="w-4 h-4 inline mr-1 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Copied!
                            `;
                            e.target.classList.remove('text-blue-400', 'hover:text-blue-300');
                            e.target.classList.add('text-green-400');
                            
                            setTimeout(() => {
                                e.target.innerHTML = originalText;
                                e.target.classList.remove('text-green-400');
                                e.target.classList.add('text-blue-400', 'hover:text-blue-300');
                            }, 2000);
                        } else {
                            // Show error feedback
                            const originalText = e.target.innerHTML;
                            e.target.innerHTML = `
                                <svg class="w-4 h-4 inline mr-1 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Failed!
                            `;
                            e.target.classList.remove('text-blue-400', 'hover:text-blue-300');
                            e.target.classList.add('text-red-400');
                            
                            setTimeout(() => {
                                e.target.innerHTML = originalText;
                                e.target.classList.remove('text-red-400');
                                e.target.classList.add('text-blue-400', 'hover:text-blue-300');
                            }, 2000);
                        }
                    });
                } else {
                    console.log('No address to copy');
                    uiAlert('No wallet address available to copy', { title: 'Nothing to copy' });
                }
            }
        });

        // Deposit button processing state function
        function setDepositButtonProcessing(isProcessing) {
            const submitBtn = document.getElementById('depositSubmitBtn');
            const btnText = document.getElementById('depositBtnText');
            const btnSpinner = document.getElementById('depositBtnSpinner');
            
            if (isProcessing) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:bg-blue-700');
                btnText.classList.add('hidden');
                btnSpinner.classList.remove('hidden');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                submitBtn.classList.add('hover:bg-blue-700');
                btnText.classList.remove('hidden');
                btnSpinner.classList.add('hidden');
            }
        }

        // Form submission
        depositForm.addEventListener('submit', (e) => {
            const amount = document.getElementById('amount').value;
            const walletType = document.getElementById('wallet_type').value;
            const paymentMethod = document.getElementById('payment_method_id').value;
            const proof = fileInput.files[0];

            if (!amount || !walletType || !paymentMethod || !proof) {
                e.preventDefault();
                uiAlert('Please fill in all required fields', { title: 'Check your details' });
                return;
            }

            // Show processing state
            setDepositButtonProcessing(true);
        });

        // Auto-close modal on successful submission
        @if(session('success'))
            closeModalFunc();
        @endif

        // View proof function
        function viewProof(depositId) {
            window.open('{{ route("user.deposit.proof", "") }}/' + depositId, '_blank');
        }

        // Cancel deposit function
        async function cancelDeposit(depositId) {
            if (await uiConfirm('Are you sure you want to cancel this deposit?')) {
                // Create a form to submit the cancel request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("user.deposit.cancel", "") }}/' + depositId;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <style>
        /* QR Code SVG styling */
        #qrCodeDiv svg {
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
        }
    </style>

    {{-- Reopen the form after a validation failure so the user can fix and resubmit --}}
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('newDepositBtn')?.click();
        });
    </script>
    @endif
@endsection
