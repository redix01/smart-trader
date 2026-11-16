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
<meta property="og:description" content="@yield('description', 'Live crypto and stock prices, a personal trade room and one-click copy trading — on one secure platform.')">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
<meta name="theme-color" content="#050a1e">
<link rel="icon" href="{{ asset('img/pdc-icon.svg') }}" type="image/svg+xml">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/pdc-icon-180.png') }}?v=6">
<link rel="manifest" href="/manifest.webmanifest?v=6">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('prime/styles.css') }}?v=6">
@stack('head')
</head>
<body>
@php
  $ico = [
    'markets' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>',
    'features' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>',
    'tools' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/></svg>',
    'crypto' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 8h4.5a2.5 2.5 0 010 5H9m0-5v10m0-5h5a2.5 2.5 0 010 5H9M11 5v3m0 10v1m2-14v3m0 10v1"/></svg>',
    'stocks' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>',
    'forex' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 014-4h14M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>',
    'copy' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0114 0M16 4.5a3.5 3.5 0 010 7M22 20a7 7 0 00-4-6.3"/></svg>',
    'about' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5v.5"/></svg>',
    'help' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 .5c0 1.7-2.5 2-2.5 3.5M12 17v.5"/></svg>',
    'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>',
    'shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
  ];
  $chev = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>';
  $menus = [
    'Explore' => [
      [route('index').'#platform', 'features', 'Platform', 'Everything inside your account'],
      [route('index').'#markets', 'markets', 'Live markets', 'Real-time stock & crypto prices'],
      [route('ai-traders.index'), 'tools', 'AI traders', 'Strategy-driven stock trading'],
      [route('index').'#earn', 'shield', 'Staking & mining', 'Put idle balance to work'],
    ],
    'Company' => [
      [route('about'), 'about', 'About us', 'Who we are and how we work'],
      [route('faq'), 'help', 'Help center', 'Answers to common questions'],
      ['mailto:support@primedigitalcap.com', 'mail', 'Contact', 'support@primedigitalcap.com'],
      [route('risk'), 'shield', 'Risk disclosure', 'Understand the risks first'],
    ],
    'Trade' => [
      [route('crypto'), 'crypto', 'Crypto', 'Bitcoin, Ethereum and more'],
      [route('stocks'), 'stocks', 'Stocks and ETFs', 'Leading US equities'],
      [route('forex'), 'forex', 'Forex', 'Major and minor currency pairs'],
      [route('copy'), 'copy', 'Copy trading', 'Mirror experienced traders'],
    ],
  ];
@endphp

<nav class="top" id="topnav">
  <div class="wrap">
    <a href="{{ route('index') }}" class="logo" aria-label="Prime Digital Capital home">@include('partials.brand-lockup')</a>
    <div class="nav-links">
      @foreach($menus as $label => $items)
        <div class="nav-item">
          <button type="button" aria-haspopup="true">{{ $label }} {!! $chev !!}</button>
          <div class="menu">
            @foreach($items as [$href, $icon, $title, $sub])
              <a href="{{ $href }}"><span class="mi">{!! $ico[$icon] !!}</span><span><b>{{ $title }}</b><small>{{ $sub }}</small></span></a>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
    <div class="nav-actions">
      @auth
        <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
      @else
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get started</a>
        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm nav-login">Log in</a>
      @endauth
      <button class="burger" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mm">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu" id="mm">
    @foreach($menus as $label => $items)
      <b>{{ $label }}</b>
      @foreach($items as [$href, $icon, $title, $sub])
        <a href="{{ $href }}">{{ $title }}</a>
      @endforeach
    @endforeach
    @guest<a href="{{ route('login') }}" class="btn btn-ghost" style="margin-top:14px">Log in</a>@endguest
  </div>
</nav>

<main>
@yield('content')
</main>

<div class="risk-bar">
  <div class="wrap">
    <p><strong>Risk disclosure:</strong> Trading crypto, stocks, forex and CFDs involves substantial risk and may result in the loss of your invested capital. Prime Digital Capital does not guarantee profits or specific trading results. Past performance is not indicative of future results. <a href="{{ route('risk') }}">Read the full risk disclosure</a>.</p>
  </div>
</div>

<footer>
  <div class="wrap">
    <div class="foot">
      <div>
        <a href="{{ route('index') }}" class="logo" aria-label="Prime Digital Capital home">@include('partials.brand-lockup', ['size' => 38])</a>
        <p>Trade crypto, stocks and currencies from one secure account — with live prices, a personal trade room and 24/7 support.</p>
      </div>
      @foreach($menus as $label => $items)
        <div><h4>{{ $label }}</h4><ul>
          @foreach($items as [$href, $icon, $title, $sub])
            <li><a href="{{ $href }}">{{ $title }}</a></li>
          @endforeach
        </ul></div>
      @endforeach
    </div>
    <p class="legal">The risk of loss in online trading of stocks, options, currencies, cryptocurrencies and CFDs can be substantial. Trading on margin is only for experienced investors with a high risk tolerance — you may lose more than your initial investment. Before trading, read our <a href="{{ route('risk') }}" class="text-link">risk disclosure</a>.</p>
    <div class="fine">
      <span>© {{ date('Y') }} Prime Digital Capital · primedigitalcap.com. All rights reserved.</span>
      <span><a href="{{ route('terms') }}">Terms &amp; Conditions</a><a href="{{ route('privacy') }}">Privacy Policy</a></span>
    </div>
  </div>
</footer>

<script>
  (function () {
    var nav = document.getElementById('topnav');
    var onScroll = function () { nav.classList.toggle('scrolled', window.scrollY > 8); };
    onScroll(); window.addEventListener('scroll', onScroll, {passive: true});

    var burger = nav.querySelector('.burger'), mm = document.getElementById('mm');
    burger.addEventListener('click', function () {
      var open = mm.classList.toggle('open');
      burger.setAttribute('aria-expanded', open);
    });

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
