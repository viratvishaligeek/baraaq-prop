<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'site_title' => ['required', 'string', 'max:150'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_email' => ['nullable', 'email', 'max:190'],
            'site_phone' => ['nullable', 'string', 'max:30'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'site_address' => ['nullable', 'string', 'max:500'],
            'business_name' => ['nullable', 'string', 'max:190'],
            'business_registration' => ['nullable', 'string', 'max:190'],
            'footer_description' => ['nullable', 'string', 'max:1500'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'header_menu_location' => ['required', 'string', 'max:60'],
            'header_cta_label' => ['nullable', 'string', 'max:80'],
            'header_cta_url' => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\\/\\/|\\/|mailto:|tel:|#)/i'],
            'footer_menu_location' => ['required', 'string', 'max:60'],
            'sticky_header' => ['sometimes', 'boolean'],
            'social_facebook' => ['nullable', 'url', 'max:500'],
            'social_instagram' => ['nullable', 'url', 'max:500'],
            'social_linkedin' => ['nullable', 'url', 'max:500'],
            'social_youtube' => ['nullable', 'url', 'max:500'],
            'social_x' => ['nullable', 'url', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'meta_robots' => ['required', 'in:index,follow,index,nofollow,noindex,follow,noindex,nofollow'],
            'default_og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:2048'],
            'default_og_image_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%'))],
            'logo_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where('mime_type', 'like', 'image/%')],
            'favicon_media_id' => ['nullable', 'integer', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'image/png'))],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'head_scripts' => ['nullable', 'string', 'max:20000'],
            'body_scripts' => ['nullable', 'string', 'max:20000'],
        ]);

        $selectedMediaPaths = [];
        foreach ([
            'logo_media_id' => 'logo',
            'favicon_media_id' => 'favicon',
            'default_og_image_media_id' => 'default_og_image',
        ] as $mediaField => $field) {
            if (! empty($data[$mediaField])) {
                $selectedMediaPaths[$field] = Media::query()->whereKey((int) $data[$mediaField])->value('path');
            }
            unset($data[$mediaField]);
        }

        $uploads = [];
        foreach (['logo', 'favicon', 'default_og_image'] as $field) {
            if ($request->hasFile($field)) {
                $uploads[$field] = $request->file($field)->store('site', 'public');
            }
        }

        $oldFiles = [];
        foreach ($uploads as $key => $path) {
            $old = Setting::read($key);
            if ($old && ! Media::where('path', $old)->exists()) {
                $oldFiles[] = $old;
            }
        }

        unset($data['logo'], $data['favicon'], $data['default_og_image']);
        $data = array_merge($data, $selectedMediaPaths, $uploads);
        $data['sticky_header'] = $request->boolean('sticky_header');

        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                Setting::write($key, $value, in_array($key, ['meta_title', 'meta_description', 'meta_keywords', 'meta_robots', 'default_og_image']) ? 'seo' : 'general');
            }
        });

        foreach ($oldFiles as $path) {
            Storage::disk('public')->delete($path);
        }

        return back()->with('success', 'Website settings saved.');
    }
}
