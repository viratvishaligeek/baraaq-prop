@if($testimonials->isNotEmpty())
@php
    $options = json_encode([
        'centeredSlides' => true,
        'paginationType' => 'progressbar',
        'loop' => (bool) ($sectionConfig['loop'] ?? true),
        'breakpoints' => [
            '0' => ['slidesPerView' => 1],
            '576' => ['slidesPerView' => 1],
            '768' => ['slidesPerView' => 1],
            '992' => ['slidesPerView' => 1],
            '1200' => ['slidesPerView' => 1],
        ],
    ], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<section class="testi-card-area-2 {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden {{ $visibility }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="title-area text-center">
                    <p class="sub-title text-anime-style-2">
<span class="double-line">
</span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
        </div>
        <div class="row gy-4 justify-content-center">
            <div class="testi-card-slide">
                <div class="swiper has-shadow th-slider" id="testiSlide-{{ $section->id }}" data-slider-options='{{ $options }}'>
                    <div class="swiper-wrapper">
                        @foreach($testimonials as $testimonial)
                            <div class="swiper-slide">
                                <div class="testi-block style-2" dir="ltr">
                                    <div class="top-wrap">
<div class="quote">@if(!empty($sectionConfig['quote_icon']))<img src="{{ $sectionConfig['quote_icon'] }}" alt="Quote">@endif</div>
</div>
                                    <div class="testi-content pe-xl-5 px-xl-5">
<p class="box-text">{{ $testimonial->quote }}</p>
</div>
                                    <div class="bottom-wrap">
                                        <div class="box-review">@for($star = 1; $star <= (int) $testimonial->rating; $star++)<i class="fa-sharp fa-solid fa-star">
</i>@endfor</div>
                                        @if($testimonial->photo)<div class="team-img">
<img src="{{ Storage::disk('public')->url($testimonial->photo) }}" alt="{{ $testimonial->name }}">
</div>@endif
                                        <div class="content">
<h3 class="box-title">{{ $testimonial->name }}</h3>
<p class="text">{{ $testimonial->designation }}@if($testimonial->company), {{ $testimonial->company }}@endif</p>
</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="slider-controller container-width">
                        <button data-slider-prev="#testiSlide-{{ $section->id }}" class="slider-arrow default slider-prev">
<i class="far fa-arrow-left">
</i>
</button>
                        <div class="slider-pagination style-2" data-slider-id="#testiSlide-{{ $section->id }}">
</div>
                        <button data-slider-next="#testiSlide-{{ $section->id }}" class="slider-arrow default slider-next">
<i class="far fa-arrow-right">
</i>
</button>
                    </div>
                </div>
            </div>
        </div>
        @if($sectionConfig['show_trust_summary'] ?? true)
            <div class="row">
<div class="col-12">
<div class="testi-bottom-review-wrap">
                <div class="testi-bottom-item">
<div class="trust-content">
<h4 class="box-title">Client feedback</h4>
</div>
<div class="bottom">
<div class="content">
<div class="th-social">@for($star = 1; $star <= (int) round($averageRating); $star++)<i class="fa-solid fa-star">
</i>@endfor</div>
<h4 class="review-title">
<span class="number">{{ $averageRating > 0 ? number_format($averageRating, 1).'/5' : 'No ratings yet' }}</span> from {{ $reviewCount }} published testimonials</h4>
</div>
</div>
</div>
            </div>
</div>
</div>
        @endif
    </div>
</section>
@endif
