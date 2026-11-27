<div class="th-hero-wrapper hero-9 position-relative">
    <div class="hero-inner">
        <div class="th-hero-bg"
            data-bg-src="{{ $sectionConfig['background_image'] ?? asset('theme-assets/img/hero/hero-10-2.jpg') }}">
        </div>
        <div class="container">
            <div class="row gy-50 gx-40 align-items-center">
                <div class="col-xl-6 col-lg-6">
                    <div class="hero-style9"><span class="hero-subtitle">{{ $sectionConfig['eyebrow'] ?? '' }}</span>
                        <h1 class="hero-title wow fadeinup" data-wow-duration="1.5s" data-wow-delay="0.2s"><span
                                class="title1">{{ $sectionConfig['heading_prefix'] ?? '' }}</span> <span
                                class="title2">{{ $sectionConfig['heading_highlight'] ?? '' }}</span> <span
                                class="title2 text-theme">{{ $sectionConfig['heading_suffix'] ?? '' }}</span></h1>
                        <p class="hero-text wow fadeinup" data-wow-duration="1.5s" data-wow-delay="0.3s">
                            {!! \App\Support\HtmlSanitizer::sanitize($sectionConfig['description'] ?? '') !!}</p>
                        <div class="btn-group wow fadeinup" data-wow-duration="1.5s" data-wow-delay="0.4s"><a
                                href="{{ $sectionConfig['primary_button_url'] ?? route('properties.index') }}"
                                class="th-btn big-btn style2">{{ $sectionConfig['primary_button_label'] ?? '' }}</a> <a
                                href="{{ $sectionConfig['secondary_button_url'] ?? '' }}"
                                class="th-btn big-btn style3">{{ $sectionConfig['secondary_button_label'] ?? '' }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                    <div class="hero-9-img"><img
                            src="{{ $sectionConfig['foreground_image'] ?? asset('theme-assets/img/hero/hero-9-right.png') }}"
                            alt=""></div>
                </div>
            </div>
        </div>
    </div>
</div>
