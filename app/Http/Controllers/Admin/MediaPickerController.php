<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaPickerController extends Controller
{
    private function authorizePicker(Request $request, bool $upload = false): void
    {
        $user = $request->user();
        $modules = [
            'properties', 'pages', 'posts', 'services', 'team',
            'testimonials', 'categories', 'banners', 'gallery', 'faqs', 'neighborhood-guides',
        ];
        $actions = $upload ? ['manage', 'create', 'edit'] : ['manage', 'view', 'create', 'edit'];
        $permissions = ['media.manage', 'settings.manage'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[] = $module.'.'.$action;
            }
        }

        abort_unless($user && ($user->isAdministrator() || collect($permissions)->contains(
            fn (string $permission) => $user->hasPermission($permission)
        )), 403);
    }

    public function upload(Request $request)
    {
        $this->authorizePicker($request, true);
        $data = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['required', 'image', 'mimes:svg,jpg,jpeg,png,webp,gif', 'max:10240'],
            'variants' => ['sometimes', 'array'],
            'variants.*' => ['sometimes', 'array'],
            'variants.*.small' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:2048'],
            'variants.*.medium' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
            'variants.*.large' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $uploaded = app(MediaUploadService::class)
            ->store($request, $data['files'], $data['variants'] ?? [])
            ->map(fn (Media $media) => [
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
            ]);

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
