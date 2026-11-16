@extends('dashboard.layout.app')

@section('content')
@php
    $user = auth()->user();
    $rate = $user->convertFrom(1);               // USD -> user's currency
    $balance = (float) $user->trading_balance;    // already in the user's currency
    $markets = collect()
        ->merge($cryptoAssets->map(fn ($a) => ['type' => 'crypto', 'symbol' => $a->symbol, 'name' => $a->name, 'usd' => (float) $a->current_price, 'chg' => (float) $a->price_change_percentage_24h]))
        ->merge($stockAssets->map(fn ($a) => ['type' => 'stock', 'symbol' => $a->symbol, 'name' => $a->name, 'usd' => (float) $a->current_price, 'chg' => (float) $a->price_change_percentage_24h]))
        ->values();
    $sel = $selected ? ['type' => $selected->type, 'symbol' => $selected->symbol, 'name' => $selected->name, 'usd' => (float) $selected->current_price, 'chg' => (float) $selected->price_change_percentage_24h] : null;
    $statusCls = ['pending' => 'bg-amber-500/15 text-amber-300', 'filled' => 'bg-emerald-500/15 text-emerald-400', 'cancelled' => 'bg-gray-700 text-gray-300'];
@endphp

<div class="tr-room space-y-4" id="tradeRoom"
     data-store="{{ route('user.liveTrading.store') }}"
     data-cancel-base="{{ url('/user/live-trading') }}"
     data-refresh="{{ route('user.liveTrading.refreshPrices') }}"
     data-rate="{{ $rate }}" data-balance="{{ $balance }}">

    {{-- ===== Header + account strip ===== --}}
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Trade Room</h1>
            <p class="text-sm text-gray-400 mt-0.5">Your personal trading desk for crypto and stocks.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 xl:flex xl:items-stretch">
            <div class="tr-stat"><span>Trading balance</span><b id="balanceDisplay">{{ money($balance) }}</b></div>
            <div class="tr-stat"><span>In open orders</span><b>{{ money($inOpenOrders) }}</b></div>
            <a href="{{ route('user.deposit') }}" class="tr-btn tr-btn-primary">Add funds</a>
            <a href="{{ route('user.liveTrading.history') }}" class="tr-btn">Order history</a>
        </div>
    </div>

    @if($markets->isEmpty())
        <div class="rounded-2xl border border-gray-700 bg-gray-800 p-10 text-center">
            <p class="text-white font-medium">No markets are available yet.</p>
            <p class="text-sm text-gray-400 mt-1">Please check back shortly or contact support.</p>
        </div>
    @else
    {{-- ===== Terminal ===== --}}
    <div class="grid gap-4 grid-cols-1 lg:grid-cols-[minmax(0,1fr)_340px] 2xl:grid-cols-[280px_minmax(0,1fr)_340px] 2xl:h-[680px]">

        {{-- Markets --}}
        <div id="marketsBackdrop" class="tr-backdrop" hidden></div>
        <aside id="marketsPanel" class="tr-panel tr-markets 2xl:order-1 flex flex-col min-h-0 overflow-hidden" aria-label="Markets">
            <div class="tr-sheet-head 2xl:hidden">
                <span class="tr-grab" aria-hidden="true"></span>
                <div class="flex items-center justify-between px-4 pt-1 pb-2">
                    <h2 class="text-base font-semibold text-white">Choose a market</h2>
                    <button type="button" id="closeMarkets" class="tr-btn tr-btn-sm" aria-label="Close markets">Close</button>
                </div>
            </div>
            <div class="p-3 border-b border-gray-700 space-y-2">
                <div class="grid grid-cols-2 gap-1 p-1 rounded-lg bg-gray-900/70" role="tablist">
                    <button type="button" class="tr-tab is-on" data-filter="crypto" role="tab">Crypto <span>{{ $cryptoAssets->count() }}</span></button>
                    <button type="button" class="tr-tab" data-filter="stock" role="tab">Stocks <span>{{ $stockAssets->count() }}</span></button>
                </div>
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                    <input type="search" id="marketSearch" placeholder="Search markets" class="w-full pl-9 pr-3 py-2 text-sm bg-gray-900/70 border border-gray-700 rounded-lg text-white placeholder-gray-500" autocomplete="off">
                </div>
            </div>
            <div class="grid grid-cols-[1fr_auto_auto] gap-x-3 px-3 py-2 text-[11px] uppercase tracking-wider text-gray-500 border-b border-gray-700">
                <span>Market</span><span class="text-right">Price</span><span class="text-right w-14">24h</span>
            </div>
            <ul id="marketList" class="overflow-y-auto flex-1 min-h-0">
                @foreach($markets as $m)
                    <li>
                        <button type="button" class="tr-mkt {{ $sel && $sel['symbol'] === $m['symbol'] && $sel['type'] === $m['type'] ? 'is-on' : '' }}"
                                data-type="{{ $m['type'] }}" data-symbol="{{ $m['symbol'] }}" data-name="{{ $m['name'] }}" data-usd="{{ $m['usd'] }}" data-chg="{{ $m['chg'] }}"
                                @if($m['type'] !== 'crypto') hidden @endif>
                            <span class="min-w-0 text-left"><b>{{ $m['symbol'] }}</b><small>{{ $m['name'] }}</small></span>
                            <span class="text-right font-mono text-[13px] text-white" data-price>{{ money_from($m['usd'], 'USD', $m['usd'] < 1 ? 4 : 2) }}</span>
                            <span class="text-right font-mono text-[12px] w-14 {{ $m['chg'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}" data-chg>{{ sprintf('%+.2f%%', $m['chg']) }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
            <p id="marketEmpty" class="hidden px-4 py-6 text-sm text-gray-400 text-center">No markets match your search.</p>
        </aside>

        {{-- Chart --}}
        <section class="tr-panel order-1 2xl:order-2 flex flex-col min-w-0 min-h-0 overflow-hidden">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 px-4 py-3 border-b border-gray-700">
                <button type="button" id="symbolPicker" class="flex items-center gap-3 min-w-0 text-left 2xl:pointer-events-none" aria-controls="marketsPanel">
                    <span id="selBadge" class="w-9 h-9 rounded-lg bg-blue-600 text-white grid place-items-center text-xs font-bold">{{ $sel ? substr($sel['symbol'], 0, 3) : '' }}</span>
                    <div class="min-w-0">
                        <div class="text-white font-semibold leading-tight"><span id="selSymbol">{{ $sel['symbol'] ?? '' }}</span><span class="text-gray-500 font-normal" id="selQuote">{{ ($sel['type'] ?? '') === 'crypto' ? ' / USD' : '' }}</span></div>
                        <div class="text-xs text-gray-400 truncate" id="selName">{{ $sel['name'] ?? '' }}</div>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 2xl:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-gray-500">Last price</div>
                    <div class="font-mono text-lg text-white" id="selPrice">{{ $sel ? money_from($sel['usd'], 'USD', $sel['usd'] < 1 ? 4 : 2) : '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider text-gray-500">24h change</div>
                    <div class="font-mono text-sm {{ ($sel['chg'] ?? 0) >= 0 ? 'text-emerald-400' : 'text-red-400' }}" id="selChg">{{ $sel ? sprintf('%+.2f%%', $sel['chg']) : '—' }}</div>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <button type="button" id="openMarkets" class="tr-btn tr-btn-sm 2xl:hidden" aria-controls="marketsPanel" aria-expanded="false"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h10"/></svg>Markets</button>
                    <span id="feedBadge" class="hidden items-center gap-1.5 text-xs text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Live</span>
                    <button type="button" id="refreshPrices" class="tr-btn tr-btn-sm" title="Refresh prices from the market feed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 11a8 8 0 10-2.3 5.7M20 4v7h-7"/></svg><span class="hidden sm:inline">Refresh</span>
                    </button>
                </div>
            </div>
            <div id="tvChart" class="flex-1 min-h-[360px] sm:min-h-[460px]"></div>
        </section>

        {{-- Order ticket --}}
        <section class="tr-panel order-2 2xl:order-3 p-4 2xl:overflow-y-auto" aria-label="Order ticket">
            <form id="orderForm" novalidate>
                <div class="grid grid-cols-2 gap-1 p-1 rounded-lg bg-gray-900/70">
                    <button type="button" class="tr-side is-buy is-on" data-side="buy">Buy</button>
                    <button type="button" class="tr-side is-sell" data-side="sell">Sell</button>
                </div>

                <div class="flex gap-4 mt-4 text-sm border-b border-gray-700">
                    <button type="button" class="tr-otype is-on" data-otype="market">Market</button>
                    <button type="button" class="tr-otype" data-otype="limit">Limit</button>
                </div>

                <div class="mt-4 space-y-3">
                    <div data-for="market">
                        <label class="tr-label" for="amountInput">Amount</label>
                        <div class="tr-input"><span>{{ currency_symbol() }}</span><input id="amountInput" type="number" inputmode="decimal" min="1" step="any" placeholder="0.00"></div>
                        <div class="grid grid-cols-4 gap-1.5 mt-2">
                            @foreach([25, 50, 75, 100] as $pct)<button type="button" class="tr-chip" data-pct="{{ $pct }}">{{ $pct === 100 ? 'Max' : $pct.'%' }}</button>@endforeach
                        </div>
                    </div>
                    <div data-for="limit" hidden>
                        <label class="tr-label" for="limitPrice">Limit price</label>
                        <div class="tr-input"><span>{{ currency_symbol() }}</span><input id="limitPrice" type="number" inputmode="decimal" step="any" placeholder="0.00"></div>
                    </div>
                    <div data-for="limit" hidden>
                        <label class="tr-label" for="limitQty">Quantity <span class="text-gray-500" id="qtyUnit"></span></label>
                        <div class="tr-input"><input id="limitQty" type="number" inputmode="decimal" step="any" placeholder="0"></div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label class="tr-label mb-0" for="leverageInput">Leverage</label>
                            <span class="font-mono text-sm text-white" id="leverageOut">1x</span>
                        </div>
                        <input id="leverageInput" type="range" min="1" max="100" step="1" value="1" class="w-full mt-2">
                        <div class="grid grid-cols-6 gap-1 mt-1.5">
                            @foreach([1, 5, 10, 25, 50, 100] as $lev)<button type="button" class="tr-chip" data-lev="{{ $lev }}">{{ $lev }}x</button>@endforeach
                        </div>
                    </div>
                </div>

                <dl class="tr-summary mt-4">
                    <div><dt>Est. price</dt><dd id="sumPrice">—</dd></div>
                    <div><dt>Est. quantity</dt><dd id="sumQty">—</dd></div>
                    <div><dt>Position size</dt><dd id="sumSize">—</dd></div>
                    <div><dt>Margin used</dt><dd id="sumCost">—</dd></div>
                    <div><dt>Balance after</dt><dd id="sumAfter">{{ money($balance) }}</dd></div>
                </dl>

                <p id="ticketError" class="hidden mt-3 text-sm text-red-400" role="alert"></p>

                <button type="submit" id="submitOrder" class="mt-4 w-full py-3 rounded-lg font-semibold text-white bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    Buy <span id="submitSymbol">{{ $sel['symbol'] ?? '' }}</span>
                </button>
                <p class="mt-3 text-[11px] leading-relaxed text-gray-500">Orders use your trading balance. Leverage magnifies gains and losses. <a href="{{ route('risk') }}" class="underline hover:text-gray-300">Risk disclosure</a></p>
            </form>
        </section>
    </div>
    @endif

    {{-- ===== Orders ===== --}}
    <section class="tr-panel overflow-hidden" aria-label="Orders">
        <div class="flex items-center justify-between px-4 border-b border-gray-700">
            <div class="flex gap-6" role="tablist">
                <button type="button" class="tr-otab is-on" data-pane="openPane">Open orders <span>{{ $openOrders->count() }}</span></button>
                <button type="button" class="tr-otab" data-pane="historyPane">Recent history <span>{{ $orderHistory->count() }}</span></button>
            </div>
            <a href="{{ route('user.liveTrading.history') }}" class="hidden sm:inline text-sm font-semibold text-gold-400 hover:text-gold-300">Full history</a>
        </div>

        @foreach(['openPane' => $openOrders, 'historyPane' => $orderHistory] as $pane => $rows)
            <div id="{{ $pane }}" @if($pane === 'historyPane') hidden @endif>
                @if($rows->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-white font-medium">{{ $pane === 'openPane' ? 'No open orders' : 'No order history yet' }}</p>
                        <p class="text-sm text-gray-400 mt-1">{{ $pane === 'openPane' ? 'Orders you place will appear here until they are filled or cancelled.' : 'Filled and cancelled orders will be listed here.' }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead><tr class="text-left">
                                <th class="px-4 py-2.5">Time</th><th class="px-4 py-2.5">Market</th><th class="px-4 py-2.5">Side</th><th class="px-4 py-2.5">Type</th>
                                <th class="px-4 py-2.5 text-right">Amount</th><th class="px-4 py-2.5 text-right">Price / Qty</th><th class="px-4 py-2.5 text-right">Leverage</th><th class="px-4 py-2.5">Status</th><th class="px-4 py-2.5"></th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($rows as $o)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-400">{{ $o->created_at->format('M d, H:i') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap"><span class="text-white font-medium">{{ $o->symbol }}</span> <span class="text-xs text-gray-500">{{ ucfirst($o->asset_type) }}</span></td>
                                        <td class="px-4 py-3"><span class="font-semibold {{ $o->side === 'buy' ? 'text-emerald-400' : 'text-red-400' }}">{{ ucfirst($o->side) }}</span></td>
                                        <td class="px-4 py-3 text-gray-300">{{ ucfirst($o->order_type) }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-white whitespace-nowrap">{{ money($o->amount) }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-300 whitespace-nowrap">{{ $o->order_type === 'limit' ? money($o->price, 2).' × '.rtrim(rtrim(number_format($o->quantity, 8), '0'), '.') : 'Market' }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-300">{{ rtrim(rtrim(number_format($o->leverage, 2), '0'), '.') }}x</td>
                                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $statusCls[$o->status] ?? 'bg-gray-700 text-gray-300' }}">{{ ucfirst($o->status) }}</span></td>
                                        <td class="px-4 py-3 text-right">
                                            @if($o->status === 'pending')
                                                <button type="button" class="text-sm font-semibold text-red-400 hover:text-red-300" data-cancel-id="{{ $o->id }}">Cancel</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </section>
</div>

{{-- Toast --}}
<div id="trToast" class="hidden fixed bottom-6 right-6 z-[60] max-w-sm rounded-xl border px-4 py-3 text-sm shadow-2xl" role="status" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
(function () {
  var room = document.getElementById('tradeRoom');
  if (!room || !document.getElementById('orderForm')) return;

  var SYM = window.CURRENCY_SYMBOL || '';
  var rate = parseFloat(room.dataset.rate) || 1;
  var balance = parseFloat(room.dataset.balance) || 0;
  var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  var state = { type: null, symbol: null, name: '', usd: 0, chg: 0, side: 'buy', otype: 'market', lev: 1 };

  var $ = function (id) { return document.getElementById(id); };
  var fmt = function (n, d) {
    if (!isFinite(n)) return '—';
    d = d == null ? (Math.abs(n) < 1 && n !== 0 ? 4 : 2) : d;
    return SYM + Number(n).toLocaleString(undefined, { minimumFractionDigits: d, maximumFractionDigits: d });
  };
  var qtyFmt = function (n) { return isFinite(n) ? Number(n).toLocaleString(undefined, { maximumFractionDigits: 8 }) : '—'; };
  var price = function () { return state.usd * rate; };   // in the user's currency

  // ---------- markets ----------
  var list = $('marketList');
  var buttons = Array.prototype.slice.call(list.querySelectorAll('.tr-mkt'));
  var filter = 'crypto';

  function applyFilter() {
    var q = $('marketSearch').value.trim().toLowerCase(), shown = 0;
    buttons.forEach(function (b) {
      var ok = b.dataset.type === filter && (!q || b.dataset.symbol.toLowerCase().indexOf(q) > -1 || b.dataset.name.toLowerCase().indexOf(q) > -1);
      b.hidden = !ok; if (ok) shown++;
    });
    $('marketEmpty').classList.toggle('hidden', shown > 0);
  }
  document.querySelectorAll('.tr-tab').forEach(function (t) {
    t.addEventListener('click', function () {
      document.querySelectorAll('.tr-tab').forEach(function (x) { x.classList.toggle('is-on', x === t); });
      filter = t.dataset.filter; applyFilter();
    });
  });
  $('marketSearch').addEventListener('input', applyFilter);
  buttons.forEach(function (b) { b.addEventListener('click', function () { select(b); closeMarkets(); }); });

  // Markets panel: a slide-up sheet below 2xl, a fixed column on wide screens.
  var panel = $('marketsPanel'), backdrop = $('marketsBackdrop'), opener = $('openMarkets');
  function openMarkets() {
    if (window.innerWidth >= 1536) return;
    panel.classList.add('is-open'); backdrop.hidden = false; document.body.style.overflow = 'hidden';
    if (opener) opener.setAttribute('aria-expanded', 'true');
    setTimeout(function () { $('marketSearch').focus({ preventScroll: true }); }, 250);
  }
  function closeMarkets() {
    if (!panel.classList.contains('is-open')) return;
    panel.classList.remove('is-open'); backdrop.hidden = true; document.body.style.overflow = '';
    if (opener) opener.setAttribute('aria-expanded', 'false');
  }
  if (opener) opener.addEventListener('click', openMarkets);
  $('symbolPicker').addEventListener('click', openMarkets);
  $('closeMarkets').addEventListener('click', closeMarkets);
  backdrop.addEventListener('click', closeMarkets);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMarkets(); });

  function select(b) {
    buttons.forEach(function (x) { x.classList.toggle('is-on', x === b); });
    state.type = b.dataset.type; state.symbol = b.dataset.symbol; state.name = b.dataset.name;
    state.usd = parseFloat(b.dataset.usd) || 0; state.chg = parseFloat(b.dataset.chg) || 0;
    if (filter !== state.type) { document.querySelector('.tr-tab[data-filter="' + state.type + '"]').click(); }
    $('selSymbol').textContent = state.symbol;
    $('selQuote').textContent = state.type === 'crypto' ? ' / USD' : '';
    $('selName').textContent = state.name;
    $('selBadge').textContent = state.symbol.slice(0, 3);
    $('submitSymbol').textContent = state.symbol;
    $('qtyUnit').textContent = '(' + state.symbol + ')';
    $('limitPrice').value = price() ? price().toFixed(price() < 1 ? 6 : 2) : '';
    paintPrice();
    drawChart();
    history.replaceState(null, '', '?asset_type=' + state.type + '&symbol=' + encodeURIComponent(state.symbol));
    recalc();
  }

  function paintPrice() {
    $('selPrice').textContent = fmt(price());
    var c = $('selChg');
    c.textContent = (state.chg >= 0 ? '+' : '') + state.chg.toFixed(2) + '%';
    c.className = 'font-mono text-sm ' + (state.chg >= 0 ? 'text-emerald-400' : 'text-red-400');
  }

  // ---------- chart ----------
  function drawChart() {
    var el = $('tvChart'); el.innerHTML = '';
    if (typeof TradingView === 'undefined') { el.innerHTML = '<p class="p-6 text-sm text-gray-400">Chart unavailable right now.</p>'; return; }
    var tv = state.type === 'crypto' ? 'BINANCE:' + state.symbol + 'USDT' : state.symbol;
    new TradingView.widget({
      container_id: 'tvChart', autosize: true, symbol: tv, interval: '15', timezone: 'Etc/UTC',
      theme: 'dark', style: '1', locale: 'en', hide_side_toolbar: window.innerWidth < 768, allow_symbol_change: false,
      backgroundColor: 'rgba(11,19,48,1)', gridColor: 'rgba(170,190,255,0.06)', withdateranges: true, save_image: false
    });
  }

  // ---------- live crypto ticks (cosmetic: estimates only; the server prices the order) ----------
  (function stream() {
    var cryptos = buttons.filter(function (b) { return b.dataset.type === 'crypto'; }).map(function (b) { return b.dataset.symbol.toLowerCase() + 'usdt@miniTicker'; });
    if (!cryptos.length || !('WebSocket' in window)) return;
    try {
      var ws = new WebSocket('wss://data-stream.binance.vision/stream?streams=' + cryptos.slice(0, 60).join('/'));
      ws.onopen = function () { var f = $('feedBadge'); f.classList.remove('hidden'); f.classList.add('inline-flex'); };
      ws.onclose = ws.onerror = function () { var f = $('feedBadge'); f.classList.add('hidden'); f.classList.remove('inline-flex'); };
      ws.onmessage = function (e) {
        var d = JSON.parse(e.data).data; if (!d) return;
        var s = d.s.replace(/USDT$/, ''), usd = +d.c, open = +d.o, chg = open ? (usd - open) / open * 100 : 0;
        var b = list.querySelector('.tr-mkt[data-type="crypto"][data-symbol="' + s + '"]'); if (!b) return;
        b.dataset.usd = usd; b.dataset.chg = chg;
        b.querySelector('[data-price]').textContent = fmt(usd * rate);
        var c = b.querySelector('[data-chg]'); c.textContent = (chg >= 0 ? '+' : '') + chg.toFixed(2) + '%';
        c.className = 'text-right font-mono text-[12px] w-14 ' + (chg >= 0 ? 'text-emerald-400' : 'text-red-400');
        if (state.type === 'crypto' && state.symbol === s) { state.usd = usd; state.chg = chg; paintPrice(); recalc(); }
      };
    } catch (e) {}
  })();

  $('refreshPrices').addEventListener('click', function () {
    var btn = this; btn.disabled = true;
    fetch(room.dataset.refresh, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (d) { toast(d.success ? 'Prices refreshed.' : (d.message || 'Could not refresh prices.'), d.success); if (d.success) setTimeout(function () { location.reload(); }, 700); })
      .catch(function () { toast('Could not refresh prices.', false); })
      .finally(function () { btn.disabled = false; });
  });

  // ---------- order ticket ----------
  document.querySelectorAll('.tr-side').forEach(function (b) {
    b.addEventListener('click', function () {
      document.querySelectorAll('.tr-side').forEach(function (x) { x.classList.toggle('is-on', x === b); });
      state.side = b.dataset.side;
      var s = $('submitOrder');
      s.classList.toggle('bg-emerald-600', state.side === 'buy'); s.classList.toggle('hover:bg-emerald-500', state.side === 'buy');
      s.classList.toggle('bg-red-600', state.side === 'sell'); s.classList.toggle('hover:bg-red-500', state.side === 'sell');
      s.firstChild.textContent = (state.side === 'buy' ? 'Buy ' : 'Sell ');
      recalc();
    });
  });
  document.querySelectorAll('.tr-otype').forEach(function (b) {
    b.addEventListener('click', function () {
      document.querySelectorAll('.tr-otype').forEach(function (x) { x.classList.toggle('is-on', x === b); });
      state.otype = b.dataset.otype;
      document.querySelectorAll('[data-for]').forEach(function (el) { el.hidden = el.dataset.for !== state.otype; });
      recalc();
    });
  });
  document.querySelectorAll('[data-pct]').forEach(function (b) {
    b.addEventListener('click', function () { $('amountInput').value = (Math.floor(balance * b.dataset.pct) / 100).toFixed(2); recalc(); });
  });
  function setLev(v) {
    state.lev = Math.max(1, Math.min(100, parseInt(v, 10) || 1));
    $('leverageInput').value = state.lev; $('leverageOut').textContent = state.lev + 'x';
    document.querySelectorAll('[data-lev]').forEach(function (x) { x.classList.toggle('is-on', +x.dataset.lev === state.lev); });
    recalc();
  }
  $('leverageInput').addEventListener('input', function () { setLev(this.value); });
  document.querySelectorAll('[data-lev]').forEach(function (b) { b.addEventListener('click', function () { setLev(b.dataset.lev); }); });
  ['amountInput', 'limitPrice', 'limitQty'].forEach(function (id) { $(id).addEventListener('input', recalc); });

  function order() {
    if (state.otype === 'market') {
      var amt = parseFloat($('amountInput').value) || 0, p = price();
      return { cost: amt, price: p, qty: p > 0 ? amt / p : NaN };
    }
    var lp = parseFloat($('limitPrice').value) || 0, q = parseFloat($('limitQty').value) || 0;
    return { cost: lp * q, price: lp, qty: q };
  }
  function recalc() {
    var o = order(), err = '';
    $('sumPrice').textContent = o.price ? fmt(o.price) : '—';
    $('sumQty').textContent = o.qty ? qtyFmt(o.qty) + ' ' + (state.symbol || '') : '—';
    $('sumSize').textContent = o.cost ? fmt(o.cost * state.lev, 2) : '—';
    $('sumCost').textContent = o.cost ? fmt(o.cost, 2) : '—';
    $('sumAfter').textContent = fmt(balance - o.cost, 2);
    $('sumAfter').className = balance - o.cost < 0 ? 'text-red-400' : '';
    if (o.cost > balance) err = 'This order is larger than your trading balance.';
    else if (state.otype === 'market' && o.cost > 0 && o.cost < 1) err = 'The minimum order is ' + fmt(1, 2) + '.';
    var e = $('ticketError'); e.textContent = err; e.classList.toggle('hidden', !err);
    $('submitOrder').disabled = !!err || !(o.cost > 0) || !state.symbol;
  }

  $('orderForm').addEventListener('submit', function (ev) {
    ev.preventDefault();
    var o = order(), btn = $('submitOrder');
    if (btn.disabled) return;
    var fd = new FormData();
    fd.append('asset_type', state.type); fd.append('symbol', state.symbol);
    fd.append('order_type', state.otype); fd.append('side', state.side); fd.append('leverage', state.lev);
    if (state.otype === 'market') fd.append('amount', o.cost.toFixed(2));
    else { fd.append('price', o.price); fd.append('quantity', o.qty); }
    btn.disabled = true; var label = btn.innerHTML; btn.textContent = 'Placing order…';
    fetch(room.dataset.store, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
      .then(function (res) {
        if (res.ok && res.d.success) {
          toast((state.side === 'buy' ? 'Buy' : 'Sell') + ' order for ' + state.symbol + ' placed.', true);
          setTimeout(function () { location.reload(); }, 900);
        } else {
          var msg = res.d.message || (res.d.errors ? Object.values(res.d.errors)[0][0] : 'Order could not be placed.');
          toast(msg, false); btn.innerHTML = label; recalc();
        }
      })
      .catch(function () { toast('Network error. Your order was not placed.', false); btn.innerHTML = label; recalc(); });
  });

  // ---------- orders panel ----------
  document.querySelectorAll('.tr-otab').forEach(function (t) {
    t.addEventListener('click', function () {
      document.querySelectorAll('.tr-otab').forEach(function (x) { x.classList.toggle('is-on', x === t); $(x.dataset.pane).hidden = x !== t; });
    });
  });
  document.querySelectorAll('button[data-cancel-id]').forEach(function (b) {
    b.addEventListener('click', function () {
      if (!confirm('Cancel this order? The amount returns to your trading balance.')) return;
      b.disabled = true;
      fetch(room.dataset.cancelBase + '/' + b.dataset.cancelId + '/cancel', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (d) { toast(d.message || (d.success ? 'Order cancelled.' : 'Could not cancel.'), d.success); if (d.success) setTimeout(function () { location.reload(); }, 700); else b.disabled = false; })
        .catch(function () { toast('Network error.', false); b.disabled = false; });
    });
  });

  var toastTimer;
  function toast(msg, ok) {
    var t = $('trToast');
    t.textContent = msg;
    t.className = 'fixed bottom-6 right-6 z-[60] max-w-sm rounded-xl border px-4 py-3 text-sm shadow-2xl ' + (ok ? 'bg-emerald-950 border-emerald-700 text-emerald-200' : 'bg-red-950 border-red-700 text-red-200');
    clearTimeout(toastTimer); toastTimer = setTimeout(function () { t.classList.add('hidden'); }, 4000);
  }

  // ---------- start ----------
  var start = list.querySelector('.tr-mkt.is-on') || buttons[0];
  if (start) { filter = start.dataset.type; applyFilter(); select(start); }
  setLev(1);
  window.addEventListener('load', function () { if (state.symbol && !$('tvChart').querySelector('iframe')) drawChart(); });
})();
</script>
@endpush
