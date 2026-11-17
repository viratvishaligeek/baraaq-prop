@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $stats = collect(range(1, 4))->map(fn ($i) => ['value' => $sectionConfig['stat_'.$i.'_value'] ?? '', 'label' => $sectionConfig['stat_'.$i.'_label'] ?? ''])->filter(fn ($item) => $item['value'] !== '' || $item['label'] !== '');
@endphp
<section class="about-2 {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden {{ $visibility }}" id="about-{{ $section->id }}">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-4"><div class="title-area text-left mb-50"><p class="sub-title text-anime-style-1"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? 'Who We Are' }}</p><h2 class="sec-title text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2></div></div>
            <div class="col-lg-5"><div class="decroption"><p class="sec-text text-anime-style-3">{!! \\App\\Support\\HtmlSanitizer::sanitize($sectionConfig['description'] ?? '') !!}</p>@if(!empty($sectionConfig['button_label']))<a href="{{ $sectionConfig['button_url'] ?? '/about' }}" class="th-btn big-btn bg-theme mt-3">{{ $sectionConfig['button_label'] }}</a>@endif</div></div>
        </div>
        <div class="row gy-40 gx-70 justify-content-between">
            <div class="col-xxl-5"><div class="img-box2 about-1 fadeinup wow" data-wow-duration="1.5s" data-wow-delay="0.3s"><div class="img1"><img src="{{ $sectionConfig['image_primary'] ?? asset('theme-assets/img/about/about-1-1.jpg') }}" alt="{{ $sectionConfig['heading'] ?? 'About us' }}"></div><div class="about-logo-wrap-2"><div class="logo-icon-wrap"><div class="logo-icon"><img src="{{ asset('theme-assets/img/icon/logo.png') }}" alt=""></div><div class="logo-icon-wrap__text"><span class="logo-animation">PILLAR REAL ESTATE * REAL ESTATE SOLUTION *</span></div></div></div></div></div>
            <div class="col-xxl-7">
                <div class="about-wrapper"><div class="about-item-area">
                    <div class="about-item hover-item item-active"><div class="box-wrapp"><div class="box-left"><div class="box-icon"><img src="{{ asset('theme-assets/img/icon/eye.svg') }}" alt=""></div><h3 class="box-title">{{ $sectionConfig['vision_title'] ?? 'Our Vision' }}</h3></div><div class="box-content"><div class="box-img"><img src="{{ $sectionConfig['image_secondary'] ?? asset('theme-assets/img/about/about-1_1.jpg') }}" alt=""></div><p class="box-text">{{ $sectionConfig['vision_text'] ?? '' }}</p></div></div></div>
                    <div class="about-item hover-item"><div class="box-wrapp"><div class="box-left"><div class="box-icon"><img src="{{ asset('theme-assets/img/icon/eye.svg') }}" alt=""></div><h3 class="box-title">{{ $sectionConfig['mission_title'] ?? 'Our Mission' }}</h3></div><div class="box-content"><div class="box-img"><img src="{{ $sectionConfig['image_tertiary'] ?? asset('theme-assets/img/about/about-1_2.jpg') }}" alt=""></div><p class="box-text">{{ $sectionConfig['mission_text'] ?? '' }}</p></div></div></div>
                </div>
                @if(($sectionConfig['show_stats'] ?? true) && $stats->isNotEmpty())<div class="counter-box-wrap">@foreach($stats as $stat)<div class="counter-box fadeinup wow"><div class="media-body"><h4 class="box-number"><span class="counter-number">{{ $stat['value'] }}</span></h4><p class="box-text">{{ $stat['label'] }}</p></div></div>@if(!$loop->last)<div class="divider"></div>@endif @endforeach</div>@endif
            </div>
        </div>
    </div>
</section>