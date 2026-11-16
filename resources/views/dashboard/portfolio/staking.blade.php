@extends('dashboard.layout.app')

@section('content')
@php
    $statusBadge = fn ($status) => match ($status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'completed' => 'bg-blue-500/15 text-blue-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        default => 'bg-amber-500/15 text-amber-300',
    };
@endphp
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Portfolio</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Staking</h1>
            <p class="text-sm text-gray-400 mt-0.5">Funds you have locked to earn rewards, and what they are worth now.</p>
        </div>
        <a href="{{ route('user.staking.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            Start staking
        </a>
    </div>

    @include('dashboard.portfolio.partials.footer-menu')

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total staked</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white font-mono break-all">{{ money($totalStaked, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Active stakes</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-emerald-400">{{ $activeStakings }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Total rewards</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-emerald-400 font-mono break-all">{{ money($totalRewards, 2) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
            <div class="text-xs text-gray-400">Current value</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold text-white font-mono break-all">{{ money($currentValue, 2) }}</div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-700">
            <h2 class="text-base font-semibold text-white">Staking activity</h2>
        </div>
        @if($stakings->count() > 0)
            <ul class="md:hidden divide-y divide-gray-700">
                @foreach($stakings as $staking)
                    <li class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-white truncate">{{ $staking->plan->name }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusBadge($staking->status) }}">{{ ucfirst($staking->status) }}</span>
                                    <span class="text-xs text-emerald-400 font-semibold">{{ $staking->apy_rate }}% APY</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-semibold text-white font-mono">{{ money($staking->amount_staked, 2) }}</div>
                                <div class="text-xs text-gray-400">staked</div>
                            </div>
                        </div>
                        <dl class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div class="rounded-lg bg-gray-900/50 px-3 py-2"><dt class="text-gray-400">Rewards</dt><dd class="mt-0.5 font-mono text-emerald-400">{{ money($staking->total_rewards, 2) }}</dd></div>
                            <div class="rounded-lg bg-gray-900/50 px-3 py-2"><dt class="text-gray-400">Current value</dt><dd class="mt-0.5 font-mono text-white">{{ money($staking->current_value, 2) }}</dd></div>
                        </dl>
                        <a href="{{ route('user.staking.show', $staking) }}" class="mt-2 inline-flex items-center min-h-[40px] text-sm font-semibold text-gold-400 hover:text-gold-300">View details</a>
                    </li>
                @endforeach
            </ul>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900/50">
                        <tr class="text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <th class="px-6 py-3">Plan</th>
                            <th class="px-6 py-3">Amount staked</th>
                            <th class="px-6 py-3">APY</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Rewards</th>
                            <th class="px-6 py-3">Current value</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($stakings as $staking)
                            <tr class="hover:bg-gray-700/40">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $staking->plan->name }}</div>
                                    <div class="text-xs text-gray-400 max-w-xs truncate">{{ $staking->plan->description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($staking->amount_staked, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-emerald-400">{{ $staking->apy_rate }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusBadge($staking->status) }}">{{ ucfirst($staking->status) }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-emerald-400 font-mono">{{ money($staking->total_rewards, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-white font-mono">{{ money($staking->current_value, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right"><a href="{{ route('user.staking.show', $staking) }}" class="font-semibold text-gold-400 hover:text-gold-300">View details</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-4 py-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v10M9.5 9.5h3.75a1.75 1.75 0 010 3.5h-2.5a1.75 1.75 0 000 3.5H14.5"/></svg>
                </div>
                <div class="mt-3 text-white font-medium">Nothing staked yet</div>
                <p class="mt-1 text-sm text-gray-400">Lock funds in a staking pool to earn a fixed APY.</p>
                <a href="{{ route('user.staking.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Start staking</a>
            </div>
        @endif
    </div>
</div>
@endsection
