<?php

namespace App\Http\Controllers;

use App\Services\PlatformSettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function __construct(private PlatformSettingsService $settings) {}

    public function __invoke(Request $request)
    {
        return Inertia::render('Support', [
            'support' => [
                'email' => $this->settings->get('support_email', config('mail.from.address')),
                'phone' => $this->settings->get('support_phone', ''),
                'livechat_widget_code' => $this->settings->get('livechat_widget_code', ''),
            ],
        ]);
    }
}
