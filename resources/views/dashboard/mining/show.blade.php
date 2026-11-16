@extends('dashboard.layout.app')

@section('content')
@php
    $statusCls = match ($mining->status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'completed' => 'bg-blue-500/15 text-blue-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        'suspended' => 'bg-amber-500/15 text-amber-300',
        default => 'bg-gray-700 text-gray-300',
    };
    $unpaidMined = app(\App\Services\EarnService::class)->unpaidMined($mining);
    $canWithdraw = $unpaidMined > 0;
    $canCancel = $mining->isActive() && $mining->canBeCancelled();
    $row = 'flex justify-between gap-4 py-2.5';
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Mining</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1 flex flex-wrap items-center gap-2">
                {{ $mining->plan->name }}
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusCls }}">{{ ucfirst($mining->status) }}</span>
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">Your mining contract, its output and progress.</p>
        </div>
        <a href="{{ route('user.mining.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg border border-gray-700 bg-gray-800 text-sm font-semibold text-gray-200 hover:text-white hover:border-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>Mining
        </a>
    </div>

    <!-- Key figures -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Amount invested</div>
            <div class="text-lg sm:text-xl font-semibold text-white font-mono mt-1 break-words">{{ $mining->formatted_amount_invested }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Total mined</div>
            <div class="text-lg sm:text-xl font-semibold text-emerald-400 font-mono mt-1 break-words">{{ $mining->formatted_total_mined }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">ROI</div>
            <div class="text-lg sm:text-xl font-semibold text-white font-mono mt-1">{{ $mining->roi_percentage }}%</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6 min-w-0">
            <!-- Mining Progress -->
            @if($mining->isActive())
                <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-white">Progress</h2>
                        <span class="text-sm font-mono text-white">{{ $mining->mining_progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2 mt-3 overflow-hidden">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mining->mining_progress }}%"></div>
                    </div>
                    @if($mining->end_date)
                        <p class="text-sm text-gray-400 mt-2">Ends {{ $mining->end_date->diffForHumans() }}</p>
                    @endif
                </div>
            @endif

            <!-- Mining Information -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-white">Mining information</h2>
                <dl class="mt-3 grid grid-cols-1 md:grid-cols-2 md:gap-x-8 text-sm">
                    <div class="divide-y divide-gray-700">
                        <div class="{{ $row }}"><dt class="text-gray-400">Plan</dt><dd class="text-white font-medium text-right">{{ $mining->plan->name }}</dd></div>
                        <div class="{{ $row }}"><dt class="text-gray-400">Status</dt><dd><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusCls }}">{{ ucfirst($mining->status) }}</span></dd></div>
                        <div class="{{ $row }}"><dt class="text-gray-400">Hashrate</dt><dd class="text-white font-semibold text-right">{{ $mining->hashrate }}</dd></div>
                        <div class="{{ $row }}"><dt class="text-gray-400">Equipment</dt><dd class="text-white text-right">{{ $mining->equipment }}</dd></div>
                    </div>
                    <div class="divide-y divide-gray-700 border-t border-gray-700 md:border-t-0">
                        <div class="{{ $row }}"><dt class="text-gray-400">Amount invested</dt><dd class="text-white font-mono text-right">{{ $mining->formatted_amount_invested }}</dd></div>
                        <div class="{{ $row }}"><dt class="text-gray-400">Total mined</dt><dd class="text-emerald-400 font-mono text-right">{{ $mining->formatted_total_mined }}</dd></div>
                        <div class="{{ $row }}"><dt class="text-gray-400">Ready to withdraw</dt><dd class="text-white font-mono text-right">{{ money_from($unpaidMined, $mining->currency ?: 'USD') }}</dd></div>
                        <div class="{{ $row }}"><dt class="text-gray-400">ROI</dt><dd class="text-white font-mono text-right">{{ $mining->roi_percentage }}%</dd></div>
                        <div class="{{ $row }}"><dt class="text-gray-400">Started</dt><dd class="text-white text-right">{{ $mining->start_date->format('M d, Y') }}</dd></div>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Actions -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                <h2 class="text-base font-semibold text-white">Actions</h2>
                <div class="space-y-2 mt-4">
                    @if($canWithdraw)
                        <form method="POST" action="{{ route('user.mining.withdraw', $mining) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                Withdraw mined crypto
                            </button>
                        </form>
                    @endif

                    @if($canCancel)
                        <form method="POST" action="{{ route('user.mining.cancel', $mining) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/10 text-sm font-semibold">
                                Cancel mining
                            </button>
                        </form>
                    @endif

                    @unless($canWithdraw || $canCancel)
                        <p class="text-sm text-gray-400">No actions available for this contract right now.</p>
                    @endunless
                </div>
            </div>

            <!-- Notes -->
            @if($mining->notes)
                <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
                    <h2 class="text-base font-semibold text-white">Notes</h2>
                    <p class="text-sm text-gray-300 mt-2">{{ $mining->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
