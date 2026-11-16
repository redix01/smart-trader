@extends('dashboard.layout.app')

@section('content')
<style>
/* segmented tabs: showTab() toggles text-blue-400 on the active one */
.tab-button.text-blue-400{background:#111b3e;color:#fff!important}
</style>
@php
    $allMinings = $activeMinings->concat($completedMinings);
    $totalMined = $activeMinings->sum('total_mined') + $completedMinings->sum('total_mined');
    $totalInvested = $activeMinings->sum('amount_invested') + $completedMinings->sum('amount_invested');
    $totalValue = $activeMinings->sum('current_value') + $completedMinings->sum('current_value');
    $totalPnL = $totalValue - $totalInvested;
    $badge = fn ($status) => match ($status) {
        'active' => 'bg-emerald-500/15 text-emerald-400',
        'completed' => 'bg-blue-500/15 text-blue-300',
        'cancelled' => 'bg-red-500/15 text-red-400',
        'suspended' => 'bg-amber-500/15 text-amber-300',
        default => 'bg-gray-700 text-gray-300',
    };
    $check = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l4 4 10-10"/></svg>';
@endphp
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Earn</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Mining</h1>
            <p class="text-sm text-gray-400 mt-0.5">Rent mining power by the month and track what it earns.</p>
        </div>
        <a href="{{ route('user.mining.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            Start mining
        </a>
    </div>

    <!-- Tabs -->
    <nav class="grid grid-cols-2 gap-1 p-1 rounded-lg bg-gray-800 border border-gray-700 sm:w-72" role="tablist">
        <button type="button" onclick="showTab('mining')" id="miningTab" class="tab-button border-blue-500 text-blue-400 rounded-md py-2 text-sm font-semibold">
            Plans
        </button>
        <button type="button" onclick="showTab('statistics')" id="statisticsTab" class="tab-button border-transparent text-gray-400 hover:text-white rounded-md py-2 text-sm font-semibold">
            My mining
        </button>
    </nav>

    <!-- Mining Tab Content -->
    <div id="miningContent" class="tab-content">
        @if($miningPlans->count() > 0)
            @php $popular = $miningPlans->count() >= 3 ? intdiv($miningPlans->count(), 2) : ($miningPlans->count() === 2 ? 1 : -1); @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                @foreach($miningPlans as $plan)
                    @php $featured = $loop->index === $popular; @endphp
                    <div class="flex flex-col rounded-2xl border p-5 sm:p-6 {{ $featured ? 'pc-card pc-card--accent border-blue-500/40' : 'border-gray-700 bg-gray-800' }}">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-base font-semibold text-white">{{ $plan->name }}</h3>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($featured)
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-500 text-white">Popular</span>
                                @endif
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">{{ ucfirst($plan->type) }}</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-400">Monthly rental</div>
                            <div class="flex items-baseline gap-1.5 mt-0.5 min-w-0">
                                <span class="text-3xl sm:text-4xl font-bold text-white font-mono tracking-tight truncate">{{ auth()->user()->formatAmount($plan->price) }}</span>
                                <span class="text-sm font-semibold {{ $featured ? 'text-blue-200' : 'text-gold-400' }}">/ mo</span>
                            </div>
                        </div>
                        <dl class="grid grid-cols-2 gap-x-3 gap-y-3 mt-5 mb-5 rounded-xl p-3 text-sm {{ $featured ? 'bg-black/25' : 'bg-gray-900' }}">
                            <div class="min-w-0"><dt class="text-[11px] text-gray-400">Hashrate</dt><dd class="text-white font-semibold truncate mt-0.5">{{ $plan->hashrate ?? 'N/A' }}</dd></div>
                            <div class="min-w-0"><dt class="text-[11px] text-gray-400">Uptime</dt><dd class="text-white font-semibold truncate mt-0.5">{{ $plan->downtime ?? 'N/A' }}</dd></div>
                            <div class="min-w-0 col-span-2"><dt class="text-[11px] text-gray-400">Equipment</dt><dd class="text-white font-semibold truncate mt-0.5">{{ $plan->equipment ?? 'N/A' }}</dd></div>
                            <div class="min-w-0 col-span-2"><dt class="text-[11px] text-gray-400">Electricity</dt><dd class="text-white font-semibold truncate mt-0.5">{{ $plan->electricity_costs ?? 'N/A' }}</dd></div>
                        </dl>
                        <a href="{{ route('user.mining.create', ['plan' => $plan->id]) }}" class="mt-auto w-full inline-flex items-center justify-center px-4 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            Start mining
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-gray-700 bg-gray-800 p-8 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 9h6v6H9z"/></svg>
                </div>
                <h3 class="text-white font-medium mt-3">No mining plans available</h3>
                <p class="text-sm text-gray-400 mt-1">Check back later for new mining plans.</p>
            </div>
        @endif
    </div>

    <!-- Statistics Tab Content -->
    <div id="statisticsContent" class="tab-content hidden">
        <div class="space-y-4 sm:space-y-6">
            <!-- Performance Overview -->
            <div class="rounded-2xl border border-gray-700 bg-gray-800 grid grid-cols-2 lg:grid-cols-4 overflow-hidden">
                <div class="p-4 sm:p-5 min-w-0">
                    <div class="text-xs text-gray-400">Total mining</div>
                    <div class="text-lg sm:text-2xl font-semibold text-white mt-1">{{ $allMinings->count() }}</div>
                    <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $activeMinings->count() }} active · {{ $completedMinings->count() }} completed</div>
                </div>
                <div class="p-4 sm:p-5 min-w-0 border-l border-gray-700">
                    <div class="text-xs text-gray-400">Total mined</div>
                    <div class="text-lg sm:text-2xl font-semibold text-emerald-400 font-mono mt-1 truncate">{{ money_from($totalMined) }}</div>
                </div>
                <div class="p-4 sm:p-5 min-w-0 border-t lg:border-t-0 lg:border-l border-gray-700">
                    <div class="text-xs text-gray-400">Total invested</div>
                    <div class="text-lg sm:text-2xl font-semibold text-white font-mono mt-1 truncate">{{ money_from($totalInvested) }}</div>
                </div>
                <div class="p-4 sm:p-5 min-w-0 border-t border-l lg:border-t-0 border-gray-700">
                    <div class="text-xs text-gray-400">Current value</div>
                    <div class="text-lg sm:text-2xl font-semibold text-white font-mono mt-1 truncate">{{ money_from($totalValue) }}</div>
                    <div class="text-xs font-mono mt-0.5 truncate {{ $totalPnL >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ ($totalPnL >= 0 ? '+' : '') . money_from($totalPnL) }} P&amp;L</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Mining History -->
                <div class="lg:col-span-2 min-w-0 rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
                    <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-700">
                        <h2 class="text-base font-semibold text-white">Mining history</h2>
                    </div>
                    @if($allMinings->count() > 0)
                        <!-- Phones: cards -->
                        <ul class="md:hidden divide-y divide-gray-700">
                            @foreach($allMinings as $mining)
                                <li>
                                    <a href="{{ route('user.mining.show', $mining) }}" class="block p-4 hover:bg-gray-700/40">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-white truncate">{{ $mining->plan->name }}</div>
                                                <div class="text-xs text-gray-400 mt-0.5">Started {{ $mining->start_date->format('M d, Y') }}</div>
                                            </div>
                                            <span class="shrink-0 px-2 py-0.5 rounded-md text-xs font-semibold {{ $badge($mining->status) }}">{{ ucfirst($mining->status) }}</span>
                                        </div>
                                        <dl class="grid grid-cols-3 gap-2 mt-3 text-sm">
                                            <div class="min-w-0"><dt class="text-xs text-gray-400">Invested</dt><dd class="text-white font-mono truncate">{{ money_from($mining->amount_invested, $mining->currency ?: 'USD') }}</dd></div>
                                            <div class="min-w-0"><dt class="text-xs text-gray-400">Mined</dt><dd class="text-white font-mono truncate">{{ money_from($mining->total_mined, $mining->currency ?: 'USD') }}</dd></div>
                                            <div class="min-w-0"><dt class="text-xs text-gray-400">Value</dt><dd class="text-white font-mono truncate">{{ money_from($mining->current_value, $mining->currency ?: 'USD') }}</dd></div>
                                        </dl>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Desktop: table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-900/50">
                                    <tr>
                                        @foreach(['Plan', 'Status', 'Invested', 'Mined', 'Value', 'Start date'] as $col)
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    @foreach($allMinings as $mining)
                                        <tr class="hover:bg-gray-700/40">
                                            <td class="px-4 py-3 whitespace-nowrap font-medium"><a href="{{ route('user.mining.show', $mining) }}" class="text-white hover:text-gold-400">{{ $mining->plan->name }}</a></td>
                                            <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $badge($mining->status) }}">{{ ucfirst($mining->status) }}</span></td>
                                            <td class="px-4 py-3 whitespace-nowrap text-white font-mono">{{ money_from($mining->amount_invested, $mining->currency ?: 'USD') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-white font-mono">{{ money_from($mining->total_mined, $mining->currency ?: 'USD') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-white font-mono">{{ money_from($mining->current_value, $mining->currency ?: 'USD') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-400">{{ $mining->start_date->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-4 py-10 text-center">
                            <div class="w-12 h-12 rounded-full bg-gray-700 grid place-items-center mx-auto">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 9h6v6H9z"/></svg>
                            </div>
                            <h3 class="text-white font-medium mt-3">No mining yet</h3>
                            <p class="text-sm text-gray-400 mt-1">Pick a plan to start mining. Your contracts will appear here.</p>
                            <a href="{{ route('user.mining.create') }}" class="mt-4 inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Start mining</a>
                        </div>
                    @endif
                </div>

                <!-- Performance Tips -->
                <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 h-fit">
                    <h2 class="text-base font-semibold text-white">Performance tips</h2>
                    <ul class="space-y-3 mt-4">
                        @foreach([
                            ['Track your ROI', 'Monitor your return on investment regularly.'],
                            ['Optimize timing', 'Start mining during favorable market conditions.'],
                            ['Diversify', 'Spread investments across different mining plans.'],
                        ] as [$title, $text])
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-blue-600/15 text-gold-400 grid place-items-center shrink-0">{!! $check !!}</span>
                                <div>
                                    <h4 class="text-sm font-medium text-white">{{ $title }}</h4>
                                    <p class="text-sm text-gray-400">{{ $text }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-400');
        button.classList.add('border-transparent', 'text-gray-400');
    });

    // Show selected tab content
    if (tabName === 'mining') {
        document.getElementById('miningContent').classList.remove('hidden');
        document.getElementById('miningTab').classList.add('border-blue-500', 'text-blue-400');
        document.getElementById('miningTab').classList.remove('border-transparent', 'text-gray-400');
    } else if (tabName === 'statistics') {
        document.getElementById('statisticsContent').classList.remove('hidden');
        document.getElementById('statisticsTab').classList.add('border-blue-500', 'text-blue-400');
        document.getElementById('statisticsTab').classList.remove('border-transparent', 'text-gray-400');
    }
}
</script>
@endsection
