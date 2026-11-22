<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\Setting;
use App\Rules\SafeLink;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class HomepageController extends Controller
{
    private const HOMEPAGE_SLUG = 'home-9';

    public function index()
    {
        $homepage = Homepage::query()
            ->where('slug', self::HOMEPAGE_SLUG)
            ->firstOrFail();

        $homepage->load('sections.mediaAttachments.media');

        return view('admin.homepages.index', [
            'homepage' => $homepage,
            'sectionDefinitions' => config('cms-sections.sections', []),
            'siteSettings' => Setting::allValues(),
        ]);
    }

    public function update(Request $request, Homepage $homepage)
    {
        $this->ensureFixedHomepage($homepage);

        $data = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'site_title' => ['nullable', 'string', 'max:150'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:190'],
            'site_email' => ['nullable', 'email', 'max:190'],
            'site_phone' => ['nullable', 'string', 'max:30'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'site_address' => ['nullable', 'string', 'max:500'],
            'header_menu_location' => ['nullable', 'string', 'max:60'],
            'header_cta_label' => ['nullable', 'string', 'max:80'],
            'header_cta_url' => ['nullable', 'string', 'max:2048', new SafeLink()],
            'footer_description' => ['nullable', 'string', 'max:1500'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'footer_rights_text' => ['nullable', 'string', 'max:255'],
            'footer_map_embed_url' => ['nullable', 'url', 'max:2048'],
            'footer_gallery_handle' => ['nullable', 'string', 'max:100'],
            'footer_gallery_title' => ['nullable', 'string', 'max:100'],
            'footer_about_heading' => ['nullable', 'string', 'max:100'],
            'footer_services_heading' => ['nullable', 'string', 'max:100'],
            'footer_quick_links_heading' => ['nullable', 'string', 'max:100'],
            'footer_support_heading' => ['nullable', 'string', 'max:100'],
            'footer_location_heading' => ['nullable', 'string', 'max:100'],
            'footer_social_heading' => ['nullable', 'string', 'max:100'],
            'footer_quick_links_menu_location' => ['nullable', 'string', 'max:60'],
            'footer_support_menu_location' => ['nullable', 'string', 'max:60'],
            'footer_menu_location' => ['nullable', 'string', 'max:60'],
            'social_facebook' => ['nullable', 'url', 'max:500'],
            'social_instagram' => ['nullable', 'url', 'max:500'],
            'social_linkedin' => ['nullable', 'url', 'max:500'],
            'social_youtube' => ['nullable', 'url', 'max:500'],
            'social_x' => ['nullable', 'url', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'footer_gallery_images' => ['nullable', 'array', 'max:7'],
            'footer_gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $settingGroups = [
            'site_title' => 'general',
            'site_tagline' => 'general',
            'business_name' => 'business',
            'site_email' => 'contact',
            'site_phone' => 'contact',
            'whatsapp_number' => 'contact',
            'site_address' => 'contact',
            'header_menu_location' => 'navigation',
            'header_cta_label' => 'navigation',
            'header_cta_url' => 'navigation',
            'footer_menu_location' => 'navigation',
            'footer_quick_links_menu_location' => 'navigation',
            'footer_support_menu_location' => 'navigation',
            'footer_description' => 'footer',
            'footer_copyright' => 'footer',
            'footer_rights_text' => 'footer',
            'footer_map_embed_url' => 'footer',
            'footer_gallery_handle' => 'footer',
            'footer_gallery_title' => 'footer',
            'footer_about_heading' => 'footer',
            'footer_services_heading' => 'footer',
            'footer_quick_links_heading' => 'footer',
            'footer_support_heading' => 'footer',
            'footer_location_heading' => 'footer',
            'footer_social_heading' => 'footer',
            'social_facebook' => 'social',
            'social_instagram' => 'social',
            'social_linkedin' => 'social',
            'social_youtube' => 'social',
            'social_x' => 'social',
        ];

        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($homepage, $data, $settingGroups, $request, &$uploadedPaths): void {
                $homepage->update([
                    'meta_title' => array_key_exists('meta_title', $data) ? $data['meta_title'] : $homepage->meta_title,
                    'meta_description' => array_key_exists('meta_description', $data) ? $data['meta_description'] : $homepage->meta_description,
                ]);

                foreach ($settingGroups as $key => $group) {
                    if (array_key_exists($key, $data)) {
                        Setting::write($key, $data[$key], $group, 'string');
                    }
                }

                if ($request->hasFile('logo')) {
                    $file = $request->file('logo');
                    $path = $file->store('homepage/branding', 'public');
                    if (! $path) {
                        throw new RuntimeException('The uploaded logo could not be stored.');
                    }

                    $uploadedPaths[] = $path;
                    Media::create([
                        'uploaded_by' => $request->user()->id,
                        'disk' => 'public',
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'alt_text' => (string) ($data['site_title'] ?? $homepage->name),
                    ]);
                    Setting::write('logo', $path, 'branding', 'string');
                }

                if ($request->hasFile('footer_gallery_images')) {
                    $mediaIds = [];

                    foreach ($request->file('footer_gallery_images') as $file) {
                        $path = $file->store('homepage/footer-gallery', 'public');
                        if (! $path) {
                            throw new RuntimeException('A footer gallery image could not be stored.');
                        }

                        $uploadedPaths[] = $path;
                        $media = Media::create([
                            'uploaded_by' => $request->user()->id,
                            'disk' => 'public',
                            'path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'alt_text' => 'Homepage footer gallery',
                        ]);
                        $mediaIds[] = $media->id;
                    }

                    Setting::write('footer_gallery_images', $mediaIds, 'footer', 'json');
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

        $this->clearHomepageCache();

        return redirect()->route('admin.homepages.index')
            ->with('success', 'Homepage content and site-wide homepage details saved successfully.');
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
                'url' => ['nullable', 'string', 'max:' . max(2048, $max), new SafeLink()],
                'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                'select' => ['nullable', 'string', 'in:' . implode(',', array_keys($field['options'] ?? []))],
                default => ['nullable', 'string', 'max:' . $max],
            };
        }

        $rules['is_enabled'] = ['sometimes', 'boolean'];
        $validated = $request->validate($rules);
        $submitted = $validated['config'] ?? [];
        $config = array_merge($definition['defaults'] ?? [], $section->config ?? []);
        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($fields, $submitted, $request, $section, $validated, &$config, &$uploadedPaths): void {
                foreach ($fields as $key => $field) {
                    $type = $field['type'] ?? 'text';

                    if ($type === 'checkbox') {
                        if (array_key_exists($key, $submitted)) {
                            $config[$key] = (bool) $submitted[$key];
                        }
                        continue;
                    }

                    if ($type === 'richtext') {
                        if (array_key_exists($key, $submitted)) {
                            $config[$key] = HtmlSanitizer::sanitize($submitted[$key]);
                        }
                        continue;
                    }

                    if ($type === 'image') {
                        $file = $request->file('config.' . $key);
                        if (! $file) {
                            continue;
                        }

                        $path = $file->store('homepage', 'public');
                        if (! $path) {
                            throw new RuntimeException('The uploaded homepage image could not be stored.');
                        }

                        $uploadedPaths[] = $path;
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

                $section->update([
                    'config' => $config,
                    'is_enabled' => array_key_exists('is_enabled', $validated)
                        ? (bool) $validated['is_enabled']
                        : $section->is_enabled,
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

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
