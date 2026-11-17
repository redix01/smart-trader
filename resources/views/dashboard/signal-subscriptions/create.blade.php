@extends('dashboard.layout.app')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Signals</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Subscribe to a signal plan</h1>
            <p class="text-sm text-gray-400 mt-0.5">Pick a plan; the price is paid from your wallet balance.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.signal-subscriptions.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">My subscriptions</a>
        </div>
    </div>

    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Your balances</h2>
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <div class="text-xs text-gray-400">Wallet (used to pay)</div>
                <div class="text-lg sm:text-xl font-semibold text-white mt-1 truncate">{{ $user->formatAmount($user->balance) }}</div>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                <div class="text-xs text-gray-400">Trading</div>
                <div class="text-lg sm:text-xl font-semibold text-white mt-1 truncate">{{ $user->formatAmount($user->trading_balance) }}</div>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4 col-span-2 sm:col-span-1">
                <div class="text-xs text-gray-400">Total</div>
                <div class="text-lg sm:text-xl font-semibold text-white mt-1 truncate">{{ $user->formatAmount($user->getTotalBalanceAttribute()) }}</div>
            </div>
        </div>
    </section>

    @if($signalPlans->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($signalPlans as $plan)
                <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 flex flex-col">
                    <h3 class="text-base font-semibold text-white">{{ $plan->name }}</h3>
                    @if($plan->description)
                        <p class="text-sm text-gray-400 mt-1">{{ $plan->description }}</p>
                    @endif
                    <div class="text-2xl sm:text-3xl font-bold text-white mt-4">{{ money_from($plan->price, $plan->currency, 2) }}</div>
                    <dl class="mt-4 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3">
                            <dt class="text-xs text-gray-400">Signals</dt>
                            <dd class="text-white font-semibold mt-0.5">{{ $plan->signal_quantity ?? 'N/A' }}</dd>
                        </div>
                        <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3">
                            <dt class="text-xs text-gray-400">Days</dt>
                            <dd class="text-white font-semibold mt-0.5">{{ $plan->signal_duration ?? 'N/A' }}</dd>
                        </div>
                        <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3">
                            <dt class="text-xs text-gray-400">Success</dt>
                            <dd class="text-emerald-400 font-semibold mt-0.5">{{ number_format($plan->success_rate ?? 0, 1) }}%</dd>
                        </div>
                    </dl>
                    <div class="mt-auto pt-5">
                        @if($user->balance >= $plan->price)
                            <button onclick="openSubscribeModal({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price }})"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                Subscribe
                            </button>
                        @else
                            <p class="text-xs text-amber-300 mb-2">Your wallet balance is too low for this plan.</p>
                            <a href="{{ route('user.deposit') }}"
                               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                                Fund account
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <section class="rounded-2xl border border-gray-700 bg-gray-800 px-4 py-12 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-gray-700 grid place-items-center text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg>
            </div>
            <h3 class="text-white font-medium mt-3">No signal plans available</h3>
            <p class="text-sm text-gray-400 mt-1">Check back soon — new plans are added regularly.</p>
        </section>
    @endif
</div>

<!-- Subscribe Modal -->
<div id="subscribeModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4" onclick="if (event.target === this) closeSubscribeModal()">
        <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
                <h3 class="text-base font-semibold text-white">Confirm subscription</h3>
            </div>
            <form id="subscribeForm" action="{{ route('user.signal-subscriptions.store') }}" method="POST" class="p-4 sm:p-6 space-y-4">
                @csrf
                <input type="hidden" id="modal_plan_id" name="plan_id">
                <input type="hidden" id="modal_amount" name="amount">

                <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                    <div class="text-xs text-gray-400">Plan</div>
                    <p class="text-white font-medium mt-0.5" id="modal_plan_name"></p>
                    <div class="text-xs text-gray-400 mt-3">Amount to pay</div>
                    <p class="text-2xl font-bold text-white mt-0.5" id="modal_amount_display"></p>
                </div>
                <p class="text-xs text-gray-500">The amount is deducted from your wallet balance.</p>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeSubscribeModal()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSubscribeModal(planId, planName, amount) {
    document.getElementById('modal_plan_id').value = planId;
    document.getElementById('modal_amount').value = amount;
    document.getElementById('modal_plan_name').textContent = planName;
    document.getElementById('modal_amount_display').textContent = CURRENCY_SYMBOL + amount.toFixed(2);
    document.getElementById('subscribeModal').classList.remove('hidden');
}

function closeSubscribeModal() {
    document.getElementById('subscribeModal').classList.add('hidden');
}
</script>
@endsection
