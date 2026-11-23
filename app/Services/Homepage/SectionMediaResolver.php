<?php
namespace App\Services\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
class SectionMediaResolver
{
    public function forRendering(HomepageSection $section, array $config): array
    {
        $definitions = config('cms-sections.sections', []);
        $definition = $definitions[$section->section_key] ?? [];
        $imageFields = collect($definition['fields'] ?? [])->filter(fn (array $schema) => ($schema['type'] ?? null) === 'image')->keys();
        $mediaIds = $imageFields->map(fn (string $field) => $config[$field] ?? null)->filter(fn ($value) => (is_string($value) || is_int($value)) && ctype_digit((string) $value))->map(fn ($value) => (int) $value)->unique()->values();
        if ($mediaIds->isEmpty()) return $config;
        $attachments = ($section->relationLoaded('mediaAttachments') ? $section->mediaAttachments : $section->mediaAttachments()->with('media')->get())->keyBy('field_key');
        $media = Media::query()->whereIn('id', $mediaIds)->get()->keyBy('id');
        foreach ($imageFields as $field) {
            $value = $config[$field] ?? null;
            if ((!is_string($value) && !is_int($value)) || !ctype_digit((string) $value)) continue;
            $record = $attachments->get($field)?->media;
            if (!$record || (int) $record->id !== (int) $value) $record = $media->get((int) $value);
            $config[$field] = $record?->url;
            $config[$field . '_alt'] = $record?->alt_text;
            $config[$field . '_media_name'] = $record?->original_name;
        }
        return $config;
    }
}
