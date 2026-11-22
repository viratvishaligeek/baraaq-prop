<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Setting;
use App\Support\MediaReferenceChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'settings' => Setting::allValues(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sticky_header' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'meta_robots' => ['required', Rule::in(['index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow'])],
            'robots_rules' => ['nullable', 'string', 'max:10000'],
            'default_og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:2048'],
            'default_og_image_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%'))],
            'favicon_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'image/png'))],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'head_scripts' => ['nullable', 'string', 'max:20000'],
            'body_scripts' => ['nullable', 'string', 'max:20000'],
        ]);

        $selectedMediaPaths = [];
        foreach ([
            'favicon_media_id' => 'favicon',
            'default_og_image_media_id' => 'default_og_image',
        ] as $mediaField => $field) {
            if (! empty($data[$mediaField])) {
                $selectedMediaPaths[$field] = Media::query()
                    ->whereKey((int) $data[$mediaField])
                    ->value('path');
            }
            unset($data[$mediaField]);
        }

        $uploads = [];
        $uploadedPaths = [];
        foreach (['favicon', 'default_og_image'] as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $path = $file->store('site', 'public');
            if (! $path) {
                Storage::disk('public')->delete($uploadedPaths);
                throw new RuntimeException('The uploaded '.$field.' file could not be stored.');
            }

            $uploads[$field] = $path;
            $uploadedPaths[] = $path;
        }

        $replacements = array_merge($selectedMediaPaths, $uploads);
        $oldFiles = [];
        foreach ($replacements as $key => $newPath) {
            $oldPath = Setting::read($key);
            if ($oldPath && $oldPath !== $newPath
                && ! Media::where('path', $oldPath)->exists()
                && ! app(MediaReferenceChecker::class)->isPathReferenced($oldPath)) {
                $oldFiles[] = $oldPath;
            }
        }

        unset($data['favicon'], $data['default_og_image']);
        $data = array_merge($data, $selectedMediaPaths, $uploads);
        $data['sticky_header'] = $request->boolean('sticky_header');

        try {
            DB::transaction(function () use ($data, $uploads, $request): void {
                foreach ($uploads as $field => $path) {
                    $file = $request->file($field);
                    Media::create([
                        'uploaded_by' => $request->user()->id,
                        'disk' => 'public',
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'alt_text' => $field === 'favicon' ? 'Site favicon' : 'Default social sharing image',
                    ]);
                }

                foreach ($data as $key => $value) {
                    $group = in_array($key, ['meta_title', 'meta_description', 'meta_keywords', 'meta_robots', 'robots_rules', 'default_og_image'], true)
                        ? 'seo'
                        : 'general';
                    Setting::write($key, $value, $group);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

        Storage::disk('public')->delete($oldFiles);

        return back()->with('success', 'Website settings saved.');
    }
}
