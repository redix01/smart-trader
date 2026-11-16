@extends('dashboard.layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Markets</p>
            <h1 class="text-2xl font-bold text-white mt-1">Crypto Prices</h1>
            <p class="text-gray-400 mt-1">Prices stream tick by tick. Select a coin to load its chart.</p>
        </div>
        <div class="flex items-center gap-3">
            <span id="feed-status" class="inline-flex items-center gap-2 text-xs text-gray-400"><span class="w-2 h-2 rounded-full bg-gray-500" id="feed-dot"></span><span id="feed-label">Connecting…</span></span>
            <a href="{{ route('user.liveTrading.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Open Trade Room →</a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div class="xl:col-span-2 rounded-xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700">
                        <tr><th class="text-left px-4 py-3">Asset</th><th class="text-right px-4 py-3">Price (USD)</th><th class="text-right px-4 py-3">24h</th><th class="text-right px-4 py-3 hidden sm:table-cell">24h Vol</th></tr>
                    </thead>
                    <tbody id="crypto-rows" class="divide-y divide-gray-700/60"></tbody>
                </table>
            </div>
        </div>

        <div class="xl:col-span-3 rounded-xl overflow-hidden border border-gray-700 bg-gray-800" style="min-height:600px">
            <div class="px-4 py-3 border-b border-gray-700 flex items-center justify-between">
                <h3 class="text-white font-semibold" id="chart-title">BTC / USDT</h3>
                <span class="text-xs text-gray-400">Chart by TradingView</span>
            </div>
            <div id="crypto-chart" style="height:560px"></div>
        </div>
    </div>

    <div class="rounded-xl overflow-hidden border border-gray-700 bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-700"><h3 class="text-lg font-semibold text-white">Crypto market heatmap</h3></div>
        <div class="tradingview-widget-container" style="height:520px"><div class="tradingview-widget-container__widget" style="height:100%"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-crypto-coins-heatmap.js" async>
            {"dataSource":"Crypto","blockSize":"market_cap_calc","blockColor":"change","locale":"en","symbolUrl":"","colorTheme":"dark","hasTopBar":false,"isDataSetEnabled":false,"isZoomEnabled":true,"hasSymbolTooltip":true,"isMonoSize":false,"width":"100%","height":"100%"}
            </script>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const COINS = [
        ['BTC','Bitcoin'],['ETH','Ethereum'],['BNB','BNB'],['SOL','Solana'],['XRP','XRP'],
        ['DOGE','Dogecoin'],['ADA','Cardano'],['TRX','TRON'],['AVAX','Avalanche'],['LINK','Chainlink'],
        ['DOT','Polkadot'],['LTC','Litecoin'],['TON','Toncoin'],['SHIB','Shiba Inu'],['BCH','Bitcoin Cash']
    ];
    const tbody = document.getElementById('crypto-rows');
    const dot = document.getElementById('feed-dot'), label = document.getElementById('feed-label');
    const last = {};

    const fmtPrice = p => p >= 1 ? p.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                                 : p.toLocaleString('en-US', {maximumSignificantDigits: 4});
    const fmtVol = v => v >= 1e9 ? (v / 1e9).toFixed(2) + 'B' : v >= 1e6 ? (v / 1e6).toFixed(1) + 'M' : Math.round(v).toLocaleString();

    tbody.innerHTML = COINS.map(([s, n]) => `
        <tr data-sym="${s}" class="cursor-pointer hover:bg-gray-700/40 transition-colors">
            <td class="px-4 py-3"><div class="font-semibold text-white">${s}</div><div class="text-xs text-gray-400">${n}</div></td>
            <td class="px-4 py-3 text-right font-mono text-white" data-f="price">—</td>
            <td class="px-4 py-3 text-right font-mono" data-f="chg">—</td>
            <td class="px-4 py-3 text-right font-mono text-gray-400 hidden sm:table-cell" data-f="vol">—</td>
        </tr>`).join('');

    function update(sym, price, chg, vol) {
        const row = tbody.querySelector(`tr[data-sym="${sym}"]`);
        if (!row || !isFinite(price)) return;
        const pCell = row.querySelector('[data-f="price"]');
        pCell.textContent = fmtPrice(price);
        if (last[sym] !== undefined && price !== last[sym]) {
            pCell.style.color = price > last[sym] ? '#34d399' : '#f87171';
            setTimeout(() => { pCell.style.color = ''; }, 600);
        }
        last[sym] = price;
        const c = row.querySelector('[data-f="chg"]');
        c.textContent = (chg >= 0 ? '+' : '') + chg.toFixed(2) + '%';
        c.className = 'px-4 py-3 text-right font-mono ' + (chg >= 0 ? 'text-emerald-400' : 'text-red-400');
        if (vol) row.querySelector('[data-f="vol"]').textContent = '$' + fmtVol(vol);
    }

    function status(ok, text) {
        dot.className = 'w-2 h-2 rounded-full ' + (ok ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400');
        label.textContent = text;
    }

    // Fallback: poll CoinGecko every 30s if the websocket can't connect.
    const GECKO = {BTC:'bitcoin',ETH:'ethereum',BNB:'binancecoin',SOL:'solana',XRP:'ripple',DOGE:'dogecoin',ADA:'cardano',TRX:'tron',AVAX:'avalanche-2',LINK:'chainlink',DOT:'polkadot',LTC:'litecoin',TON:'the-open-network',SHIB:'shiba-inu',BCH:'bitcoin-cash'};
    let pollTimer = null;
    function startPolling() {
        if (pollTimer) return;
        const run = () => fetch('https://api.coingecko.com/api/v3/simple/price?vs_currencies=usd&include_24hr_change=true&include_24hr_vol=true&ids=' + Object.values(GECKO).join(','))
            .then(r => r.json())
            .then(d => { for (const [s, id] of Object.entries(GECKO)) if (d[id]) update(s, d[id].usd, d[id].usd_24h_change || 0, d[id].usd_24h_vol); status(true, 'Live · updates every 30s'); })
            .catch(() => status(false, 'Price feed unavailable'));
        run(); pollTimer = setInterval(run, 30000);
    }

    // Primary: Binance public market-data stream (tick by tick, no API key).
    let opened = false;
    try {
        const streams = COINS.map(([s]) => s.toLowerCase() + 'usdt@ticker').join('/');
        const ws = new WebSocket('wss://data-stream.binance.vision/stream?streams=' + streams);
        ws.onopen = () => { opened = true; status(true, 'Live · streaming'); };
        ws.onmessage = e => { const d = JSON.parse(e.data).data; if (d) update(d.s.replace(/USDT$/, ''), +d.c, +d.P, +d.q); };
        ws.onerror = ws.onclose = () => { if (!opened || ws.readyState === WebSocket.CLOSED) startPolling(); };
        setTimeout(() => { if (!opened) startPolling(); }, 5000);
    } catch (e) { startPolling(); }

    // Chart
    function loadChart(sym) {
        document.getElementById('chart-title').textContent = sym + ' / USDT';
        tbody.querySelectorAll('tr').forEach(r => r.classList.toggle('bg-gray-700/40', r.dataset.sym === sym));
        const el = document.getElementById('crypto-chart');
        el.innerHTML = '';
        const draw = () => new TradingView.widget({
            container_id: 'crypto-chart', autosize: true, symbol: 'BINANCE:' + sym + 'USDT', interval: '15',
            timezone: 'Etc/UTC', theme: 'dark', style: '1', locale: 'en', allow_symbol_change: true,
            backgroundColor: 'rgba(10,12,20,1)', gridColor: 'rgba(255,255,255,0.04)'
        });
        if (window.TradingView) draw(); else window.addEventListener('load', draw, {once: true});
    }
    tbody.addEventListener('click', e => { const r = e.target.closest('tr[data-sym]'); if (r) loadChart(r.dataset.sym); });
    loadChart('BTC');
})();
</script>
@endpush
