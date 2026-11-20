@extends('dashboard.layout.app')
@section('content')

@php
    $user = auth()->user()->fresh();

    // Balances (all shown in the user's currency via formatAmount)
    $walletBalance = $user->balance ?? 0;
    $tradingBalance = $user->trading_balance ?? 0;
    $holdingsBalance = $user->holding_balance ?? 0;
    $stakingBalance = $user->staking_balance ?? 0;
    $profitBalance = $user->profit ?? 0;
    $depositBalance = $user->deposit_balance ?? 0;
    $totalPortfolio = $walletBalance + $depositBalance + $tradingBalance + $holdingsBalance + $stakingBalance + $profitBalance;

    $balances = [
        ['label' => 'Wallet', 'value' => $walletBalance, 'color' => '#3b82f6', 'href' => route('user.deposit'), 'hint' => 'Available to fund'],
        ['label' => 'Deposits', 'value' => $depositBalance, 'color' => '#22c55e', 'href' => route('user.transactions.index'), 'hint' => 'Credited deposits'],
        ['label' => 'Trading', 'value' => $tradingBalance, 'color' => '#7dd3fc', 'href' => route('user.liveTrading.index'), 'hint' => 'Ready in the Trade Room'],
        ['label' => 'Holdings', 'value' => $holdingsBalance, 'color' => '#2dd4a7', 'href' => route('user.holding.index'), 'hint' => 'Assets you own'],
        ['label' => 'Staking', 'value' => $stakingBalance, 'color' => '#f59e0b', 'href' => route('user.staking.index'), 'hint' => 'Locked and earning'],
        ['label' => 'Profit', 'value' => $profitBalance, 'color' => '#a78bfa', 'href' => route('user.transactions.index'), 'hint' => 'Realised gains'],
    ];

    // Greeting
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $firstName = \Illuminate\Support\Str::of($user->name)->trim()->explode(' ')->first() ?: 'there';

    // Getting-started checklist
    $steps = [
        ['label' => 'Fund your wallet', 'done' => ($walletBalance + $depositBalance + $tradingBalance) > 0, 'href' => route('user.deposit')],
        ['label' => 'Place your first trade', 'done' => $totalTrades > 0, 'href' => route('user.liveTrading.index')],
        ['label' => 'Copy an experienced trader', 'done' => $activeCopyTrades > 0, 'href' => route('user.copyTrading.index')],
        ['label' => 'Activate a plan', 'done' => $totalPlans > 0, 'href' => route('user.plans.index')],
    ];
    $stepsDone = collect($steps)->where('done', true)->count();

    $strength = $user->tradingStrengthSummary();

    $icon = fn ($d, $cls = 'w-5 h-5') => '<svg class="'.$cls.'" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">'.$d.'</svg>';
    $ic = [
        'deposit' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
        'withdraw' => '<circle cx="12" cy="12" r="9"/><path d="M8 12h8"/>',
        'trade' => '<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>',
        'copy' => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M16 4.5a3.5 3.5 0 010 7M22 20a7 7 0 00-4-6.3"/>',
        'stocks' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'crypto' => '<path d="M9 8h4.5a2.5 2.5 0 010 5H9m0-5v10m0-5h5a2.5 2.5 0 010 5H9M11 5v3m0 10v1m2-14v3m0 10v1"/>',
        'check' => '<path d="M5 12l4 4 10-10"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'bot' => '<rect x="5" y="8" width="14" height="12" rx="2"/><path d="M12 4v4M9 14h.01M15 14h.01"/>',
        'plan' => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'wallet' => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18M16 15h2"/>',
    ];
@endphp

<div class="space-y-6 dash-home">

    {{-- ===== Unread messages from the admin team ===== --}}
    @php
        $adminMessages = \App\Models\UserNotification::where('user_id', $user->id)
            ->where('type', \App\Http\Controllers\Admin\UserToolsController::MESSAGE_TYPE)
            ->whereNull('read_at')->latest()->take(3)->get();
    @endphp
    @foreach($adminMessages as $msg)
        <section class="rounded-2xl border border-gold-500/40 bg-gray-800 p-4 sm:p-5 flex gap-3 items-start" data-admin-message="{{ $msg->id }}">
            <span class="w-9 h-9 shrink-0 rounded-lg bg-gold-500/15 text-gold-400 grid place-items-center">{!! $icon('<path d="M4 5h16v11H8l-4 4z"/>', 'w-4 h-4') !!}</span>
            <div class="min-w-0 flex-1">
                <p class="text-xs text-gold-400 font-semibold">Message from support · {{ $msg->created_at->diffForHumans() }}</p>
                <h2 class="text-sm font-semibold text-white mt-0.5">{{ $msg->title }}</h2>
                <p class="text-sm text-gray-300 mt-1 whitespace-pre-line break-words">{{ $msg->message }}</p>
            </div>
            <button type="button" class="shrink-0 text-xs font-semibold text-gray-400 hover:text-white" onclick="fetch('{{ route('user.notifications.read', $msg->id) }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}}).then(()=>this.closest('section').remove())">Dismiss</button>
        </section>
    @endforeach

    {{-- ===== Welcome ===== --}}
    <section class="dash-hero relative overflow-hidden rounded-2xl border border-gray-700 bg-gray-800 p-5 sm:p-8">
        <div class="relative grid gap-8 lg:grid-cols-5 lg:items-center">
            <div class="lg:col-span-3">
                <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">{{ now()->format('l, j F') }}</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mt-2">{{ $greeting }}, {{ $firstName }}</h1>
                <p class="text-gray-400 mt-2 max-w-xl">Here’s where your account stands today. Fund your wallet, trade in the Trade Room, or let a copy trader or AI trader work for you.</p>
                <div class="mt-6 grid grid-cols-2 sm:flex sm:flex-wrap gap-2.5">
                    <a href="{{ route('user.deposit') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">{!! $icon($ic['deposit'], 'w-4 h-4') !!}Deposit</a>
                    <a href="{{ route('user.withdrawal') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold border border-gray-600">{!! $icon($ic['withdraw'], 'w-4 h-4') !!}Withdraw</a>
                    <a href="{{ route('user.liveTrading.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold border border-gray-600">{!! $icon($ic['trade'], 'w-4 h-4') !!}Trade Room</a>
                    <a href="{{ route('user.copyTrading.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold border border-gray-600">{!! $icon($ic['copy'], 'w-4 h-4') !!}Copy a trader</a>
                </div>
            </div>

            <div class="lg:col-span-2">
                @if($stepsDone < count($steps))
                    <div class="rounded-xl border border-gray-700 bg-gray-900/60 p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-white">Get started</h3>
                            <span class="text-xs font-mono text-gray-400">{{ $stepsDone }} / {{ count($steps) }}</span>
                        </div>
                        <div class="mt-3 h-1.5 rounded-full bg-gray-700 overflow-hidden"><div class="h-full rounded-full bg-blue-500" style="width: {{ $stepsDone / count($steps) * 100 }}%"></div></div>
                        <ul class="mt-4 space-y-1">
                            @foreach($steps as $step)
                                <li>
                                    <a href="{{ $step['href'] }}" class="flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-gray-700/60">
                                        @if($step['done'])
                                            <span class="w-6 h-6 rounded-full bg-emerald-500/15 text-emerald-400 grid place-items-center">{!! $icon($ic['check'], 'w-3.5 h-3.5') !!}</span>
                                            <span class="text-sm text-gray-400 line-through">{{ $step['label'] }}</span>
                                        @else
                                            <span class="w-6 h-6 rounded-full border border-gray-600"></span>
                                            <span class="text-sm text-white flex-1">{{ $step['label'] }}</span>
                                            <span class="text-gray-400">{!! $icon($ic['arrow'], 'w-4 h-4') !!}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="rounded-xl border border-gray-700 bg-gray-900/60 p-5">
                        <h3 class="text-sm font-semibold text-white">Account snapshot</h3>
                        <dl class="mt-4 grid grid-cols-2 gap-4">
                            <div><dt class="text-xs text-gray-400">Active plans</dt><dd class="text-xl font-semibold text-white mt-1">{{ $totalPlans }}</dd></div>
                            <div><dt class="text-xs text-gray-400">Copy trades</dt><dd class="text-xl font-semibold text-white mt-1">{{ $activeCopyTrades }}</dd></div>
                            <div><dt class="text-xs text-gray-400">Active bots</dt><dd class="text-xl font-semibold text-white mt-1">{{ $activeBots }}</dd></div>
                            <div><dt class="text-xs text-gray-400">Open trades</dt><dd class="text-xl font-semibold text-white mt-1">{{ $openTrades->count() }}</dd></div>
                        </dl>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ===== Balances ===== --}}
    <section class="rounded-2xl border border-gray-700 bg-gray-800 p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-400 flex items-center gap-2">{!! $icon($ic['wallet'], 'w-4 h-4') !!} Total portfolio value</p>
                <div class="text-3xl sm:text-4xl font-bold text-white mt-1 tracking-tight">{{ $user->formatAmount($totalPortfolio) }}</div>
                <p class="text-xs text-gray-400 mt-1">Wallet, deposits, trading, holdings, staking and profit · updated {{ now()->format('H:i') }}</p>
            </div>
            <a href="{{ route('user.portfolio.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gold-400 hover:text-gold-300">View portfolio {!! $icon($ic['arrow'], 'w-4 h-4') !!}</a>
        </div>

        @if($totalPortfolio > 0)
            <div class="mt-5 flex h-2.5 rounded-full overflow-hidden bg-gray-700 gap-0.5" role="img" aria-label="Portfolio allocation">
                @foreach($balances as $b)
                    @if($b['value'] > 0)<span style="width: {{ $b['value'] / $totalPortfolio * 100 }}%; background: {{ $b['color'] }}"></span>@endif
                @endforeach
            </div>
        @endif

        <div class="mt-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($balances as $b)
                <a href="{{ $b['href'] }}" class="group rounded-xl border border-gray-700 bg-gray-900/50 p-4 hover:border-gold-500 transition-colors">
                    <div class="flex items-center gap-2 text-xs text-gray-400"><span class="w-2 h-2 rounded-full" style="background: {{ $b['color'] }}"></span>{{ $b['label'] }}</div>
                    <div class="text-lg font-semibold text-white mt-2 truncate">{{ $user->formatAmount($b['value']) }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $b['hint'] }}</div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ===== Live markets & Trade Room (every row opens that market in the Trade Room) ===== --}}
    @php
        $tradeUrl = fn ($type, $sym) => route('user.liveTrading.index', ['asset_type' => $type, 'symbol' => $sym]);
        $marketCards = [
            ['key' => 'stock', 'title' => 'Stocks', 'sub' => 'US shares', 'icon' => 'stocks', 'tone' => 'text-sky-300 bg-sky-500/15',
             'rows' => [['AAPL', 'Apple'], ['NVDA', 'Nvidia'], ['MSFT', 'Microsoft'], ['TSLA', 'Tesla']]],
            ['key' => 'crypto', 'title' => 'Crypto', 'sub' => '24h change', 'icon' => 'crypto', 'tone' => 'text-amber-300 bg-amber-500/15',
             'rows' => [['BTC', 'Bitcoin'], ['ETH', 'Ethereum'], ['SOL', 'Solana'], ['XRP', 'XRP']]],
        ];
    @endphp
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="dashMarkets" data-fx="{{ to_user_currency(1) }}">
        @foreach($marketCards as $mc)
            <div class="pc-card rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5 flex flex-col">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 shrink-0 rounded-xl {{ $mc['tone'] }} grid place-items-center">{!! $icon($ic[$mc['icon']]) !!}</span>
                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-white leading-tight">{{ $mc['title'] }}</h2>
                        <p class="text-xs text-gray-400">{{ $mc['sub'] }}</p>
                    </div>
                    <span class="ml-auto inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-300 bg-emerald-500/10 rounded-full px-2 py-0.5" data-live-badge="{{ $mc['key'] }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 pc-pulse"></span>Live
                    </span>
                </div>
                <ul class="mt-4 -mx-2 flex-1">
                    @foreach($mc['rows'] as [$sym, $name])
                        <li>
                            <a href="{{ $tradeUrl($mc['key'], $sym) }}" class="group flex items-center gap-3 rounded-xl px-2 py-2.5 hover:bg-white/[.04]" data-q="{{ $mc['key'] }}:{{ $sym }}">
                                <span class="w-9 h-9 shrink-0 rounded-lg bg-gray-900 border border-gray-700 grid place-items-center text-[10px] font-bold text-gray-200 tracking-wide">{{ $sym }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-white truncate">{{ $name }}</span>
                                    <span class="block text-xs text-gray-500">{{ $sym }}{{ $mc['key'] === 'crypto' ? ' / USDT' : '' }}</span>
                                </span>
                                <span class="text-right">
                                    <span class="block text-sm font-semibold text-white font-mono" data-price>—</span>
                                    <span class="inline-block mt-0.5 text-[11px] font-semibold rounded-md px-1.5 py-0.5 bg-gray-700/60 text-gray-300" data-chg>…</span>
                                </span>
                                <span class="hidden sm:inline-flex text-xs font-semibold text-gold-400 opacity-0 group-hover:opacity-100 transition-opacity">Trade</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('user.liveTrading.index', ['asset_type' => $mc['key']]) }}" class="mt-3 inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-700 bg-gray-900/60 hover:border-gold-500 px-4 py-2.5 text-sm font-semibold text-white">
                    Trade {{ strtolower($mc['title']) }} {!! $icon($ic['arrow'], 'w-4 h-4') !!}
                </a>
            </div>
        @endforeach

        <div class="pc-card pc-card--accent rounded-2xl border border-blue-500/40 p-5 sm:p-6 flex flex-col md:col-span-2 xl:col-span-1 relative overflow-hidden">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 shrink-0 rounded-xl bg-blue-600 text-white grid place-items-center shadow-lg shadow-blue-600/30">{!! $icon($ic['trade']) !!}</span>
                <div>
                    <h2 class="text-base font-semibold text-white leading-tight">Trade Room</h2>
                    <p class="text-xs text-blue-200/80">Long or short, up to 100× leverage</p>
                </div>
            </div>
            <div class="mt-5">
                <div class="text-xs text-blue-200/80">Trading balance</div>
                <div class="text-3xl font-semibold text-white mt-1 font-mono tracking-tight">{{ $user->formatAmount($tradingBalance) }}</div>
            </div>
            <ul class="mt-4 space-y-2 text-sm text-gray-300">
                <li class="flex items-center gap-2">{!! $icon($ic['check'], 'w-4 h-4 text-emerald-400') !!} Market and limit orders</li>
                <li class="flex items-center gap-2">{!! $icon($ic['check'], 'w-4 h-4 text-emerald-400') !!} Take profit and stop loss</li>
                <li class="flex items-center gap-2">{!! $icon($ic['check'], 'w-4 h-4 text-emerald-400') !!} Live profit and loss on every position</li>
            </ul>
            <div class="mt-auto pt-5 grid grid-cols-2 gap-2">
                <a href="{{ route('user.liveTrading.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 px-4 py-3 text-sm font-semibold text-white">Open Trade Room</a>
                <a href="{{ route('user.holding.buy-assets') }}" class="inline-flex items-center justify-center rounded-xl border border-blue-400/40 hover:border-blue-300 px-4 py-3 text-sm font-semibold text-white">Buy &amp; hold</a>
            </div>
        </div>
    </section>

    {{-- ===== Strength, performance, activity ===== --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Trading Strength (live; refreshed every 30s by the script below) --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-white">Trading Strength</h3>
                <span class="text-xs text-gray-400" id="strengthUpdated">Live</span>
            </div>
            <div class="mt-4" id="strengthCard" data-url="{{ route('user.tradingStrength') }}">
                <div class="flex items-end gap-2">
                    <div class="text-4xl font-bold text-gold-400"><span id="strengthScore">{{ number_format($strength['score'], 1) }}</span>%</div>
                    <div class="text-sm text-gray-400 pb-1" id="strengthLabel">{{ $strength['label'] }}</div>
                </div>
                <div class="mt-3 w-full bg-gray-700 rounded-full h-2">
                    <div id="strengthBar" class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $strength['score'])) }}%"></div>
                </div>
                <details class="mt-4 text-sm group">
                    <summary class="cursor-pointer text-gray-400 hover:text-white list-none flex items-center gap-1">How it's calculated {!! $icon('<path d="M6 9l6 6 6-6"/>', 'w-4 h-4 group-open:rotate-180 transition-transform') !!}</summary>
                    <div class="mt-3 space-y-2.5" id="strengthBreakdown">
                        @foreach($strength['components'] as $c)
                            <div data-key="{{ $c['key'] }}">
                                <div class="flex justify-between text-xs text-gray-400"><span>{{ $c['label'] }}</span><span><span data-points>{{ number_format($c['points'], 1) }}</span> / {{ $c['max'] }}</span></div>
                                <div class="mt-1 w-full bg-gray-700 rounded-full h-1.5"><div data-bar class="bg-blue-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $c['max'] > 0 ? min(100, ($c['points'] / $c['max']) * 100) : 0 }}%"></div></div>
                            </div>
                        @endforeach
                        <p class="text-xs text-gray-500">Capital: trading balance. Performance: win rate once you have {{ \App\Services\TradingStrengthService::MIN_CLOSED_TRADES }}+ closed trades. Activity: trades in the last 30 days. Diversification: an active plan, bot/AI trader and copy trade.</p>
                    </div>
                </details>
            </div>
        </div>

        {{-- Performance --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-5 sm:p-6">
            <h3 class="text-base font-semibold text-white">Performance</h3>
            <dl class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-gray-900/50 border border-gray-700 p-4"><dt class="text-xs text-gray-400">Win rate</dt><dd class="text-2xl font-semibold text-white mt-1">{{ number_format($winRate, 1) }}%</dd></div>
                <div class="rounded-xl bg-gray-900/50 border border-gray-700 p-4"><dt class="text-xs text-gray-400">Total trades</dt><dd class="text-2xl font-semibold text-white mt-1">{{ $totalTrades }}</dd></div>
                <div class="rounded-xl bg-gray-900/50 border border-gray-700 p-4"><dt class="text-xs text-gray-400">Winning trades</dt><dd class="text-2xl font-semibold text-white mt-1">{{ $winningTrades }}</dd></div>
                <div class="rounded-xl bg-gray-900/50 border border-gray-700 p-4"><dt class="text-xs text-gray-400">Avg. profit</dt><dd class="text-2xl font-semibold mt-1 {{ $avgProfit > 0 ? 'text-emerald-400' : ($avgProfit < 0 ? 'text-red-400' : 'text-white') }}">{{ $avgProfit > 0 ? '+' : '' }}{{ $user->formatAmount($avgProfit) }}</dd></div>
            </dl>
            @if($totalTrades === 0)
                <p class="text-sm text-gray-400 mt-4">No trades yet. <a href="{{ route('user.liveTrading.index') }}" class="text-gold-400 hover:text-gold-300 font-semibold">Place your first trade</a> to start building a track record.</p>
            @endif
        </div>

        {{-- Account activity --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-5 sm:p-6">
            <h3 class="text-base font-semibold text-white">Account activity</h3>
            <ul class="mt-4 divide-y divide-gray-700">
                <li class="flex items-center gap-3 py-3">
                    <span class="w-9 h-9 rounded-lg bg-gray-700 text-gray-300 grid place-items-center">{!! $icon($ic['plan'], 'w-4 h-4') !!}</span>
                    <span class="flex-1"><span class="block text-sm text-white">Active plans</span><span class="block text-xs text-gray-400">{{ $tradingPlans }} trading · {{ $signalPlans }} signal · {{ $stakingPlans }} staking · {{ $miningPlans }} mining</span></span>
                    <span class="text-lg font-semibold text-white">{{ $totalPlans }}</span>
                </li>
                <li class="flex items-center gap-3 py-3">
                    <span class="w-9 h-9 rounded-lg bg-gray-700 text-gray-300 grid place-items-center">{!! $icon($ic['bot'], 'w-4 h-4') !!}</span>
                    <span class="flex-1"><span class="block text-sm text-white">Trading bots</span><span class="block text-xs text-gray-400">Profit {{ $user->formatAmount($totalBotProfit) }}</span></span>
                    <span class="text-lg font-semibold text-white">{{ $activeBots }}</span>
                </li>
                <li class="flex items-center gap-3 py-3">
                    <span class="w-9 h-9 rounded-lg bg-gray-700 text-gray-300 grid place-items-center">{!! $icon($ic['copy'], 'w-4 h-4') !!}</span>
                    <span class="flex-1"><span class="block text-sm text-white">Copy trades</span><span class="block text-xs text-gray-400">Traders you’re following</span></span>
                    <span class="text-lg font-semibold text-white">{{ $activeCopyTrades }}</span>
                </li>
                <li class="flex items-center gap-3 py-3">
                    <span class="w-9 h-9 rounded-lg bg-gray-700 text-gray-300 grid place-items-center">{!! $icon($ic['clock'], 'w-4 h-4') !!}</span>
                    <span class="flex-1"><span class="block text-sm text-white">Next renewal</span><span class="block text-xs text-gray-400">Earliest active plan</span></span>
                    <span class="text-sm font-semibold text-white">{{ optional(optional($activePlans->first())->end_date)->format('M d, Y') ?? '—' }}</span>
                </li>
            </ul>
        </div>
    </section>

    {{-- ===== Trades + recent activity ===== --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="flex items-center justify-between gap-4 px-5 sm:px-6 border-b border-gray-700">
                <nav class="flex gap-6" aria-label="Trades">
                    <button id="openTradesTab" class="tab-button active py-4 border-b-2 border-blue-500 text-blue-400 font-medium text-sm">Open trades <span class="ml-1 text-xs text-gray-400">{{ $openTrades->count() }}</span></button>
                    <button id="closedTradesTab" class="tab-button py-4 border-b-2 border-transparent text-gray-400 hover:text-gray-300 font-medium text-sm">Closed trades <span class="ml-1 text-xs text-gray-400">{{ $closedTrades->count() }}</span></button>
                </nav>
                <a href="{{ route('user.liveTrading.history') }}" class="hidden sm:inline-flex text-sm font-semibold text-gold-400 hover:text-gold-300">History</a>
            </div>

            <div id="openTradesContent" class="tab-content active">
                @forelse($openTrades as $trade)
                    @if($loop->first)<div class="overflow-x-auto"><table class="min-w-full text-sm"><thead><tr class="text-left">
                        <th class="px-5 py-3">Market</th><th class="px-5 py-3">Type</th><th class="px-5 py-3">Margin</th><th class="px-5 py-3">Leverage</th><th class="px-5 py-3">Entry</th><th class="px-5 py-3">Status</th>
                    </tr></thead><tbody class="divide-y divide-gray-700">@endif
                    <tr>
                        <td class="px-5 py-3 whitespace-nowrap text-white font-medium">{{ $trade->market }}@if($trade->by_manager)<span class="block text-[11px] font-normal text-gold-400">Placed by your account manager</span>@endif</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $trade->side === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ ucfirst($trade->side ?? 'N/A') }}</span></td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-300">{{ $user->formatAmount($trade->amount) }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-300">{{ $trade->leverage ? $trade->leverage.'x' : '—' }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-300">{{ $trade->entry ? $user->formatAmount($trade->entry, 4) : '—' }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-300">{{ ucfirst($trade->status) }}</td>
                    </tr>
                    @if($loop->last)</tbody></table></div>@endif
                @empty
                    <div class="px-6 py-12 text-center">
                        <span class="mx-auto w-12 h-12 rounded-full bg-gray-700 text-gray-300 grid place-items-center">{!! $icon($ic['trade']) !!}</span>
                        <p class="mt-3 text-white font-medium">No open trades</p>
                        <p class="text-sm text-gray-400 mt-1">Your open positions will show up here.</p>
                        <a href="{{ route('user.liveTrading.index') }}" class="inline-flex mt-4 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Open the Trade Room</a>
                    </div>
                @endforelse
            </div>

            <div id="closedTradesContent" class="tab-content hidden">
                @forelse($closedTrades as $trade)
                    @if($loop->first)<div class="overflow-x-auto"><table class="min-w-full text-sm"><thead><tr class="text-left">
                        <th class="px-5 py-3">Market</th><th class="px-5 py-3">Type</th><th class="px-5 py-3">Margin</th><th class="px-5 py-3">Entry</th><th class="px-5 py-3">Exit</th><th class="px-5 py-3">P&amp;L</th><th class="px-5 py-3">Date</th>
                    </tr></thead><tbody class="divide-y divide-gray-700">@endif
                    <tr>
                        <td class="px-5 py-3 whitespace-nowrap text-white font-medium">{{ $trade->market }}@if($trade->by_manager)<span class="block text-[11px] font-normal text-gold-400">Placed by your account manager</span>@endif</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $trade->side === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ ucfirst($trade->side ?? 'N/A') }}</span></td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-300">{{ $user->formatAmount($trade->amount) }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-300">{{ $trade->entry ? $user->formatAmount($trade->entry, 4) : '—' }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-300">{{ $trade->exit ? $user->formatAmount($trade->exit, 4) : '—' }}</td>
                        <td class="px-5 py-3 whitespace-nowrap font-semibold {{ ($trade->pnl ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ ($trade->pnl ?? 0) >= 0 ? '+' : '' }}{{ $user->formatAmount($trade->pnl ?? 0) }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-400">{{ optional($trade->date)->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    @if($loop->last)</tbody></table></div>@endif
                @empty
                    <div class="px-6 py-12 text-center">
                        <span class="mx-auto w-12 h-12 rounded-full bg-gray-700 text-gray-300 grid place-items-center">{!! $icon($ic['check']) !!}</span>
                        <p class="mt-3 text-white font-medium">No closed trades yet</p>
                        <p class="text-sm text-gray-400 mt-1">Completed trades and their results will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-white">Recent activity</h3>
                <a href="{{ route('user.transactions.index') }}" class="text-sm font-semibold text-gold-400 hover:text-gold-300">See all</a>
            </div>
            @forelse($recentTransactions as $tx)
                @if($loop->first)<ul class="mt-3 divide-y divide-gray-700">@endif
                <li class="flex items-center gap-3 py-3">
                    <span class="w-9 h-9 rounded-lg grid place-items-center text-xs font-bold {{ in_array($tx->type, ['buy', 'deposit']) ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ strtoupper(substr($tx->asset->symbol ?? $tx->type ?? '?', 0, 3)) }}</span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-sm text-white truncate">{{ ucfirst($tx->type ?? 'Transaction') }} {{ $tx->asset->name ?? '' }}</span>
                        <span class="block text-xs text-gray-400">{{ optional($tx->created_at)->diffForHumans() }}</span>
                    </span>
                    <span class="text-sm font-semibold text-white whitespace-nowrap">{{ $user->formatAmount($tx->total_amount ?? 0) }}</span>
                </li>
                @if($loop->last)</ul>@endif
            @empty
                <div class="py-10 text-center">
                    <span class="mx-auto w-12 h-12 rounded-full bg-gray-700 text-gray-300 grid place-items-center">{!! $icon($ic['clock']) !!}</span>
                    <p class="mt-3 text-white font-medium">Nothing here yet</p>
                    <p class="text-sm text-gray-400 mt-1">Buys, sells and transfers will be listed here.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Spacer for the mobile bottom menu --}}
    <div class="h-16 lg:hidden"></div>
</div>

{{-- Mobile bottom menu (the sidebar covers this on desktop) --}}
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-gray-900/95 backdrop-blur border-t border-gray-700 z-40" aria-label="Quick menu">
    <div class="grid grid-cols-4 py-2">
        @foreach([
            ['Trade', route('user.liveTrading.index'), $ic['trade']],
            ['Deposit', route('user.deposit'), $ic['deposit']],
            ['Portfolio', route('user.portfolio.index'), $ic['wallet']],
            ['Copy', route('user.copyTrading.index'), $ic['copy']],
        ] as [$label, $href, $path])
            <a href="{{ $href }}" class="flex flex-col items-center gap-1 text-gray-400 hover:text-white">
                {!! $icon($path, 'w-5 h-5') !!}
                <span class="text-[11px] font-medium">{{ $label }}</span>
            </a>
        @endforeach
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tabs = [['openTradesTab', 'openTradesContent'], ['closedTradesTab', 'closedTradesContent']];
        tabs.forEach(function (pair) {
            document.getElementById(pair[0]).addEventListener('click', function () {
                tabs.forEach(function (other) {
                    var on = other === pair, btn = document.getElementById(other[0]), panel = document.getElementById(other[1]);
                    btn.classList.toggle('border-blue-500', on); btn.classList.toggle('text-blue-400', on);
                    btn.classList.toggle('border-transparent', !on); btn.classList.toggle('text-gray-400', !on);
                    panel.classList.toggle('hidden', !on);
                });
            });
        });
    });
</script>

@endsection

@push('scripts')
<script>
// Live prices for the dashboard market cards (USD feeds converted to the user's currency).
(function () {
    const root = document.getElementById('dashMarkets');
    if (!root) return;
    const fx = parseFloat(root.dataset.fx) || 1;
    const sym = window.CURRENCY_SYMBOL || '';
    const fmt = v => sym + (v * fx).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: v * fx < 1 ? 4 : 2 });
    const paint = (key, price, pct) => {
        const a = root.querySelector('[data-q="' + key + '"]');
        if (!a || !(price > 0)) return;
        a.querySelector('[data-price]').textContent = fmt(price);
        const c = a.querySelector('[data-chg]');
        const up = pct >= 0;
        c.textContent = (up ? '+' : '') + pct.toFixed(2) + '%';
        c.className = 'inline-block mt-0.5 text-[11px] font-semibold rounded-md px-1.5 py-0.5 ' + (up ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300');
    };
    const offline = k => { const b = root.querySelector('[data-live-badge="' + k + '"]'); if (b) { b.className = 'ml-auto text-[11px] font-medium text-gray-400 bg-gray-700/60 rounded-full px-2 py-0.5'; b.textContent = 'Delayed'; } };

    const stocks = () => fetch('/market/stocks.json').then(r => r.json())
        .then(d => (d.quotes || []).forEach(q => paint('stock:' + q.symbol, q.price, q.change_pct || 0)))
        .catch(() => offline('stock'));
    const cryptoSyms = [...root.querySelectorAll('[data-q^="crypto:"]')].map(a => a.dataset.q.split(':')[1] + 'USDT');
    const crypto = () => fetch('https://data-api.binance.vision/api/v3/ticker/24hr?symbols=' + encodeURIComponent(JSON.stringify(cryptoSyms)))
        .then(r => r.json())
        .then(rows => rows.forEach(t => paint('crypto:' + t.symbol.replace(/USDT$/, ''), +t.lastPrice, +t.priceChangePercent)))
        .catch(gecko);
    // Fallback when Binance is unreachable (blocked region / network): CoinGecko simple price.
    const GECKO = { BTC: 'bitcoin', ETH: 'ethereum', SOL: 'solana', XRP: 'ripple', BNB: 'binancecoin', DOGE: 'dogecoin' };
    function gecko() {
        const ids = cryptoSyms.map(s => GECKO[s.replace(/USDT$/, '')]).filter(Boolean);
        return fetch('https://api.coingecko.com/api/v3/simple/price?vs_currencies=usd&include_24hr_change=true&ids=' + ids.join(','))
            .then(r => r.json())
            .then(d => Object.entries(GECKO).forEach(([s, id]) => d[id] && paint('crypto:' + s, d[id].usd, d[id].usd_24h_change || 0)))
            .catch(() => offline('crypto'));
    }
    stocks(); crypto();
    setInterval(stocks, 60000); setInterval(crypto, 15000);
})();
</script>
<script>
(function () {
  var card = document.getElementById('strengthCard');
  if (!card) return;
  var url = card.dataset.url;
  function apply(d) {
    document.getElementById('strengthScore').textContent = Number(d.score).toFixed(1);
    document.getElementById('strengthLabel').textContent = d.label;
    document.getElementById('strengthBar').style.width = Math.min(100, Math.max(0, d.score)) + '%';
    d.components.forEach(function (c) {
      var row = card.querySelector('[data-key="' + c.key + '"]');
      if (!row) return;
      row.querySelector('[data-points]').textContent = Number(c.points).toFixed(1);
      row.querySelector('[data-bar]').style.width = (c.max ? Math.min(100, c.points / c.max * 100) : 0) + '%';
    });
    var u = document.getElementById('strengthUpdated');
    if (u) u.textContent = 'Updated ' + new Date(d.updated_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
  }
  function refresh() {
    if (document.hidden) return;
    fetch(url, {headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, credentials: 'same-origin'})
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
      .then(apply)
      .catch(function () {});
  }
  setInterval(refresh, 30000);
  document.addEventListener('visibilitychange', function () { if (!document.hidden) refresh(); });
})();
</script>
@endpush
