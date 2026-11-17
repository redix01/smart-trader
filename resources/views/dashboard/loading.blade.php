<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Loading… — Prime Digital Capital</title>
    <link rel="icon" href="{{ asset('img/pdc-icon.svg') }}?v=6" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('prime/app.css') }}?v={{ filemtime(public_path('prime/app.css')) }}">
    <style>
        body{min-height:100vh;display:grid;place-items:center;margin:0}
        .wrap-load{text-align:center}
        .spinner{width:44px;height:44px;margin:28px auto 0;border:3px solid var(--line);border-top-color:var(--accent);border-radius:50%;animation:spin 1s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
        .loading-text{margin-top:18px;font-size:15px;color:var(--soft)}
    </style>
</head>
<body>
<div class="wrap-load">
    <x-brand-logo :size="44" />
    <div class="spinner" role="status" aria-label="Loading"></div>
    <p class="loading-text">Please wait, we're redirecting you…</p>
</div>
<script>
    setTimeout(function () { window.location.href = '/user/dashboard'; }, 3000);
</script>
</body>
</html>
