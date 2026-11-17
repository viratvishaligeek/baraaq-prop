@extends('admin.layouts.app')
@section('title', 'Website settings')
@section('content')
    <div class="mb-4">
        <h1 class="page-title">Website settings</h1>
        <p class="text-muted">One place for identity, navigation, contact details, appearance and default SEO.</p>
    </div>
    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')
        <div class="content-card mb-4">
            <h4 class="mb-3">Website identity</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Website name *</label>
                    <input class="form-control" name="site_title"
                        value="{{ old('site_title', $settings['site_title'] ?? config('app.name')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tagline</label>
                    <input class="form-control" name="site_tagline"
                        value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Logo</label>
                    <input class="form-control" type="file" name="logo" accept="image/*">
                    @if (!empty($settings['logo']))
                        <img src="{{ Storage::disk('public')->url($settings['logo']) }}" alt="Current logo" class="mt-2"
                            style="max-width:180px;max-height:70px">
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Favicon</label>
                    <input class="form-control" type="file" name="favicon" accept=".png,.ico,image/png">
                    @if (!empty($settings['favicon']))
                        <img src="{{ Storage::disk('public')->url($settings['favicon']) }}" alt="Current favicon"
                            class="mt-2" width="32">
                    @endif
                </div>
            </div>
        </div>
        <div class="content-card mb-4">
            <h4 class="mb-3">Contact and business details</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Contact email</label>
                    <input class="form-control" type="email" name="site_email"
                        value="{{ old('site_email', $settings['site_email'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input class="form-control" name="site_phone"
                        value="{{ old('site_phone', $settings['site_phone'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">WhatsApp number</label>
                    <input class="form-control" name="whatsapp_number"
                        value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Business name</label>
                    <input class="form-control" name="business_name"
                        value="{{ old('business_name', $settings['business_name'] ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Full address</label>
                    <textarea class="form-control" name="site_address" rows="2">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Business registration / legal detail</label>
                    <input class="form-control" name="business_registration"
                        value="{{ old('business_registration', $settings['business_registration'] ?? '') }}">
                </div>
            </div>
        </div>
        <div class="content-card mb-4">
            <h4 class="mb-3">Header and footer</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Header menu location</label>
                    <input class="form-control" name="header_menu_location"
                        value="{{ old('header_menu_location', $settings['header_menu_location'] ?? 'primary') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Footer menu location</label>
                    <input class="form-control" name="footer_menu_location"
                        value="{{ old('footer_menu_location', $settings['footer_menu_location'] ?? 'footer') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Footer description</label>
                    <textarea class="form-control" name="footer_description" rows="3">{{ old('footer_description', $settings['footer_description'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Copyright text</label>
                    <input class="form-control" name="footer_copyright"
                        value="{{ old('footer_copyright', $settings['footer_copyright'] ?? '') }}">
                </div>
                <div class="col-md-6 form-check mt-5">
                    <input class="form-check-input" type="checkbox" name="sticky_header" value="1" id="sticky-header"
                        @checked(old('sticky_header', $settings['sticky_header'] ?? true))>
                    <label class="form-check-label" for="sticky-header">Enable sticky header</label>
                </div>
            </div>
        </div>
        <div class="content-card mb-4">
            <h4 class="mb-3">Social media</h4>
            <div class="row g-3">
                @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'x' => 'X / Twitter'] as $key => $label)
                    <div class="col-md-6">
                        <label class="form-label">{{ $label }} URL</label>
                        <input class="form-control" type="url" name="social_{{ $key }}"
                            value="{{ old('social_' . $key, $settings['social_' . $key] ?? '') }}">
                    </div>
                @endforeach
            </div>
        </div>
        <div class="content-card mb-4">
            <h4 class="mb-3">Global SEO defaults</h4>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Default meta title</label>
                    <input class="form-control" name="meta_title"
                        value="{{ old('meta_title', $settings['meta_title'] ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Default meta description</label>
                    <textarea class="form-control" name="meta_description" rows="3">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Meta keywords</label>
                    <input class="form-control" name="meta_keywords"
                        value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Robots policy</label>
                    <select class="form-select" name="meta_robots">
                        @foreach (['index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow'] as $policy)
                            <option value="{{ $policy }}" @selected(old('meta_robots', $settings['meta_robots'] ?? 'index,follow') === $policy)>{{ $policy }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Default social sharing image</label>
                    <input class="form-control" type="file" name="default_og_image" accept="image/*">
                    @if (!empty($settings['default_og_image']))
                        <img src="{{ Storage::disk('public')->url($settings['default_og_image']) }}" alt=""
                            class="mt-2" style="max-width:160px">
                    @endif
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
            <p class="text-muted small">Only add scripts from providers you trust. These fields are rendered as
                administrator-supplied code.</p>
            <div class="mb-3">
                <label class="form-label">Head scripts</label>
                <textarea class="form-control font-monospace" name="head_scripts" rows="5">{{ old('head_scripts', $settings['head_scripts'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="form-label">Before closing body scripts</label>
                <textarea class="form-control font-monospace" name="body_scripts" rows="5">{{ old('body_scripts', $settings['body_scripts'] ?? '') }}</textarea>
            </div>
        </div>
        <button class="btn btn-primary btn-lg">Save all settings</button>
    </form>
@endsection
