<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    protected $fillable = ['name', 'location'];

    public function allItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Cache only the menu identifier. Caching Eloquent models serializes their
     * class and relationship state, which can become invalid after deployments.
     */
    public static function cached(string $location): ?self
    {
        $menuId = Cache::remember(
            'menu:id:'.$location,
            3600,
            fn () => static::query()->where('location', $location)->value('id')
        );

        if (! $menuId) {
            return null;
        }

        $relations = [];
        $prefix = 'items';
        for ($depth = 0; $depth <= 4; $depth++) {
            foreach (['page', 'category', 'service'] as $relation) {
                $relations[] = $prefix.'.'.$relation;
            }
            if ($depth < 4) {
                $prefix .= '.children';
            }
        }

        return static::query()->whereKey($menuId)->with($relations)->first();
    }

    public static function forgetLocationCache(?string $location): void
    {
        if (! $location) {
            return;
        }

        Cache::forget('menu:id:'.$location);

        // Remove the legacy key written by versions that cached Eloquent models.
        Cache::forget('menu:'.$location);
    }

    protected static function booted(): void
    {
        static::saved(function (Menu $menu): void {
            static::forgetLocationCache($menu->getOriginal('location'));
            static::forgetLocationCache($menu->location);
        });

        static::deleted(function (Menu $menu): void {
            static::forgetLocationCache($menu->location);
        });
    }
}
