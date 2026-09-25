<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSystemSettingsRequest;
use App\Models\SystemSettings;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Cache;

class SystemSettingsController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {
    }

    public function getSystemSettings()
    {
        $settings = Cache::remember('system_settings', now()->addHours(6), function () {
            $logoSetting = SystemSettings::where('key', 'system_logo')->first();
            $logoSetting?->mergeCasts(['value' => 'array']);

            return [
                'systemLogo' => $logoSetting?->value['url'] ?? null,
                'systemName' => SystemSettings::get('system_name') ?? "Portfolio",
                'systemOwner' => SystemSettings::get('system_owner') ?? null,
                'systemColor' => SystemSettings::get('system_color') ?? "#1976D2",
            ];
        });

        return response()->json($settings);
    }

    public function updateSystemSettings(UpdateSystemSettingsRequest $request)
    {
        $data = $request->validated();

        SystemSettings::set('system_name', $data['systemName']);
        SystemSettings::set('system_owner', $data['systemOwner']);
        SystemSettings::set('system_color', $data['systemColor']);

        $logoSetting = SystemSettings::firstOrNew(['key' => 'system_logo']);
        $logoSetting->mergeCasts(['value' => 'array']);
        $logoSetting->type = 'image';
        $logoSetting->group = $logoSetting->group ?? 'general';

        $this->fileUploadService->handle($request, $logoSetting, 'systemLogo', 'value', 'uploads/images');

        $logoSetting->save();

        Cache::forget('system_settings');

        return response()->json([
            'message' => 'System settings updated successfully.',
        ]);
    }
}