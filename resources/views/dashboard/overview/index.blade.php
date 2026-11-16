@extends('dashboard.layout.app')

@section('title', 'Trading Overview')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Trading</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Trading overview</h1>
            <p class="text-sm text-gray-400 mt-0.5">Live, bot and copy trading results in one place.</p>
        </div>
        <a href="{{ route('user.liveTrading.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Open Trade Room</a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Total volume</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold font-mono break-all text-white">{{ auth()->user()->formatAmount($totalTradingVolume) }}</div>
            <div class="text-xs text-gray-500 mt-0.5">All trading activity</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Total P&amp;L</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold font-mono break-all {{ $totalProfitLoss >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $totalProfitLoss >= 0 ? '+' : '' }}{{ auth()->user()->formatAmount($totalProfitLoss) }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $totalProfitLoss >= 0 ? 'Profit' : 'Loss' }}</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Portfolio value</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold font-mono break-all text-white">{{ auth()->user()->formatAmount($totalHoldingsValue) }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $totalAssets }} assets</div>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 min-w-0">
            <div class="text-xs text-gray-400">Active positions</div>
            <div class="mt-1 text-lg sm:text-2xl font-semibold font-mono break-all text-white">{{ $openLiveTrades + $activeBots + $activeCopyTrades }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Trades, bots and copies</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 flex flex-col">
            <h2 class="text-base font-semibold text-white">Live trading</h2>
            <p class="text-xs text-gray-400 mt-0.5">Trades you place yourself.</p>
            <dl class="mt-4 space-y-2.5 text-sm flex-1">
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Open trades</dt><dd class="font-semibold font-mono text-right text-white">{{ $openLiveTrades }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Closed trades</dt><dd class="font-semibold font-mono text-right text-white">{{ $closedLiveTrades }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Total volume</dt><dd class="font-semibold font-mono text-right text-white">{{ auth()->user()->formatAmount($liveTradingVolume) }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Total trades</dt><dd class="font-semibold font-mono text-right text-white">{{ $totalLiveTrades }}</dd></div>
            </dl>
            <a href="{{ route('user.liveTrading.index') }}" class="mt-4 pt-3 border-t border-gray-700 inline-flex items-center gap-1 min-h-[40px] text-sm font-semibold text-gold-400 hover:text-gold-300">Go to live trading &rarr;</a>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 flex flex-col">
            <h2 class="text-base font-semibold text-white">Bot trading</h2>
            <p class="text-xs text-gray-400 mt-0.5">Automated strategies running for you.</p>
            <dl class="mt-4 space-y-2.5 text-sm flex-1">
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Active bots</dt><dd class="font-semibold font-mono text-right text-white">{{ $activeBots }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Total bots</dt><dd class="font-semibold font-mono text-right text-white">{{ $totalBots }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Total profit</dt><dd class="font-semibold font-mono text-right {{ $totalBotProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $totalBotProfit >= 0 ? '+' : '' }}{{ auth()->user()->formatAmount($totalBotProfit) }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Trading volume</dt><dd class="font-semibold font-mono text-right text-white">{{ auth()->user()->formatAmount($botTradingVolume) }}</dd></div>
            </dl>
            <a href="{{ route('user.botTrading.index') }}" class="mt-4 pt-3 border-t border-gray-700 inline-flex items-center gap-1 min-h-[40px] text-sm font-semibold text-gold-400 hover:text-gold-300">Manage bots &rarr;</a>
        </div>
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6 flex flex-col">
            <h2 class="text-base font-semibold text-white">Copy trading</h2>
            <p class="text-xs text-gray-400 mt-0.5">Experienced traders you follow.</p>
            <dl class="mt-4 space-y-2.5 text-sm flex-1">
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Active copies</dt><dd class="font-semibold font-mono text-right text-white">{{ $activeCopyTrades }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Total copies</dt><dd class="font-semibold font-mono text-right text-white">{{ $totalCopyTrades }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Total trades</dt><dd class="font-semibold font-mono text-right text-white">{{ $totalCopyTradeCount }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Win / loss</dt><dd class="font-semibold font-mono text-right text-white"><span class="text-emerald-400">{{ $totalCopyWins }}</span> / <span class="text-red-400">{{ $totalCopyLosses }}</span></dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Total P&amp;L</dt><dd class="font-semibold font-mono text-right {{ $totalCopyPnL >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $totalCopyPnL >= 0 ? '+' : '' }}{{ auth()->user()->formatAmount($totalCopyPnL) }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-gray-400">Trading volume</dt><dd class="font-semibold font-mono text-right text-white">{{ auth()->user()->formatAmount($copyTradingVolume) }}</dd></div>
            </dl>
            <a href="{{ route('user.copyTrading.index') }}" class="mt-4 pt-3 border-t border-gray-700 inline-flex items-center gap-1 min-h-[40px] text-sm font-semibold text-gold-400 hover:text-gold-300">View copy trades &rarr;</a>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-white">Activity count</h2>
        <div class="mt-4 grid grid-cols-3 gap-3">
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3 sm:p-4 text-center">
                <div class="text-xl sm:text-2xl font-semibold text-white">{{ $totalLiveTrades }}</div>
                <div class="text-xs sm:text-sm text-gray-400">Live trades</div>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3 sm:p-4 text-center">
                <div class="text-xl sm:text-2xl font-semibold text-white">{{ $totalBots }}</div>
                <div class="text-xs sm:text-sm text-gray-400">Trading bots</div>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-900/50 p-3 sm:p-4 text-center">
                <div class="text-xl sm:text-2xl font-semibold text-white">{{ $totalCopyTrades }}</div>
                <div class="text-xs sm:text-sm text-gray-400">Copy trades</div>
            </div>
        </div>
    </div>
</div>

@include('dashboard.overview.partials.footer-menu')

@endsection
