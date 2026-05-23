<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Services\PlatformSettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function __construct(private PlatformSettingsService $platformSettings) {}

    public function index()
    {
        return Inertia::render('Admin/Settings/Index', [
            'groups' => $this->platformSettings->getAdminFormGroups(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:255',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($data['settings'] as $setting) {
            $definition = $this->platformSettings->definitionFor($setting['key']);

            if (! $definition) {
                continue;
            }

            PlatformSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $definition['group'],
                    'type' => $definition['type'],
                ]
            );
        }

        return redirect()->back()->with('success', 'Settings updated.');
    }
}
