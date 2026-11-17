<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    public static function read(string $key, mixed $default = null): mixed
    {
        $setting = cache()->remember('setting:'.$key, 3600, fn () => static::where('key', $key)->first(['value', 'type']));

        if (!$setting || $setting->value === null) {
            return $default;
        }

        return $setting->type === 'json'
            ? (json_decode($setting->value, true) ?? $default)
            : $setting->value;
    }

    public static function allValues(): array
    {
        return cache()->remember('settings:all', 3600, function () {
            return static::query()->get(['key', 'value', 'type'])->mapWithKeys(function ($setting) {
                $value = $setting->type === 'json' ? json_decode($setting->value, true) : $setting->value;

                return [$setting->key => $value];
            })->all();
        });
    }

    public static function write(string $key, mixed $value, string $group = 'general', ?string $type = null): void
    {
        $type ??= is_array($value) || is_object($value) ? 'json' : 'string';
        $stored = $type === 'json' ? json_encode($value, JSON_THROW_ON_ERROR) : (is_bool($value) ? ($value ? '1' : '0') : (string) $value);

        static::updateOrCreate(['key' => $key], [
            'group' => $group,
            'value' => $stored,
            'type' => $type,
        ]);

        cache()->forget('setting:'.$key);
        cache()->forget('settings:'.$group);
        cache()->forget('settings:all');
    }
}
