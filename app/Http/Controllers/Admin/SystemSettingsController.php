<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSystemSettingsRequest;
use App\Models\SystemSettings;
use App\Services\FileUploadService;
use App\Services\SystemSettingsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SystemSettingsController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private SystemSettingsService $settings
    ) {
    }

    public function getSystemSettings()
    {
        return response()->json($this->settings->all());
    }

    public function updateSystemSettings(UpdateSystemSettingsRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            SystemSettings::set('system_name', $data['systemName']);
            SystemSettings::set('system_owner', $data['systemOwner']);
            SystemSettings::set('system_color', $data['systemColor']);

            $logo = SystemSettings::firstOrNew(['key' => 'system_logo']);
            $logo->type = 'image';
            $logo->group ??= 'general';
            $this->fileUploadService->handle($request, $logo, 'systemLogo', 'value', 'uploads/images');
            $logo->save();
        });

        Cache::forget('system_settings');

        Cache::forget('system_settings');

        return response()->json([
            'message' => 'System settings updated successfully.',
        ]);
    }
}