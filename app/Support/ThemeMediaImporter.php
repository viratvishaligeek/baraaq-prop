<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThemeMediaImporter
{
    public function import(string $relativePath, string $altText, ?int $uploadedBy = null): ?Media
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

        if (! Str::startsWith($relativePath, 'theme-assets/img/') || str_contains($relativePath, '..')) {
            return null;
        }

        $source = public_path($relativePath);

        if (! File::isFile($source)) {
            return null;
        }

        $mimeType = File::mimeType($source) ?: '';

        if (! str_starts_with($mimeType, 'image/')) {
            return null;
        }

        $dimensions = @getimagesize($source);

        // Reject broken or tiny assets rather than registering unusable media.
        if (! is_array($dimensions) || ($dimensions[0] ?? 0) < 240 || ($dimensions[1] ?? 0) < 180) {
            return null;
        }

        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        $target = 'buraaqh/'.sha1($relativePath).'.'.$extension;
        $disk = Storage::disk('public');

        if (! $disk->exists($target)) {
            $disk->put($target, File::get($source));
        }

        return Media::query()->firstOrCreate(
            ['disk' => 'public', 'path' => $target],
            [
                'uploaded_by' => $uploadedBy,
                'original_name' => basename($source),
                'mime_type' => $mimeType,
                'size' => File::size($source),
                'alt_text' => Str::limit($altText, 255, ''),
            ]
        );
    }
}
