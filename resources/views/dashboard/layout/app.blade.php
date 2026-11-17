
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Helpers\WebsiteSettingsHelper::getSiteName() }} - Dashboard</title>
  <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">
  <link rel="manifest" href="/manifest.webmanifest?v=3">
  <link rel="apple-touch-icon" sizes="180x180" href="/img/pdc-icon-180.png?v=6">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="theme-color" content="#050a1e">
    
    <!-- Tailwind CSS CDN for immediate styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.brand-theme')

    <!-- Pusher JS for real-time updates -->
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

    <!-- TradingView Widget -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>

    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite assets (uncomment when running npm run dev) -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    
    @livewireStyles

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,400&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>window.CURRENCY_SYMBOL = @json(auth()->check() ? auth()->user()->currency_symbol : '$');</script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('prime/app.css') }}?v={{ filemtime(public_path('prime/app.css')) }}">
    <style>[x-cloak]{display:none!important}</style>


    @include('partials.dialogs')
</head>

<body class="bg-gray-900 text-white min-h-screen">
    <div class="h-screen bg-gray-900">
        <!-- Sidebar Backdrop -->
        <div id="sidebarBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-gray-800 border-r border-gray-700 flex flex-col transform transition-transform duration-300 ease-in-out z-50">
            <!-- User Profile Section -->
            <div class="p-3 border-b border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-white font-semibold">Menu</h3>
                    <!-- Close button -->
                    <button id="sidebarClose" class="p-1 text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 nm-avatar rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-lg">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold">{{ auth()->user()->name }}</h3>
                        <p class="text-gray-400 text-sm">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>



            @include('dashboard.layout.sidebar-nav')
            <!-- Bottom padding to ensure last items are visible -->
            <div class="pb-8"></div>
        </div>

        <!-- Main Content -->
        <div class="w-full h-full flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-gray-800 border-b border-gray-700 py-2">
                <div class="flex items-center">
                    <!-- Left side - Brand and menu -->
                    <div class="flex items-center space-x-4 px-4 sm:px-6">
                        <!-- Menu Toggle Button -->
                        <button id="sidebarToggle" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div class="flex items-center">
                            @if(\App\Helpers\WebsiteSettingsHelper::hasTextLogo())
                                <!-- Text Logo -->
                                <div class="h-16 flex items-center">
                                    <span class="text-white font-extrabold text-2xl tracking-wide">{{ \App\Helpers\WebsiteSettingsHelper::getTextLogo() }}</span>
                                </div>
                            @elseif(\App\Helpers\WebsiteSettingsHelper::hasImageLogo())
                                <!-- Image Logo -->
                                <img src="{{ \App\Helpers\WebsiteSettingsHelper::getLogoUrl() }}"
                                     alt="{{ \App\Helpers\WebsiteSettingsHelper::getSiteName() }}"
                                     class="h-16 w-auto object-contain">
                            @else
                                <a href="{{ route('user.dashboard') }}" aria-label="Prime Digital Capital — dashboard"><x-brand-logo /></a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right side - Install PWA, Notification and user profile -->
                    <div class="flex items-center space-x-1 sm:space-x-2 ml-auto min-w-0 pr-2 sm:pr-4">
                        <!-- Install PWA Button (shown when installable) -->
                        <button id="installPwaBtn" type="button" aria-label="Install app" class="hidden inline-flex items-center gap-1.5 flex-shrink-0 whitespace-nowrap px-2.5 py-1.5 sm:px-3 sm:py-2 bg-blue-600 text-white rounded-full text-xs sm:text-sm font-medium hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v10m0 0l-4-4m4 4l4-4M5 20h14"></path></svg>
                            <span class="sm:hidden">Install</span><span class="hidden sm:inline">Install App</span>
                        </button>

                        <!-- Notifications -->
                        <div class="relative" id="notificationDropdown">
                            <button id="notificationButton" class="relative p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                </svg>
                                <span id="notificationBadge" class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 hidden"></span>
                                <span id="notificationCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                            </button>
                            
                            <!-- Notification Dropdown -->
                            <div id="notificationDropdownMenu" class="absolute right-0 mt-2 w-80 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-50 opacity-0 invisible transform scale-95 transition-all duration-200">
                                <div class="p-4 border-b border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-white">Notifications</h3>
                                        <a href="{{ route('user.notifications.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
                                    </div>
                                </div>
                                
                                <div id="notificationList" class="max-h-96 overflow-y-auto">
                                    <!-- Notifications will be loaded here -->
                                    <div class="p-4 text-center text-gray-400">
                                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
                                        <p class="mt-2">Loading notifications...</p>
                                    </div>
                                </div>
                                
                                <div class="p-3 border-t border-gray-700">
                                    <div class="flex justify-between">
                                        <button id="markAllReadBtn" class="text-sm text-blue-400 hover:text-blue-300">Mark all as read</button>
                                        <button id="clearAllBtn" class="text-sm text-red-400 hover:text-red-300">Clear all</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative group" id="userDropdown">
                            <button class="flex items-center space-x-1 sm:space-x-2 p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                <div class="w-7 h-7 nm-avatar rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transform scale-95 group-hover:scale-100 transition-all duration-200">
                                <div class="py-1">
                                    <a href="{{ route('user.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                        Profile
                                    </a>
                                    <a href="{{ route('user.support.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.759 8.071 16 9.007 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a3.998 3.998 0 00-.027-1.789l-1.562 1.562C4.241 8.071 4 9.007 4 10c0 .993.241 1.929.668 2.754l1.524-1.525zm11.33-4.753l-1.562 1.562a4.006 4.006 0 00-1.789-.027l-1.58-1.58a5.98 5.98 0 012.516-.552 5.976 5.976 0 012.415.597zM4.668 7.246l1.525 1.525a3.997 3.997 0 00-.078 2.183l-1.562 1.562C4.241 11.929 4 10.993 4 10c0-.993.241-1.929.668-2.754z" clip-rule="evenodd"></path>
                                        </svg>
                                        Support
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                            <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
      </div>
  </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900">
                <div class="container mx-auto px-6 py-8">
                    @if(session('success'))
                        <div id="alert-success" class="mb-3 rounded-md border border-green-500 bg-green-600 text-white px-4 py-3">
                            <div class="font-medium">{{ session('success') }}</div>
                        </div>
                        <script>console.log('[Flash] success:', @json(session('success')));</script>
                    @endif

                    @if(session('error'))
                        <div id="alert-error" class="mb-3 rounded-md border border-red-500 bg-red-600 text-white px-4 py-3">
                            <div class="font-medium">{{ session('error') }}</div>
                        </div>
                        <script>console.log('[Flash] error:', @json(session('error')));</script>
                    @endif

                    @if(session('warning'))
                        <div id="alert-warning" class="mb-3 rounded-md border border-yellow-500 bg-yellow-600 text-white px-4 py-3">
                            <div class="font-medium">{{ session('warning') }}</div>
                        </div>
                        <script>console.log('[Flash] warning:', @json(session('warning')));</script>
                    @endif

                    @if ($errors->any())
                        <div id="alert-validation" class="mb-3 rounded-md border border-yellow-500 bg-yellow-600 text-white px-4 py-3">
                            <div class="font-semibold mb-1">Please fix the following:</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <script>console.log('[Flash] validation errors:', @json($errors->all()));</script>
                    @endif

                    @yield('content')
                    <p class="mt-10 pt-6 border-t border-gray-700 text-xs text-gray-500 leading-relaxed">
                        <span class="text-gray-300 font-semibold">Risk disclosure:</span> Trading forex, CFDs and crypto involves substantial risk and may result in the loss of your invested capital. Prime Digital Capital does not guarantee profits. Past performance is not indicative of future results.
                        <a href="{{ route('risk') }}" class="text-gold-400 hover:underline">Read more</a>
                    </p>
                </div>
            </main>
        </div>
    </div>


@livewireScripts
    <div id="pwaPrompt" class="hidden fixed inset-0 z-50 items-end justify-center">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="relative m-4 w-full max-w-sm bg-white text-gray-900 rounded-xl shadow-xl p-4">
            <div class="flex items-start">
                <img src="/img/pdc-icon-180.png?v=6" alt="App Icon" class="w-10 h-10 mr-3 rounded">
                <div class="flex-1">
                    <div class="font-semibold">Add Prime Digital Capital to Home Screen</div>
                    <div id="pwaPromptText" class="text-sm text-gray-600 mt-1">Install the app for faster access and an app-like experience.</div>
                </div>
            </div>
            <div class="mt-4 flex gap-2 justify-end">
                <button id="pwaPromptDismiss" class="px-3 py-2 text-sm rounded border border-gray-300">Maybe later</button>
                <button id="pwaPromptInstall" class="px-3 py-2 text-sm rounded bg-blue-600 text-white">Install</button>
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
    </script>
    <script>
        // Delayed install dialog for PWA (with iOS support)
        (function() {
            var promptEvent = null;
            var promptEl = document.getElementById('pwaPrompt');
            var installBtn = document.getElementById('pwaPromptInstall');
            var dismissBtn = document.getElementById('pwaPromptDismiss');
            var promptText = document.getElementById('pwaPromptText');

            if (!promptEl) return;

            function isDismissed() { try { return Date.now() < parseInt(localStorage.getItem('pwaPromptSnoozeUntil') || '0', 10); } catch (e) { return false; } }

            function rememberDismissed() { try { localStorage.setItem('pwaPromptSnoozeUntil', String(Date.now() + 7 * 24 * 60 * 60 * 1000)); } catch (e) {} } // "Maybe later" snoozes for 7 days


            function showPrompt() {
                promptEl.classList.remove('hidden');
                promptEl.classList.add('flex');
            }

            function hidePrompt() {
                rememberDismissed();
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
                if (!isDismissed()) setTimeout(showPrompt, 6000);
            });

            if (isIOS && !isStandalone && !isDismissed()) {
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
            });

            window.addEventListener('appinstalled', function() {
                localStorage.setItem('pwaInstalled', '1');
                hidePrompt();
            });
        })();
    </script>
    <script>
        // PWA install prompt handling
        (function() {
            var installBtn = document.getElementById('installPwaBtn');
            if (!installBtn) return;

            // Hide if already installed or running as standalone
            var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
            if (isStandalone || localStorage.getItem('pwaInstalled') === '1') {
                installBtn.classList.add('hidden');
                return;
            }

            var deferredPrompt = null;
            window.addEventListener('beforeinstallprompt', function(e) {
                e.preventDefault();
                deferredPrompt = e;
                installBtn.classList.remove('hidden');
            });

            installBtn.addEventListener('click', async function() {
                if (!deferredPrompt) return;
                installBtn.disabled = true;
                try {
                    deferredPrompt.prompt();
                    var choice = await deferredPrompt.userChoice;
                    if (choice && choice.outcome === 'accepted') {
                        installBtn.classList.add('hidden');
                    } else {
                        installBtn.disabled = false;
                    }
                } catch (err) {
                    installBtn.disabled = false;
                    console.error('Install prompt error:', err);
                }
                deferredPrompt = null;
            });

            window.addEventListener('appinstalled', function() {
                localStorage.setItem('pwaInstalled', '1');
                installBtn.classList.add('hidden');
            });
        })();
    </script>
    
    <!-- User Dropdown Script -->
    
    @stack('scripts')

<script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            // Always start with sidebar hidden on page load
            sidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
            
            // Clear any existing localStorage to ensure clean state
            localStorage.removeItem('sidebarOpen');
            
            // Toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                const isCurrentlyOpen = !sidebar.classList.contains('-translate-x-full');
                
                if (isCurrentlyOpen) {
                    // Close sidebar
                    sidebar.classList.add('-translate-x-full');
                    sidebarBackdrop.classList.add('hidden');
                    localStorage.setItem('sidebarOpen', 'false');
                } else {
                    // Open sidebar
                    sidebar.classList.remove('-translate-x-full');
                    sidebarBackdrop.classList.remove('hidden');
                    localStorage.setItem('sidebarOpen', 'true');
                }
            });
            
            // Close sidebar when clicking backdrop
            sidebarBackdrop.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                sidebarBackdrop.classList.add('hidden');
                localStorage.setItem('sidebarOpen', 'false');
            });
            
            // Close sidebar when clicking close button
            sidebarClose.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                sidebarBackdrop.classList.add('hidden');
                localStorage.setItem('sidebarOpen', 'false');
            });
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);
                const isClickOnBackdrop = sidebarBackdrop.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && !isClickOnBackdrop && !sidebar.classList.contains('-translate-x-full')) {
                    // Close sidebar on any device when clicking outside
                    sidebar.classList.add('-translate-x-full');
                    sidebarBackdrop.classList.add('hidden');
                    localStorage.setItem('sidebarOpen', 'false');
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                // Always show backdrop when sidebar is open (both mobile and desktop)
                if (!sidebar.classList.contains('-translate-x-full')) {
                    sidebarBackdrop.classList.remove('hidden');
                }
            });

            // Plans dropdown functionality
            const plansDropdown = document.getElementById('plansDropdown');
            const plansDropdownMenu = document.getElementById('plansDropdownMenu');
            const plansDropdownArrow = plansDropdown ? plansDropdown.querySelector('svg:last-child') : null;

            if (plansDropdown && plansDropdownMenu) {
                plansDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isOpen = plansDropdownMenu.classList.contains('opacity-100');
                    
                    if (isOpen) {
                        // Close dropdown
                        plansDropdownMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                        plansDropdownMenu.classList.add('opacity-0', 'invisible', '-translate-y-2');
                        plansDropdownArrow.style.transform = 'rotate(0deg)';
                    } else {
                        // Open dropdown
                        plansDropdownMenu.classList.remove('opacity-0', 'invisible', '-translate-y-2');
                        plansDropdownMenu.classList.add('opacity-100', 'visible', 'translate-y-0');
                        plansDropdownArrow.style.transform = 'rotate(180deg)';
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!plansDropdown.contains(e.target) && !plansDropdownMenu.contains(e.target)) {
                        plansDropdownMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                        plansDropdownMenu.classList.add('opacity-0', 'invisible', '-translate-y-2');
                        plansDropdownArrow.style.transform = 'rotate(0deg)';
                    }
                });

                // Close dropdown when sidebar closes
                sidebarToggle.addEventListener('click', function() {
                    plansDropdownMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                    plansDropdownMenu.classList.add('opacity-0', 'invisible', '-translate-y-2');
                    plansDropdownArrow.style.transform = 'rotate(0deg)';
                });
            }

            // User dropdown functionality
            const userDropdownButton = document.getElementById('userDropdownButton');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            const userDropdownArrow = document.getElementById('userDropdownArrow');


            if (userDropdownButton && userDropdownMenu && userDropdownArrow) {
                userDropdownButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isOpen = userDropdownMenu.classList.contains('opacity-100');
                    
                    if (isOpen) {
                        // Close dropdown
                        userDropdownMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                        userDropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                        userDropdownArrow.style.transform = 'rotate(0deg)';
                    } else {
                        // Open dropdown
                        userDropdownMenu.classList.remove('opacity-0', 'invisible', 'scale-95');
                        userDropdownMenu.classList.add('opacity-100', 'visible', 'scale-100');
                        userDropdownArrow.style.transform = 'rotate(180deg)';
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userDropdownButton.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                        userDropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                        userDropdownArrow.style.transform = 'rotate(0deg)';
                    }
                });

                // Close dropdown when pressing Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        userDropdownMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                        userDropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                        userDropdownArrow.style.transform = 'rotate(0deg)';
                    }
                });
            }
        });

        // Notification dropdown functionality
        @auth
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdownMenu = document.getElementById('notificationDropdownMenu');
        const notificationList = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationCount = document.getElementById('notificationCount');



        if (notificationDropdown && notificationButton && notificationDropdownMenu) {
            let isOpen = false;

            // Toggle notification dropdown
            notificationButton.addEventListener('click', function(e) {
                e.stopPropagation();
                isOpen = !isOpen;
                
                if (isOpen) {
                    notificationDropdownMenu.classList.remove('opacity-0', 'invisible', 'scale-95');
                    notificationDropdownMenu.classList.add('opacity-100', 'visible', 'scale-100');
                    loadNotifications();
                } else {
                    notificationDropdownMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                    notificationDropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationDropdown.contains(e.target)) {
                    isOpen = false;
                    notificationDropdownMenu.classList.remove('opacity-100', 'visible', 'scale-100');
                    notificationDropdownMenu.classList.add('opacity-0', 'invisible', 'scale-95');
                }
            });

            // Load notifications
            function loadNotifications() {
                fetch('/user/notifications/recent', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.notifications && data.notifications.length > 0) {
                            notificationList.innerHTML = data.notifications.map(notification => `
                                <div class="p-3 border-b border-gray-700 hover:bg-gray-700 transition-colors">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center ${getNotificationIconClass(notification.type)}">
                                                ${getNotificationIcon(notification.type)}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-white truncate">${notification.title}</p>
                                            <p class="text-xs text-gray-400 mt-1">${notification.message}</p>
                                            <p class="text-xs text-gray-500 mt-1">${formatTime(notification.created_at)}</p>
                                        </div>
                                        ${!notification.read_at ? '<div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>' : ''}
                                    </div>
                                </div>
                            `).join('');
                        } else {
                            notificationList.innerHTML = `
                                <div class="p-4 text-center text-gray-400">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6z"></path>
                                    </svg>
                                    <p>No notifications</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        notificationList.innerHTML = `
                            <div class="p-4 text-center text-red-400">
                                <p>Error loading notifications</p>
                            </div>
                        `;
                    });
            }

            // Load unread count
            function loadUnreadCount() {
                fetch('/user/notifications/unread-count', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > 0) {
                            notificationCount.textContent = data.count > 99 ? '99+' : data.count;
                            notificationCount.classList.remove('hidden');
                            notificationBadge.classList.remove('hidden');
                        } else {
                            notificationCount.classList.add('hidden');
                            notificationBadge.classList.add('hidden');
                        }
                    })
                    .catch(error => console.error('Error loading unread count:', error));
            }

            // Mark all as read
            document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
                fetch('/user/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        loadUnreadCount();
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Clear all notifications
            document.getElementById('clearAllBtn')?.addEventListener('click', async function() {
                if (await uiConfirm('Are you sure you want to clear all notifications?')) {
                    fetch('/user/notifications/clear-all', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotifications();
                            loadUnreadCount();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });

            // Helper functions
            function getNotificationIconClass(type) {
                const classes = {
                    'deposit': 'bg-green-600',
                    'deposit_submitted': 'bg-green-600',
                    'deposit_approved': 'bg-green-600',
                    'withdrawal': 'bg-red-600',
                    'withdrawal_submitted': 'bg-red-600',
                    'withdrawal_approved': 'bg-red-600',
                    'trading': 'bg-blue-600',
                    'copy_trade': 'bg-purple-600',
                    'copy_trade_started': 'bg-purple-600',
                    'copy_trade_stopped': 'bg-red-600',
                    'bot_trade': 'bg-yellow-600',
                    'bot_trade_executed': 'bg-yellow-600',
                    'bot_created': 'bg-green-600',
                    'bot_started': 'bg-green-600',
                    'bot_paused': 'bg-yellow-600',
                    'bot_resumed': 'bg-green-600',
                    'bot_stopped': 'bg-red-600',
                    'system': 'bg-gray-600',
                    'security': 'bg-orange-600'
                };
                return classes[type] || 'bg-gray-600';
            }

            function getNotificationIcon(type) {
                const icons = {
                    'deposit': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>',
                    'deposit_submitted': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>',
                    'deposit_approved': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'withdrawal': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path></svg>',
                    'withdrawal_submitted': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path></svg>',
                    'withdrawal_approved': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'trading': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>',
                    'copy_trade': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>',
                    'copy_trade_started': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                    'copy_trade_stopped': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path></svg>',
                    'bot_trade': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
                    'bot_trade_executed': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
                    'bot_created': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>',
                    'bot_started': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'bot_paused': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'bot_resumed': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    'bot_stopped': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
                    'system': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
                    'security': '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>'
                };
                return icons[type] || '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6z"></path></svg>';
            }

            function formatTime(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) return 'Just now';
                if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
                if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
                return Math.floor(diffInSeconds / 86400) + 'd ago';
            }

            // Load initial data
            loadUnreadCount();
            
            // Refresh unread count every 30 seconds
            setInterval(loadUnreadCount, 30000);
        }
        @endauth

@yield('scripts')
</script>


<script src="{{ asset('front/livewire/livewire5dd3.js') }}"   data-csrf="QHTgDfeSDEhGixs61ktyfaAnqYfyNU0Xv8qcvRbs" data-update-uri="/livewire/update" data-navigate-once="true"></script>

</body>
</html>
