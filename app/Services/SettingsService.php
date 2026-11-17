<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::read($key, $default);
    }
    public function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void
    {
        Setting::write($key, $value, $group, $type);
    }
    public function all(string $group = 'general')
    {
        return cache()->remember('settings:' . $group, 3600, fn() => Setting::where('group', $group)->pluck('value', 'key'));
    }
}
