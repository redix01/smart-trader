@extends('dashboard.layout.app')

@section('content')
@php
    $statusCls = match ($staking->status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'completed' => 'bg-blue-500/15 text-blue-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        default => 'bg-gray-700 text-gray-300',
    };
    $card = 'rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6';
    $row = 'flex justify-between gap-4 py-2.5';
    $cur = $staking->currency ?: 'USD';
    $currentValue = $staking->current_value ?? $staking->calculateCurrentValue();
    $unpaidRewards = app(\App\Services\EarnService::class)->unpaidRewards($staking);
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Staking</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1 flex flex-wrap items-center gap-2">
                {{ $staking->plan->name }}
                <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusCls }}">{{ ucfirst($staking->status) }}</span>
            </h1>
            <p class="text-sm text-gray-400 mt-0.5">Your stake, its rewards and key dates.</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('user.staking.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>Staking
            </a>
            @if($staking->isActive())
                <form method="POST" action="{{ route('user.staking.withdraw', $staking) }}" class="flex-1 sm:flex-none">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        Withdraw
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Key figures -->
    <div class="rounded-2xl border border-gray-700 bg-gray-800 grid grid-cols-3 divide-x divide-gray-700">
        <div class="p-3 sm:p-5 min-w-0">
            <div class="text-[11px] sm:text-xs text-gray-400">Staked</div>
            <div class="text-base sm:text-2xl font-semibold text-white font-mono mt-1 truncate">{{ money_from($staking->amount_staked, $cur) }}</div>
        </div>
        <div class="p-3 sm:p-5 min-w-0">
            <div class="text-[11px] sm:text-xs text-gray-400">Current value</div>
            <div class="text-base sm:text-2xl font-semibold text-white font-mono mt-1 truncate">{{ money_from($currentValue, $cur) }}</div>
        </div>
        <div class="p-3 sm:p-5 min-w-0">
            <div class="text-[11px] sm:text-xs text-gray-400 truncate">Rewards paid <span class="hidden sm:inline">· {{ $staking->formatted_apy_rate }} APY</span></div>
            <div class="text-base sm:text-2xl font-semibold text-emerald-400 font-mono mt-1 truncate">{{ money_from($staking->total_rewards, $cur) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Staking Information -->
        <div class="space-y-4 sm:space-y-6">
            <div class="{{ $card }}">
                <h2 class="text-base font-semibold text-white">Staking information</h2>
                <dl class="mt-3 divide-y divide-gray-700 text-sm">
                    <div class="{{ $row }}"><dt class="text-gray-400">Plan</dt><dd class="text-white font-medium text-right">{{ $staking->plan->name }}</dd></div>
                    <div class="{{ $row }}"><dt class="text-gray-400">Currency</dt><dd class="text-white text-right">{{ $staking->currency }}</dd></div>
                    <div class="{{ $row }}"><dt class="text-gray-400">Status</dt><dd><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusCls }}">{{ ucfirst($staking->status) }}</span></dd></div>
                    <div class="{{ $row }}"><dt class="text-gray-400">APY</dt><dd class="text-emerald-400 font-semibold font-mono text-right">{{ $staking->formatted_apy_rate }}</dd></div>
                    <div class="{{ $row }}"><dt class="text-gray-400">Amount staked</dt><dd class="text-white font-mono text-right">{{ money_from($staking->amount_staked, $cur) }}</dd></div>
                    <div class="{{ $row }}"><dt class="text-gray-400">Current value</dt><dd class="text-white font-mono text-right">{{ money_from($currentValue, $cur) }}</dd></div>
                    <div class="{{ $row }}"><dt class="text-gray-400">Ready to withdraw</dt><dd class="text-white font-mono text-right">{{ money_from($unpaidRewards, $cur) }}</dd></div>
                    <div class="{{ $row }}"><dt class="text-gray-400">Rewards paid out</dt><dd class="text-emerald-400 font-mono text-right">{{ money_from($staking->total_rewards, $cur) }}</dd></div>
                    @if($staking->last_reward_date)
                        <div class="{{ $row }}"><dt class="text-gray-400">Last reward</dt><dd class="text-white text-right">{{ $staking->last_reward_date->format('M d, Y H:i') }}</dd></div>
                    @endif
                </dl>
            </div>

            <!-- Actions -->
            @if($staking->isActive())
                <div class="{{ $card }}">
                    <h2 class="text-base font-semibold text-white">Actions</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                        <form method="POST" action="{{ route('user.staking.withdraw', $staking) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                                Withdraw rewards
                            </button>
                        </form>

                        <form method="POST" action="{{ route('user.staking.cancel', $staking) }}" data-confirm="End this stake? Your principal and any unpaid rewards go back to your main balance." data-confirm-tone="danger">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/10 text-sm font-semibold">
                                Cancel staking
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Timeline and Details -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Timeline -->
            <div class="{{ $card }}">
                <h2 class="text-base font-semibold text-white">Timeline</h2>
                <ol class="mt-4 space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-full bg-emerald-500/15 text-emerald-400 grid place-items-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-medium text-white">Staking started</h4>
                            <p class="text-sm text-gray-400">{{ $staking->start_date->format('M d, Y H:i') }}</p>
                        </div>
                    </li>
                    @if($staking->end_date)
                        <li class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-full bg-blue-500/15 text-blue-300 grid place-items-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-medium text-white">Staking ends</h4>
                                <p class="text-sm text-gray-400">{{ $staking->end_date->format('M d, Y H:i') }}</p>
                                @if($staking->isExpiringSoon())
                                    <p class="text-xs text-amber-300 mt-0.5">Ending soon</p>
                                @endif
                            </div>
                        </li>
                    @endif
                    @if($staking->last_reward_date)
                        <li class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-full bg-amber-500/15 text-amber-300 grid place-items-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h3.75a1.75 1.75 0 010 3.5h-2.5a1.75 1.75 0 000 3.5H14.5"/></svg>
                            </span>
                            <div>
                                <h4 class="text-sm font-medium text-white">Last reward</h4>
                                <p class="text-sm text-gray-400">{{ $staking->last_reward_date->format('M d, Y H:i') }}</p>
                            </div>
                        </li>
                    @endif
                </ol>
            </div>

            <!-- Plan Details -->
            <div class="{{ $card }}">
                <h2 class="text-base font-semibold text-white">Plan details</h2>
                <dl class="mt-3 divide-y divide-gray-700 text-sm">
                    @if($staking->plan->minimum_amount)
                        <div class="{{ $row }}"><dt class="text-gray-400">Minimum</dt><dd class="text-white font-mono text-right">{{ money_from($staking->plan->minimum_amount, $staking->plan->staking_currency ?: $cur) }}</dd></div>
                    @endif
                    @if($staking->plan->lock_period)
                        <div class="{{ $row }}"><dt class="text-gray-400">Lock period</dt><dd class="text-white text-right">{{ $staking->plan->lock_period }} days</dd></div>
                    @endif
                    @if($staking->plan->reward_frequency)
                        <div class="{{ $row }}"><dt class="text-gray-400">Reward frequency</dt><dd class="text-white text-right">{{ ucfirst($staking->plan->reward_frequency) }}</dd></div>
                    @endif
                    @if($staking->plan->staking_duration)
                        <div class="{{ $row }}"><dt class="text-gray-400">Duration</dt><dd class="text-white text-right">{{ $staking->plan->staking_duration }} days</dd></div>
                    @endif
                    @if($staking->plan->description)
                        <div class="py-2.5"><dt class="text-gray-400">Description</dt><dd class="text-gray-300 mt-1">{{ $staking->plan->description }}</dd></div>
                    @endif
                </dl>
            </div>

            <!-- Notes -->
            @if($staking->notes)
                <div class="{{ $card }}">
                    <h2 class="text-base font-semibold text-white">Notes</h2>
                    <p class="text-sm text-gray-300 mt-2">{{ $staking->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
