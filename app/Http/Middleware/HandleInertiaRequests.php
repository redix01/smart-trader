<?php

namespace App\Http\Middleware;

use App\Services\PortfolioService;
use App\Services\PlatformSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $portfolio = app(PortfolioService::class);
        $platformSettings = app(PlatformSettingsService::class);
        $liveChatWidgetCode = Schema::hasTable('platform_settings')
            ? ($platformSettings->get('livechat_widget_code', '') ?? '')
            : '';
        $supportEmail = Schema::hasTable('platform_settings')
            ? ($platformSettings->get('support_email', config('mail.from.address')) ?? '')
            : config('mail.from.address');
        $supportPhone = Schema::hasTable('platform_settings')
            ? ($platformSettings->get('support_phone', '') ?? '')
            : '';

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'walletSummary' => $user ? $portfolio->summary($user) : null,
            'platform' => [
                'site_name' => $platformSettings->getSiteName(),
                'livechat_widget_code' => $liveChatWidgetCode,
                'support_email' => $supportEmail,
                'support_phone' => $supportPhone,
            ],
        ];
    }
}
