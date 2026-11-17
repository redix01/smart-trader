@extends('dashboard.layout.app')

@php
    $livechatService = app(\App\Services\LivechatService::class);
    $livechatConfig = $livechatService->getWidgetConfig();
    $showLivechat = $livechatService->shouldShowOnPage('support');
@endphp

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Help</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Support</h1>
            <p class="text-sm text-gray-400 mt-0.5">Find quick answers or get in touch with our team.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 items-start">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Contact Information -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-white">Contact us</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    @php $siteEmail = \App\Helpers\WebsiteSettingsHelper::getSiteEmail(); @endphp
                    <a href="mailto:{{ $siteEmail }}" class="flex items-center gap-3 rounded-xl border border-gray-700 bg-gray-900/50 p-4 hover:bg-gray-700/50 transition-colors min-w-0">
                        <div class="w-10 h-10 shrink-0 rounded-full bg-gray-700 grid place-items-center text-gold-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-white">Email support</h3>
                            <p class="text-sm text-gray-400 break-all">{{ $siteEmail }}</p>
                        </div>
                    </a>

                    <div class="flex items-center gap-3 rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                        <div class="w-10 h-10 shrink-0 rounded-full bg-gray-700 grid place-items-center text-gold-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white">Response time</h3>
                            <p class="text-sm text-gray-400">Within 24 hours</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-white">Frequently asked questions</h2>
                <div class="mt-2 divide-y divide-gray-700">
                    <details class="group py-1" open>
                        <summary class="flex items-center justify-between gap-3 min-h-[48px] cursor-pointer list-none text-sm font-semibold text-white">
                            <span>How do I make a deposit?</span>
                            <svg class="w-4 h-4 shrink-0 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <p class="pb-3 text-sm text-gray-400">Go to the Transactions section and select "Deposit". Choose your preferred payment method and follow the instructions.</p>
                    </details>
                    <details class="group py-1">
                        <summary class="flex items-center justify-between gap-3 min-h-[48px] cursor-pointer list-none text-sm font-semibold text-white">
                            <span>How long does withdrawal take?</span>
                            <svg class="w-4 h-4 shrink-0 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <p class="pb-3 text-sm text-gray-400">Withdrawals are typically processed within 24-48 hours after approval by our team.</p>
                    </details>
                    <details class="group py-1">
                        <summary class="flex items-center justify-between gap-3 min-h-[48px] cursor-pointer list-none text-sm font-semibold text-white">
                            <span>What are the trading fees?</span>
                            <svg class="w-4 h-4 shrink-0 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <p class="pb-3 text-sm text-gray-400">Trading fees vary by plan. Check your subscription details in the dashboard for specific rates.</p>
                    </details>
                    <details class="group py-1">
                        <summary class="flex items-center justify-between gap-3 min-h-[48px] cursor-pointer list-none text-sm font-semibold text-white">
                            <span>How do I change my password?</span>
                            <svg class="w-4 h-4 shrink-0 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <p class="pb-3 text-sm text-gray-400">Go to your profile settings and select "Change Password" to update your account security.</p>
                    </details>
                </div>
            </div>
        </div>

        <!-- Live Chat Widget -->
        @if($showLivechat)
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-white">Live chat</h2>
                <p class="text-sm text-gray-400 mt-1 mb-4">Chat with our support team in real time.</p>

                <!-- Live chat launcher: the chat bubble itself is injected by the widget script below -->
                <div id="livechat-widget" class="rounded-xl border border-gray-700 bg-gray-900/50 p-5 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto mb-3 text-gold-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-white font-medium mb-1">Talk to our team</h3>
                    <p class="text-gray-400 text-sm mb-4">Our support team is online 24/7. Start a chat here, or use the chat button in the corner of the page.</p>
                    <button type="button" id="openLivechat" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Open chat</button>
                    <p id="livechatNote" class="text-gray-400 text-xs mt-3 hidden">The chat is still loading. Please try again in a moment.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@if($showLivechat)
<!-- Livechat Widget Script -->
@php
    $widgetScript = $livechatService->getWidgetScript();
@endphp
@if($widgetScript)
{!! $widgetScript !!}
@else
<!-- Default Jivo widget (used when no custom script is saved in admin settings) -->
<script src="//code.jivosite.com/widget/lS6DU72Mu5" async></script>
@endif

<script>
document.getElementById('openLivechat').addEventListener('click', function () {
    var note = document.getElementById('livechatNote');
    if (window.jivo_api && typeof window.jivo_api.open === 'function') {
        note.classList.add('hidden');
        window.jivo_api.open();
    } else {
        note.classList.remove('hidden');
    }
});
</script>

@if($livechatConfig['custom_css'])
<style>
{!! $livechatConfig['custom_css'] !!}
</style>
@endif
@endif

@endsection
