<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class DetectUserLocale
{
    private array $countryLanguageMap = [
        'US' => 'en', 'GB' => 'en', 'AU' => 'en', 'CA' => 'en', 'NZ' => 'en', 'IE' => 'en',
        'ES' => 'es', 'MX' => 'es', 'AR' => 'es', 'CO' => 'es', 'CL' => 'es', 'PE' => 'es', 'CU' => 'es', 'EC' => 'es', 'GT' => 'es', 'DO' => 'es', 'VE' => 'es',
        'FR' => 'fr', 'BE' => 'fr', 'LU' => 'fr', 'MC' => 'fr', 'CD' => 'fr', 'CI' => 'fr', 'CM' => 'fr', 'SN' => 'fr', 'MA' => 'fr', 'DZ' => 'fr', 'TN' => 'fr',
        'DE' => 'de', 'AT' => 'de',
        'IT' => 'it',
        'PT' => 'pt', 'BR' => 'pt', 'AO' => 'pt', 'MZ' => 'pt',
        'RU' => 'ru', 'BY' => 'ru', 'KZ' => 'ru', 'UA' => 'ru',
        'CN' => 'zh', 'TW' => 'zh', 'SG' => 'zh', 'HK' => 'zh',
        'JP' => 'ja',
        'KR' => 'ko',
        'SA' => 'ar', 'AE' => 'ar', 'EG' => 'ar', 'IQ' => 'ar', 'JO' => 'ar', 'KW' => 'ar',
        'LB' => 'ar', 'LY' => 'ar', 'OM' => 'ar', 'QA' => 'ar', 'SY' => 'ar',
        'YE' => 'ar', 'BH' => 'ar', 'SD' => 'ar', 'PS' => 'ar',
        'TR' => 'tr',
        'IN' => 'hi',
        'ID' => 'id',
        'NL' => 'nl',
        'PL' => 'pl',
        'SE' => 'sv', 'NO' => 'nb', 'DK' => 'da', 'FI' => 'fi',
        'GR' => 'el',
        'CZ' => 'cs',
        'RO' => 'ro',
        'HU' => 'hu',
        'TH' => 'th',
        'VN' => 'vi',
        'IL' => 'he',
        'CH' => 'de',
    ];

    private array $supportedLocales = [
        'en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'zh', 'ja', 'ko',
        'ar', 'tr', 'hi', 'id', 'nl', 'pl',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->detectLocale($request);

        app()->setLocale($locale);

        return $next($request);
    }

    private function detectLocale(Request $request): string
    {
        if ($locale = session('locale')) {
            return $locale;
        }

        if ($locale = $this->fromBrowser($request)) {
            return $locale;
        }

        if ($locale = $this->fromIp($request)) {
            session(['locale' => $locale]);
            return $locale;
        }

        return config('app.fallback_locale', 'en');
    }

    private function fromBrowser(Request $request): string
    {
        $locale = $request->getPreferredLanguage();
        if ($locale) {
            $lang = substr($locale, 0, 2);
            if (in_array($lang, $this->supportedLocales)) {
                return $lang;
            }
        }
        return '';
    }

    private function fromIp(Request $request): string
    {
        $ip = $request->ip();

        if ($ip === '127.0.0.1' || $ip === '::1') {
            return '';
        }

        $cacheKey = "user_locale_{$ip}";

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=countryCode");

                if ($response->successful()) {
                    $data = $response->json();
                    $countryCode = $data['countryCode'] ?? '';

                    if ($countryCode && isset($this->countryLanguageMap[$countryCode])) {
                        return $this->countryLanguageMap[$countryCode];
                    }
                }
            } catch (\Exception $e) {
                logger()->warning('IP geolocation failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            }

            return '';
        });
    }
}
