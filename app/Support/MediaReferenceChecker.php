<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MediaReferenceChecker
{
    public function isReferenced(Media $media): bool
    {
        if (Schema::hasTable('property_accommodations')
            && Schema::hasColumn('property_accommodations', 'image_media_id')
            && DB::table('property_accommodations')->where('image_media_id', $media->id)->exists()) {
            return true;
        }

        if ($this->homepageSectionReferencesMediaId($media->id) || $this->isPathReferenced($media->path)) {
            return true;
        }

        if (Schema::hasTable('settings')) {
            $galleryValue = DB::table('settings')->where('key', 'footer_gallery_images')->value('value');
            $galleryIds = is_string($galleryValue) ? json_decode($galleryValue, true) : null;

            if (! is_array($galleryIds)) {
                return false;
            }

            $galleryIdsOnly = array_filter($galleryIds, 'is_numeric');

            return in_array((int) $media->id, array_map('intval', $galleryIdsOnly), true)
                || in_array($media->path, $galleryIds, true);
        }

        return false;
    }

    private function homepageSectionReferencesMediaId(int $mediaId): bool
    {
        if (! Schema::hasTable('homepage_sections') || ! Schema::hasColumn('homepage_sections', 'config')) {
            return false;
        }

        foreach (DB::table('homepage_sections')->pluck('config') as $storedConfig) {
            $config = is_array($storedConfig) ? $storedConfig : json_decode((string) $storedConfig, true);
            if (! is_array($config)) {
                continue;
            }

            foreach ($config as $key => $value) {
                if (is_numeric($value)
                    && (int) $value === $mediaId
                    && preg_match('/image|photo|icon|background|logo|shape/i', (string) $key)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isPathReferenced(string $path): bool
    {
        $references = [
            'properties' => ['featured_image', 'og_image'],
            'property_images' => ['path'],
            'posts' => ['featured_image', 'og_image'],
            'pages' => ['og_image'],
            'services' => ['featured_image', 'og_image'],
            'gallery_items' => ['image', 'og_image'],
            'neighborhood_guides' => ['hero_image', 'og_image'],
            'service_images' => ['path'],
            'team_members' => ['photo'],
            'testimonials' => ['photo'],
            'banners' => ['image'],
            'categories' => ['icon_path'],
            'settings' => ['value'],
        ];

        foreach ($references as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)
                    && DB::table($table)->where($column, $path)->exists()) {
                    return true;
                }
            }
        }

        $richTextReferences = [
            'posts' => ['content'],
            'pages' => ['content'],
            'services' => ['content'],
            'properties' => ['description'],
            'team_members' => ['bio'],
            'testimonials' => ['quote'],
            'banners' => ['description'],
        ];

        foreach ($richTextReferences as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)
                    && DB::table($table)->where($column, 'like', '%'.$path.'%')->exists()) {
                    return true;
                }
            }
        }

        if (Schema::hasTable('homepage_sections') && Schema::hasColumn('homepage_sections', 'config')) {
            foreach (DB::table('homepage_sections')->pluck('config') as $storedConfig) {
                $config = is_array($storedConfig) ? $storedConfig : json_decode((string) $storedConfig, true);
                if (! is_array($config)) {
                    continue;
                }

                foreach ($config as $key => $value) {
                    if (is_string($value)
                        && $value === $path
                        && preg_match('/image|photo|icon|background|logo|shape/i', (string) $key)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
