@extends('dashboard.layout.app')

@section('content')
@php
    $rate = number_format(\App\Services\ReferralService::COMMISSION_RATE * 100);
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Account</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Referrals</h1>
            <p class="text-sm text-gray-400 mt-0.5">Invite friends and earn {{ $rate }}% of their first deposit.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total referrals</div>
            <div class="mt-1 text-xl sm:text-2xl font-semibold text-white">{{ number_format($totalReferrals) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Total earnings</div>
            <div class="mt-1 text-xl sm:text-2xl font-semibold text-emerald-400 font-mono break-all">{{ $user->formatAmount($totalEarnings) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Pending earnings</div>
            <div class="mt-1 text-xl sm:text-2xl font-semibold text-amber-300 font-mono break-all">{{ $user->formatAmount($pendingEarnings) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 items-start">
        <!-- Referral Link Card -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-white">Your referral link</h2>
            <p class="text-sm text-gray-400 mt-1 mb-4">Share this link with friends. When they register and make their first deposit, you earn a {{ $rate }}% reward.</p>

            <div class="space-y-3">
                <input type="text" id="referralLink" value="{{ $referralLink }}" readonly aria-label="Referral link"
                       class="w-full px-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-sm text-white font-mono truncate focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button type="button" onclick="copyReferralLink()" id="copyLinkBtn"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span id="copyLinkText">Copy link</span>
                </button>

                <p class="text-xs text-gray-500 pt-1">Or share directly</p>
                <div class="grid grid-cols-4 gap-2">
                <a href="https://wa.me/?text={{ urlencode('Join me on ' . config('app.name') . ': ' . $referralLink) }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center min-h-[44px] rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-gray-200 hover:text-white transition-colors" title="Share on WhatsApp" aria-label="Share on WhatsApp">
                    <i class="fab fa-whatsapp text-lg"></i>
                </a>
                <a href="https://t.me/share/url?url={{ urlencode($referralLink) }}&text={{ urlencode('Join me on ' . config('app.name')) }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center min-h-[44px] rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-gray-200 hover:text-white transition-colors" title="Share on Telegram" aria-label="Share on Telegram">
                    <i class="fab fa-telegram-plane text-lg"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode($referralLink) }}&text={{ urlencode('Join me on ' . config('app.name')) }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center min-h-[44px] rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-gray-200 hover:text-white transition-colors" title="Share on X" aria-label="Share on X">
                    <i class="fab fa-twitter text-lg"></i>
                </a>
                <a href="mailto:?subject={{ urlencode('Join me on ' . config('app.name')) }}&body={{ urlencode('Sign up using my referral link: ' . $referralLink) }}" 
                   class="flex items-center justify-center min-h-[44px] rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-gray-200 hover:text-white transition-colors" title="Share via email" aria-label="Share via email">
                    <i class="fas fa-envelope text-lg"></i>
                </a>
                </div>
            </div>
        </div>

        <!-- Referral Records -->
        <div class="lg:col-span-2 rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
                <h2 class="text-base font-semibold text-white">People you referred</h2>
            </div>

            @if($user->referredUsers->isEmpty())
                <div class="px-4 py-12 text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M16 4.5a3.5 3.5 0 010 7M22 20a7 7 0 00-4-6.3"/></svg>
                    </div>
                    <div class="mt-3 text-white font-medium">No referrals yet</div>
                    <p class="mt-1 text-sm text-gray-400">Copy your link and share it to start earning.</p>
                    <button type="button" onclick="copyReferralLink()" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">Copy my link</button>
                </div>
            @else
                <ul class="divide-y divide-gray-700">
                    @foreach($user->referredUsers as $referred)
                        @php
                            $record = $user->referralRecords->firstWhere('referred_user_id', $referred->id);
                        @endphp
                        <li class="px-4 sm:px-6 py-4 flex items-center gap-3">
                            <div class="w-9 h-9 shrink-0 bg-blue-600 rounded-full grid place-items-center">
                                <span class="text-white text-sm font-semibold">{{ substr($referred->name, 0, 1) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-white truncate">{{ $referred->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $referred->email }} &middot; joined {{ $referred->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                @if($record && $record->status === 'paid')
                                    <div class="text-sm font-semibold text-white font-mono">{{ $user->formatAmount($record->amount) }}</div>
                                    <span class="mt-1 inline-block px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-500/15 text-emerald-400">Paid</span>
                                @elseif($record && $record->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-500/15 text-amber-300">Pending</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">Signed up</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

<script>
    function copyReferralLink() {
        const linkInput = document.getElementById('referralLink');
        const copyText = document.getElementById('copyLinkText');
        const copyBtn = document.getElementById('copyLinkBtn');

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(linkInput.value).then(function() {
                showCopied(copyText, copyBtn);
            });
        } else {
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            showCopied(copyText, copyBtn);
        }
    }

    function showCopied(copyText, copyBtn) {
        const originalText = copyText.textContent;
        copyText.textContent = 'Copied!';
        copyBtn.classList.remove('bg-blue-600', 'hover:bg-blue-500');
        copyBtn.classList.add('bg-green-600', 'hover:bg-green-500');

        setTimeout(function() {
            copyText.textContent = originalText;
            copyBtn.classList.remove('bg-green-600', 'hover:bg-green-500');
            copyBtn.classList.add('bg-blue-600', 'hover:bg-blue-500');
        }, 2000);
    }
</script>
@endsection
