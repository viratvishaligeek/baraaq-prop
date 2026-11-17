@php
    $image = fn ($path) => $path && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) ? $path : asset($path);
    $textWidth = (int) ($sectionConfig['text_column_width'] ?? 4) === 5 ? 5 : 4;
    $mediaWidth = 12 - $textWidth;
    $animation = ($sectionConfig['animate_items'] ?? true);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="download-sec-1 overflow-hidden {{ $visibility }}" @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $image($sectionConfig['background_image']) }}" @endif>
    @if(!empty($sectionConfig['shape_image']))
        <div class="shape-mockup jump" style="top: 7%; right: 6%;">
<img src="{{ $image($sectionConfig['shape_image']) }}" alt="img">
</div>
    @endif
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-xl-{{ $textWidth }}">
                <div class="download-text-left">
                    <div class="title-area text-left white-style mb-30">
                        <p class="sub-title text-anime-style-1">
<span class="double-line">
</span> {{ $sectionConfig['eyebrow'] ?? '' }}</p>
                        <h2 class="sec-title text-white text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2>
                        <p class="sec-text fs-18 pe-xl-4 me-xl-5 text-white text-anime-style-3">{{ $sectionConfig['description'] ?? '' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-{{ $mediaWidth }}">
                <div class="scan-download-wrap">
                    <div class="download-logo-circle hero-img-shape-2{{ $animation ? ' fadeinup wow' : '' }}" @if($animation) data-wow-delay="0.2s" @endif>
                        <div class="logo-icon-wrap">
                            <a href="{{ $sectionConfig['video_url'] ?? '#' }}" class="logo-icon popup-video" aria-label="Play app introduction">
<i class="{{ $sectionConfig['play_icon'] ?? 'fa-sharp fa-solid fa-play' }}">
</i>
</a>
                            <div class="logo-icon-wrap__text">
<span class="logo-animation">{{ $sectionConfig['logo_text'] ?? '' }}</span>
</div>
                        </div>
                    </div>
                    <div class="scan-download-card">
                        <div class="scan-download-card-inner">
                            <h5>{{ $sectionConfig['qr_label'] ?? '' }}</h5>
                            @if(!empty($sectionConfig['arrow_image']))<img class="shape" src="{{ $image($sectionConfig['arrow_image']) }}" alt="img">@endif
                            <div class="qr-code-box">
<img class="qr-code" src="{{ $image($sectionConfig['qr_image'] ?? '') }}" alt="QR code">
</div>
                        </div>
                    </div>
                    <div class="download-thumb{{ $animation ? ' fadeinup wow' : '' }}" @if($animation) data-wow-delay="0.4s" @endif>
                        <img src="{{ $image($sectionConfig['app_image'] ?? '') }}" alt="App preview">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
