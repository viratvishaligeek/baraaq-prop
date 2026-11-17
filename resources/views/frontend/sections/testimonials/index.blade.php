@php
    $items = $testimonials->take(max(1, min(24, (int) ($sectionConfig['items_limit'] ?? 8))));
    $options = json_encode(['centeredSlides' => true, 'paginationType' => 'progressbar', 'loop' => true, 'breakpoints' => ['0' => ['slidesPerView' => 1], '576' => ['slidesPerView' => 1], '768' => ['slidesPerView' => 1], '992' => ['slidesPerView' => 2], '1200' => ['slidesPerView' => 2], '1300' => ['slidesPerView' => 3]]], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
@if($items->isNotEmpty())
<section class="testi-card-area-1 bg-smoke space bg-smoke overflow-hidden {{ $visibility }}" id="testimonials-{{ $section->id }}">
    <div class="container-fluid">
        <div class="row justify-content-center"><div class="col-xl-7"><div class="title-area text-center"><p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? 'Testimonials' }}</p><h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2></div></div></div>
        <div class="row gy-4 justify-content-center"><div class="testi-card-slide"><div class="swiper has-shadow th-slider" id="testiSlide-{{ $section->id }}" data-slider-options='{{ $options }}'><div class="swiper-wrapper">
            @foreach($items as $testimonial)
                <div class="swiper-slide"><div class="testi-block" dir="ltr"><div class="top-wrap"><div class="quote"><img src="{{ $sectionConfig['quote_icon'] ?? asset('theme-assets/img/icon/quote.svg') }}" alt="quote"></div><div class="logo"><img src="{{ asset('theme-assets/img/icon/brand-logo-'.(($loop->index % 3)+1).'.png') }}" alt=""></div></div><div class="testi-content"><p class="box-text">“{{ $testimonial->quote }}”</p></div><div class="bottom-wrap"><div class="content"><h3 class="box-title">{{ $testimonial->name }}</h3><p class="text">{{ $testimonial->designation }}@if(($sectionConfig['show_company'] ?? true) && $testimonial->company), {{ $testimonial->company }}@endif</p></div><div class="team-img"><img src="{{ $testimonial->photo ? Storage::disk('public')->url($testimonial->photo) : asset('theme-assets/img/testimonial/testi_1_1.jpg') }}" alt="{{ $testimonial->name }}">@if(($sectionConfig['show_rating'] ?? true) && $testimonial->rating)<div class="box-review"><i class="fa-sharp fa-solid fa-star"></i><div class="rating"><span>{{ number_format($testimonial->rating, 1) }}</span></div></div>@endif</div></div></div></div>
            @endforeach
        </div><div class="slider-controller container-width"><button data-slider-prev="#testiSlide-{{ $section->id }}" class="slider-arrow default slider-prev"><i class="far fa-arrow-left"></i></button><div class="slider-pagination"></div><button data-slider-next="#testiSlide-{{ $section->id }}" class="slider-arrow default slider-next"><i class="far fa-arrow-right"></i></button></div></div></div></div>
    </div>
</section>
@endif