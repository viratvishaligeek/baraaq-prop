<?php

namespace App\Services\Homepage;

use App\Models\HomepageSection;
use App\Models\Media;

class SectionMediaResolver
{
    /**
     * Convert Media Library IDs to public URLs only at render time.
     * The persisted section JSON keeps stable media IDs.
     */
    public function forRendering(HomepageSection $section, array $config): array
    {
        $definition = config('cms-sections.sections.'.$section->section_key, []);
        $imageFields = collect($definition['fields'] ?? [])
            ->filter(fn (array $schema) => ($schema['type'] ?? null) === 'image')
            ->keys();

        $mediaIds = $imageFields
            ->map(fn (string $field) => $config[$field] ?? null)
            ->filter(fn ($value) => (is_string($value) || is_int($value)) && ctype_digit((string) $value))
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        if ($mediaIds->isEmpty()) {
            return $config;
        }

        $media = Media::query()->whereIn('id', $mediaIds)->get()->keyBy('id');

        foreach ($imageFields as $field) {
            $value = $config[$field] ?? null;

            if ((! is_string($value) && ! is_int($value)) || ! ctype_digit((string) $value)) {
                continue;
            }

            $record = $media->get((int) $value);
            // Missing/deleted records safely render as no image, never as a broken numeric URL.
            $config[$field] = $record?->url;
            $config[$field.'_alt'] = $record?->alt_text;
            $config[$field.'_media_name'] = $record?->original_name;
        }

        return $config;
    }
}
