<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        return view('admin.media.index', ['media' => Media::latest()->paginate(30)]);
    }
    public function store(Request $r)
    {
        $data = $r->validate([
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf,mp4', 'max:10240'],
            'variants' => ['sometimes', 'array'],
            'variants.*' => ['sometimes', 'array'],
            'variants.*.small' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants.*.medium' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'variants.*.large' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        foreach ($data['files'] as $index => $file) {
            $path = $file->store('media', 'public');
            $attributes = [
                'uploaded_by' => $r->user()->id,
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

            Media::create($attributes);
        }

        return back()->with('success', 'Files uploaded with available responsive image variants.');
    }
    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'media_ids' => ['required', 'array', 'min:1'],
            'media_ids.*' => ['required', 'integer', 'distinct', 'exists:media,id'],
        ]);

        $deleted = 0;
        $skipped = 0;

        foreach (Media::query()->whereIn('id', $data['media_ids'])->get() as $media) {
            if ($media->attachments()->exists() || $this->isReferencedByContent($media)) {
                $skipped++;
                continue;
            }

            $this->deleteFiles($media);
            $media->delete();
            $deleted++;
        }

        $response = back()->with('success', $deleted . ' media file(s) deleted.');
        if ($skipped > 0) {
            $response->withErrors(['media' => $skipped . ' selected file(s) were retained because website content still references them.']);
        }

        return $response;
    }

    public function editedCopy(Request $request, Media $media)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'variants' => ['sometimes', 'array'],
            'variants.small' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants.medium' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'variants.large' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $file = $data['image'];
        $path = $file->store('media/edited', 'public');
        $attributes = [
            'uploaded_by' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'original_name' => pathinfo($media->original_name, PATHINFO_FILENAME) . '-edited.' . $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $data['alt_text'] ?? $media->alt_text,
        ];
        foreach (['small', 'medium', 'large'] as $size) {
            if (! empty($data['variants'][$size])) {
                $attributes[$size . '_path'] = $data['variants'][$size]->store('media/variants/' . $size, 'public');
            }
        }
        $copy = Media::create($attributes);

        return response()->json([
            'saved' => true,
            'media' => ['id' => $copy->id, 'name' => $copy->original_name, 'url' => $copy->url],
        ], 201);
    }

    public function update(Request $r, Media $media)
    {
        $data = $r->validate(['alt_text' => ['nullable', 'string', 'max:255']]);
        $media->update($data);
        return back()->with('success', 'Media details updated.');
    }
    public function destroy(Media $media)
    {
        if ($media->attachments()->exists() || $this->isReferencedByContent($media)) {
            return back()->withErrors(['media' => 'This image is currently used by website content, a property gallery, a homepage or a reusable section. Replace or clear those references before deleting it.']);
        }
        $this->deleteFiles($media);
        $media->delete();
        return back()->with('success', 'Media deleted.');
    }

    private function deleteFiles(Media $media): void
    {
        foreach ([$media->path, $media->small_path, $media->medium_path, $media->large_path] as $path) {
            if ($path) {
                Storage::disk($media->disk)->delete($path);
            }
        }
    }

    private function isReferencedByContent(Media $media): bool
    {
        $references = [
            'properties' => ['featured_image', 'og_image'],
            'property_accommodations' => ['image_media_id'],
            'property_images' => ['path'],
            'posts' => ['featured_image', 'og_image'],
            'services' => ['featured_image', 'og_image'],
            'team_members' => ['photo'],
            'testimonials' => ['photo'],
            'banners' => ['image'],
            'categories' => ['icon_path'],
            'settings' => ['value'],
        ];

        foreach ($references as $table => $columns) {
            if (! Schema::hasTable($table)) continue;

            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)
                    && DB::table($table)->where($column, $media->path)->exists()) {
                    return true;
                }
            }
        }

        return false;
    }
}
