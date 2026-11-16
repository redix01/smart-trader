{{-- Live crypto prices for any element with data-live-price="SYM" / data-live-chg="SYM" / data-live-vol="SYM".
     Primary feed: Binance public market-data stream (tick by tick, no key). Fallback: CoinGecko every 30s. --}}
@once
@push('scripts')
<script>
(function () {
  var GECKO = {BTC:'bitcoin',ETH:'ethereum',SOL:'solana',XRP:'ripple',BNB:'binancecoin',DOGE:'dogecoin',ADA:'cardano',AVAX:'avalanche-2',LINK:'chainlink',DOT:'polkadot',LTC:'litecoin',TRX:'tron'};
  var syms = {};
  document.querySelectorAll('[data-live-price],[data-live-chg],[data-live-vol]').forEach(function (el) {
    syms[el.dataset.livePrice || el.dataset.liveChg || el.dataset.liveVol] = true;
  });
  syms = Object.keys(syms).filter(function (s) { return GECKO[s]; });
  if (!syms.length) return;
  var last = {};
  function fmt(p, prefix) { return (prefix || '') + (p >= 1 ? p.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : p.toLocaleString('en-US', {maximumSignificantDigits: 4})); }
  function vol(v) { return '$' + (v >= 1e9 ? (v / 1e9).toFixed(2) + 'B' : (v / 1e6).toFixed(1) + 'M'); }
  function update(s, price, chg, v) {
    if (!isFinite(price)) return;
    document.querySelectorAll('[data-live-price="' + s + '"]').forEach(function (el) {
      el.textContent = fmt(price, el.dataset.prefix);
      if (last[s] !== undefined && last[s] !== price) {
        var row = el.closest('tr') || el;
        row.classList.remove('flash-up', 'flash-down'); void row.offsetWidth;
        row.classList.add(price > last[s] ? 'flash-up' : 'flash-down');
      }
    });
    document.querySelectorAll('[data-live-chg="' + s + '"]').forEach(function (el) {
      el.textContent = (chg >= 0 ? '+' : '') + chg.toFixed(2) + '%' + (el.dataset.suffix || '');
      el.classList.toggle('up', chg >= 0); el.classList.toggle('down', chg < 0);
    });
    if (v) document.querySelectorAll('[data-live-vol="' + s + '"]').forEach(function (el) { el.textContent = vol(v); });
    last[s] = price;
  }
  var polling = false;
  function poll() {
    if (polling) return; polling = true;
    var run = function () {
      fetch('https://api.coingecko.com/api/v3/simple/price?vs_currencies=usd&include_24hr_change=true&include_24hr_vol=true&ids=' + syms.map(function (s) { return GECKO[s]; }).join(','))
        .then(function (r) { return r.json(); })
        .then(function (d) { syms.forEach(function (s) { var x = d[GECKO[s]]; if (x) update(s, x.usd, x.usd_24h_change || 0, x.usd_24h_vol); }); })
        .catch(function () {});
    };
    run(); setInterval(run, 30000);
  }
  var opened = false;
  try {
    var ws = new WebSocket('wss://data-stream.binance.vision/stream?streams=' + syms.map(function (s) { return s.toLowerCase() + 'usdt@ticker'; }).join('/'));
    ws.onopen = function () { opened = true; };
    ws.onmessage = function (e) { var d = JSON.parse(e.data).data; if (d) update(d.s.replace(/USDT$/, ''), +d.c, +d.P, +d.q); };
    ws.onerror = ws.onclose = function () { poll(); };
    setTimeout(function () { if (!opened) poll(); }, 5000);
  } catch (e) { poll(); }
})();
</script>
@endpush
@endonce
