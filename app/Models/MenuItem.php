<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'parent_id', 'label', 'url', 'link_type', 'link_id', 'target', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function getResolvedUrlAttribute(): string
    {
        if (($this->link_type ?? 'custom') === 'page' && $this->link_id) {
            $page = Page::query()->whereKey($this->link_id)->where('status', 'published')->first();
            return $page ? route('pages.show', $page->slug) : '#';
        }

        if (in_array($this->link_type, ['blog_category', 'property_category'], true) && $this->link_id) {
            $category = Category::query()->whereKey($this->link_id)->where('type', $this->link_type === 'blog_category' ? 'post' : 'property')->first();
            if (! $category) {
                return '#';
            }

            return $this->link_type === 'blog_category'
                ? route('blog.index', ['category' => $category->slug])
                : route('properties.index', ['category' => $category->slug]);
        }

        if (($this->link_type ?? 'custom') === 'service') {
            $service = $this->link_id ? Service::published()->find($this->link_id) : null;
            return $service ? route('services.show', $service->slug) : route('services.index');
        }

        return $this->url ?: '#';
    }
}
