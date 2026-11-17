@extends('dashboard.layout.app')
@section('title', 'Trading History')
@section('content')
@php
    $qty = fn ($q) => rtrim(rtrim(number_format((float) $q, 8), '0'), '.');
    $reason = ['manual' => 'Closed', 'take_profit' => 'Take profit', 'stop_loss' => 'Stop loss', 'liquidation' => 'Liquidated'];
    $realised = $closedTrades->whereIn('status', ['closed', 'liquidated'])->sum('realized_pnl');
    $wins = $closedTrades->where('realized_pnl', '>', 0)->count();
    $settled = $closedTrades->whereIn('status', ['closed', 'liquidated'])->count();
@endphp
<div class="space-y-4 tr-room">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.liveTrading.index') }}" class="tr-icon-btn" aria-label="Back to Trade Room"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></a>
            <h1 class="text-xl sm:text-2xl font-bold text-white">Trading history</h1>
        </div>
        <a href="{{ route('user.liveTrading.index') }}" class="tr-btn tr-btn-primary">Trade Room</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
        <div class="tr-stat"><span>Realised P&amp;L</span><b class="{{ $realised > 0 ? 'text-emerald-400' : ($realised < 0 ? 'text-red-400' : '') }}">{{ ($realised > 0 ? '+' : ($realised < 0 ? '−' : '')).money(abs($realised)) }}</b></div>
        <div class="tr-stat"><span>Win rate</span><b>{{ $settled ? number_format($wins / $settled * 100, 1) : '0.0' }}%</b></div>
        <div class="tr-stat col-span-2 sm:col-span-1"><span>Active</span><b>{{ $openTrades->count() }}</b></div>
    </div>

    <section class="tr-panel overflow-hidden">
        <div class="flex gap-5 px-4 border-b border-gray-700" role="tablist">
            <button type="button" class="tr-otab is-on" data-pane="activePane">Active <span>{{ $openTrades->count() }}</span></button>
            <button type="button" class="tr-otab" data-pane="closedPane">Closed <span>{{ $closedTrades->count() }}</span></button>
        </div>
        @foreach(['activePane' => $openTrades, 'closedPane' => $closedTrades] as $pane => $rows)
            <div id="{{ $pane }}" @if($pane === 'closedPane') hidden @endif>
                @forelse($rows as $t)
                    @if($loop->first)<ul class="divide-y divide-gray-700">@endif
                    <li class="p-4 md:px-5">
                        <div class="flex items-start gap-3">
                            <span class="w-10 h-10 shrink-0 rounded-lg grid place-items-center text-[10px] font-bold {{ $t->side === 'buy' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">{{ $t->side === 'buy' ? 'LONG' : 'SHORT' }}</span>
                            <div class="min-w-0 flex-1">
                                <div class="text-white font-semibold">{{ $t->symbol }} <span class="text-xs font-normal text-gray-500">{{ ucfirst($t->asset_type) }} · {{ ucfirst($t->order_type) }} · {{ $qty($t->leverage) }}x</span></div>
                                <div class="text-xs text-gray-400">{{ ($t->closed_at ?? $t->filled_at ?? $t->created_at)->format('M d, Y H:i') }}</div>
                            </div>
                            <div class="text-right">
                                @if($t->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-amber-500/15 text-amber-300">Waiting</span>
                                @elseif($t->status === 'open')
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-500/15 text-blue-300">Open</span>
                                @elseif($t->status === 'cancelled')
                                    <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-700 text-gray-300">Cancelled</span>
                                @else
                                    @php $p = (float) $t->realized_pnl; @endphp
                                    <div class="font-mono font-semibold {{ $p > 0 ? 'text-emerald-400' : ($p < 0 ? 'text-red-400' : 'text-gray-300') }}">{{ ($p > 0 ? '+' : ($p < 0 ? '−' : '')).money(abs($p)) }}</div>
                                    <div class="text-xs {{ $t->status === 'liquidated' ? 'text-red-400' : 'text-gray-400' }}">{{ $reason[$t->close_reason] ?? ucfirst($t->status) }}</div>
                                @endif
                            </div>
                        </div>
                        <dl class="grid grid-cols-3 md:grid-cols-6 gap-x-3 gap-y-2 mt-3 text-xs">
                            <div><dt class="text-gray-500">Size</dt><dd class="font-mono text-gray-200 mt-0.5">{{ $qty($t->quantity) }}</dd></div>
                            <div><dt class="text-gray-500">Margin</dt><dd class="font-mono text-gray-200 mt-0.5">{{ money($t->amount) }}</dd></div>
                            <div><dt class="text-gray-500">{{ $t->status === 'pending' ? 'Limit' : 'Entry' }}</dt><dd class="font-mono text-gray-200 mt-0.5">{{ money($t->status === 'pending' ? $t->price : ($t->entry_price ?? 0)) }}</dd></div>
                            @if($t->exit_price)<div><dt class="text-gray-500">Exit</dt><dd class="font-mono text-gray-200 mt-0.5">{{ money($t->exit_price) }}</dd></div>@endif
                            @if($t->take_profit || $t->stop_loss)<div><dt class="text-gray-500">TP / SL</dt><dd class="font-mono text-gray-200 mt-0.5">{{ $t->take_profit ? money($t->take_profit) : '—' }} / {{ $t->stop_loss ? money($t->stop_loss) : '—' }}</dd></div>@endif
                        </dl>
                        @if(in_array($t->status, ['pending', 'open']))
                            <a href="{{ route('user.liveTrading.index', ['asset_type' => $t->asset_type, 'symbol' => $t->symbol]) }}" class="tr-row-btn inline-block text-center">Manage in Trade Room</a>
                        @endif
                    </li>
                    @if($loop->last)</ul>@endif
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-white font-medium">{{ $pane === 'activePane' ? 'No active trades' : 'No closed trades yet' }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $pane === 'activePane' ? 'Open positions and waiting orders will appear here.' : 'Closed, liquidated and cancelled trades will be listed here.' }}</p>
                    </div>
                @endforelse
            </div>
        @endforeach
    </section>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tr-otab').forEach(function (t) {
  t.addEventListener('click', function () {
    document.querySelectorAll('.tr-otab').forEach(function (x) { x.classList.toggle('is-on', x === t); document.getElementById(x.dataset.pane).hidden = x !== t; });
  });
});
</script>
@endpush
