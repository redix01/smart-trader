@extends('frontpage.prime.layout')

@section('title', 'Prime Digital Capital — Upgrade the Way You Trade')

@push('head')
<script type="application/ld+json">
{"@context":"https://schema.org","@graph":[
 {"@type":"Organization","@id":"https://primedigitalcap.com/#organization","name":"Prime Digital Capital","alternateName":"PrimeDigitalCap",
  "url":"https://primedigitalcap.com/","email":"support@primedigitalcap.com",
  "description":"Online trading platform for crypto, stocks and currencies with live prices, a personal trade room and copy trading."},
 {"@type":"WebSite","@id":"https://primedigitalcap.com/#website","url":"https://primedigitalcap.com/","name":"Prime Digital Capital","publisher":{"@id":"https://primedigitalcap.com/#organization"}}
]}
</script>
@endpush

@section('content')

{{-- ================= HERO ================= --}}
<header class="hero">
  <div class="hero-glow"></div>
  <div class="hero-grid"></div>
  <div class="wrap">
    <p class="eyebrow">Trading platform</p>
    <h1>Upgrade the way <em>you trade</em></h1>
    <p class="lead">Prime Digital Capital blends advanced power with simplicity — strong security, 24/7 support, live market prices and an intuitive trade room built for crypto, stocks and currencies.</p>
    <div class="cta">
      @auth
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">Go to your dashboard</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-primary">Get started →</a>
        <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
      @endauth
    </div>
    <div class="trust"><span>Bank-grade security</span><span>Live prices, tick by tick</span><span>24/7 live-chat support</span></div>

    {{-- Platform preview (HTML mockup of the Trade Room) --}}
    <div class="preview reveal" aria-hidden="true">
      <div class="preview-inner">
        <div class="pv-bar"><i></i><i></i><i></i><span>primedigitalcap.com/user/live-trading</span></div>
        <div class="pv-body">
          <div class="pv-side">
            <b>Account</b>
            <a>Dashboard</a><a>Deposit</a><a>Withdraw</a>
            <b>Markets</b>
            <a class="on">Trade Room</a><a>Stock prices</a><a>Crypto prices</a><a>Copy trading</a>
          </div>
          <div class="pv-main">
            <div class="pv-head">
              <div><div class="sym">BTC / USD</div><div class="ch" data-hero-chg>+2.12% today</div></div>
              <div class="px" data-hero-px>$97,412.50</div>
            </div>
            <div class="pv-chart">
              <svg viewBox="0 0 600 260" preserveAspectRatio="none">
                <defs><linearGradient id="g" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#4f8cff" stop-opacity=".45"/><stop offset="1" stop-color="#4f8cff" stop-opacity="0"/></linearGradient></defs>
                <path d="M0 200 L40 186 L80 192 L120 160 L160 170 L200 138 L240 150 L280 112 L320 124 L360 96 L400 108 L440 74 L480 88 L520 56 L560 64 L600 40 L600 260 L0 260Z" fill="url(#g)"/>
                <path d="M0 200 L40 186 L80 192 L120 160 L160 170 L200 138 L240 150 L280 112 L320 124 L360 96 L400 108 L440 74 L480 88 L520 56 L560 64 L600 40" fill="none" stroke="#8fbcff" stroke-width="2.5"/>
              </svg>
            </div>
          </div>
          <div class="pv-order">
            <div class="pv-tabs"><span class="buy">Buy</span><span>Sell</span></div>
            <div class="pv-field">Order<b>Market</b></div>
            <div class="pv-field">Amount<b>$2,500.00</b></div>
            <div class="pv-field">Leverage<b>10x</b></div>
            <div class="pv-field">Take profit<b>$99,800</b></div>
            <div class="pv-field">Stop loss<b>$95,900</b></div>
            <div class="pv-go">Buy BTC</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="ticker">
    <div class="tradingview-widget-container"><div class="tradingview-widget-container__widget"></div>
      <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
      {"symbols":[{"proName":"BINANCE:BTCUSDT","title":"Bitcoin"},{"proName":"BINANCE:ETHUSDT","title":"Ethereum"},{"proName":"BINANCE:SOLUSDT","title":"Solana"},{"proName":"NASDAQ:AAPL","title":"Apple"},{"proName":"NASDAQ:NVDA","title":"Nvidia"},{"proName":"NASDAQ:TSLA","title":"Tesla"},{"proName":"FOREXCOM:SPXUSD","title":"S&P 500"},{"proName":"FX_IDC:EURUSD","title":"EUR/USD"},{"proName":"OANDA:XAUUSD","title":"Gold"}],"showSymbolLogo":true,"isTransparent":true,"displayMode":"adaptive","colorTheme":"dark","locale":"en"}
      </script>
    </div>
  </div>
</header>

{{-- ================= FEATURES ================= --}}
<section id="features">
  <div class="wrap">
    <div class="sec-head center reveal">
      <p class="eyebrow">Features</p>
      <h2>All the features you need to trade with confidence</h2>
    </div>
    <div class="features">
      <div class="feature reveal">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg></div>
        <h3>Portfolio management</h3>
        <p>Track balances, open positions and performance across every asset from one clear dashboard.</p>
      </div>
      <div class="feature reveal">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2"/></svg></div>
        <h3>One-click copy trading</h3>
        <p>Follow experienced traders and mirror their positions automatically, with your own limits in place.</p>
      </div>
      <div class="feature reveal">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/></svg></div>
        <h3>Flexible leverage</h3>
        <p>Scale your exposure with adjustable leverage and clear margin requirements on every order.</p>
      </div>
      <div class="feature reveal">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
        <h3>Live market prices</h3>
        <p>Crypto prices stream tick by tick, and stock quotes refresh in real time inside your dashboard.</p>
      </div>
      <div class="feature reveal">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12h4l3-8 4 16 3-8h4"/></svg></div>
        <h3>Personal trade room</h3>
        <p>Place market orders on crypto and stocks, set take-profit and stop-loss, and manage every position yourself.</p>
      </div>
      <div class="feature reveal">
        <div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div>
        <h3>24/7 live-chat support</h3>
        <p>Reach a support team any time of day, ready to help with your account, deposits or trades.</p>
      </div>
    </div>
  </div>
</section>

{{-- ================= WHY US / MARKETS ================= --}}
<section id="markets">
  <div class="wrap">
    <div class="sec-head reveal">
      <p class="eyebrow">Why choose us?</p>
      <h2>Everything you need to trade, <em>in one place</em></h2>
      <p class="lead">Manage assets, place trades, track progress and move funds across markets, accounts and devices.</p>
    </div>

    <div class="bento">
      <div class="tile t-assets reveal">
        <h3>Unified asset management</h3>
        <p>Watch crypto, stocks and currencies side by side — prices below update live.</p>
        <div class="markets">
          <table>
            <thead><tr><th>Markets</th><th>Price (USD)</th><th>24h Chg.</th></tr></thead>
            <tbody id="live-markets">
              <tr data-sym="BTC"><td><span class="asset"><span class="dot" style="background:#f7931a">₿</span>BTC</span></td><td data-f="p">—</td><td data-f="c">—</td></tr>
              <tr data-sym="ETH"><td><span class="asset"><span class="dot" style="background:#627eea">Ξ</span>ETH</span></td><td data-f="p">—</td><td data-f="c">—</td></tr>
              <tr data-sym="SOL"><td><span class="asset"><span class="dot" style="background:#9945ff">S</span>SOL</span></td><td data-f="p">—</td><td data-f="c">—</td></tr>
              <tr data-sym="XRP"><td><span class="asset"><span class="dot" style="background:#23292f">X</span>XRP</span></td><td data-f="p">—</td><td data-f="c">—</td></tr>
              <tr data-sym="BNB"><td><span class="asset"><span class="dot" style="background:#f0b90b">B</span>BNB</span></td><td data-f="p">—</td><td data-f="c">—</td></tr>
            </tbody>
          </table>
        </div>
        <div class="markets" style="margin-top:12px">
          <div class="tradingview-widget-container" style="height:260px"><div class="tradingview-widget-container__widget" style="height:100%"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-quotes.js" async>
            {"width":"100%","height":"100%","symbolsGroups":[{"name":"Stocks","symbols":[{"name":"NASDAQ:AAPL","displayName":"Apple"},{"name":"NASDAQ:NVDA","displayName":"Nvidia"},{"name":"NASDAQ:NFLX","displayName":"Netflix"},{"name":"NASDAQ:ADBE","displayName":"Adobe"},{"name":"NASDAQ:TSLA","displayName":"Tesla"}]}],"showSymbolLogo":true,"isTransparent":true,"colorTheme":"dark","locale":"en","backgroundColor":"rgba(0,0,0,0)"}
            </script>
          </div>
        </div>
      </div>

      <div class="tile t-copy reveal">
        <h3>Copy trading</h3>
        <p>Pick a strategy provider, set how much to allocate, and mirror their trades automatically.</p>
        <div class="trader">
          <div class="tr-top">
            <div class="avatar">AM</div>
            <div class="who"><b>Strategy provider</b><span>Example profile</span></div>
            <a href="{{ route('register') }}" class="btn btn-primary follow">Copy</a>
          </div>
          <div class="tr-meta"><span>360 watchers</span><span>69.6K followers</span></div>
          <div class="tr-stats">
            <div><small>Win rate</small><b class="up">54.81%</b></div>
            <div><small>Profit share</small><b>43.3%</b></div>
            <div><small>Trades</small><b>578</b></div>
            <div><small>Wins</small><b>28</b></div>
            <div><small>Losses</small><b>0</b></div>
            <div><small>Min. start</small><b>$5,000</b></div>
          </div>
          <p class="note">Illustrative profile. Copying a trader means copying their losses as well as their gains.</p>
        </div>
      </div>

      <div class="tile t-mini reveal">
        <div class="mini-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 014-4h14M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg></div>
        <h3>Multi-account support</h3>
        <p>Swap between accounts and currencies conveniently.</p>
      </div>
      <div class="tile t-mini reveal">
        <div class="mini-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/></svg></div>
        <h3>Global market access</h3>
        <p>Trade crypto, stocks and currencies in one unified view.</p>
      </div>
      <div class="tile t-mini reveal">
        <div class="mini-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
        <h3>Fast deposits &amp; withdrawals</h3>
        <p>Move funds in and out quickly with supported payment methods.</p>
      </div>
    </div>
  </div>
</section>

{{-- ================= CRYPTO CARD ================= --}}
<section>
  <div class="wrap card-sec">
    <div class="reveal">
      <p class="eyebrow">Crypto card</p>
      <h2>A crypto card for <em>seamless payments</em></h2>
      <p class="lead" style="margin-top:16px">Pay for services instantly using a secure crypto card designed for simple, reliable payments.</p>
      <div class="card-list">
        <div><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></div><div><h3>Seamless payments</h3><p>Pay for signals, subscriptions and platform features directly from your account.</p></div></div>
        <div><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><div><h3>Built-in security</h3><p>Protected with industry-standard encryption and advanced fraud detection.</p></div></div>
        <div><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg></div><div><h3>Physical &amp; virtual cards</h3><p>Get instant access with a virtual card, or order a physical card delivered to you.</p></div></div>
      </div>
    </div>
    <div class="cards-art reveal" aria-hidden="true">
      <div class="pcard a"><div class="row"><span class="brand">Prime Digital</span><span>VIRTUAL</span></div><div class="chip"></div><div class="row"><span class="num">•••• 1234</span><span>12/29</span></div></div>
      <div class="pcard b"><div class="row"><span class="brand">Prime Digital</span><span>PHYSICAL</span></div><div class="chip"></div><div class="row"><span class="num">•••• 8842</span><span>08/30</span></div></div>
    </div>
  </div>
</section>

{{-- ================= TRADING TOOLS ================= --}}
<section id="tools">
  <div class="wrap">
    <div class="sec-head center reveal">
      <p class="eyebrow">Trading tools</p>
      <h2>Tools that move at market speed</h2>
      <p class="lead">Trade in real time, manage multiple assets and scale exposure confidently with tools built for fast-moving markets.</p>
    </div>
    <div class="tools">
      <div class="tool reveal">
        <h3>Built for speed</h3>
        <p>Built to keep up with live markets — fast execution and fast-moving signals.</p>
        <div class="signal-board">
          <div><small>TRADES</small><b>203</b></div>
          <div><small>PROFITABLE</small><b class="up">12</b></div>
          <div><small>ACTIVE SIGNALS</small><b>20</b></div>
        </div>
        <div class="bars"><i style="height:40%"></i><i style="height:65%"></i><i style="height:52%"></i><i style="height:80%"></i><i style="height:70%"></i><i style="height:92%"></i><i style="height:60%"></i><i style="height:84%"></i><i style="height:100%"></i><i style="height:76%"></i></div>
      </div>
      <div class="tool reveal">
        <h3>Adjustable leverage</h3>
        <p>Fine-tune exposure with responsive controls and clear risk boundaries.</p>
        <div class="lev">
          <div class="lev-track"><div class="lev-fill"></div><div class="lev-knob"></div></div>
          <div class="lev-scale"><span>1x</span><span>10x</span><span>25x</span><span>50x</span></div>
        </div>
        <p class="note" style="margin-top:22px">Leverage magnifies losses as well as gains.</p>
      </div>
    </div>

    <div class="stats" style="margin-top:16px">
      <div class="stat reveal"><div class="n">100+</div><div class="l">Crypto, stock &amp; FX markets</div></div>
      <div class="stat reveal"><div class="n">24/7</div><div class="l">Crypto trading &amp; support</div></div>
      <div class="stat reveal"><div class="n">Live</div><div class="l">Tick-by-tick crypto prices</div></div>
      <div class="stat reveal"><div class="n">3 min</div><div class="l">To open an account</div></div>
    </div>
  </div>
</section>

{{-- ================= HOW IT WORKS ================= --}}
<section>
  <div class="wrap">
    <div class="sec-head center reveal">
      <p class="eyebrow">Get started</p>
      <h2>Start trading in three steps</h2>
      <p class="lead">From sign-up to your first trade in a few minutes.</p>
    </div>
    <div class="quotes">
      <div class="quote reveal"><span class="eyebrow" style="margin:0;align-self:flex-start">Step 01</span><h3>Create your account</h3><p>Sign up with your email and verify your identity to secure your account.</p></div>
      <div class="quote reveal"><span class="eyebrow" style="margin:0;align-self:flex-start">Step 02</span><h3>Fund your wallet</h3><p>Deposit with a supported payment method and see your balance on the dashboard.</p></div>
      <div class="quote reveal"><span class="eyebrow" style="margin:0;align-self:flex-start">Step 03</span><h3>Trade or copy</h3><p>Open the Trade Room to place your own trades, or copy an experienced trader.</p></div>
    </div>
  </div>
</section>

{{-- ================= FAQ ================= --}}
<section>
  <div class="wrap">
    <div class="sec-head center reveal">
      <p class="eyebrow">FAQ</p>
      <h2>Questions, answered</h2>
    </div>
    <div class="faq reveal">
      <details><summary>What can I trade on Prime Digital Capital?</summary><p>Leading cryptocurrencies, US stocks and ETFs, and major currency pairs — all from a single account.</p></details>
      <details><summary>Are the prices in the dashboard live?</summary><p>Yes. Crypto prices stream tick by tick and stock quotes update in real time on the Stock Prices and Crypto Prices pages.</p></details>
      <details><summary>What is the Trade Room?</summary><p>Your personal trading desk: pick an asset, choose your amount and leverage, set take-profit and stop-loss, and manage your open positions.</p></details>
      <details><summary>How do I fund my account?</summary><p>Go to Deposit in your dashboard, choose a supported payment method and follow the instructions. Withdrawals are requested from the same dashboard.</p></details>
      <details><summary>Is trading risky?</summary><p>Yes. Trading involves substantial risk and you may lose some or all of your capital. Read our <a href="{{ route('risk') }}" class="text-link">risk disclosure</a> before you start.</p></details>
    </div>
  </div>
</section>

{{-- ================= CTA ================= --}}
<section style="padding-top:0">
  <div class="wrap">
    <div class="cta-band reveal">
      <h2>Start trading with <em>Prime Digital Capital</em></h2>
      <p class="lead">Open an account in minutes and get live prices, a personal trade room and copy trading in one place.</p>
      <div class="cta">
        @auth
          <a href="{{ route('user.dashboard') }}" class="btn btn-primary">Go to your dashboard</a>
        @else
          <a href="{{ route('register') }}" class="btn btn-primary">Get started →</a>
          <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
        @endauth
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
  var rows = document.getElementById('live-markets');
  var heroPx = document.querySelector('[data-hero-px]'), heroChg = document.querySelector('[data-hero-chg]');
  var last = {};
  function fmt(p) { return p >= 1 ? p.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : p.toLocaleString('en-US', {maximumSignificantDigits: 4}); }
  function update(sym, price, chg) {
    if (!isFinite(price)) return;
    var r = rows.querySelector('tr[data-sym="' + sym + '"]');
    if (r) {
      var p = r.querySelector('[data-f="p"]'), c = r.querySelector('[data-f="c"]');
      p.textContent = fmt(price);
      if (last[sym] !== undefined && last[sym] !== price) { p.className = price > last[sym] ? 'flash-up' : 'flash-down'; void p.offsetWidth; }
      c.textContent = (chg >= 0 ? '+' : '') + chg.toFixed(2) + '%';
      c.className = chg >= 0 ? 'up' : 'down';
    }
    if (sym === 'BTC' && heroPx) {
      heroPx.textContent = '$' + fmt(price);
      heroChg.textContent = (chg >= 0 ? '+' : '') + chg.toFixed(2) + '% today';
      heroChg.style.color = chg >= 0 ? 'var(--up)' : 'var(--down)';
    }
    last[sym] = price;
  }
  var GECKO = {BTC: 'bitcoin', ETH: 'ethereum', SOL: 'solana', XRP: 'ripple', BNB: 'binancecoin'};
  var polling = false;
  function poll() {
    if (polling) return; polling = true;
    var run = function () {
      fetch('https://api.coingecko.com/api/v3/simple/price?vs_currencies=usd&include_24hr_change=true&ids=' + Object.values(GECKO).join(','))
        .then(function (r) { return r.json(); })
        .then(function (d) { Object.keys(GECKO).forEach(function (s) { var x = d[GECKO[s]]; if (x) update(s, x.usd, x.usd_24h_change || 0); }); })
        .catch(function () {});
    };
    run(); setInterval(run, 30000);
  }
  var opened = false;
  try {
    var ws = new WebSocket('wss://data-stream.binance.vision/stream?streams=' + Object.keys(GECKO).map(function (s) { return s.toLowerCase() + 'usdt@ticker'; }).join('/'));
    ws.onopen = function () { opened = true; };
    ws.onmessage = function (e) { var d = JSON.parse(e.data).data; if (d) update(d.s.replace(/USDT$/, ''), +d.c, +d.P); };
    ws.onerror = ws.onclose = function () { poll(); };
    setTimeout(function () { if (!opened) poll(); }, 5000);
  } catch (e) { poll(); }
})();
</script>
@endpush
