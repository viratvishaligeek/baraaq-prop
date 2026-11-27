@extends('admin.layouts.app')

@section('title', 'Website settings')

@section('content')
    @php
        $logoPath = ltrim((string) ($settings['logo'] ?? ''), '/');
        $logoUrl = $logoPath === '' ? '' : (\\App\\Rules\\SafeLink::isHttpUrl($logoPath)
            ? $logoPath
            : (\\Illuminate\\Support\\Str::startsWith($logoPath, 'theme-assets/')
                ? asset($logoPath)
                : Storage::disk('public')->url($logoPath)));
        $faviconPath = ltrim((string) ($settings['favicon'] ?? ''), '/');
        $faviconUrl = $faviconPath === '' ? '' : (\App\Rules\SafeLink::isHttpUrl($faviconPath)
            ? $faviconPath
            : (\Illuminate\Support\Str::startsWith($faviconPath, 'theme-assets/')
                ? asset($faviconPath)
                : Storage::disk('public')->url($faviconPath)));
        $defaultOgPath = ltrim((string) ($settings['default_og_image'] ?? ''), '/');
        $defaultOgUrl = $defaultOgPath === '' ? '' : (\App\Rules\SafeLink::isHttpUrl($defaultOgPath)
            ? $defaultOgPath
            : (\Illuminate\Support\Str::startsWith($defaultOgPath, 'theme-assets/')
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
                <input type="hidden" name="favicon_media_id" id="media-favicon" data-remove-flag-id="remove-favicon">
                <input type="hidden" name="remove_favicon" id="remove-favicon" value="0">
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-open-media-picker data-target="media-favicon">Choose PNG from Media Library</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-clear-media data-target="media-favicon">Remove selected favicon</button>
                </div>
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
                <div class="col-12"><h5 class="mt-2 mb-0">Local content SEO</h5><p class="text-muted small mb-0">Search snippets for Gallery, FAQs and Neighborhood Guides.</p></div>
                <div class="col-md-6"><label class="form-label">Gallery meta title</label><input class="form-control" name="gallery_meta_title" maxlength="255" value="{{ old('gallery_meta_title', $settings['gallery_meta_title'] ?? '') }}"></div>
                <div class="col-md-6"><label class="form-label">FAQ meta title</label><input class="form-control" name="faq_meta_title" maxlength="255" value="{{ old('faq_meta_title', $settings['faq_meta_title'] ?? '') }}"></div>
                <div class="col-md-6"><label class="form-label">Gallery meta description</label><textarea class="form-control" name="gallery_meta_description" rows="2" maxlength="1000">{{ old('gallery_meta_description', $settings['gallery_meta_description'] ?? '') }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Gallery page heading</label><input class="form-control" name="gallery_page_heading" maxlength="180" value="{{ old('gallery_page_heading', $settings['gallery_page_heading'] ?? 'Property Gallery') }}"></div>
                <div class="col-md-6"><label class="form-label">Gallery page introduction</label><textarea class="form-control" name="gallery_page_intro" rows="2" maxlength="500">{{ old('gallery_page_intro', $settings['gallery_page_intro'] ?? '') }}</textarea></div>
                <div class="col-md-6"><label class="form-label">FAQ meta description</label><textarea class="form-control" name="faq_meta_description" rows="2" maxlength="1000">{{ old('faq_meta_description', $settings['faq_meta_description'] ?? '') }}</textarea></div>
                <div class="col-md-6"><label class="form-label">FAQ page heading</label><input class="form-control" name="faq_page_heading" maxlength="180" value="{{ old('faq_page_heading', $settings['faq_page_heading'] ?? 'Frequently Asked Questions') }}"></div>
                <div class="col-md-6"><label class="form-label">FAQ page introduction</label><textarea class="form-control" name="faq_page_intro" rows="2" maxlength="500">{{ old('faq_page_intro', $settings['faq_page_intro'] ?? '') }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Neighborhood Guides meta title</label><input class="form-control" name="neighborhood_guides_meta_title" maxlength="255" value="{{ old('neighborhood_guides_meta_title', $settings['neighborhood_guides_meta_title'] ?? '') }}"></div>
                <div class="col-md-6"><label class="form-label">Neighborhood Guides meta description</label><textarea class="form-control" name="neighborhood_guides_meta_description" rows="2" maxlength="1000">{{ old('neighborhood_guides_meta_description', $settings['neighborhood_guides_meta_description'] ?? '') }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Neighborhood Guides page heading</label><input class="form-control" name="neighborhood_guides_page_heading" maxlength="180" value="{{ old('neighborhood_guides_page_heading', $settings['neighborhood_guides_page_heading'] ?? 'Neighborhood Guides') }}"></div>
                <div class="col-md-6"><label class="form-label">Neighborhood Guides page introduction</label><textarea class="form-control" name="neighborhood_guides_page_intro" rows="2" maxlength="500">{{ old('neighborhood_guides_page_intro', $settings['neighborhood_guides_page_intro'] ?? '') }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Service area city</label><input class="form-control" name="service_area_city" maxlength="100" value="{{ old('service_area_city', $settings['service_area_city'] ?? 'Bareilly') }}"></div>
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
                    <input type="hidden" name="default_og_image_media_id" id="media-default-og-image" data-remove-flag-id="remove-default-og-image">
                    <input type="hidden" name="remove_default_og_image" id="remove-default-og-image" value="0">
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-open-media-picker data-target="media-default-og-image">Choose from Media Library</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-clear-media data-target="media-default-og-image">Remove selected image</button>
                    </div>
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
