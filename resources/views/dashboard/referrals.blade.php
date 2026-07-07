@extends('dashboard.layout.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Referrals</h1>
            <p class="text-gray-400 mt-1">Invite friends and earn rewards from their first deposit</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Referrals</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalReferrals) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Earnings</p>
                    <p class="text-2xl font-bold text-green-400 mt-1">{{ $user->formatAmount($totalEarnings) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Pending Earnings</p>
                    <p class="text-2xl font-bold text-yellow-400 mt-1">{{ $user->formatAmount($pendingEarnings) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Referral Link Card -->
        <div class="lg:col-span-1 bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Your Referral Link</h3>
            <p class="text-gray-400 text-sm mb-4">Share this link with friends. When they register and make their first deposit, you earn a {{ number_format(\App\Services\ReferralService::COMMISSION_RATE * 100) }}% reward.</p>

            <div class="space-y-4">
                <div class="relative">
                    <input type="text" id="referralLink" value="{{ $referralLink }}" readonly
                           class="w-full bg-gray-900 border border-gray-600 rounded-lg px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500">
                </div>

                <button onclick="copyReferralLink()" id="copyLinkBtn"
                        class="w-full flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg px-4 py-3 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span id="copyLinkText">Copy Link</span>
                </button>

                <div class="grid grid-cols-4 gap-2 pt-2">
                    <a href="https://wa.me/?text={{ urlencode('Join me on ' . config('app.name') . ': ' . $referralLink) }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center p-3 bg-green-600 hover:bg-green-500 rounded-lg transition-colors" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp text-white text-lg"></i>
                    </a>
                    <a href="https://t.me/share/url?url={{ urlencode($referralLink) }}&text={{ urlencode('Join me on ' . config('app.name')) }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center p-3 bg-blue-500 hover:bg-blue-400 rounded-lg transition-colors" title="Share on Telegram">
                        <i class="fab fa-telegram-plane text-white text-lg"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($referralLink) }}&text={{ urlencode('Join me on ' . config('app.name')) }}" target="_blank" rel="noopener"
                       class="flex items-center justify-center p-3 bg-sky-500 hover:bg-sky-400 rounded-lg transition-colors" title="Share on X/Twitter">
                        <i class="fab fa-twitter text-white text-lg"></i>
                    </a>
                    <a href="mailto:?subject={{ urlencode('Join me on ' . config('app.name')) }}&body={{ urlencode('Sign up using my referral link: ' . $referralLink) }}"
                       class="flex items-center justify-center p-3 bg-red-500 hover:bg-red-400 rounded-lg transition-colors" title="Share via Email">
                        <i class="fas fa-envelope text-white text-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Referral Records -->
        <div class="lg:col-span-2 bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Referral Records</h3>

            @if($user->referredUsers->isEmpty())
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-400">No referrals yet. Start sharing your link!</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-700">
                                <th class="pb-3 text-sm font-medium text-gray-400">Name</th>
                                <th class="pb-3 text-sm font-medium text-gray-400">Joined</th>
                                <th class="pb-3 text-sm font-medium text-gray-400">Status</th>
                                <th class="pb-3 text-sm font-medium text-gray-400 text-right">Reward</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($user->referredUsers as $referred)
                                @php
                                    $record = $user->referralRecords->firstWhere('referred_user_id', $referred->id);
                                @endphp
                                <tr>
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-white text-sm font-semibold">{{ substr($referred->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-medium">{{ $referred->name }}</p>
                                                <p class="text-gray-400 text-sm">{{ $referred->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-gray-300 text-sm">
                                        {{ $referred->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="py-4">
                                        @if($record && $record->status === 'paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                Paid
                                            </span>
                                        @elseif($record && $record->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Signed Up
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-right text-white font-medium">
                                        @if($record && $record->status === 'paid')
                                            {{ $user->formatAmount($record->amount) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
