<?php

namespace App\Services\CircleRate;

use Illuminate\Support\Facades\Cache;

class CircleRateCache
{
    public static function version(): int
    {
        Cache::add('circle-rate:cache-version', 1, now()->addYears(10));
        return (int) Cache::get('circle-rate:cache-version', 1);
    }

    public static function bumpVersion(): void
    {
        Cache::add('circle-rate:cache-version', 1, now()->addYears(10));
        Cache::increment('circle-rate:cache-version');
    }
}
