<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaPickerController extends Controller
{
    private function authorizePicker(Request $request): void
    {
        $user = $request->user();
        $permissions = [
            'media.manage', 'pages.manage', 'settings.manage', 'properties.manage',
            'posts.manage', 'services.manage', 'team.manage', 'testimonials.manage',
            'categories.manage', 'banners.manage', 'widgets.manage',
        ];

        abort_unless($user && ($user->isAdministrator() || collect($permissions)->contains(
            fn (string $permission) => $user->hasPermission($permission)
        )), 403);
    }

    public function upload(Request $request)
    {
        $this->authorizePicker($request);
        $data = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            'variants' => ['sometimes', 'array'],
            'variants.*' => ['sometimes', 'array'],
            'variants.*.small' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants.*.medium' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'variants.*.large' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $uploaded = collect($data['files'])->map(function ($file, $index) use ($request, $data) {
            $path = $file->store('media', 'public');
            $attributes = [
                'uploaded_by' => $request->user()->id,
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
            foreach (['small', 'medium', 'large'] as $size) {
                $variant = $data['variants'][$index][$size] ?? null;
                if ($variant) {
                    $attributes[$size . '_path'] = $variant->store('media/variants/' . $size, 'public');
                }
            }
            $media = Media::create($attributes);

            return [
                'id' => $media->id,
                'name' => $media->original_name,
                'url' => $media->url,
                'thumbnail_url' => $media->thumbnail_url,
                'medium_url' => $media->medium_url,
                'large_url' => $media->large_url,
                'alt' => $media->alt_text,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'disk' => $media->disk,
            ];
        });

        return response()->json(['uploaded' => true, 'data' => $uploaded], 201);
    }

    public function __invoke(Request $request)
    {
        $this->authorizePicker($request);
        $search = trim((string) $request->query('search', ''));

        $media = Media::query()
            ->where('mime_type', 'like', 'image/%')
            ->when($search !== '', fn($query) => $query->where('original_name', 'like', '%' . $search . '%'))
            ->latest()
            ->paginate(24);

        return response()->json([
            'data' => $media->getCollection()->map(fn(Media $item) => [
                'id' => $item->id,
                'name' => $item->original_name,
                'url' => Storage::disk($item->disk)->url($item->path),
                'thumbnail_url' => $item->thumbnail_url,
                'medium_url' => $item->medium_url,
                'large_url' => $item->large_url,
                'alt' => $item->alt_text,
                'mime_type' => $item->mime_type,
                'size' => $item->size,
                'disk' => $item->disk,
            ]),
            'next_page_url' => $media->nextPageUrl(),
        ]);
    }
}
