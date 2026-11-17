<?php

namespace App\Services\Pages;

use App\Models\Banner;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\TeamMember;
use App\Services\Widgets\CmsWidgetRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PageBuilderService
{
    public function __construct(private readonly CmsWidgetRegistry $registry)
    {
    }

    public function frontendData(): array
    {
        return [
            'properties' => Property::published()->with('category:id,name')->latest('published_at')->limit(6)->get(),
            'featuredProperties' => Property::published()->where('is_featured', true)->with('category:id,name')->latest('published_at')->limit(4)->get(),
            'banners' => Banner::visible()->where('placement', 'home')->orderBy('sort_order')->get(),
            'posts' => Post::published()->with('author:id,name')->latest('published_at')->limit(3)->get(),
            'teamMembers' => TeamMember::where('status', 'published')->where('is_active', true)->orderBy('sort_order')->limit(4)->get(),
        ];
    }

    public function add(Page $page, string $key): HomepageSection
    {
        $definition = $this->registry->find($key);
        if (! $definition) {
            throw ValidationException::withMessages(['section_key' => 'The selected widget design is not registered.']);
        }

        return $page->sections()->create([
            'instance_key' => (string) Str::uuid(),
            'section_key' => $definition['key'],
            'section_type' => $definition['type'],
            'source_design' => $definition['source_design'] ?? 'default',
            'sort_order' => ((int) $page->sections()->max('sort_order')) + 1,
            'is_enabled' => true,
            'config' => $definition['defaults'] ?? [],
            'data_source' => [],
        ]);
    }

    public function duplicate(Page $page, HomepageSection $section): HomepageSection
    {
        abort_unless($section->page_id === $page->id, 404);

        return DB::transaction(function () use ($page, $section) {
            $page->sections()->where('sort_order', '>', $section->sort_order)->lockForUpdate()->increment('sort_order');
            $copy = $section->replicate();
            $copy->instance_key = (string) Str::uuid();
            $copy->sort_order = $section->sort_order + 1;
            $copy->save();

            foreach ($section->mediaAttachments as $attachment) {
                $copy->mediaAttachments()->create(['field_key' => $attachment->field_key, 'media_id' => $attachment->media_id]);
            }

            return $copy;
        });
    }

    public function reorder(Page $page, array $ids): void
    {
        $sections = $page->sections()->whereIn('id', $ids)->get()->keyBy('id');
        abort_unless($sections->count() === count($ids), 422, 'The submitted order contains a section that does not belong to this page.');

        DB::transaction(function () use ($ids, $sections) {
            foreach ($ids as $position => $id) $sections[$id]->update(['sort_order' => $position + 1]);
        });
    }

    public function save(Page $page, HomepageSection $section, array $input): void
    {
        abort_unless($section->page_id === $page->id, 404);
        $definition = $this->registry->find($section->section_key);
        abort_unless($definition, 422, 'The widget design is no longer registered.');
        $config = array_intersect_key($input['config'] ?? [], array_flip(array_keys($definition['fields'] ?? [])));

        DB::transaction(function () use ($section, $definition, $config, $input) {
            foreach ($config as $field => $value) {
                if (($definition['fields'][$field]['type'] ?? null) !== 'image') continue;
                if ($value === null || $value === '') {
                    $section->mediaAttachments()->where('field_key', $field)->delete();
                } elseif (ctype_digit((string) $value)) {
                    abort_unless(Media::whereKey((int) $value)->where('mime_type', 'like', 'image/%')->exists(), 422, 'Choose a valid image from the Media Library.');
                    $section->mediaAttachments()->updateOrCreate(['field_key' => $field], ['media_id' => (int) $value]);
                } else {
                    $section->mediaAttachments()->where('field_key', $field)->delete();
                }
            }

            $section->update([
                'config' => array_merge($section->config ?? [], $config),
                'data_source' => $input['data_source'] ?? ($section->data_source ?? []),
            ]);
        });
    }
}
