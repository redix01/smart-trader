@extends('frontpage.prime.layout')

@section('title', 'Prime Digital Capital — Upgrade the Way You Trade')

@push('head')
<link rel="preload" as="video" href="{{ asset('prime/hero-bg.mp4') }}" type="video/mp4">
<script type="application/ld+json">
{"@context":"https://schema.org","@graph":[
 {"@type":"Organization","@id":"https://primedigitalcap.com/#organization","name":"Prime Digital Capital","alternateName":"PrimeDigitalCap",
  "url":"https://primedigitalcap.com/","email":"support@primedigitalcap.com",
  "description":"Online trading platform for crypto, stocks and currencies with live prices, a personal trade room and copy trading."},
 {"@type":"WebSite","@id":"https://primedigitalcap.com/#website","url":"https://primedigitalcap.com/","name":"Prime Digital Capital","publisher":{"@id":"https://primedigitalcap.com/#organization"}}
]}
</script>
@endpush

@php
  // Candles for the dashboard mockup chart (deterministic so the page doesn't jump between loads).
  mt_srand(11);
  $candles = []; $p = 150;
  for ($i = 0; $i < 64; $i++) {
      $o = $p; $c = $p + mt_rand(-200, 205) / 10; $c = max(30, min(235, $c));
      $h = min($o, $c) - mt_rand(0, 110) / 10; $l = max($o, $c) + mt_rand(0, 110) / 10;
      $candles[] = [$i * 9.2 + 6, $o, $c, $h, $l]; $p = $c;
  }
  $svgIcon = fn ($d) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">'.$d.'</svg>';
@endphp

@section('content')

{{-- ================= HERO (video background) ================= --}}
<header class="hero">
  <video class="hero-video" autoplay muted loop playsinline preload="auto" poster="{{ asset('prime/hero-poster.jpg') }}" aria-hidden="true">
    <source src="{{ asset('prime/hero-bg.mp4') }}" type="video/mp4">
  </video>
  <div class="hero-shade"></div>
  <div class="wrap">
    <span class="pill reveal">Trading Platform</span>
    <h1 class="reveal">Upgrade the way you trade</h1>
    <p class="lead reveal">Prime Digital Capital blends advanced power with simplicity, offering strong security, 24/7 support, and an intuitive trading platform.</p>
    <div class="cta reveal">
      @auth
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">Go to your dashboard</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-primary">Get started</a>
      @endauth
    </div>

    {{-- Dashboard mockup --}}
    <div class="mock reveal" aria-hidden="true">
      <div class="mk">
        <div class="mk-side">
          <span class="logo"><span class="mark">PD</span></span>
          <a class="on">{!! $svgIcon('<path d="M3 11l9-8 9 8v10H3z"/>') !!}Dashboard</a>
          <a>{!! $svgIcon('<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>') !!}Deposit</a>
          <a>{!! $svgIcon('<circle cx="12" cy="12" r="9"/><path d="M8 12h8"/>') !!}Withdraw</a>
          <a>{!! $svgIcon('<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 10h16"/>') !!}Assets</a>
          <a>{!! $svgIcon('<circle cx="12" cy="12" r="9"/><path d="M12 3v9l6 3"/>') !!}Markets</a>
          <a>{!! $svgIcon('<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>') !!}Trade Room</a>
          <a>{!! $svgIcon('<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0"/>') !!}Copy trading</a>
          <a>{!! $svgIcon('<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.6 1.6 0 00-2.7 1.1V21a2 2 0 11-4 0v-.1A1.6 1.6 0 009 19.4a1.6 1.6 0 00-1.8.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1A1.6 1.6 0 003 15.2a1.6 1.6 0 00-1.5-1H1.4a2 2 0 110-4h.1A1.6 1.6 0 003 9a1.6 1.6 0 00-.3-1.8l-.1-.1a2 2 0 112.8-2.8l.1.1A1.6 1.6 0 007.3 4.6H7.4A1.6 1.6 0 009 3.1V3a2 2 0 114 0v.1a1.6 1.6 0 001 1.5 1.6 1.6 0 001.8-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.6 1.6 0 00-.3 1.8V9a1.6 1.6 0 001.5 1h.1a2 2 0 110 4h-.1a1.6 1.6 0 00-1.5 1z"/>') !!}Settings</a>
        </div>
        <div class="mk-main">
          <div class="mk-top">
            <span>Dashboard</span>
            <span class="mk-bal"><span>14,869.63 USD <b>REAL</b></span></span>
          </div>
          <div class="mk-grid">
            <div class="mk-pane">
              <div class="mk-label">Total balance</div>
              <div class="mk-big">$14,869.63</div>
              <div class="mk-assets">
                <div class="hd"><span>Total assets</span><a>View all assets</a></div>
                <div class="mk-row"><span class="coin" style="background:#f7931a">₿</span><span class="nm">Bitcoin<small>BTC</small></span><span class="vl" data-live-price="BTC" data-prefix="$">—</span></div>
                <div class="mk-row"><span class="coin" style="background:#627eea">Ξ</span><span class="nm">Ethereum<small>ETH</small></span><span class="vl" data-live-price="ETH" data-prefix="$">—</span></div>
                <div class="mk-row"><span class="coin" style="background:#2563eb">$</span><span class="nm">US Dollar<small>USD</small></span><span class="vl">$5,269.59<small>Cash</small></span></div>
                <div class="mk-row"><span class="coin" style="background:#e5e7eb;color:#111">A</span><span class="nm">Apple<small>AAPL</small></span><span class="vl">$1,333.80<small>7.77 AAPL</small></span></div>
              </div>
            </div>
            <div class="mk-pane">
              <div class="mk-label">Categories</div>
              <div class="cat-bar"><i style="width:78%;background:#3b82f6"></i><i style="width:8%;background:#7dd3fc"></i><i style="width:10%;background:#2dd4a7"></i><i style="width:4%;background:#f43f5e"></i></div>
              <div class="cat-legend">
                <span style="--c:#3b82f6">Crypto<b>78%</b></span>
                <span style="--c:#7dd3fc">Stocks<b>8%</b></span>
                <span style="--c:#2dd4a7">Fiat<b>10%</b></span>
              </div>
              <div class="meter"><div class="row"><span>Trading progress</span><span>40%</span></div><div class="track"><i style="width:40%"></i></div></div>
              <div class="meter"><div class="row"><span>Signal strength</span><span style="color:var(--warn)">60%</span></div>
                <div class="segs">@for($i = 0; $i < 16; $i++)<i class="{{ $i < 10 ? 'on' : '' }}"></i>@endfor</div>
              </div>
            </div>
            <div class="mk-pane mk-chart">
              <div class="bar"><span class="tag">BTC/USD</span><span>1m</span><span style="margin-left:auto" class="mono" data-live-price="BTC" data-prefix="$">—</span><span class="mono" data-live-chg="BTC"></span></div>
              <svg viewBox="0 0 600 260" preserveAspectRatio="none">
                @foreach([52,104,156,208] as $y)<line x1="0" x2="600" y1="{{ $y }}" y2="{{ $y }}" stroke="rgba(170,190,255,.07)"/>@endforeach
                @foreach($candles as [$x, $o, $c, $h, $l])
                  @php $col = $c <= $o ? '#2dd4a7' : '#f43f5e'; @endphp
                  <line x1="{{ $x }}" x2="{{ $x }}" y1="{{ $h }}" y2="{{ $l }}" stroke="{{ $col }}" stroke-width="1"/>
                  <rect x="{{ $x - 3 }}" y="{{ min($o, $c) }}" width="6" height="{{ max(1.5, abs($c - $o)) }}" fill="{{ $col }}"/>
                @endforeach
              </svg>
            </div>
            <div class="mk-pane mk-order">
              <div class="tabs"><span class="on">Buy</span><span>Sell</span><span>Convert</span></div>
              <div class="fld"><small>Trade type</small><div><span>Crypto</span><span>▾</span></div></div>
              <div class="fld"><small>Trade amount</small><div><span>$1,000</span><span style="color:var(--text-3)">BTC</span></div></div>
              <div class="fld"><small>Leverage</small><div><span>10x</span><span style="color:var(--text-3)">Max 50x</span></div></div>
            </div>
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
<section class="sec no-line" id="features" style="padding-top:96px">
  <div class="wrap">
    <div class="fgrid reveal">
      <div><div class="ft">{!! $svgIcon('<path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/>') !!}<h3>Portfolio management</h3></div><p>Effortlessly manage your portfolio with an intuitive, user-friendly dashboard.</p></div>
      <div><div class="ft">{!! $svgIcon('<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2"/>') !!}<h3>One-click copy trading</h3></div><p>Copy the trades of experienced traders with a single click, with your own limits in place.</p></div>
      <div><div class="ft">{!! $svgIcon('<path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/>') !!}<h3>Up to 50x leverage</h3></div><p>Scale your trading potential with adjustable leverage and clear margin requirements.</p></div>
      <div><div class="ft">{!! $svgIcon('<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>') !!}<h3>Personal trade room</h3></div><p>Place your own crypto and stock trades, set take-profit and stop-loss, and manage every position.</p></div>
      <div><div class="ft">{!! $svgIcon('<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>') !!}<h3>Real-time prices</h3></div><p>Crypto prices stream tick by tick and stock quotes update live inside your dashboard.</p></div>
      <div><div class="ft">{!! $svgIcon('<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>') !!}<h3>24/7 live-chat support</h3></div><p>Access world-class support, ready to help with any question about your account or trades.</p></div>
    </div>
  </div>
</section>

{{-- ================= WHY US ================= --}}
<section class="sec isolate" id="markets">
  <div class="candles-bg"></div>
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">Why choose us?</span>
      <h2>Everything you need to trade, in one place</h2>
      <p class="lead">Manage assets, place trades, track progress, and move funds seamlessly across markets, accounts, and devices.</p>
    </div>

    <div class="duo">
      <div class="reveal">
        <h3>Unified asset management</h3>
        <p>Manage multiple assets and accounts from a single interface.</p>
        <div class="panel">
          <h4>Markets <span class="mono" style="float:right;font-size:12px;color:var(--up)">● LIVE</span></h4>
          <table class="mtable">
            <thead><tr><th>Markets</th><th>Price (USD)</th><th>24h Chg.</th></tr></thead>
            <tbody>
              @foreach([['BTC','₿','#f7931a'],['ETH','Ξ','#627eea'],['SOL','S','#14b8a6'],['XRP','X','#334155'],['BNB','B','#d4a017'],['DOGE','Ð','#c2a633']] as [$s, $g, $c])
              <tr><td><span class="asset"><span class="star">★</span><span class="coin" style="background:{{ $c }}">{{ $g }}</span>{{ $s }}</span></td><td data-live-price="{{ $s }}">—</td><td data-live-chg="{{ $s }}">—</td></tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="reveal">
        <h3>Copy trading</h3>
        <p>Mirror experienced traders in real time, with seamless execution and tight spreads.</p>
        <div class="panel">
          <div class="trader-hd">
            <div class="av">PD</div>
            <div><b>Strategy provider</b><small>Example profile</small>
              <div class="trader-meta"><span>360 watchers</span><span>69.6K followers</span></div>
            </div>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm" style="margin-left:auto">Copy</a>
          </div>
          <div class="tstats">
            <div><small>Win rate</small><b class="up">54.81%</b></div>
            <div><small>Profit share</small><b class="down">43.3%</b></div>
            <div><small>Wins</small><b>28</b></div>
            <div><small>Losses</small><b>0</b></div>
            <div><small>Trades</small><b>578</b></div>
            <div><small>Min. startup</small><b>$5,000</b></div>
          </div>
          <p class="note">Illustrative profile. Copying a trader means copying their losses as well as their gains.</p>
        </div>
      </div>
    </div>

    <div class="trio">
      <div class="reveal"><div class="ft">{!! $svgIcon('<path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 014-4h14M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 01-4 4H3"/>') !!}<h3>Multi-account support</h3></div><p>Swap between accounts and currencies conveniently.</p></div>
      <div class="reveal"><div class="ft">{!! $svgIcon('<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>') !!}<h3>Global market access</h3></div><p>Trade assets across crypto, stocks, and currencies in one unified view.</p></div>
      <div class="reveal"><div class="ft">{!! $svgIcon('<path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/>') !!}<h3>Fast deposits &amp; withdrawals</h3></div><p>Move funds in and out quickly with supported payment methods.</p></div>
    </div>
  </div>
</section>

{{-- ================= CRYPTO CARD ================= --}}
<section class="sec">
  <div class="wrap">
    <div class="card-stage reveal" aria-hidden="true">
      <div class="pcard">
        <div class="row"><span class="logo"><span class="mark">PD</span></span><span class="chip"></span></div>
        <div class="row"><span class="num">•••• 1234</span><span class="net">VISA</span></div>
      </div>
    </div>
    <div class="sec-head reveal" style="margin-bottom:56px">
      <span class="pill">Crypto card</span>
      <h2>A crypto card for seamless payments</h2>
      <p class="lead">Pay for services instantly using a secure crypto card designed for simple, reliable payments.</p>
    </div>
    <div class="cols3 reveal">
      <div><div class="ft">{!! $svgIcon('<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>') !!}<h3>Seamless payments</h3></div><p>Pay for signals, subscriptions, and platform features directly from your account.</p></div>
      <div><div class="ft">{!! $svgIcon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>') !!}<h3>Built-in security</h3></div><p>Protected with industry-standard encryption and advanced fraud detection.</p></div>
      <div><div class="ft">{!! $svgIcon('<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>') !!}<h3>Physical &amp; virtual cards</h3></div><p>Get instant access with a virtual card, or order a physical card delivered to you.</p></div>
    </div>
  </div>
</section>

{{-- ================= TRADING TOOLS ================= --}}
<section class="sec" id="tools">
  <div class="wrap">
    <div class="orbs reveal" aria-hidden="true"><i></i><i class="m"></i><i class="c">{!! $svgIcon('<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>') !!}</i><i class="m"></i><i></i></div>
    <div class="sec-head reveal">
      <span class="pill">Trading tools</span>
      <h2>Tools that move at market speed</h2>
      <p class="lead">Trade in real time, manage multiple assets, and scale exposure confidently with tools built for fast-moving markets.</p>
    </div>
    <div class="tools">
      <div class="reveal">
        <h3>Built for speed</h3>
        <p>Built to keep up with live markets, seamless execution and fast-moving signals.</p>
        <div class="board">
          <div class="leds">@for($i = 0; $i < 10; $i++)<i class="{{ $i < 7 ? 'on' : '' }}"></i>@endfor</div>
          <div class="scale"><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span></div>
          <div class="nums">
            <div class="big" id="sig-trades">187<small>S/H</small></div>
            <div class="kv"><div><small>TRADES</small><b id="sig-t">12</b></div><div><small>PROFITS</small><b class="up" id="sig-p">395</b></div></div>
          </div>
          <div class="foot2"><span>20 ACTIVE SIGNALS</span><span style="display:flex;gap:8px;align-items:center">STRENGTH <span class="str"><i></i><i></i><i></i><i></i><i class="off"></i></span></span></div>
        </div>
      </div>
      <div class="reveal">
        <h3>Up to 50x leverage</h3>
        <p>Fine-tune exposure with responsive controls and clear risk boundaries.</p>
        <div class="levpanel">
          <div class="fld"><small>Trade amount</small><div><span>$1,000</span><span style="color:var(--text-3)">USD</span></div></div>
          <div class="fld"><small>Position size</small><div><span id="lev-size">$25,000</span><span style="color:var(--text-3)" id="lev-x">25x</span></div></div>
          <div class="fld"><small>Leverage</small></div>
          <div class="lev-steps" id="lev-steps"><span data-x="1">1x</span><span data-x="5">5x</span><span data-x="10">10x</span><span data-x="25" class="on">25x</span><span data-x="50">50x</span></div>
          <div class="lev-track"><i id="lev-fill" style="width:50%"></i></div>
          <p class="note">Leverage magnifies losses as well as gains.</p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ================= GET STARTED ================= --}}
<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">Get started</span>
      <h2>Start trading in three steps</h2>
      <p class="lead">From sign-up to your first trade in a few minutes.</p>
    </div>
    <div class="steps">
      <div class="step reveal"><span class="n">01</span><h3>Create your account</h3><p>Sign up with your email and verify your identity to secure your account.</p></div>
      <div class="step reveal"><span class="n">02</span><h3>Fund your wallet</h3><p>Deposit with a supported payment method and see your balance on the dashboard.</p></div>
      <div class="step reveal"><span class="n">03</span><h3>Trade or copy</h3><p>Open the Trade Room to place your own trades, or copy an experienced trader.</p></div>
    </div>
  </div>
</section>

{{-- ================= FAQ ================= --}}
<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">FAQ</span>
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

@include('frontpage.prime.cta')

@endsection

@include('frontpage.prime.live-crypto')

@push('scripts')
<script>
(function () {
  // Leverage selector
  var steps = document.getElementById('lev-steps');
  if (steps) steps.addEventListener('click', function (e) {
    var s = e.target.closest('[data-x]'); if (!s) return;
    steps.querySelectorAll('span').forEach(function (x) { x.classList.toggle('on', x === s); });
    var x = +s.dataset.x;
    document.getElementById('lev-size').textContent = '$' + (1000 * x).toLocaleString('en-US');
    document.getElementById('lev-x').textContent = x + 'x';
    document.getElementById('lev-fill').style.width = (x / 50 * 100) + '%';
  });
  // Signal board ticks
  var t = 12, p = 395, leds = document.querySelectorAll('.board .leds i');
  setInterval(function () {
    t += Math.random() < .5 ? 1 : 0; p += Math.floor(Math.random() * 4);
    document.getElementById('sig-t').textContent = t;
    document.getElementById('sig-p').textContent = p;
    var on = 5 + Math.floor(Math.random() * 5);
    leds.forEach(function (l, i) { l.classList.toggle('on', i < on); });
  }, 1600);
})();
</script>
@endpush
