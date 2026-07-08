
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }} - {{ __('Sign Up') }}</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">
    
    <!-- Tailwind CSS CDN for immediate styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Vite assets (uncomment when running npm run dev) -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-black min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            @if(\App\Helpers\WebsiteSettingsHelper::hasTextLogo())
                <h1 class="text-2xl font-bold text-white">{{ \App\Helpers\WebsiteSettingsHelper::getTextLogo() }}</h1>
            @elseif(\App\Helpers\WebsiteSettingsHelper::hasImageLogo())
                <img src="{{ \App\Helpers\WebsiteSettingsHelper::getLogoUrl() }}"
                     alt="{{ \App\Helpers\WebsiteSettingsHelper::getSiteName() }}"
                     class="h-16 w-auto object-contain mx-auto">
            @else
                <img src="{{ asset('img/brand-logo.svg') }}"
                     alt="{{ \App\Helpers\WebsiteSettingsHelper::getSiteName() }}"
                     class="h-14 w-auto object-contain mx-auto">
            @endif
        </div>

        <!-- Register Form Card -->
        <div class="bg-gray-800 rounded-lg p-8 shadow-2xl border border-gray-700">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <!-- Referral code (auto-filled from referral link) -->
                <input type="hidden" name="ref" value="{{ old('ref', $referralCode ?? '') }}">
                
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Full Name') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input 
                            id="name" 
                            name="name" 
                            type="text" 
                            value="{{ old('name') }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('name') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="{{ __('Enter your full name') }}"
                            required 
                            autocomplete="name"
                            autofocus
                        >
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Username') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input 
                            id="username" 
                            name="username" 
                            type="text" 
                            value="{{ old('username') }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('username') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="{{ __('Enter unique username') }}"
                            required 
                            autocomplete="username"
                        >
                    </div>
                    @error('username')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Email') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            value="{{ old('email') }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('email') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="{{ __('Enter your email') }}"
                            required 
                            autocomplete="email"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Phone Number') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                        </div>
                        <input 
                            id="phone" 
                            name="phone" 
                            type="tel" 
                            value="{{ old('phone') }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('phone') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="{{ __('Enter your phone number') }}"
                            required 
                            autocomplete="tel"
                        >
                    </div>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Country Field -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Country') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <select 
                            id="country" 
                            name="country" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('country') border-red-500 focus:ring-red-500 @enderror"
                            required 
                        >
                            <option value="">{{ __('Select your country') }}</option>
                            <option value="United States" {{ old('country') == 'United States' ? 'selected' : '' }}>🇺🇸 United States</option>
                            <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>🇬🇧 United Kingdom</option>
                            <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>🇨🇦 Canada</option>
                            <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>🇩🇪 Germany</option>
                            <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>🇫🇷 France</option>
                            <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>🇦🇺 Australia</option>
                            <option value="Japan" {{ old('country') == 'Japan' ? 'selected' : '' }}>🇯🇵 Japan</option>
                            <option value="South Korea" {{ old('country') == 'South Korea' ? 'selected' : '' }}>🇰🇷 South Korea</option>
                            <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>🇸🇬 Singapore</option>
                            <option value="Netherlands" {{ old('country') == 'Netherlands' ? 'selected' : '' }}>🇳🇱 Netherlands</option>
                            <option value="Switzerland" {{ old('country') == 'Switzerland' ? 'selected' : '' }}>🇨🇭 Switzerland</option>
                            <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>🇸🇪 Sweden</option>
                            <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>🇳🇴 Norway</option>
                            <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>🇩🇰 Denmark</option>
                            <option value="Finland" {{ old('country') == 'Finland' ? 'selected' : '' }}>🇫🇮 Finland</option>
                            <option value="Belgium" {{ old('country') == 'Belgium' ? 'selected' : '' }}>🇧🇪 Belgium</option>
                            <option value="Austria" {{ old('country') == 'Austria' ? 'selected' : '' }}>🇦🇹 Austria</option>
                            <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>🇮🇹 Italy</option>
                            <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>🇪🇸 Spain</option>
                            <option value="Portugal" {{ old('country') == 'Portugal' ? 'selected' : '' }}>🇵🇹 Portugal</option>
                            <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>🇮🇪 Ireland</option>
                            <option value="New Zealand" {{ old('country') == 'New Zealand' ? 'selected' : '' }}>🇳🇿 New Zealand</option>
                            <option value="Hong Kong" {{ old('country') == 'Hong Kong' ? 'selected' : '' }}>🇭🇰 Hong Kong</option>
                            <option value="Taiwan" {{ old('country') == 'Taiwan' ? 'selected' : '' }}>🇹🇼 Taiwan</option>
                            <option value="Israel" {{ old('country') == 'Israel' ? 'selected' : '' }}>🇮🇱 Israel</option>
                            <option value="United Arab Emirates" {{ old('country') == 'United Arab Emirates' ? 'selected' : '' }}>🇦🇪 United Arab Emirates</option>
                            <option value="Saudi Arabia" {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>🇸🇦 Saudi Arabia</option>
                            <option value="Brazil" {{ old('country') == 'Brazil' ? 'selected' : '' }}>🇧🇷 Brazil</option>
                            <option value="Mexico" {{ old('country') == 'Mexico' ? 'selected' : '' }}>🇲🇽 Mexico</option>
                            <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>🇮🇳 India</option>
                            <option value="China" {{ old('country') == 'China' ? 'selected' : '' }}>🇨🇳 China</option>
                        </select>
                    </div>
                    @error('country')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency Field -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Preferred Currency') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <select 
                            id="currency" 
                            name="currency" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('currency') border-red-500 focus:ring-red-500 @enderror"
                            required
                        >
                            <option value="">{{ __('Select your preferred currency') }}</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                            <option value="JPY" {{ old('currency') == 'JPY' ? 'selected' : '' }}>JPY</option>
                            <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>CAD</option>
                            <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD</option>
                            <option value="CHF" {{ old('currency') == 'CHF' ? 'selected' : '' }}>CHF</option>
                            <option value="CNY" {{ old('currency') == 'CNY' ? 'selected' : '' }}>CNY</option>
                            <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>INR</option>
                            <option value="BRL" {{ old('currency') == 'BRL' ? 'selected' : '' }}>BRL</option>
                        </select>
                    </div>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            class="block w-full pl-10 pr-10 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('password') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="{{ __('Enter your password') }}"
                            required 
                            autocomplete="new-password"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword('password')"
                        >
                            <svg id="eye-icon-password" class="h-5 w-5 text-gray-400 hover:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            <svg id="eye-slash-icon-password" class="h-5 w-5 text-gray-400 hover:text-gray-300 hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Confirm Password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            class="block w-full pl-10 pr-10 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            placeholder="{{ __('Confirm your password') }}"
                            required 
                            autocomplete="new-password"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword('password_confirmation')"
                        >
                            <svg id="eye-icon-confirm" class="h-5 w-5 text-gray-400 hover:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            <svg id="eye-slash-icon-confirm" class="h-5 w-5 text-gray-400 hover:text-gray-300 hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Honeypot Fields (Hidden from users) -->
                <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;">
                    <label for="website">Website (leave blank)</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    
                    <label for="phone_alt">Phone Alternative (leave blank)</label>
                    <input type="text" id="phone_alt" name="phone_alt" tabindex="-1" autocomplete="off">
                    
                    <label for="company">Company (leave blank)</label>
                    <input type="text" id="company" name="company" tabindex="-1" autocomplete="off">
                </div>
                
                <!-- Time-based honeypot -->
                <input type="hidden" name="registration_time" value="{{ time() }}">

                <!-- Terms & Conditions -->
                <div class="flex items-center">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-600 rounded bg-gray-700"
                        required
                    >
                    <label for="terms" class="ml-2 block text-sm text-gray-300">
                        {{ __('I agree to the') }}
                        <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors">{{ __('Terms & Conditions') }}</a>
                    </label>
        </div>

                <!-- Create Account Button -->
                <button 
                    type="submit" 
                    id="registerSubmitBtn"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-cyan-400 hover:from-blue-600 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105"
                >
                    <span id="registerBtnText">{{ __('Create Account') }}</span>
                    <span id="registerBtnSpinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Creating Account...') }}
                    </span>
                </button>
            </form>

            <!-- Sign In Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-400">
                    {{ __('Have an account?') }}
                    <a href="{{ route('login') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">
                        {{ __('Sign In') }}
                    </a>
                </p>
        </div>
        </div>

        <!-- Back to Home Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('index') }}" class="text-sm text-gray-500 hover:text-gray-400 transition-colors">
                ← {{ __('Back to Home') }}
            </a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(`eye-icon-${fieldId === 'password' ? 'password' : 'confirm'}`);
            const eyeSlashIcon = document.getElementById(`eye-slash-icon-${fieldId === 'password' ? 'password' : 'confirm'}`);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        // Register form processing state
        function setRegisterButtonProcessing(isProcessing) {
            const button = document.getElementById('registerSubmitBtn');
            const buttonText = document.getElementById('registerBtnText');
            const buttonSpinner = document.getElementById('registerBtnSpinner');
            
            if (isProcessing) {
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.classList.remove('hover:from-blue-600', 'hover:to-cyan-500', 'hover:scale-105');
                buttonText.classList.add('hidden');
                buttonSpinner.classList.remove('hidden');
            } else {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                button.classList.add('hover:from-blue-600', 'hover:to-cyan-500', 'hover:scale-105');
                buttonText.classList.remove('hidden');
                buttonSpinner.classList.add('hidden');
            }
        }

        // Register form submission
        document.querySelector('form[action="{{ route("register") }}"]').addEventListener('submit', function(e) {
            // Set button to processing state
            setRegisterButtonProcessing(true);
        });

        // Auto-hide validation messages after 5 seconds
        setTimeout(function() {
            const errorMessages = document.querySelectorAll('.text-red-400');
            errorMessages.forEach(function(message) {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease-out';
                setTimeout(function() {
                    message.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
