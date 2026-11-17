@php
    $image = fn ($path) => $path && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/'))
        ? $path
        : asset($path);
    $features = [];
    $featureColumns = (int) ($sectionConfig['feature_columns'] ?? 2) === 4 ? 3 : 6;
    $animationClass = ($sectionConfig['animate_images'] ?? true) ? ' fadeinup wow' : '';
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $backgroundImage = $sectionConfig['background_image'] ?? null;
    for ($i = 1; $i <= 4; $i++) {
        $features[] = [
            'icon' => $sectionConfig['feature_'.$i.'_icon'] ?? null,
            'title' => $sectionConfig['feature_'.$i.'_title'] ?? '',
            'text' => $sectionConfig['feature_'.$i.'_text'] ?? '',
        ];
    }
@endphp

<div class="about-1-wrapper {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden {{ $visibility }}" id="about-sec-{{ $section->id }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if($backgroundImage) data-bg-src="{{ $backgroundImage }}" @endif>
    <div class="shape-mockup" data-bottom="0" data-left="0"><img src="{{ $image($sectionConfig['shape_bottom_image'] ?? '/theme-assets/img/icon/about-2-shape.png') }}" alt="img"></div>
    <div class="shape-mockup spin d-none d-lg-block" data-bottom="11%" data-left="5%"><img src="{{ $image($sectionConfig['shape_spin_image'] ?? '/theme-assets/img/icon/circle-start.png') }}" alt="img"></div>
    <div class="container">
        <div class="row gy-40 gx-70 justify-content-center">
            <div class="col-xl-7">
                <div class="img-box1 about-1{{ $animationClass }}" data-wow-duration="1.5s" data-wow-delay="0.3s">
                    <div class="about-logo-wrap-2">
                        <div class="logo-icon-wrap">
                            <div class="logo-icon"><img src="{{ $image($sectionConfig['logo_image'] ?? '') }}" alt="img"></div>
                            <div class="logo-icon-wrap__text"><span class="logo-animation">{{ $sectionConfig['logo_text'] ?? '' }}</span></div>
                        </div>
                    </div>
                    <div class="about-left">
                        <div class="img-box"><img src="{{ $image($sectionConfig['image_left_large'] ?? '') }}" alt="About image"></div>
                        <div class="img-box small"><img src="{{ $image($sectionConfig['image_left_small'] ?? '') }}" alt="About image"></div>
                    </div>
                    <div class="about-middle"><img class="tilt-active" src="{{ $image($sectionConfig['image_center'] ?? '') }}" alt="About image"></div>
                    <div class="about-right">
                        <div class="img-box small"><img src="{{ $image($sectionConfig['image_right_small'] ?? '') }}" alt="About image"></div>
                        <div class="img-box big"><img src="{{ $image($sectionConfig['image_right_large'] ?? '') }}" alt="About image"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="title-area text-{{ $sectionConfig['content_alignment'] ?? 'left' }} mb-50">
                    <p class="sub-title text-anime-style-1"><span class="double-line"></span> {{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2>
                    <p class="sec-text text-anime-style-3">{{ $sectionConfig['description'] ?? '' }}</p>
                </div>
                <div class="row gy-40 gx-60 mb-4">
                    @foreach($features as $feature)
                        <div class="col-lg-{{ $featureColumns }} col-md-6{{ $animationClass }}" data-wow-delay="{{ ['.1s','.3s','.5s','.7s'][$loop->index] }}">
                            <div class="about-1-item">
                                <div class="icon"><img src="{{ $image($feature['icon']) }}" alt="icon"></div>
                                <div class="content">
                                    <h3 class="box-title">{{ $feature['title'] }}</h3>
                                    <p class="box-text">{{ $feature['text'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="about-bottom-wrap fadeinup wow" data-wow-delay=".9s">
                            @if(!empty($sectionConfig['button_label']))
                                <a href="{{ $sectionConfig['button_url'] ?? '#' }}" class="th-btn bg-black pill">{{ $sectionConfig['button_label'] }}</a>
                            @endif
                            <div class="about1-call-wrapper">
                                <div class="icon"><i class="{{ $sectionConfig['phone_icon'] ?? 'fa-regular fa-phone' }}"></i></div>
                                <div class="content">
                                    <p>{{ $sectionConfig['phone_label'] ?? '' }}</p>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sectionConfig['phone_number'] ?? '') }}" class="link">{{ $sectionConfig['phone_number'] ?? '' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
