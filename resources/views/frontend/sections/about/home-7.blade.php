@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
<section class="about-5 space {{ $visibility }}" id="about-{{ $section->id }}">
    <div class="container">
        <div class="row gy-2">
            <div class="col-lg-4"><div class="title-area text-left mb-1"><p class="sub-title text-anime-style-1"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? 'About Us' }}</p></div></div>
            <div class="col-lg-7"><div class="about-5-top pe-xxl-5"><h3 class="about-5-top-title">{{ $sectionConfig['heading'] ?? '' }}</h3><p>{{ $sectionConfig['description'] ?? '' }}</p>@if(!empty($sectionConfig['button_label']))<a href="{{ $sectionConfig['button_url'] ?? '/about' }}" class="th-btn pill bg-black">{{ $sectionConfig['button_label'] }}</a>@endif</div></div>
        </div>
        <div class="row gy-4 align-items-center mt-3">
            <div class="col-lg-5"><div class="about5-img-left"><img src="{{ $sectionConfig['image_primary'] ?? asset('theme-assets/img/about/about-5-left.jpg') }}" alt="{{ $sectionConfig['heading'] ?? 'About us' }}"></div></div>
            <div class="col-lg-7"><div class="about5-right-wrap"><div class="bottom-wrap"><div class="img-box"><img src="{{ $sectionConfig['image_secondary'] ?? asset('theme-assets/img/about/about-5-right-1.jpg') }}" alt=""></div><div class="img-box-content"><div class="content"><h3>{{ $sectionConfig['vision_text'] ?? '' }}</h3><div class="bottom"><h5 class="title">{{ $sectionConfig['vision_title'] ?? 'Our Vision' }}</h5><h5 class="desi">{{ $sectionConfig['mission_title'] ?? 'Our Mission' }}</h5></div></div><img src="{{ $sectionConfig['image_tertiary'] ?? asset('theme-assets/img/about/about-5-right-2.jpg') }}" alt=""></div></div>
                <div class="about-counter-wrap mt-4">@for($i=1;$i<=3;$i++)<div class="counter-box"><h4 class="box-number"><span class="counter-number">{{ $sectionConfig['stat_'.$i.'_value'] ?? '' }}</span></h4><p class="box-text">{{ $sectionConfig['stat_'.$i.'_label'] ?? '' }}</p></div>@endfor</div>
            </div></div>
        </div>
    </div>
</section>