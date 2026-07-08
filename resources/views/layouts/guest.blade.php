<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="manifest" href="/manifest.webmanifest?v=2">
        <link rel="apple-touch-icon" sizes="180x180" href="/img/100x2.png?v=2">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#0b1020">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body  class="font-sans text-gray-900 antialiased">
        <div id="pwaPrompt" class="hidden fixed inset-0 z-50 items-end justify-center">
            <div class="fixed inset-0 bg-black/50"></div>
            <div class="relative m-4 w-full max-w-sm bg-white text-gray-900 rounded-xl shadow-xl p-4">
                <div class="flex items-start">
                    <img src="/img/100x2.png?v=2" alt="App Icon" class="w-10 h-10 mr-3 rounded">
                    <div class="flex-1">
                        <div class="font-semibold">Add 100x to Home Screen</div>
                        <div id="pwaPromptText" class="text-sm text-gray-600 mt-1">Install the app for faster access and an app-like experience.</div>
                    </div>
                </div>
                <div class="mt-4 flex gap-2 justify-end">
                    <button id="pwaPromptDismiss" class="px-3 py-2 text-sm rounded border border-gray-300">Maybe later</button>
                    <button id="pwaPromptInstall" class="px-3 py-2 text-sm rounded bg-blue-600 text-white">Install</button>
                </div>
            </div>
        </div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="{{ route('index') }}">
                    @if(\App\Helpers\WebsiteSettingsHelper::hasTextLogo())
                        <!-- Text Logo -->
                        <div class="flex items-center justify-center px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg" style="width: 200px; height: 160px;">
                            <span class="text-white font-bold text-3xl">{{ \App\Helpers\WebsiteSettingsHelper::getTextLogo() }}</span>
                        </div>
                    @elseif(\App\Helpers\WebsiteSettingsHelper::hasImageLogo())
                        <!-- Image Logo -->
                        <img src="{{ \App\Helpers\WebsiteSettingsHelper::getLogoUrl() }}"
                             alt="{{ \App\Helpers\WebsiteSettingsHelper::getSiteName() }}"
                             width="200" height="160"
                             style="background-color: white; object-fit: contain;">
                    @else
                        <!-- Brand Logo (default) -->
                        <div class="flex items-center justify-center px-6 py-4 rounded-lg" style="width: 200px; height: 160px; background-color: #0C0F19;">
                            <img src="{{ asset('img/brand-logo.svg') }}"
                                 alt="{{ \App\Helpers\WebsiteSettingsHelper::getSiteName() }}"
                                 class="w-full h-auto object-contain">
                        </div>
                    @endif
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        <!-- Language Switcher -->
        <div class="mt-4 text-center">
            <div x-data="{ langOpen: false }" class="relative inline-block text-left">
                <button @click="langOpen = !langOpen" class="inline-flex items-center text-xs text-gray-500 hover:text-gray-400 transition-colors">
                    <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m0 4a7 7 0 100 14 7 7 0 000-14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15V5a2 2 0 00-2-2H9"/></svg>
                    {{ strtoupper(app()->getLocale()) }}
                    <svg class="h-3 w-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="langOpen" @click.away="langOpen = false" class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg z-50 py-1 max-h-48 overflow-y-auto min-w-[120px]" style="display: none;">
                    @foreach(['en' => 'English', 'es' => 'Español', 'fr' => 'Français', 'de' => 'Deutsch', 'pt' => 'Português', 'it' => 'Italiano', 'ru' => 'Русский', 'zh' => '中文', 'ja' => '日本語', 'ko' => '한국어', 'ar' => 'العربية', 'tr' => 'Türkçe'] as $code => $name)
                        <a href="{{ url('/language/' . $code) }}" class="block px-4 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap {{ app()->getLocale() === $code ? 'font-bold' : '' }}">
                            {{ $name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/service-worker.js').catch(function(e){
                        console.error('Service worker registration failed', e);
                    });
                });
            }

            (function() {
                var promptEvent = null;
                var promptEl = document.getElementById('pwaPrompt');
                var installBtn = document.getElementById('pwaPromptInstall');
                var dismissBtn = document.getElementById('pwaPromptDismiss');
                var promptText = document.getElementById('pwaPromptText');

                if (!promptEl) return;

                function showPrompt() {
                    promptEl.classList.remove('hidden');
                    promptEl.classList.add('flex');
                }

                function hidePrompt() {
                    promptEl.classList.add('hidden');
                    promptEl.classList.remove('flex');
                }

                var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
                var isIOS = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
                if (isStandalone || localStorage.getItem('pwaInstalled') === '1') {
                    return;
                }

                window.addEventListener('beforeinstallprompt', function(e) {
                    e.preventDefault();
                    promptEvent = e;
                    setTimeout(showPrompt, 6000);
                });

                if (isIOS && !isStandalone && localStorage.getItem('pwaPromptDismissed') !== '1') {
                    if (promptText) {
                        promptText.textContent = "On iPhone/iPad: tap the Share button, then 'Add to Home Screen'.";
                    }
                    if (installBtn) {
                        installBtn.textContent = 'OK, Got it';
                    }
                    setTimeout(showPrompt, 6000);
                }

                installBtn && installBtn.addEventListener('click', async function() {
                    if (promptEvent) {
                        try {
                            promptEvent.prompt();
                            var res = await promptEvent.userChoice;
                            if (res && res.outcome === 'accepted') hidePrompt();
                        } catch (err) {
                            console.error('Install failed', err);
                        } finally {
                            promptEvent = null;
                        }
                    } else {
                        hidePrompt();
                    }
                });

                dismissBtn && dismissBtn.addEventListener('click', function() {
                    hidePrompt();
                    localStorage.setItem('pwaPromptDismissed', '1');
                });

                window.addEventListener('appinstalled', function() {
                    localStorage.setItem('pwaInstalled', '1');
                    hidePrompt();
                });
            })();
        </script>
    </body>
</html>
