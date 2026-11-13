<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Nano Market Pro — Forex & CFD Trading Platform')</title>
<meta name="description" content="@yield('description', 'Nano Market Pro is a forex and CFD trading platform: sub-15ms execution, copy trading, AI traders, signals and market analysis — with clear communication about risk.')">
<meta name="author" content="Nano Market Pro">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Nano Market Pro">
<meta property="og:title" content="@yield('title', 'Nano Market Pro — Forex & CFD Trading Platform')">
<meta property="og:description" content="$15B+ monthly volume, sub-15ms execution, copy trading and AI traders — with clear communication about risk.">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
<meta name="theme-color" content="#0b1020">
<link rel="icon" href="{{ asset('img/nano-icon.svg') }}" type="image/svg+xml">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/nano-icon-180.png') }}?v=3">
<link rel="manifest" href="/manifest.webmanifest?v=3">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,400&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('nano/styles.css') }}?v={{ filemtime(public_path('nano/styles.css')) }}">
@stack('head')
</head>
<body>

<nav>
  <div class="wrap">
    <a href="{{ route('index') }}" class="logo"><b>NM</b>Nano Market Pro</a>
    <div class="links">
      <a href="{{ route('index') }}" @if(request()->routeIs('index')) aria-current="page" @endif>Home</a>
      <a href="{{ route('forex') }}">Markets</a>
      <a href="{{ route('copy') }}">Copy trading</a>
      <a href="{{ route('ai-traders.index') }}">AI traders</a>
      <a href="{{ route('about') }}">About</a>
      <a href="{{ route('faq') }}">FAQ</a>
      <a href="{{ route('risk') }}" @if(request()->routeIs('risk')) aria-current="page" @endif>Risk disclosure</a>
    </div>
    <div class="nav-actions">
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-navy nav-cta">Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="nav-login">Log in</a>
        <a href="{{ route('register') }}" class="btn btn-navy nav-cta">Open account</a>
      @endauth
    </div>
  </div>
</nav>

@yield('content')

<div class="risk-bar">
  <div class="wrap">
    <p><strong>Risk disclosure:</strong> Forex and CFD trading involve substantial risk and may result in the loss of your invested capital. Nano Market Pro does not guarantee profits or specific trading results. Past performance is not indicative of future results. <a href="{{ route('risk') }}">Read the full risk disclosure</a>.</p>
  </div>
</div>

<footer>
  <div class="wrap">
    <div class="foot">
      <div><p class="tag">Nano Market Pro</p><p>Intelligent trading. Innovative technology. A more informed approach to the markets.</p></div>
      <div><h4>Platform</h4><ul>
        <li><a href="{{ route('forex') }}">Forex</a></li>
        <li><a href="{{ route('crypto') }}">Crypto</a></li>
        <li><a href="{{ route('stocks') }}">Stocks</a></li>
        <li><a href="{{ route('copy') }}">Copy trading</a></li>
        <li><a href="{{ route('ai-traders.index') }}">AI traders</a></li>
      </ul></div>
      <div><h4>Company &amp; legal</h4><ul>
        <li><a href="{{ route('about') }}">About</a></li>
        <li><a href="{{ route('faq') }}">FAQ</a></li>
        <li><a href="{{ route('terms') }}">Terms</a></li>
        <li><a href="{{ route('privacy') }}">Privacy policy</a></li>
        <li><a href="{{ route('risk') }}">Risk disclosure</a></li>
        <li><a href="mailto:support@nanomarketpro.net">support@nanomarketpro.net</a></li>
      </ul></div>
    </div>
    <div class="fine"><span>© {{ date('Y') }} Nano Market Pro · nanomarketpro.net. All rights reserved.</span><span>Operating fully online · Support 24/7</span></div>
  </div>
</footer>
@stack('scripts')
</body>
</html>
