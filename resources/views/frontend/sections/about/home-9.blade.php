@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
<section class="about-7 {{ $sectionConfig['vertical_spacing'] ?? '' }} overflow-hidden pt-2 {{ $visibility }}" id="about">
    <div class="container">
        <div class="row justify-content-center mb-35"><div class="col-xl-6"><div class="title-area text-center mb-1"><p class="sub-title style4 text-anime-style-1">{{ $sectionConfig['eyebrow'] ?? '' }}</p><h2 class="sec-title style-2 text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2>@if(!empty($sectionConfig['button_label']))<a href="{{ $sectionConfig['button_url'] ?? '' }}" class="th-btn big-btn bg-theme">{{ $sectionConfig['button_label'] }}</a>@endif</div></div></div>
        <div class="row gy-40 justify-content-center">
            <div class="col-xl-4 col-lg-6 col-md-6"><div class="about7-left"><img class="d-none d-xl-block" src="{{ $sectionConfig['image_primary'] ?? asset('theme-assets/img/about/about7-1.jpg') }}" alt=""><div class="about7-content-box"><h4 class="box-title">{{ $sectionConfig['mission_title'] ?? '' }}</h4><p class="box-text">{{ $sectionConfig['mission_text'] ?? '' }}</p></div></div></div>
            <div class="col-xl-4 col-lg-6 col-md-6 d-none d-xl-block"><div class="about7-middle"><img src="{{ $sectionConfig['image_secondary'] ?? asset('theme-assets/img/about/about7-2.jpg') }}" alt=""></div></div>
            <div class="col-xl-4 col-lg-6 col-md-6"><div class="about7-right"><img class="d-none d-xl-block" src="{{ $sectionConfig['image_tertiary'] ?? asset('theme-assets/img/about/about7-3.jpg') }}" alt=""><div class="about7-content-box"><h4 class="box-title">{{ $sectionConfig['vision_title'] ?? '' }}</h4><p class="box-text">{{ $sectionConfig['vision_text'] ?? '' }}</p></div></div></div>
        </div>
    </div>
</section>