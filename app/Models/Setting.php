<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    public static function read(string $key, mixed $default = null): mixed
    {
        // Cache scalar data only. Older releases cached a Setting model here,
        // which can become an incomplete object after a deployment.
        // The new scalar cache key intentionally ignores legacy cached Setting models.
        $setting = cache()->remember(
            'setting:data:' . $key,
            3600,
            fn () => static::query()->where('key', $key)->first(['value', 'type'])?->only(['value', 'type'])
        );

        if (!$setting || ($setting['value'] ?? null) === null) {
            return $default;
        }

        return match ($setting['type'] ?? 'string') {
            'json' => json_decode($setting['value'], true) ?? $default,
            'boolean' => filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN),
            default => $setting['value'],
        };
    }

    public static function allValues(): array
    {
        return cache()->remember('settings:all', 3600, function () {
            return static::query()->get(['key', 'value', 'type'])->mapWithKeys(function ($setting) {
                $value = match ($setting->type) {
                    'json' => json_decode($setting->value, true),
                    'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                    default => $setting->value,
                };

                return [$setting->key => $value];
            })->all();
        });
    }

    public static function write(string $key, mixed $value, string $group = 'general', ?string $type = null): void
    {
        $type ??= is_array($value) || is_object($value) ? 'json' : (is_bool($value) ? 'boolean' : 'string');
        $stored = $type === 'json' ? json_encode($value, JSON_THROW_ON_ERROR) : ($type === 'boolean' ? ($value ? '1' : '0') : (string) $value);

        static::updateOrCreate(['key' => $key], [
            'group' => $group,
            'value' => $stored,
            'type' => $type,
        ]);

        cache()->forget('setting:' . $key);
        cache()->forget('setting:data:' . $key);
        cache()->forget('settings:' . $group);
        cache()->forget('settings:all');
    }
}
