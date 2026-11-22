@extends('admin.layouts.app')

@section('title', 'Website settings')

@section('content')
    @php
        $faviconPath = ltrim((string) ($settings['favicon'] ?? ''), '/');
        $faviconUrl = $faviconPath === '' ? '' : (\\App\\Rules\\SafeLink::isHttpUrl($faviconPath)
            ? $faviconPath
            : (\\Illuminate\\Support\\Str::startsWith($faviconPath, 'theme-assets/')
                ? asset($faviconPath)
                : Storage::disk('public')->url($faviconPath)));
        $defaultOgPath = ltrim((string) ($settings['default_og_image'] ?? ''), '/');
        $defaultOgUrl = $defaultOgPath === '' ? '' : (\\App\\Rules\\SafeLink::isHttpUrl($defaultOgPath)
            ? $defaultOgPath
            : (\\Illuminate\\Support\\Str::startsWith($defaultOgPath, 'theme-assets/')
                ? asset($defaultOgPath)
                : Storage::disk('public')->url($defaultOgPath)));
    @endphp
    <div class="mb-4">
        <h1 class="page-title" id="general-settings">Website settings</h1>
        <p class="text-muted">Manage global SEO defaults, appearance, favicon and technical scripts. Website identity, contact details, navigation, footer and social content are managed under Admin → Homepage.</p>
    </div>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="content-card mb-4">
            <h4 class="mb-3">Favicon</h4>
            <div class="col-md-6">
                <label class="form-label">Site favicon</label>
                <input class="form-control" type="file" name="favicon" id="upload-favicon" accept=".png,.ico,image/png,image/x-icon">
                <input type="hidden" name="favicon_media_id" id="media-favicon" data-media-upload-input="upload-favicon">
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-open-media-picker data-target="media-favicon">Choose PNG from Media Library</button>
                <div class="mt-2">
                    <img src="{{ $faviconUrl }}"
                        alt="Current favicon" width="32" data-media-preview @if (empty($settings['favicon'])) hidden @endif>
                    <span class="small text-muted" data-media-meta></span>
                </div>
            </div>
        </div>

        <div class="content-card mb-4">
            <h4 class="mb-3">Header behavior</h4>
            <input type="hidden" name="sticky_header" value="0">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="sticky_header" value="1" id="sticky-header"
                    @checked(old('sticky_header', $settings['sticky_header'] ?? true))>
                <label class="form-check-label" for="sticky-header">Enable sticky header</label>
            </div>
        </div>

        <div class="content-card mb-4">
            <h4 class="mb-3" id="seo-settings">Global SEO defaults</h4>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Default meta title</label>
                    <input class="form-control" name="meta_title" maxlength="255"
                        value="{{ old('meta_title', $settings['meta_title'] ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Default meta description</label>
                    <textarea class="form-control" name="meta_description" rows="3" maxlength="1000">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Meta keywords</label>
                    <input class="form-control" name="meta_keywords" maxlength="1000"
                        value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Robots policy</label>
                    <select class="form-select" name="meta_robots">
                        @foreach (['index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow'] as $policy)
                            <option value="{{ $policy }}" @selected(old('meta_robots', $settings['meta_robots'] ?? 'index,follow') === $policy)>{{ $policy }}</option>
                        @endforeach
                    </select>
                    <div class="mt-3">
                        <label class="form-label" for="robots-rules">robots.txt directives</label>
                        <textarea class="form-control font-monospace" id="robots-rules" name="robots_rules" rows="6" maxlength="10000" placeholder="User-agent: *&#10;Allow: /&#10;Disallow: /admin">{{ old('robots_rules', $settings['robots_rules'] ?? "User-agent: *\nAllow: /\nDisallow: /admin") }}</textarea>
                        <div class="form-text">One directive per line. Supported directives: User-agent, Allow, Disallow and Crawl-delay. The sitemap URL is appended automatically.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Default social sharing image</label>
                    <input class="form-control" type="file" name="default_og_image" id="upload-default-og-image" accept="image/*">
                    <input type="hidden" name="default_og_image_media_id" id="media-default-og-image" data-media-upload-input="upload-default-og-image">
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-open-media-picker data-target="media-default-og-image">Choose from Media Library</button>
                    <div class="mt-2">
                        <img src="{{ $defaultOgUrl }}"
                            alt="Default social image preview" style="max-width:160px" data-media-preview @if (empty($settings['default_og_image'])) hidden @endif>
                        <span class="small text-muted" data-media-meta></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card mb-4">
            <h4 class="mb-3">Global appearance</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Primary color</label>
                    <input class="form-control form-control-color" type="color" name="primary_color"
                        value="{{ old('primary_color', $settings['primary_color'] ?? '#068FFF') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary color</label>
                    <input class="form-control form-control-color" type="color" name="secondary_color"
                        value="{{ old('secondary_color', $settings['secondary_color'] ?? '#044DBC') }}">
                </div>
            </div>
        </div>

        <div class="content-card mb-4">
            <h4 class="mb-2">Global scripts</h4>
            <p class="text-muted small">Only add scripts from providers you trust. These fields are rendered as administrator-supplied code.</p>
            <div class="mb-3">
                <label class="form-label">Head scripts</label>
                <textarea class="form-control font-monospace" name="head_scripts" rows="5" maxlength="20000">{{ old('head_scripts', $settings['head_scripts'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="form-label">Before closing body scripts</label>
                <textarea class="form-control font-monospace" name="body_scripts" rows="5" maxlength="20000">{{ old('body_scripts', $settings['body_scripts'] ?? '') }}</textarea>
            </div>
        </div>

        <button class="btn btn-primary btn-lg">Save website settings</button>
    </form>
@endsection
