<?php

namespace App\Services\Widgets;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Property;
use App\Models\Widget;
use App\Models\WidgetArea;

class WidgetRenderer
{
    public function forLocation(string $location)
    {
        $area = WidgetArea::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->with(['items' => fn($query) => $query->where('is_active', true)->orderBy('sort_order')->with('widget')])
            ->first();

        if (! $area) {
            return collect();
        }

        return $area->items
            ->filter(fn($item) => $item->widget && $item->widget->status === 'published')
            ->map(fn($item) => $this->payload($item->widget));
    }

    private function payload(Widget $widget): array
    {
        $config = $widget->config ?? [];
        if (isset($config['image_url']) && ctype_digit((string) $config['image_url'])) {
            $media = Media::query()->find((int) $config['image_url']);
            $config['image_alt'] = $media?->alt_text ?? '';
            $config['image_url'] = $media?->url ?? '';
        }
        $data = match ($widget->type) {
            'recent_posts' => Post::published()->latest('published_at')->limit((int) ($config['limit'] ?? 5))->get(),
            'categories' => Category::where('type', 'property')->where('is_active', true)->orderBy('sort_order')->limit((int) ($config['limit'] ?? 12))->get(),
            'featured_properties' => Property::published()->where('is_featured', true)->with('category:id,name')->latest('published_at')->limit((int) ($config['limit'] ?? 4))->get(),
            default => null,
        };

        return ['id' => $widget->id, 'name' => $widget->name, 'type' => $widget->type, 'config' => $config, 'data' => $data];
    }
}
