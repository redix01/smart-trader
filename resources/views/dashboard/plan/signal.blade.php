@extends('dashboard.layout.app')

@section('content')
@php
    $check = '<svg class="w-4 h-4 text-gold-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>';
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Plans</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Signal plans</h1>
            <p class="text-sm text-gray-400 mt-0.5">Subscribe to trade signals from our analysts, delivered to your account.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('user.plans.my-plans') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">My plans</a>
        </div>
    </div>

    <!-- Signal Plans Grid -->
    @if($signalPlans->count() > 0)
        @php $popular = $signalPlans->count() >= 3 ? intdiv($signalPlans->count(), 2) : ($signalPlans->count() === 2 ? 1 : -1); @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
            @foreach($signalPlans as $plan)
                @php $featured = $loop->index === $popular; $tile = $featured ? 'bg-black/25' : 'bg-gray-900'; @endphp
                <div class="flex flex-col rounded-2xl border p-5 sm:p-6 {{ $featured ? 'pc-card pc-card--accent border-blue-500/40' : 'border-gray-700 bg-gray-800' }}">
                    <!-- Plan Header -->
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-base font-semibold text-white">{{ $plan->name }}</h3>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($featured)
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-500 text-white">Popular</span>
                            @endif
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ ucfirst($plan->signal_market_type) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2 min-w-0">
                        <span class="text-3xl sm:text-4xl font-bold text-white font-mono tracking-tight truncate">{{ money_from($plan->price, $plan->currency ?: 'USD', 2) }}</span>
                        @if($plan->has_discount)
                            <span class="text-sm text-gray-500 line-through font-mono">{{ money_from($plan->original_price, $plan->currency ?: 'USD', 2) }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-400 mt-1">{{ $plan->signal_quantity }} signals · {{ $plan->signal_duration }} days</p>

                    <!-- Key terms -->
                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <div class="rounded-xl {{ $tile }} px-3 py-2.5">
                            <div class="text-xs text-gray-400">Success rate</div>
                            <div class="text-sm font-semibold text-white font-mono">{{ number_format($plan->success_rate, 1) }}%</div>
                        </div>
                        <div class="rounded-xl {{ $tile }} px-3 py-2.5">
                            <div class="text-xs text-gray-400">Signal strength</div>
                            <div class="flex text-amber-400 mt-0.5" aria-label="{{ $plan->signal_strength }} of 5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" @if($i <= $plan->signal_strength) fill="currentColor" @else fill="none" stroke="currentColor" @endif><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <ul class="space-y-2 mt-4 mb-5 text-sm text-gray-300">
                        @if($plan->daily_signals)
                            <li class="flex items-start gap-2">{!! $check !!}<span>{{ $plan->daily_signals }} signals per day</span></li>
                        @endif
                        @if($plan->max_daily_signals)
                            <li class="flex items-start gap-2">{!! $check !!}<span>Up to {{ $plan->max_daily_signals }} per day</span></li>
                        @endif
                        @if($plan->signal_features && is_array($plan->signal_features))
                            @foreach(array_slice($plan->signal_features, 0, 3) as $feature)
                                <li class="flex items-start gap-2">{!! $check !!}<span>{{ $feature }}</span></li>
                            @endforeach
                            @if(count($plan->signal_features) > 3)
                                <li class="text-gray-500 pl-6">+{{ count($plan->signal_features) - 3 }} more features</li>
                            @endif
                        @endif
                    </ul>

                    <!-- Subscribe Button -->
                    <button type="button" onclick="openSubscribeModal({{ $plan->id }}, @js($plan->name), {{ $plan->price }})" class="mt-auto w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        Subscribe
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Plans Available -->
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-8 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-white font-medium mt-3">No signal plans available</h3>
            <p class="text-sm text-gray-400 mt-1">New signal plans are added regularly. Check back soon.</p>
        </div>
    @endif
</div>

<!-- Subscribe Modal -->
<div id="subscribeModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm">
    <div class="flex items-end sm:items-center justify-center min-h-full sm:p-4" onclick="if (event.target === this) closeSubscribeModal()">
        <div class="w-full max-w-md rounded-t-2xl sm:rounded-2xl bg-gray-800 border border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="flex justify-between items-center px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Subscribe to signal plan</h3>
                <button type="button" onclick="closeSubscribeModal()" class="w-10 h-10 -mr-2 grid place-items-center text-gray-400 hover:text-white" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="subscribeForm" action="{{ route('user.signal-subscriptions.store') }}" method="POST" class="p-4 sm:p-6 space-y-4">
                @csrf
                <input type="hidden" id="modal_plan_id" name="plan_id">

                <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-700 bg-gray-900/50 p-4">
                    <div class="min-w-0">
                        <div class="text-xs text-gray-400">Plan</div>
                        <div class="text-white font-semibold truncate" id="modal_plan_name"></div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400">Price</div>
                        <div class="text-white font-semibold font-mono" id="modal_plan_price"></div>
                    </div>
                </div>

                <div>
                    <label for="modal_amount" class="block text-sm font-medium text-gray-300 mb-1.5">Amount to pay</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-mono">{{ currency_symbol() }}</span>
                        <input type="number" id="modal_amount" name="amount" step="0.01" min="0" required inputmode="decimal" class="w-full pl-8 pr-3 py-2.5 rounded-lg bg-gray-900/60 border border-gray-600 text-white font-mono placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeSubscribeModal()" class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="flex-[2] inline-flex items-center justify-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        Subscribe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSubscribeModal(planId, planName, planPrice) {
    document.getElementById('modal_plan_id').value = planId;
    document.getElementById('modal_plan_name').textContent = planName;
    document.getElementById('modal_plan_price').textContent = CURRENCY_SYMBOL + planPrice.toFixed(2);
    document.getElementById('modal_amount').value = planPrice;
    document.getElementById('subscribeModal').classList.remove('hidden');
}

function closeSubscribeModal() {
    document.getElementById('subscribeModal').classList.add('hidden');
}
</script>
@endsection
