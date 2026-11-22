@extends('admin.layouts.app')

@section('title', 'Homepage content')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Homepage content</h1>
            <p class="text-muted mb-0">Edit the content and images shown on the fixed Homepage 9 design.</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View live homepage
        </a>
    </div>

    <div class="content-card mb-4">
        <h2 class="section-title mb-3">Homepage SEO</h2>
        <form method="POST" action="{{ route('admin.homepages.update', $homepage) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label class="form-label" for="meta_title">SEO title</label>
                <input class="form-control" id="meta_title" name="meta_title" maxlength="255"
                    value="{{ old('meta_title', $homepage->meta_title) }}">
            </div>
            <div class="mb-3">
                <label class="form-label" for="meta_description">SEO description</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                    maxlength="1000">{{ old('meta_description', $homepage->meta_description) }}</textarea>
            </div>
            <button class="btn btn-primary" type="submit">Save SEO details</button>
        </form>
    </div>

    <div class="content-card mb-4">
        <h2 class="section-title mb-1">Header and contact details</h2>
        <p class="text-muted small mb-3">These details appear in the Home 9 header, footer and business information.</p>
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.homepages.update', $homepage) }}">
            @csrf
            @method('PATCH')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Website name</label>
                    <input class="form-control" name="site_title" maxlength="150" value="{{ old('site_title', $siteSettings['site_title'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Business name</label>
                    <input class="form-control" name="business_name" maxlength="190" value="{{ old('business_name', $siteSettings['business_name'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tagline</label>
                    <input class="form-control" name="site_tagline" maxlength="255" value="{{ old('site_tagline', $siteSettings['site_tagline'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Header button label</label>
                    <input class="form-control" name="header_cta_label" maxlength="80" value="{{ old('header_cta_label', $siteSettings['header_cta_label'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Header button URL</label>
                    <input class="form-control" name="header_cta_url" maxlength="2048" value="{{ old('header_cta_url', $siteSettings['header_cta_url'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact email</label>
                    <input class="form-control" type="email" name="site_email" maxlength="190" value="{{ old('site_email', $siteSettings['site_email'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact phone</label>
                    <input class="form-control" name="site_phone" maxlength="30" value="{{ old('site_phone', $siteSettings['site_phone'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp number</label>
                    <input class="form-control" name="whatsapp_number" maxlength="30" value="{{ old('whatsapp_number', $siteSettings['whatsapp_number'] ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Business address</label>
                    <textarea class="form-control" name="site_address" rows="2" maxlength="500">{{ old('site_address', $siteSettings['site_address'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Header / footer logo</label>
                    @if (!empty($siteSettings['logo']))
                        <div class="mb-2"><img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="" style="max-width:180px;max-height:70px;object-fit:contain"></div>
                    @endif
                    <input class="form-control" type="file" name="logo" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">Upload a replacement logo (up to 5 MB).</div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3"><button class="btn btn-primary" type="submit">Save header and contact details</button></div>
        </form>
    </div>

    <div class="content-card mb-4">
        <h2 class="section-title mb-1">Footer content, menus and social links</h2>
        <p class="text-muted small mb-3">Manage footer headings, descriptions, contact map, gallery and social destinations. Edit the actual menu items under Admin → Menus.</p>
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.homepages.update', $homepage) }}">
            @csrf
            @method('PATCH')
            <div class="row g-3">
                @foreach ([
                    'footer_about_heading' => 'About column heading',
                    'footer_services_heading' => 'Services column heading',
                    'footer_quick_links_heading' => 'Quick links heading',
                    'footer_support_heading' => 'Support heading',
                    'footer_location_heading' => 'Location heading',
                    'footer_social_heading' => 'Social links heading',
                    'footer_quick_links_menu_location' => 'Quick links menu location',
                    'footer_support_menu_location' => 'Support menu location',
                    'footer_menu_location' => 'Footer navigation menu location',
                    'footer_gallery_handle' => 'Gallery handle',
                    'footer_gallery_title' => 'Gallery title',
                ] as $key => $label)
                    <div class="col-md-6">
                        <label class="form-label">{{ $label }}</label>
                        <input class="form-control" name="{{ $key }}" value="{{ old($key, $siteSettings[$key] ?? '') }}" maxlength="{{ str_contains($key, 'menu_location') ? 60 : 100 }}">
                    </div>
                @endforeach
                <div class="col-12">
                    <label class="form-label">Footer description</label>
                    <textarea class="form-control" name="footer_description" rows="3" maxlength="1500">{{ old('footer_description', $siteSettings['footer_description'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Footer copyright text</label>
                    <input class="form-control" name="footer_copyright" maxlength="255" value="{{ old('footer_copyright', $siteSettings['footer_copyright'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Footer map embed URL</label>
                    <input class="form-control" name="footer_map_embed_url" value="{{ old('footer_map_embed_url', $siteSettings['footer_map_embed_url'] ?? '') }}" maxlength="2048">
                </div>
                <div class="col-12">
                    <label class="form-label">Footer gallery images</label>
                    @if (!empty($siteSettings['footer_gallery_images']))
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach ($siteSettings['footer_gallery_images'] as $galleryImage)
                                @php
                                    $galleryMedia = is_numeric($galleryImage) ? \App\Models\Media::query()->find((int) $galleryImage) : null;
                                    $galleryUrl = $galleryMedia?->url ?? (\Illuminate\Support\Str::startsWith((string) $galleryImage, ['http://', 'https://']) ? $galleryImage : asset(ltrim((string) $galleryImage, '/')));
                                @endphp
                                <img src="{{ $galleryUrl }}" alt="" style="width:76px;height:58px;object-fit:cover" class="rounded border">
                            @endforeach
                        </div>
                    @endif
                    <input class="form-control" type="file" name="footer_gallery_images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                    <div class="form-text">Uploading images replaces the current footer gallery (up to 7 images, 5 MB each).</div>
                </div>
                @foreach ([
                    'social_facebook' => 'Facebook URL',
                    'social_instagram' => 'Instagram URL',
                    'social_linkedin' => 'LinkedIn URL',
                    'social_youtube' => 'YouTube URL',
                    'social_x' => 'X URL',
                ] as $key => $label)
                    <div class="col-md-6">
                        <label class="form-label">{{ $label }}</label>
                        <input class="form-control" type="url" name="{{ $key }}" value="{{ old($key, $siteSettings[$key] ?? '') }}" maxlength="500">
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-end mt-3"><button class="btn btn-primary" type="submit">Save footer and social content</button></div>
        </form>
    </div>

    @forelse ($homepage->sections as $section)
        @php
            $definition = $sectionDefinitions[$section->section_key] ?? null;
            $fields = $definition['fields'] ?? [];
            $config = $section->config ?? [];
        @endphp
        @continue(!$definition || empty($fields))

        <div class="content-card mb-4" id="section-{{ $section->id }}">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="section-title mb-1">{{ $definition['label'] ?? str($section->section_key)->replace('.', ' ')->title() }}</h2>
                    <p class="text-muted small mb-0">{{ $section->is_enabled ? 'Visible on homepage' : 'Currently hidden' }}</p>
                </div>
                <span class="badge {{ $section->is_enabled ? 'bg-success' : 'bg-secondary' }}">{{ $section->is_enabled ? 'Enabled' : 'Disabled' }}</span>
            </div>

            <form method="POST" enctype="multipart/form-data"
                action="{{ route('admin.homepages.sections.update', [$homepage, $section]) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="is_enabled" value="0">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="section-{{ $section->id }}-enabled" name="is_enabled" value="1" @checked($section->is_enabled)>
                    <label class="form-check-label" for="section-{{ $section->id }}-enabled">Show this section on the homepage</label>
                </div>
                <div class="row g-3">
                    @foreach ($fields as $key => $field)
                        @php
                            $type = $field['type'] ?? 'text';
                            $value = old('config.' . $key, $config[$key] ?? '');
                            $attachment = $section->mediaAttachments->firstWhere('field_key', $key);
                            $currentImage = $attachment?->media?->url;
                            if (!$currentImage && is_numeric($value)) {
                                $currentImage = \App\Models\Media::query()->find((int) $value)?->url;
                            }
                        @endphp
                        <div class="{{ in_array($type, ['textarea', 'richtext', 'image'], true) ? 'col-12' : 'col-md-6' }}">
                            @if ($type === 'checkbox')
                                <div class="form-check mt-2">
                                    <input type="hidden" name="config[{{ $key }}]" value="0">
                                    <input class="form-check-input" type="checkbox" id="section-{{ $section->id }}-{{ $key }}"
                                        name="config[{{ $key }}]" value="1" @checked((bool) $value)>
                                    <label class="form-check-label" for="section-{{ $section->id }}-{{ $key }}">{{ $field['label'] ?? str($key)->replace('_', ' ')->title() }}</label>
                                </div>
                            @else
                                <label class="form-label" for="section-{{ $section->id }}-{{ $key }}">{{ $field['label'] ?? str($key)->replace('_', ' ')->title() }}</label>
                                @if ($type === 'textarea' || $type === 'richtext')
                                    <textarea class="form-control" id="section-{{ $section->id }}-{{ $key }}"
                                        name="config[{{ $key }}]" rows="3" maxlength="{{ $field['max'] ?? 2000 }}">{{ $value }}</textarea>
                                @elseif ($type === 'select')
                                    <select class="form-select" id="section-{{ $section->id }}-{{ $key }}" name="config[{{ $key }}]">
                                        @foreach (($field['options'] ?? []) as $option => $label)
                                            <option value="{{ $option }}" @selected((string) $value === (string) $option)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($type === 'image')
                                    @if ($currentImage)
                                        <div class="mb-2"><img src="{{ $currentImage }}" alt="" style="max-width:180px;max-height:110px;object-fit:contain" class="rounded border p-1"></div>
                                    @endif
                                    <input class="form-control" type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                                        id="section-{{ $section->id }}-{{ $key }}" name="config[{{ $key }}]">
                                    <div class="form-text">Upload a replacement image (JPG, PNG, WebP or GIF; up to 10 MB). Leave empty to keep the current image.</div>
                                @else
                                    <input class="form-control" type="{{ in_array($type, ['number', 'url', 'color'], true) ? $type : 'text' }}"
                                        id="section-{{ $section->id }}-{{ $key }}" name="config[{{ $key }}]"
                                        value="{{ $value }}" maxlength="{{ $field['max'] ?? 255 }}"
                                        @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                        @if(isset($field['max']) && $type === 'number') max="{{ $field['max'] }}" @endif>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i> Save section</button>
                </div>
            </form>
        </div>
    @empty
        <div class="alert alert-warning">Homepage 9 has no configured sections. Run the existing homepage content seeder after reviewing the production database backup.</div>
    @endforelse
@endsection
