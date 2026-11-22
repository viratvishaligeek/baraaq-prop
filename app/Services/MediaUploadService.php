<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MediaUploadService
{
    public function store(Request $request, array $files, array $variants = []): EloquentCollection
    {
        $uploadedPaths = [];

        try {
            return DB::transaction(function () use ($request, $files, $variants, &$uploadedPaths): EloquentCollection {
                $mediaItems = new EloquentCollection();

                foreach ($files as $index => $file) {
                    $path = $file->store('media', 'public');
                    if (! $path) {
                        throw new RuntimeException('A media file could not be stored.');
                    }

                    $uploadedPaths[] = $path;
                    $attributes = [
                        'uploaded_by' => $request->user()->id,
                        'disk' => 'public',
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ];

                    foreach (['small', 'medium', 'large'] as $size) {
                        $variant = $variants[$index][$size] ?? null;
                        if (! $variant) {
                            continue;
                        }

                        $variantPath = $variant->store('media/variants/'.$size, 'public');
                        if (! $variantPath) {
                            throw new RuntimeException('A responsive image variant could not be stored.');
                        }

                        $uploadedPaths[] = $variantPath;
                        $attributes[$size.'_path'] = $variantPath;
                    }

                    $mediaItems->push(Media::create($attributes));
                }

                return $mediaItems;
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }
    }
}
