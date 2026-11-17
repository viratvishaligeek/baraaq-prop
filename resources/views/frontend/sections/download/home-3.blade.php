@php
    $animateContent = $sectionConfig['animate_content'] ?? true;
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="download-sec-2 {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden {{ $visibility }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    <div class="shape-mockup spin z-index-3 d-none d-xxl-block" style="top: 46%; right: 6%;">@if(!empty($sectionConfig['round_shape_image']))<img src="{{ $sectionConfig['round_shape_image'] }}" alt="Decorative shape">@endif</div>
    <div class="shape-mockup jump d-none d-xl-block" style="top: 19%; left: 5%;">@if(!empty($sectionConfig['scan_arrow_image']))<img src="{{ $sectionConfig['scan_arrow_image'] }}" alt="Decorative shape">@endif</div>
    <div class="container">
        <div class="row gy-4">
            <div class="col-xl-4 col-lg-6">
                <div class="download-2-thumbs{{ $animateContent ? ' fadeinup wow' : '' }}" data-wow-duration="1.5s" data-wow-delay="0.3s">
                    <div class="top">@if(!empty($sectionConfig['image_1']))<img src="{{ $sectionConfig['image_1'] }}" alt="App preview">@endif</div>
                    <div class="bottom">
                        <div class="left">@if(!empty($sectionConfig['image_2']))<img src="{{ $sectionConfig['image_2'] }}" alt="App preview">@endif</div>
                        <div class="right">@if(!empty($sectionConfig['image_3']))<img src="{{ $sectionConfig['image_3'] }}" alt="App preview">@endif</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6">
                <div class="title-area download-2 text-center">
                    <p class="sub-title{{ $animateContent ? ' text-anime-style-1' : '' }}">
<span class="double-line">
</span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title{{ $animateContent ? ' text-anime-style-2' : '' }} mb-4">{{ $sectionConfig['heading'] ?? '' }}</h2>
                    <p class="box-text{{ $animateContent ? ' text-anime-style-3' : '' }}">{{ $sectionConfig['description'] ?? '' }}</p>
                </div>
                <div class="scan-download-2-wrap">
                    <div class="scan-download-item-wrap">
                        <div class="scan-download-card-wrap">
                            <div class="scan-download-card">
                                <h5 class="download-text">{{ $sectionConfig['scan_label'] ?? '' }}</h5>
                                @if(!empty($sectionConfig['scan_arrow_image']))<div class="scan-2-arrow">
<img src="{{ $sectionConfig['scan_arrow_image'] }}" alt="Arrow">
</div>@endif
                            </div>
                        </div>
                        <div class="scan-download-item scan">
                            <div class="scan-download-item__thumb">@if(!empty($sectionConfig['store_icon_1']))<img src="{{ $sectionConfig['store_icon_1'] }}" alt="Google Play">@endif</div>
                            <div class="scan-download-item__content">
<p>{{ $sectionConfig['play_prelabel'] ?? '' }}</p>
<h4 class="box-title">
<a href="{{ $sectionConfig['play_url'] ?? '#' }}" target="_blank" rel="noopener">{{ $sectionConfig['play_label'] ?? '' }}</a>
</h4>
</div>
                            <div class="scan-download-item__right">@if(!empty($sectionConfig['scan_image']))<img src="{{ $sectionConfig['scan_image'] }}" alt="QR code">@endif</div>
                        </div>
                        <div class="scan-download-item scan">
                            <div class="scan-download-item__thumb">@if(!empty($sectionConfig['store_icon_2']))<img src="{{ $sectionConfig['store_icon_2'] }}" alt="App Store">@endif</div>
                            <div class="scan-download-item__content">
<p>{{ $sectionConfig['app_prelabel'] ?? '' }}</p>
<h4 class="box-title">
<a href="{{ $sectionConfig['app_url'] ?? '#' }}" target="_blank" rel="noopener">{{ $sectionConfig['app_label'] ?? '' }}</a>
</h4>
</div>
                            <div class="scan-download-item__right">@if(!empty($sectionConfig['scan_image']))<img src="{{ $sectionConfig['scan_image'] }}" alt="QR code">@endif</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="download-thumb style-2{{ $animateContent ? ' fadeinup wow' : '' }}" data-wow-delay="0.5s">
                    @if(!empty($sectionConfig['right_image']))<img src="{{ $sectionConfig['right_image'] }}" alt="Download app">@endif
                    @if(!empty($sectionConfig['round_shape_image']))<div class="download-2-round-bg">
<img src="{{ $sectionConfig['round_shape_image'] }}" alt="Decorative shape">
</div>@endif
                </div>
            </div>
        </div>
    </div>
</div>
