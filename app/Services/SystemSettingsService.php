<?php

namespace App\Services;

use App\Models\SystemSettings;
use Illuminate\Support\Facades\Cache;

class SystemSettingsService
{
    public function all(): array
    {
        return Cache::remember('system_settings', now()->addHours(6), fn() => [
            'systemLogo' => SystemSettings::where('key', 'system_logo')->first()?->value,
            'systemName' => SystemSettings::get('system_name') ?? 'Portfolio',
            'systemOwner' => SystemSettings::get('system_owner'),
            'systemColor' => SystemSettings::get('system_color') ?? '#1976D2',
        ]);
    }
}