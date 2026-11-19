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
        $r->validate(['files' => ['required', 'array', 'max:20'], 'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf,mp4', 'max:10240']]);
        foreach ($r->file('files') as $file) {
            $path = $file->store('media', 'public');
            Media::create(['uploaded_by' => $r->user()->id, 'disk' => 'public', 'path' => $path, 'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType(), 'size' => $file->getSize()]);
        }
        return back()->with('success', 'Files uploaded.');
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

            Storage::disk($media->disk)->delete($media->path);
            $media->delete();
            $deleted++;
        }

        $response = back()->with('success', $deleted . ' media file(s) deleted.');
        if ($skipped > 0) {
            $response->withErrors(['media' => $skipped . ' selected file(s) were retained because website content still references them.']);
        }

        return $response;
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
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
        return back()->with('success', 'Media deleted.');
    }

    private function isReferencedByContent(Media $media): bool
    {
        $references = [
            'properties' => ['featured_image', 'og_image'],
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
