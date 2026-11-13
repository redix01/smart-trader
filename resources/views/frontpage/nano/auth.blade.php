<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Account') — Nano Market Pro</title>
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#0b1020">
<link rel="icon" href="{{ asset('img/nano-icon.svg') }}?v=4" type="image/svg+xml">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/nano-icon-180.png') }}?v=4">
<link rel="manifest" href="/manifest.webmanifest?v=3">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,400&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('nano/styles.css') }}?v={{ filemtime(public_path('nano/styles.css')) }}">
</head>
<body class="auth-page">

<header class="auth-top">
  <div class="wrap">
    <a href="{{ route('index') }}" class="logo" aria-label="Nano Market Pro — home"><x-nano-logo /></a>
    <a href="{{ route('index') }}" class="small muted">← Back to home</a>
  </div>
</header>

<main class="auth-main">
  <div class="auth-card @yield('card_class')">
    @yield('content')
  </div>
</main>

<script>
document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var input = document.getElementById(btn.dataset.togglePassword);
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.textContent = show ? 'Hide' : 'Show';
  });
});
document.querySelectorAll('form[data-submit-once]').forEach(function (form) {
  form.addEventListener('submit', function () {
    var b = form.querySelector('button[type=submit]');
    if (b) { b.disabled = true; b.textContent = b.dataset.busy || 'Please wait…'; }
  });
});
</script>
@stack('scripts')
</body>
</html>
