<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
    private const HOMEPAGE_SLUG = 'home-9';

    public function index()
    {
        $homepage = Homepage::query()
            ->where('slug', self::HOMEPAGE_SLUG)
            ->firstOrFail();

        $homepage->load([
            'sections.mediaAttachments.media',
        ]);

        return view('admin.homepages.index', [
            'homepage' => $homepage,
            'sectionDefinitions' => config('cms-sections.sections', []),
        ]);
    }

    public function update(Request $request, Homepage $homepage)
    {
        $this->ensureFixedHomepage($homepage);

        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $homepage->update($data);
        $this->clearHomepageCache();

        return redirect()->route('admin.homepages.index')
            ->with('success', 'Homepage SEO details saved successfully.');
    }

    public function updateSection(Request $request, Homepage $homepage, HomepageSection $section)
    {
        $this->ensureFixedHomepage($homepage);
        abort_unless((int) $section->homepage_id === (int) $homepage->id, 404);

        $definition = config('cms-sections.sections.' . $section->section_key);
        abort_unless(is_array($definition), 404, 'Homepage section definition was not found.');

        $fields = $definition['fields'] ?? [];
        $rules = [];

        foreach ($fields as $key => $field) {
            $type = $field['type'] ?? 'text';
            $max = (int) ($field['max'] ?? 500);

            $rules['config.' . $key] = match ($type) {
                'checkbox' => ['sometimes', 'boolean'],
                'number' => ['nullable', 'numeric', 'min:' . ($field['min'] ?? 0), 'max:' . ($field['max'] ?? 9999999)],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
                'url' => ['nullable', 'string', 'max:' . max(2048, $max)],
                'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'select' => ['nullable', 'string', 'in:' . implode(',', array_keys($field['options'] ?? []))],
                default => ['nullable', 'string', 'max:' . $max],
            };
        }

        $rules['is_enabled'] = ['sometimes', 'boolean'];
        $validated = $request->validate($rules);
        $submitted = $validated['config'] ?? [];
        $config = $section->config ?? [];

        foreach ($fields as $key => $field) {
            $type = $field['type'] ?? 'text';

            if ($type === 'checkbox') {
                if (array_key_exists($key, $submitted)) {
                    $config[$key] = (bool) $submitted[$key];
                }
                continue;
            }

            if ($type === 'image') {
                $file = $request->file('config.' . $key);
                if (! $file) {
                    continue;
                }

                $path = $file->store('homepage', 'public');
                $media = Media::create([
                    'uploaded_by' => $request->user()->id,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'alt_text' => (string) ($config[$key . '_alt'] ?? ''),
                ]);

                $section->mediaAttachments()->updateOrCreate(
                    ['field_key' => $key],
                    ['media_id' => $media->id]
                );
                $config[$key] = $media->id;
                continue;
            }

            if (array_key_exists($key, $submitted)) {
                $config[$key] = $submitted[$key];
            }
        }

        $section->update(['config' => $config, 'is_enabled' => array_key_exists('is_enabled', $validated) ? (bool) $validated['is_enabled'] : $section->is_enabled]);
        $this->clearHomepageCache();

        return redirect()->route('admin.homepages.index')
            ->with('success', ($definition['label'] ?? 'Homepage section') . ' updated successfully.');
    }

    private function ensureFixedHomepage(Homepage $homepage): void
    {
        abort_unless($homepage->slug === self::HOMEPAGE_SLUG, 404);
    }

    private function clearHomepageCache(): void
    {
        Cache::forget('homepage:published-default');
        Cache::forget('homepage:published-default:id');
    }
}
