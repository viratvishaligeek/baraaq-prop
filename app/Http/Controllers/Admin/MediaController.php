<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Support\MediaReferenceChecker;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class MediaController extends Controller
{
    public function index()
    {
        $filters = request()->validate([
            'q' => ['nullable', 'string', 'max:160'],
            'type' => ['nullable', 'in:images,documents'],
        ]);

        $media = Media::query()
            ->when(($filters['type'] ?? null) === 'images', fn ($query) => $query->where('mime_type', 'like', 'image/%'))
            ->when(($filters['type'] ?? null) === 'documents', fn ($query) => $query->whereNotNull('mime_type')->where('mime_type', 'not like', 'image/%'))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('original_name', 'like', '%'.$term.'%')->orWhere('path', 'like', '%'.$term.'%')->orWhere('alt_text', 'like', '%'.$term.'%')))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.media.index', compact('media', 'filters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['required', 'file', 'mimes:svg,jpg,jpeg,png,webp,gif,pdf,mp4', 'max:10240'],
            'variants' => ['sometimes', 'array'],
            'variants.*' => ['sometimes', 'array'],
            'variants.*.small' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:2048'],
            'variants.*.medium' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
            'variants.*.large' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:8192'],
        ]);

        app(MediaUploadService::class)->store($request, $data['files'], $data['variants'] ?? []);

        return back()->with('success', 'Files uploaded with available responsive image variants.');
    }
    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'media_ids' => ['required', 'array', 'min:1'],
            'media_ids.*' => ['required', 'integer', 'distinct', 'exists:media,id'],
        ]);

        $deletedMedia = [];
        $skipped = 0;

        foreach (Media::query()->whereIn('id', $data['media_ids'])->get() as $media) {
            if ($media->attachments()->exists() || app(MediaReferenceChecker::class)->isReferenced($media)) {
                $skipped++;
                continue;
            }

            $deletedMedia[] = $media;
        }

        DB::transaction(function () use ($deletedMedia): void {
            foreach ($deletedMedia as $media) {
                $media->delete();
            }
        });

        foreach ($deletedMedia as $media) {
            $this->deleteFiles($media);
        }

        $response = back()->with('success', count($deletedMedia).' media file(s) deleted.');
        if ($skipped > 0) {
            $response->withErrors(['media' => $skipped.' selected file(s) were retained because website content still references them.']);
        }

        return $response;
    }

    public function editedCopy(Request $request, Media $media)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:10240'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'variants' => ['sometimes', 'array'],
            'variants.small' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:2048'],
            'variants.medium' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
            'variants.large' => ['sometimes', 'file', 'image', 'mimes:svg,jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $uploadedPaths = [];

        try {
            $copy = DB::transaction(function () use ($data, $media, $request, &$uploadedPaths): Media {
                $file = $data['image'];
                $path = $file->store('media/edited', 'public');
                if (! $path) {
                    throw new RuntimeException('The edited image could not be stored.');
                }

                $uploadedPaths[] = $path;
                $attributes = [
                    'uploaded_by' => $request->user()->id,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => pathinfo($media->original_name, PATHINFO_FILENAME).'-edited.'.$file->getClientOriginalExtension(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'alt_text' => $data['alt_text'] ?? $media->alt_text,
                ];

                foreach (['small', 'medium', 'large'] as $size) {
                    if (empty($data['variants'][$size])) {
                        continue;
                    }

                    $variantPath = $data['variants'][$size]->store('media/variants/'.$size, 'public');
                    if (! $variantPath) {
                        throw new RuntimeException('An edited responsive image variant could not be stored.');
                    }

                    $uploadedPaths[] = $variantPath;
                    $attributes[$size.'_path'] = $variantPath;
                }

                return Media::create($attributes);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

        return response()->json([
            'saved' => true,
            'media' => ['id' => $copy->id, 'name' => $copy->original_name, 'url' => $copy->url],
        ], 201);
    }

    public function update(Request $request, Media $media)
    {
        $data = $request->validate(['alt_text' => ['nullable', 'string', 'max:255']]);
        $media->update($data);

        return back()->with('success', 'Media details updated.');
    }

    public function destroy(Media $media)
    {
        if ($media->attachments()->exists() || app(MediaReferenceChecker::class)->isReferenced($media)) {
            return back()->withErrors(['media' => 'This image is currently used by website content, a property gallery, an accommodation space, the homepage or site settings. Replace or clear those references before deleting it.']);
        }

        DB::transaction(fn () => $media->delete());
        $this->deleteFiles($media);

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

}
