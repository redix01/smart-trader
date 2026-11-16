<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Prime Digital Capital — Trade Crypto, Stocks & Forex')</title>
<meta name="description" content="@yield('description', 'Prime Digital Capital is an online trading platform for crypto, stocks and currencies: live prices, a personal trade room, copy trading and 24/7 support.')">
<meta name="author" content="Prime Digital Capital">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Prime Digital Capital">
<meta property="og:title" content="@yield('title', 'Prime Digital Capital — Trade Crypto, Stocks & Forex')">
<meta property="og:description" content="Live crypto and stock prices, a personal trade room and one-click copy trading — on one secure platform.">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
<meta name="theme-color" content="#06070c">
<link rel="icon" href="{{ asset('img/pdc-icon.svg') }}" type="image/svg+xml">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/pdc-icon-180.png') }}?v=4">
<link rel="manifest" href="/manifest.webmanifest?v=4">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('prime/styles.css') }}?v=2">
@stack('head')
</head>
<body>

<nav class="top">
  <div class="wrap">
    <a href="{{ route('index') }}" class="logo"><span class="mark">PD</span><span class="name">Prime Digital Capital</span></a>
    <div class="nav-links">
      <div class="nav-item">
        <button type="button">Explore</button>
        <div class="menu">
          <a href="{{ route('index') }}#markets">Markets<small>Live crypto &amp; stock prices</small></a>
          <a href="{{ route('index') }}#features">Platform features<small>Everything in one account</small></a>
          <a href="{{ route('index') }}#tools">Trading tools<small>Signals, leverage, execution</small></a>
        </div>
      </div>
      <div class="nav-item">
        <button type="button">Trade</button>
        <div class="menu">
          <a href="{{ route('crypto') }}">Crypto</a>
          <a href="{{ route('stocks') }}">Stocks &amp; ETFs</a>
          <a href="{{ route('forex') }}">Forex</a>
          <a href="{{ route('copy') }}">Copy trading</a>
        </div>
      </div>
      <div class="nav-item">
        <button type="button">Company</button>
        <div class="menu">
          <a href="{{ route('about') }}">About us</a>
          <a href="{{ route('faq') }}">Help center</a>
          <a href="mailto:support@primedigitalcap.com">Contact</a>
          <a href="{{ route('risk') }}">Risk disclosure</a>
        </div>
      </div>
    </div>
    <div class="nav-actions">
      @auth
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="btn btn-ghost nav-login">Log in</a>
        <a href="{{ route('register') }}" class="btn btn-primary">Get started</a>
      @endauth
      <button class="burger" type="button" aria-label="Open menu" aria-expanded="false" onclick="var m=document.getElementById('mm');m.classList.toggle('open');this.setAttribute('aria-expanded',m.classList.contains('open'))">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu" id="mm">
    <b>Explore</b>
    <a href="{{ route('index') }}#markets">Markets</a>
    <a href="{{ route('index') }}#features">Platform features</a>
    <b>Trade</b>
    <a href="{{ route('crypto') }}">Crypto</a>
    <a href="{{ route('stocks') }}">Stocks &amp; ETFs</a>
    <a href="{{ route('copy') }}">Copy trading</a>
    <b>Company</b>
    <a href="{{ route('about') }}">About us</a>
    <a href="{{ route('faq') }}">Help center</a>
    @guest<a href="{{ route('login') }}">Log in</a>@endguest
  </div>
</nav>

@yield('content')

<div class="risk-bar">
  <div class="wrap">
    <p><strong>Risk disclosure:</strong> Trading crypto, stocks, forex and CFDs involves substantial risk and may result in the loss of your invested capital. Prime Digital Capital does not guarantee profits or specific trading results. Past performance is not indicative of future results. <a href="{{ route('risk') }}">Read the full risk disclosure</a>.</p>
  </div>
</div>

<footer>
  <div class="wrap">
    <div class="foot">
      <div>
        <a href="{{ route('index') }}" class="logo"><span class="mark">PD</span><span class="name">Prime Digital Capital</span></a>
        <p>Trade crypto, stocks and currencies from one secure account — with live prices, a personal trade room and 24/7 support.</p>
      </div>
      <div><h4>Explore</h4><ul>
        <li><a href="{{ route('index') }}#markets">Markets</a></li>
        <li><a href="{{ route('index') }}#features">Features</a></li>
        <li><a href="{{ route('index') }}#tools">Trading tools</a></li>
      </ul></div>
      <div><h4>Trade</h4><ul>
        <li><a href="{{ route('crypto') }}">Crypto</a></li>
        <li><a href="{{ route('stocks') }}">Stocks and ETFs</a></li>
        <li><a href="{{ route('forex') }}">Forex</a></li>
        <li><a href="{{ route('copy') }}">Copy trading</a></li>
      </ul></div>
      <div><h4>Company</h4><ul>
        <li><a href="{{ route('about') }}">About us</a></li>
        <li><a href="{{ route('faq') }}">Help center</a></li>
        <li><a href="{{ route('terms') }}">Terms &amp; conditions</a></li>
        <li><a href="{{ route('privacy') }}">Privacy policy</a></li>
        <li><a href="mailto:support@primedigitalcap.com">support@primedigitalcap.com</a></li>
      </ul></div>
    </div>
    <p class="legal">The risk of loss in online trading of stocks, options, currencies, cryptocurrencies and CFDs can be substantial. Trading on margin is only for experienced investors with a high risk tolerance — you may lose more than your initial investment. Before trading, read our <a href="{{ route('risk') }}" class="text-link">risk disclosure</a>.</p>
    <div class="fine"><span>© {{ date('Y') }} Prime Digital Capital · primedigitalcap.com. All rights reserved.</span><span>Operating fully online · Support 24/7</span></div>
  </div>
</footer>

<script>
  (function () {
    var els = document.querySelectorAll('.reveal');
    if (!('IntersectionObserver' in window)) { els.forEach(function (e) { e.classList.add('in'); }); return; }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
    }, { threshold: 0.12 });
    els.forEach(function (e) { io.observe(e); });
  })();
</script>
@stack('scripts')
</body>
</html>
