<?php

namespace App\Models;

use App\Rules\SafeLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'url', 'link_type', 'link_id',
        'target', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        $flushMenuCache = static function (MenuItem $item): void {
            $menuIds = array_filter([
                $item->menu_id,
                $item->getOriginal('menu_id'),
            ]);

            if ($menuIds === []) {
                return;
            }

            Menu::query()
                ->whereIn('id', $menuIds)
                ->pluck('location')
                ->each(fn (string $location) => Menu::forgetLocationCache($location));
        };

        static::saved($flushMenuCache);
        static::deleted($flushMenuCache);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'link_id')->where('status', 'published');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'link_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'link_id')->published();
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function getResolvedUrlAttribute(): string
    {
        if (($this->link_type ?? 'custom') === 'page' && $this->link_id) {
            $page = $this->page;

            return $page ? route('pages.show', $page->slug) : '#';
        }

        if (in_array($this->link_type, ['blog_category', 'property_category'], true) && $this->link_id) {
            $category = $this->category;
            if ($category && $category->type !== ($this->link_type === 'blog_category' ? 'post' : 'property')) {
                $category = null;
            }

            if (! $category) {
                return '#';
            }

            return $this->link_type === 'blog_category'
                ? route('blog.index', ['category' => $category->slug])
                : route('properties.index', ['category' => $category->slug]);
        }

        if (($this->link_type ?? 'custom') === 'service') {
            $service = $this->service;

            return $service ? route('services.show', $service->slug) : route('services.index');
        }

        return SafeLink::sanitize($this->url);
    }
}
