<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prime Digital Capital - {{ __('Confirm Password') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('auth.partials.head')
</head>
<body class="auth-body">
<div class="auth-shell">
@include('auth.partials.panel')
<main class="auth-main">
    <div class="w-full max-w-md">
        <div class="text-center mb-8 lg-hide">
            <a href="{{ route('index') }}" class="inline-block">@include('partials.brand-lockup', ['size' => 40])</a>
        </div>
        <div class="bg-gray-800 rounded-lg p-8 shadow-2xl border border-gray-700">
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-blue-400">{{ __('Security check') }}</p>
            <h1 class="text-2xl font-bold text-white mt-1">{{ __('Confirm your password') }}</h1>
            <p class="text-sm text-gray-400 mt-1 mb-6">{{ __('This is a secure area. Please confirm your password before continuing.') }}</p>
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6" onsubmit="this.querySelector('button[type=submit]').disabled=true">
                @csrf
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" required autofocus autocomplete="current-password"
                           class="block w-full px-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full py-3 px-4 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50">{{ __('Confirm') }}</button>
            </form>
        </div>
    </div>
</main>
</div>
</body>
</html>
