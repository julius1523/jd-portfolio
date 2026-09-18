<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSettings extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('system_settings'));
        static::deleted(fn() => Cache::forget('system_settings'));
    }

    public static function get(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }
}
