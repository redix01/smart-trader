@extends('frontpage.nano.layout')

@section('title', 'Nano Market Pro — Forex & CFD Trading Platform | Founded by Thomas Peggs')

@push('head')
<script type="application/ld+json">
{"@context":"https://schema.org","@graph":[
 {"@type":"Organization","@id":"https://nanomarketpro.net/#organization","name":"Nano Market Pro","alternateName":"NanoMarketPro",
  "url":"https://nanomarketpro.net/","slogan":"Intelligent Trading. Innovative Technology. A More Informed Approach to the Markets.",
  "description":"Forex and CFD trading platform offering low-latency execution, copy trading, AI traders, forex signals and market analysis.",
  "founder":{"@id":"https://thomaspeggs.com/#person"}},
 {"@type":"Person","@id":"https://thomaspeggs.com/#person","name":"Thomas Peggs","url":"https://thomaspeggs.com/","jobTitle":"Founder",
  "worksFor":{"@id":"https://nanomarketpro.net/#organization"}},
 {"@type":"WebSite","@id":"https://nanomarketpro.net/#website","url":"https://nanomarketpro.net/","name":"Nano Market Pro","publisher":{"@id":"https://nanomarketpro.net/#organization"}}
]}
</script>
@endpush

@section('content')

<header class="hero">
  <div class="wrap">
    <div class="hero-grid">
    <div class="hero-copy">
    <p class="eyebrow">Forex · Crypto · Stocks · Copy trading</p>
    <h1>Intelligent trading. <em>Innovative</em> technology.</h1>
    <p class="lead">Nano Market Pro is a forex and CFD trading platform for retail and institutional traders. Trade the markets, follow experienced traders, or run AI-assisted strategies — on institutional-grade liquidity and low-latency execution, with disciplined risk management built in.</p>
    <div class="cta">
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-navy">Go to your dashboard</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-navy">Open an account</a>
        <a href="{{ route('login') }}" class="btn btn-line">Log in</a>
      @endauth
    </div>
    <p class="founded">Founded by <a href="https://thomaspeggs.com/" class="text-link" rel="noopener">Thomas Peggs</a></p>
    </div>

    <aside class="tv-box" aria-label="Live market prices">
      <div class="tradingview-widget-container">
        <div class="tradingview-widget-container__widget"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-quotes.js" async>
        {
          "width": "100%",
          "height": 520,
          "symbolsGroups": [
            {"name": "Forex", "originalName": "Forex", "symbols": [
              {"name": "FX:EURUSD", "displayName": "EUR/USD"},
              {"name": "FX:GBPUSD", "displayName": "GBP/USD"},
              {"name": "FX:USDJPY", "displayName": "USD/JPY"},
              {"name": "FX:AUDUSD", "displayName": "AUD/USD"},
              {"name": "FX:USDCAD", "displayName": "USD/CAD"}
            ]},
            {"name": "Crypto", "originalName": "Crypto", "symbols": [
              {"name": "BITSTAMP:BTCUSD", "displayName": "Bitcoin"},
              {"name": "BITSTAMP:ETHUSD", "displayName": "Ethereum"},
              {"name": "COINBASE:SOLUSD", "displayName": "Solana"},
              {"name": "BITSTAMP:XRPUSD", "displayName": "XRP"}
            ]},
            {"name": "Stocks", "originalName": "Stocks", "symbols": [
              {"name": "NASDAQ:AAPL", "displayName": "Apple"},
              {"name": "NASDAQ:NVDA", "displayName": "NVIDIA"},
              {"name": "NASDAQ:TSLA", "displayName": "Tesla"},
              {"name": "NASDAQ:MSFT", "displayName": "Microsoft"}
            ]}
          ],
          "showSymbolLogo": true,
          "colorTheme": "light",
          "isTransparent": true,
          "locale": "en",
          "backgroundColor": "#ffffff"
        }
        </script>
      </div>
      <p class="tv-credit"><a href="https://www.tradingview.com/markets/" rel="noopener nofollow" target="_blank">Market data by TradingView</a> · prices are indicative</p>
    </aside>
    </div>

    <div class="stats">
      <div class="stat"><div class="n">$15B+</div><div class="l">Monthly trading volume</div></div>
      <div class="stat"><div class="n">&lt;15ms</div><div class="l">Average execution latency</div></div>
      <div class="stat"><div class="n">40%</div><div class="l">YoY lift in active trader retention</div></div>
      <div class="stat"><div class="n">24/7</div><div class="l">Online support</div></div>
    </div>
  </div>
</header>

<section>
  <div class="wrap">
    <p class="eyebrow">What you can do</p>
    <h2>Everything a serious trader needs, in one account.</h2>
    <p class="lead" style="margin-top:16px">One login gives you markets, automation and research — so you can trade the way that suits your experience.</p>
    <div class="grid">
      <a class="card" href="{{ route('forex') }}"><span class="k">01</span><h3>Forex &amp; CFDs</h3><p>Trade major, minor and exotic currency pairs with tight spreads and fast, consistent fills.</p><span class="more"><span>Explore forex</span> →</span></a>
      <a class="card" href="{{ route('crypto') }}"><span class="k">02</span><h3>Crypto &amp; stocks</h3><p>Access leading cryptocurrencies and global equities alongside your currency positions.</p><span class="more"><span>See the markets</span> →</span></a>
      <a class="card" href="{{ route('copy') }}"><span class="k">03</span><h3>Copy trading</h3><p>Follow experienced traders and mirror their positions automatically, with your own limits in place.</p><span class="more"><span>How copy trading works</span> →</span></a>
      <a class="card" href="{{ route('ai-traders.index') }}"><span class="k">04</span><h3>AI traders</h3><p>Technology that executes predefined strategies consistently, with risk controls built into the process.</p><span class="more"><span>Meet the AI traders</span> →</span></a>
      <a class="card" href="{{ route('register') }}"><span class="k">05</span><h3>Signals &amp; analysis</h3><p>Research-led signals and market commentary that help you decide with more information.</p><span class="more"><span>Get access</span> →</span></a>
      <a class="card" href="{{ route('register') }}"><span class="k">06</span><h3>Portfolio &amp; staking</h3><p>Track holdings, balances and performance from a single dashboard, and put idle assets to work.</p><span class="more"><span>Open your dashboard</span> →</span></a>
    </div>
  </div>
</section>

<section class="tight">
  <div class="wrap">
    <p class="eyebrow">Markets</p>
    <h2>Watch the markets move.</h2>
    <p class="lead" style="margin-top:16px">Indices, forex, crypto and shares at a glance — the same instruments you can trade from your dashboard.</p>
    <div class="tv-box tv-wide">
      <div class="tradingview-widget-container">
        <div class="tradingview-widget-container__widget"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js" async>
        {
          "colorTheme": "light",
          "dateRange": "12M",
          "showChart": true,
          "locale": "en",
          "width": "100%",
          "height": 520,
          "isTransparent": true,
          "showSymbolLogo": true,
          "showFloatingTooltip": true,
          "plotLineColorGrowing": "rgba(176, 138, 62, 1)",
          "plotLineColorFalling": "rgba(11, 16, 32, 1)",
          "gridLineColor": "rgba(227, 221, 207, 1)",
          "scaleFontColor": "rgba(107, 114, 134, 1)",
          "belowLineFillColorGrowing": "rgba(176, 138, 62, 0.12)",
          "belowLineFillColorFalling": "rgba(11, 16, 32, 0.08)",
          "belowLineFillColorGrowingBottom": "rgba(176, 138, 62, 0)",
          "belowLineFillColorFallingBottom": "rgba(11, 16, 32, 0)",
          "symbolActiveColor": "rgba(176, 138, 62, 0.12)",
          "tabs": [
            {"title": "Forex", "originalTitle": "Forex", "symbols": [
              {"s": "FX:EURUSD", "d": "EUR/USD"}, {"s": "FX:GBPUSD", "d": "GBP/USD"},
              {"s": "FX:USDJPY", "d": "USD/JPY"}, {"s": "FX:AUDUSD", "d": "AUD/USD"}, {"s": "OANDA:XAUUSD", "d": "Gold"}
            ]},
            {"title": "Crypto", "originalTitle": "Crypto", "symbols": [
              {"s": "BITSTAMP:BTCUSD", "d": "Bitcoin"}, {"s": "BITSTAMP:ETHUSD", "d": "Ethereum"},
              {"s": "COINBASE:SOLUSD", "d": "Solana"}, {"s": "BITSTAMP:XRPUSD", "d": "XRP"}
            ]},
            {"title": "Stocks", "originalTitle": "Stocks", "symbols": [
              {"s": "NASDAQ:AAPL", "d": "Apple"}, {"s": "NASDAQ:NVDA", "d": "NVIDIA"},
              {"s": "NASDAQ:TSLA", "d": "Tesla"}, {"s": "NASDAQ:MSFT", "d": "Microsoft"}
            ]},
            {"title": "Indices", "originalTitle": "Indices", "symbols": [
              {"s": "FOREXCOM:SPXUSD", "d": "S&P 500"}, {"s": "FOREXCOM:NSXUSD", "d": "Nasdaq 100"},
              {"s": "FOREXCOM:DJI", "d": "Dow 30"}, {"s": "FOREXCOM:UKXGBP", "d": "FTSE 100"}
            ]}
          ]
        }
        </script>
      </div>
      <p class="tv-credit"><a href="https://www.tradingview.com/markets/" rel="noopener nofollow" target="_blank">Market data by TradingView</a> · prices are indicative</p>
    </div>
  </div>
</section>

<section class="dark">
  <div class="wrap">
    <p class="eyebrow">Execution</p>
    <h2>From order to Tier-1 liquidity in under 15ms.</h2>
    <p class="lead" style="margin-top:16px">Direct integrations with Tier-1 Prime Brokers and enterprise bridge providers keep slippage low and fills consistent.</p>
    <div class="flow">
      <div class="step"><span class="i">01</span><h3>Your order</h3><p>Placed manually, by copy trading or by an AI trader</p></div>
      <div class="step"><span class="i">02</span><h3>Trading servers</h3><p>Automated risk rules checked on every order</p></div>
      <div class="step"><span class="i">03</span><h3>Bridge</h3><p>Aggregation and smart routing across providers</p></div>
      <div class="step"><span class="i">04</span><h3>Tier-1 liquidity</h3><p>Prime Broker depth at institutional grade</p></div>
    </div>
    <div class="tags"><span class="tag">Sub-15ms average latency</span><span class="tag">Minimized slippage</span><span class="tag">AML / KYC</span><span class="tag">Segregated client funds</span></div>
  </div>
</section>

<section>
  <div class="wrap">
    <p class="eyebrow">Getting started</p>
    <h2>Open an account in three steps.</h2>
    <div class="steps3">
      <div class="card"><span class="k">Step 1</span><h3>Register</h3><p>Create your account with an email address and a secure password. It takes about two minutes.</p></div>
      <div class="card"><span class="k">Step 2</span><h3>Verify</h3><p>Complete identity verification (KYC). It keeps your account and every client's funds protected.</p></div>
      <div class="card"><span class="k">Step 3</span><h3>Fund &amp; trade</h3><p>Deposit, choose how you want to trade, and track every position from your dashboard.</p></div>
    </div>
    @guest
    <p style="margin-top:28px"><a href="{{ route('register') }}" class="btn btn-navy">Create your account</a></p>
    @endguest
  </div>
</section>

<section class="tight">
  <div class="wrap split">
    <div>
      <p class="eyebrow">Our commitment</p>
      <p class="statement" style="margin-top:18px">No guaranteed profits. No unrealistic promises. <em>Just clear technology and honest communication.</em></p>
    </div>
    <div class="prose">
      <p>Credibility in trading is earned through consistency, accountability and transparency. Markets carry risk, and no automated system can remove the possibility of loss.</p>
      <p>That is why we explain what our tools do, where their limits are and what risks come with them — before you use them. Informed users and responsible practice are the foundation of a business built to last.</p>
      <p style="margin-top:24px"><a href="{{ route('risk') }}" class="btn btn-navy">Read our risk disclosure</a></p>
    </div>
  </div>
</section>

<section class="tight">
  <div class="wrap">
    <p class="eyebrow">Principles</p>
    <h2>How we build.</h2>
    <div class="grid grid-3">
      <div class="card"><span class="k">Innovation</span><h3>Better delivery</h3><p>We keep exploring technology that improves how trading solutions are delivered and experienced.</p></div>
      <div class="card"><span class="k">Transparency</span><h3>Clear terms</h3><p>You should understand our services, our processes and the risks of market participation.</p></div>
      <div class="card"><span class="k">Precision</span><h3>Structured methods</h3><p>Systematic processes and continuous evaluation sit behind every solution we develop.</p></div>
    </div>
  </div>
</section>

<section class="tight">
  <div class="wrap">
    <div class="founder">
      <img src="https://thomaspeggs.com/assets/thomas-peggs.webp" alt="Thomas Peggs, founder of Nano Market Pro" width="660" height="880" loading="lazy">
      <div>
        <p class="eyebrow">Founder</p>
        <h3>Thomas Peggs</h3>
        <p class="role">Founder, Nano Market Pro</p>
        <p>Thomas Peggs brings hands-on experience scaling Forex and CFD trading infrastructure — from low-latency execution and MetaTrader ecosystems to global compliance frameworks. He founded Nano Market Pro to put that experience into practical, technology-driven trading solutions.</p>
        <a href="https://thomaspeggs.com/" class="btn btn-navy" rel="noopener">Read Thomas Peggs' profile</a>
      </div>
    </div>
  </div>
</section>

<section class="tight">
  <div class="wrap">
    <div class="band">
      <h2>Ready to trade with more clarity?</h2>
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-navy">Go to your dashboard</a>
      @else
        <div class="cta">
          <a href="{{ route('register') }}" class="btn btn-navy">Open an account</a>
          <a href="mailto:support@nanomarketpro.net" class="btn btn-line">Contact support</a>
        </div>
      @endauth
    </div>
  </div>
</section>

@endsection
