@extends('frontpage.prime.layout')

@section('title', 'Prime Digital Capital — Stocks, Crypto, Copy Trading & AI Traders')
@section('description', 'One account for stocks and crypto: a personal Trade Room, live prices, copy trading, AI traders, trading bots, signals, staking and mining.')

@push('head')
<link rel="preload" as="video" href="{{ asset('prime/hero-bg.mp4') }}" type="video/mp4">
<script type="application/ld+json">
{"@context":"https://schema.org","@graph":[
 {"@type":"Organization","@id":"https://primedigitalcap.com/#organization","name":"Prime Digital Capital","alternateName":"PrimeDigitalCap",
  "url":"https://primedigitalcap.com/","email":"support@primedigitalcap.com",
  "description":"Online trading platform for stocks and crypto with a personal Trade Room, live prices, copy trading, AI traders, trading bots, signals, staking and mining."},
 {"@type":"WebSite","@id":"https://primedigitalcap.com/#website","url":"https://primedigitalcap.com/","name":"Prime Digital Capital","publisher":{"@id":"https://primedigitalcap.com/#organization"}}
]}
</script>
@endpush

@php
  // Live platform data. Every query is guarded so the landing page still renders if the DB is empty or unavailable.
  $safe = function (callable $q, $default) { try { return $q(); } catch (\Throwable $e) { return $default; } };
  $copyTraders = $safe(fn () => \App\Models\CopyTrader::where('status', 'active')->orderByDesc('win_rate')->take(3)->get(), collect());
  $aiPlans = $safe(fn () => \App\Models\AiTraderPlan::where('is_active', true)->orderBy('price')->take(3)->get(), collect());
  $plans = $safe(fn () => \App\Models\Plan::where('is_active', true)->get()->groupBy('type'), collect());
  $fromPrice = fn ($type, $field = 'min_funding') => optional($plans->get($type))->min($field);
  $pairCount = $safe(fn () => \App\Models\TradePair::count(), 0);

  // Candles for the Trade Room mockup chart (seeded so the page doesn't change between loads).
  mt_srand(11);
  $candles = []; $p = 150;
  for ($i = 0; $i < 64; $i++) {
      $o = $p; $c = $p + mt_rand(-200, 205) / 10; $c = max(30, min(235, $c));
      $h = min($o, $c) - mt_rand(0, 110) / 10; $l = max($o, $c) + mt_rand(0, 110) / 10;
      $candles[] = [$i * 9.2 + 6, $o, $c, $h, $l]; $p = $c;
  }
  $svgIcon = fn ($d) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">'.$d.'</svg>';
  $go = fn ($userRoute) => auth()->check() ? route($userRoute) : route('register');
  $money = fn ($v) => '$'.number_format((float) $v, ((float) $v == floor((float) $v)) ? 0 : 2);
@endphp

@section('content')

{{-- ================= HERO ================= --}}
<header class="hero">
  <video class="hero-video" autoplay muted loop playsinline preload="auto" poster="{{ asset('prime/hero-poster.jpg') }}" aria-hidden="true">
    <source src="{{ asset('prime/hero-bg.mp4') }}" type="video/mp4">
  </video>
  <div class="hero-shade"></div>
  <div class="wrap">
    <span class="pill reveal">Stocks · Crypto · Copy trading · AI traders</span>
    <h1 class="reveal">Every market. One trading account.</h1>
    <p class="lead reveal">Trade stocks and crypto yourself in the Trade Room, copy proven traders, or hand a strategy to an AI trader — then follow it all from a single dashboard.</p>
    <div class="cta reveal">
      @auth
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">Open your dashboard</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-primary">Create free account</a>
        <a href="#platform" class="btn btn-ghost">See what’s inside</a>
      @endauth
    </div>

    {{-- Trade Room mockup --}}
    <div class="mock reveal" aria-hidden="true">
      <div class="mk">
        <div class="mk-side">
          <span class="logo"><span class="mark">PD</span></span>
          <a>{!! $svgIcon('<path d="M3 11l9-8 9 8v10H3z"/>') !!}Dashboard</a>
          <a class="on">{!! $svgIcon('<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>') !!}Trade Room</a>
          <a>{!! $svgIcon('<path d="M4 20V10M10 20V4M16 20v-7"/>') !!}Stock prices</a>
          <a>{!! $svgIcon('<circle cx="12" cy="12" r="9"/><path d="M12 3v9l6 3"/>') !!}Crypto prices</a>
          <a>{!! $svgIcon('<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0"/>') !!}Copy trading</a>
          <a>{!! $svgIcon('<rect x="4" y="7" width="16" height="12" rx="2"/><path d="M12 3v4M9 13h.01M15 13h.01"/>') !!}AI traders</a>
          <a>{!! $svgIcon('<path d="M12 2v20M5 7l7-5 7 5M5 17l7 5 7-5"/>') !!}Staking</a>
          <a>{!! $svgIcon('<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>') !!}Deposit</a>
        </div>
        <div class="mk-main">
          <div class="mk-top">
            <span>Trade Room</span>
            <span class="mk-bal"><span>14,869.63 USD <b>LIVE</b></span></span>
          </div>
          <div class="mk-grid">
            <div class="mk-pane">
              <div class="mk-label">Portfolio value</div>
              <div class="mk-big">$14,869.63</div>
              <div class="mk-assets">
                <div class="hd"><span>Holdings</span><a>Portfolio</a></div>
                <div class="mk-row"><span class="coin" style="background:#f7931a">₿</span><span class="nm">Bitcoin<small>BTC</small></span><span class="vl" data-live-price="BTC" data-prefix="$">—</span></div>
                <div class="mk-row"><span class="coin" style="background:#627eea">Ξ</span><span class="nm">Ethereum<small>ETH</small></span><span class="vl" data-live-price="ETH" data-prefix="$">—</span></div>
                <div class="mk-row"><span class="coin" style="background:#e5e7eb;color:#111">A</span><span class="nm">Apple<small>AAPL · stock</small></span><span class="vl">$1,333.80<small>7.77 shares</small></span></div>
                <div class="mk-row"><span class="coin" style="background:#16a34a">N</span><span class="nm">Nvidia<small>NVDA · stock</small></span><span class="vl">$2,104.10<small>11.5 shares</small></span></div>
              </div>
            </div>
            <div class="mk-pane">
              <div class="mk-label">Allocation</div>
              <div class="cat-bar"><i style="width:46%;background:#3b82f6"></i><i style="width:30%;background:#7dd3fc"></i><i style="width:16%;background:#2dd4a7"></i><i style="width:8%;background:#f59e0b"></i></div>
              <div class="cat-legend">
                <span style="--c:#3b82f6">Crypto<b>46%</b></span>
                <span style="--c:#7dd3fc">Stocks<b>30%</b></span>
                <span style="--c:#2dd4a7">Copy trading<b>16%</b></span>
                <span style="--c:#f59e0b">Staking<b>8%</b></span>
              </div>
              <div class="meter"><div class="row"><span>AI trader confidence</span><span>72%</span></div><div class="track"><i style="width:72%"></i></div></div>
              <div class="meter"><div class="row"><span>Signal strength</span><span style="color:var(--warn)">60%</span></div>
                <div class="segs">@for($i = 0; $i < 16; $i++)<i class="{{ $i < 10 ? 'on' : '' }}"></i>@endfor</div>
              </div>
            </div>
            <div class="mk-pane mk-chart">
              <div class="bar"><span class="tag">BTC/USD</span><span>15m</span><span style="margin-left:auto" class="mono" data-live-price="BTC" data-prefix="$">—</span><span class="mono" data-live-chg="BTC"></span></div>
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
              <div class="tabs"><span class="on">Buy</span><span>Sell</span><span>Close</span></div>
              <div class="fld"><small>Market</small><div><span>Crypto · BTC</span><span>▾</span></div></div>
              <div class="fld"><small>Amount</small><div><span>$1,000</span><span style="color:var(--text-3)">USD</span></div></div>
              <div class="fld"><small>Take profit / Stop loss</small><div><span>+8%</span><span style="color:var(--down)">−3%</span></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="ticker">
    <div class="tradingview-widget-container"><div class="tradingview-widget-container__widget"></div>
      <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
      {"symbols":[{"proName":"BINANCE:BTCUSDT","title":"Bitcoin"},{"proName":"BINANCE:ETHUSDT","title":"Ethereum"},{"proName":"BINANCE:SOLUSDT","title":"Solana"},{"proName":"NASDAQ:AAPL","title":"Apple"},{"proName":"NASDAQ:NVDA","title":"Nvidia"},{"proName":"NASDAQ:TSLA","title":"Tesla"},{"proName":"NASDAQ:MSFT","title":"Microsoft"},{"proName":"FOREXCOM:SPXUSD","title":"S&P 500"},{"proName":"OANDA:XAUUSD","title":"Gold"}],"showSymbolLogo":true,"isTransparent":true,"displayMode":"adaptive","colorTheme":"dark","locale":"en"}
      </script>
    </div>
  </div>
</header>

{{-- ================= PLATFORM OVERVIEW ================= --}}
<section class="sec no-line" id="platform" style="padding-top:96px">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">Inside your account</span>
      <h2>Six ways to put your money to work</h2>
      <p class="lead">Pick one or combine them. Each tool lives in the same dashboard and draws from the same wallet.</p>
    </div>
    <div class="fgrid reveal">
      <a href="{{ $go('user.liveTrading.index') }}"><div class="ft">{!! $svgIcon('<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>') !!}<h3>Trade Room</h3></div><p>Your own desk for buying and selling stocks and crypto, with take-profit and stop-loss on every order.</p><span class="more">Start trading →</span></a>
      <a href="{{ $go('user.copyTrading.index') }}"><div class="ft">{!! $svgIcon('<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M16 4.5a3.5 3.5 0 010 7M22 20a7 7 0 00-4-6.3"/>') !!}<h3>Copy trading</h3></div><p>Choose a trader, set an amount, and their positions are mirrored into your account automatically.</p><span class="more">Browse traders →</span></a>
      <a href="{{ route('ai-traders.index') }}"><div class="ft">{!! $svgIcon('<rect x="4" y="7" width="16" height="12" rx="2"/><path d="M12 3v4M9 13h.01M15 13h.01"/>') !!}<h3>AI traders</h3></div><p>Strategy-driven traders that work a watchlist of stocks with set risk, stop-loss and take-profit rules.</p><span class="more">Meet the AI traders →</span></a>
      <a href="{{ $go('user.botTrading.index') }}"><div class="ft">{!! $svgIcon('<rect x="5" y="8" width="14" height="12" rx="2"/><path d="M12 4v4M9 14h.01M15 14h.01M2 13h3M19 13h3"/>') !!}<h3>Trading bots</h3></div><p>Automate a rule set on a trading pair and let the bot place and close trades around the clock.</p><span class="more">Set up a bot →</span></a>
      <a href="{{ $go('user.plan.signal') }}"><div class="ft">{!! $svgIcon('<path d="M2 12h3l3-8 4 16 3-8h7"/>') !!}<h3>Signals</h3></div><p>Entry, target and stop levels from our analysts, delivered to your dashboard as markets move.</p><span class="more">See signal plans →</span></a>
      <a href="{{ $go('user.staking.index') }}"><div class="ft">{!! $svgIcon('<path d="M12 2v20M5 7l7-5 7 5M5 17l7 5 7-5"/>') !!}<h3>Staking &amp; mining</h3></div><p>Put idle balance into a staking or mining plan and track what it earns next to your trades.</p><span class="more">View plans →</span></a>
    </div>
  </div>
</section>

{{-- ================= LIVE MARKETS ================= --}}
<section class="sec isolate" id="markets">
  <div class="candles-bg"></div>
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">Live markets</span>
      <h2>Stocks and crypto, priced in real time</h2>
      <p class="lead">The same feeds you’ll see on the Stock Prices and Crypto Prices pages of your dashboard{{ $pairCount ? ' — '.$pairCount.' tradable pairs and counting' : '' }}.</p>
    </div>
    <div class="duo">
      <div class="reveal">
        <h3>Crypto, tick by tick</h3>
        <p>Every price change streams straight to the page — no refresh needed.</p>
        <div class="panel">
          <h4>Top coins <span class="mono" style="float:right;font-size:12px;color:var(--up)">● LIVE</span></h4>
          <table class="mtable">
            <thead><tr><th>Asset</th><th>Price (USD)</th><th>24h</th></tr></thead>
            <tbody>
              @foreach([['BTC','₿','#f7931a'],['ETH','Ξ','#627eea'],['SOL','S','#14b8a6'],['XRP','X','#334155'],['BNB','B','#d4a017'],['DOGE','Ð','#c2a633']] as [$s, $g, $c])
              <tr><td><span class="asset"><span class="coin" style="background:{{ $c }}">{{ $g }}</span>{{ $s }}</span></td><td data-live-price="{{ $s }}">—</td><td data-live-chg="{{ $s }}">—</td></tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="reveal">
        <h3>US stocks and ETFs</h3>
        <p>Quotes for the companies people trade most, plus the major index funds.</p>
        <div class="panel" style="padding:8px">
          <div class="tradingview-widget-container" style="height:420px"><div class="tradingview-widget-container__widget" style="height:100%"></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-quotes.js" async>
            {"width":"100%","height":"100%","symbolsGroups":[{"name":"Stocks","symbols":[{"name":"NASDAQ:AAPL","displayName":"Apple"},{"name":"NASDAQ:NVDA","displayName":"Nvidia"},{"name":"NASDAQ:MSFT","displayName":"Microsoft"},{"name":"NASDAQ:TSLA","displayName":"Tesla"},{"name":"NASDAQ:AMZN","displayName":"Amazon"},{"name":"AMEX:SPY","displayName":"S&P 500 ETF"}]}],"showSymbolLogo":true,"isTransparent":true,"colorTheme":"dark","locale":"en","backgroundColor":"rgba(0,0,0,0)"}
            </script>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ================= COPY TRADING (live from DB) ================= --}}
<section class="sec" id="copy">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">Copy trading</span>
      <h2>Follow a trader you trust</h2>
      <p class="lead">Compare track records, pick a trader and set your amount. When they open or close a position, so does your account.</p>
    </div>
    @include('frontpage.prime.copy-traders', ['copyTraders' => $copyTraders])
    <p class="note center" style="margin-top:22px">Past performance does not guarantee future results. Copying a trader copies their losses too.</p>
  </div>
</section>

{{-- ================= AI TRADERS (live from DB) ================= --}}
<section class="sec isolate" id="ai">
  <div class="candles-bg"></div>
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">AI traders</span>
      <h2>Let a strategy do the watching</h2>
      <p class="lead">Each plan gives you a team of AI traders working a set list of stocks, with risk limits, stop-loss and take-profit built in.</p>
    </div>
    @if($aiPlans->isNotEmpty())
      <div class="cards3">
        @foreach($aiPlans as $plan)
          <div class="pcard2 reveal {{ $loop->iteration === 2 ? 'feat' : '' }}">
            @if($loop->iteration === 2)<span class="badge">Recommended</span>@endif
            <h3>{{ $plan->name }}</h3>
            <div class="price">{{ $money($plan->price) }}<small> plan fee</small></div>
            <p class="desc">{{ \Illuminate\Support\Str::limit($plan->description, 110) }}</p>
            <div class="chips">
              @foreach(array_slice((array) $plan->stocks_trading, 0, 6) as $sym)<span>{{ $sym }}</span>@endforeach
              @if(count((array) $plan->stocks_trading) > 6)<span>+{{ count((array) $plan->stocks_trading) - 6 }}</span>@endif
            </div>
            <ul class="checks">
              <li>{{ $plan->number_of_traders }} AI traders</li>
              <li>Suggested capital {{ $money($plan->investment_amount) }}</li>
              @foreach(array_slice((array) $plan->features, 1, 2) as $f)<li>{{ $f }}</li>@endforeach
            </ul>
            <a href="{{ route('ai-traders.plan', $plan) }}" class="btn {{ $loop->iteration === 2 ? 'btn-primary' : 'btn-ghost' }}" style="width:100%;margin-top:auto">View plan</a>
          </div>
        @endforeach
      </div>
    @else
      <div class="center reveal"><a href="{{ route('ai-traders.index') }}" class="btn btn-primary">Meet the AI traders</a></div>
    @endif
  </div>
</section>

{{-- ================= AUTOMATION: BOTS + SIGNALS ================= --}}
<section class="sec" id="tools">
  <div class="wrap">
    <div class="orbs reveal" aria-hidden="true"><i></i><i class="m"></i><i class="c">{!! $svgIcon('<rect x="5" y="8" width="14" height="12" rx="2"/><path d="M12 4v4M9 14h.01M15 14h.01"/>') !!}</i><i class="m"></i><i></i></div>
    <div class="sec-head reveal">
      <span class="pill">Automation</span>
      <h2>Bots and signals that never sleep</h2>
      <p class="lead">Set the rules once. Bots trade them on your chosen pair day and night, and signals flag the setups worth a closer look.</p>
    </div>
    <div class="tools">
      <div class="reveal">
        <h3>Trading bots</h3>
        <p>Pick a pair, a strategy and your limits. The bot logs every trade so you can review its performance.</p>
        <div class="board">
          <div class="leds">@for($i = 0; $i < 10; $i++)<i class="{{ $i < 7 ? 'on' : '' }}"></i>@endfor</div>
          <div class="scale"><span>LOW</span><span></span><span>ACTIVITY</span><span></span><span>HIGH</span></div>
          <div class="nums">
            <div class="big"><span id="sig-trades">187</span><small>trades today</small></div>
            <div class="kv"><div><small>OPEN</small><b id="sig-t">12</b></div><div><small>CLOSED +</small><b class="up" id="sig-p">395</b></div></div>
          </div>
          <div class="foot2"><span>BTC/USDT · GRID BOT</span><span style="display:flex;gap:8px;align-items:center">STATUS <span class="str"><i></i><i></i><i></i><i></i><i class="off"></i></span></span></div>
        </div>
        <p class="note">Illustrative display.</p>
      </div>
      <div class="reveal">
        <h3>Signal plans</h3>
        <p>Analyst signals with entry, target and stop — choose how many you get{{ $fromPrice('signal', 'price') ? ', from '.$money($fromPrice('signal', 'price')) : '' }}.</p>
        <div class="levpanel">
          <div class="sig-row"><span class="tag up">BUY</span><b>ETH/USDT</b><span class="mono">Entry 1,790 · TP 1,880 · SL 1,745</span></div>
          <div class="sig-row"><span class="tag down">SELL</span><b>TSLA</b><span class="mono">Entry 352 · TP 331 · SL 361</span></div>
          <div class="sig-row"><span class="tag up">BUY</span><b>SOL/USDT</b><span class="mono">Entry 69.5 · TP 75 · SL 66.8</span></div>
          <p class="note">Sample signals for illustration only.</p>
          @if($plans->get('signal'))
          <div class="lev-steps" style="grid-template-columns:repeat({{ $plans->get('signal')->count() }},1fr);margin-top:18px">
            @foreach($plans->get('signal')->sortBy('price') as $sp)<span class="{{ $loop->first ? 'on' : '' }}">{{ \Illuminate\Support\Str::of($sp->name)->replace(' Signal Plan', '') }} · {{ $money($sp->price) }}</span>@endforeach
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ================= STAKING & MINING (live from DB) ================= --}}
<section class="sec" id="earn">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">Staking &amp; mining</span>
      <h2>Keep idle balance working</h2>
      <p class="lead">Between trades, move funds into a staking or mining plan and watch both sides of your account from one place.</p>
    </div>
    <div class="cols3 reveal">
      <div>
        <div class="ft">{!! $svgIcon('<path d="M12 2v20M5 7l7-5 7 5M5 17l7 5 7-5"/>') !!}<h3>Staking</h3></div>
        <p>Lock an amount for a set term and follow it from the Staking page.</p>
        @if($fromPrice('staking'))<p class="kp">From <b>{{ $money($fromPrice('staking')) }}</b> · {{ $plans->get('staking')->count() }} plans</p>@endif
        <a class="more" href="{{ $go('user.staking.index') }}">Start staking →</a>
      </div>
      <div>
        <div class="ft">{!! $svgIcon('<path d="M14 4l6 6-9 9H5v-6z"/><path d="M12 6l6 6"/>') !!}<h3>Mining</h3></div>
        <p>Rent mining capacity through a plan and see output build up in your dashboard.</p>
        @if($fromPrice('mining'))<p class="kp">From <b>{{ $money($fromPrice('mining')) }}</b> · {{ $plans->get('mining')->count() }} plans</p>@endif
        <a class="more" href="{{ $go('user.mining.index') }}">Start mining →</a>
      </div>
      <div>
        <div class="ft">{!! $svgIcon('<path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/>') !!}<h3>Trading plans</h3></div>
        <p>Managed trading plans with a funding range to match your budget.</p>
        @if($fromPrice('trading'))<p class="kp">From <b>{{ $money($fromPrice('trading')) }}</b> · {{ $plans->get('trading')->count() }} plans</p>@endif
        <a class="more" href="{{ $go('user.plan.trading') }}">Compare plans →</a>
      </div>
    </div>
  </div>
</section>

{{-- ================= ACCOUNT ESSENTIALS ================= --}}
<section class="sec" id="account">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">Your account</span>
      <h2>The basics, handled</h2>
      <p class="lead">Everything around the trading — funding, verification, rewards and help — is built into the same dashboard.</p>
    </div>
    <div class="fgrid reveal">
      <div><div class="ft">{!! $svgIcon('<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>') !!}<h3>Deposits &amp; withdrawals</h3></div><p>Fund with crypto or other supported methods and request withdrawals from the same screen.</p></div>
      <div><div class="ft">{!! $svgIcon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>') !!}<h3>Identity verification</h3></div><p>A short KYC check protects your account and unlocks withdrawals.</p></div>
      <div><div class="ft">{!! $svgIcon('<path d="M16 3h5v5M21 3l-7 7M8 21H3v-5M3 21l7-7"/>') !!}<h3>Wallet transfers</h3></div><p>Move funds between your holding, trading, staking and mining balances in a click.</p></div>
      <div><div class="ft">{!! $svgIcon('<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M19 8v6M16 11h6"/>') !!}<h3>Referral rewards</h3></div><p>Share your link and earn a reward when the people you invite start trading.</p></div>
      <div><div class="ft">{!! $svgIcon('<path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 01-3.4 0"/>') !!}<h3>Instant notifications</h3></div><p>Get alerted when deposits land, trades close or a copied trader acts.</p></div>
      <div><div class="ft">{!! $svgIcon('<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>') !!}<h3>Support around the clock</h3></div><p>Open a ticket from your dashboard or email support@primedigitalcap.com any time.</p></div>
    </div>
  </div>
</section>

{{-- ================= FAQ ================= --}}
<section class="sec">
  <div class="wrap">
    <div class="sec-head reveal">
      <span class="pill">FAQ</span>
      <h2>Before you sign up</h2>
    </div>
    <div class="faq reveal">
      <details><summary>Can I trade both stocks and crypto?</summary><p>Yes. The Trade Room handles both from the same balance, and the Stock Prices and Crypto Prices pages show live quotes for each.</p></details>
      <details><summary>What’s the difference between copy trading and AI traders?</summary><p>Copy trading mirrors a real trader’s positions. AI traders follow a defined strategy on a fixed list of stocks with built-in risk rules. You can use either, or both.</p></details>
      <details><summary>How much do I need to start?</summary><p>You can open an account for free. Each copy trader shows a minimum to copy, and every plan lists its price or funding range before you commit.</p></details>
      <details><summary>How do I get my money out?</summary><p>Request a withdrawal from your dashboard. Withdrawals require a completed identity check.</p></details>
      <details><summary>Is my capital at risk?</summary><p>Yes. Trading, copy trading, bots and AI strategies can all lose money. Read our <a href="{{ route('risk') }}" class="text-link">risk disclosure</a> before you start.</p></details>
    </div>
  </div>
</section>

@include('frontpage.prime.cta', ['pill' => 'Ready when you are', 'title' => 'Open your Prime Digital Capital account', 'lead' => 'Sign up free, fund your wallet, then trade, copy or automate — your choice.'])

@endsection

@include('frontpage.prime.live-crypto')

@push('scripts')
<script>
(function () {
  // Bot board: light movement so the display feels live.
  var t = 12, p = 395, n = 187, leds = document.querySelectorAll('.board .leds i');
  setInterval(function () {
    n += Math.random() < .6 ? 1 : 0; t += Math.random() < .3 ? 1 : 0; p += Math.floor(Math.random() * 3);
    var a = document.getElementById('sig-trades'); if (!a) return;
    a.textContent = n; document.getElementById('sig-t').textContent = t; document.getElementById('sig-p').textContent = p;
    var on = 5 + Math.floor(Math.random() * 5);
    leds.forEach(function (l, i) { l.classList.toggle('on', i < on); });
  }, 1600);
  // Signal plan selector
  document.querySelectorAll('.levpanel .lev-steps').forEach(function (g) {
    g.addEventListener('click', function (e) {
      var s = e.target.closest('span'); if (!s) return;
      g.querySelectorAll('span').forEach(function (x) { x.classList.toggle('on', x === s); });
    });
  });
})();
</script>
@endpush
