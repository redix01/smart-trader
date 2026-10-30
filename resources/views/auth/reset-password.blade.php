
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }} - {{ __('Reset Password') }}</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">

    <!-- Tailwind CSS CDN for immediate styling -->
    <script src="https://cdn.tailwindcss.com"></script>

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

        <!-- Reset Password Form Card -->
        <div class="bg-gray-800 rounded-lg p-8 shadow-2xl border border-gray-700">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-white">{{ __('Reset Password') }}</h2>
                <p class="mt-2 text-sm text-gray-400">
                    {{ __('Enter your email address and choose a new password.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
          @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                            value="{{ old('email', $request->email) }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('email') border-red-500 focus:ring-red-500 @enderror"
                            placeholder="{{ __('Enter your email') }}"
                            required
                            autocomplete="username"
                            autofocus
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">{{ __('New Password') }}</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="block w-full px-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors @error('password') border-red-500 focus:ring-red-500 @enderror"
                        placeholder="{{ __('Enter your new password') }}"
                        required
                        autocomplete="new-password"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">{{ __('Confirm Password') }}</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="block w-full px-3 py-3 border border-gray-600 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                        placeholder="{{ __('Confirm your new password') }}"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-cyan-400 hover:from-blue-600 hover:to-cyan-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105"
                >
                    {{ __('Reset Password') }}
                </button>
            </form>

            <!-- Back to Login Link -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-400">
                    <a href="{{ route('login') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">
                        {{ __('Back to Sign In') }}
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
</body>
</html>
