@if($testimonials->isNotEmpty())
@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $sliderId = 'testiSlide3-'.$section->id;
@endphp

<section class="testi-card-area-3 {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden {{ $visibility }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif>
    <div class="container">
        <div class="row gy-40 gx-100 justify-content-center align-items-end">
            <div class="col-lg-4">
                <div class="testi-3-rating me-xxl-5">
                    <div class="rating-count">
<h2>{{ $averageRating > 0 ? number_format($averageRating, 1) : '—' }}</h2>
<div class="star-shape spin">@if(!empty($sectionConfig['rating_shape_image']))<img src="{{ $sectionConfig['rating_shape_image'] }}" alt="Decorative shape">@endif</div>
</div>
                    <div class="reviews-star">
                        <div class="th-social">
<i class="fa-solid fa-star">
</i> <i class="fa-solid fa-star">
</i> <i class="fa-solid fa-star">
</i> <i class="fa-solid fa-star">
</i> <i class="fa-solid fa-star">
</i>
</div>
                        <h4 class="review-title">
<span class="number">{{ $sectionConfig['trust_score_prefix'] ?? 'Trust-score on' }} <span class="counter-number">{{ $reviewCount }}</span>+</span> {{ $sectionConfig['reviews_suffix'] ?? 'reviews' }}</h4>
                    </div>
                    <a class="th-btn tab-btn style3 radius" href="{{ $sectionConfig['view_all_url'] ?? '#' }}">{{ $sectionConfig['view_all_label'] ?? 'View All Reviews' }}</a>
                    <div class="testi-bottom-item">
                        <div class="trust-content">
<div class="icon">@if(!empty($sectionConfig['star_icon_image']))<img src="{{ $sectionConfig['star_icon_image'] }}" alt="Star">@endif</div>
<h4 class="box-title">{{ $sectionConfig['trust_brand'] ?? '' }}</h4>
</div>
                        <div class="bottom">
<div class="avatar">@if(!empty($sectionConfig['avatar_group_image']))<img src="{{ $sectionConfig['avatar_group_image'] }}" alt="Reviewers">@endif</div>
</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="testi-3-title-wrap pe-xxl-5 ps-xxl-5">
                    <div class="title-area text-left">
<p class="sub-title text-anime-style-2">
<span class="double-line">
</span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
<h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
</div>
                </div>
                <div class="testi-card-slide pe-xl-5 ps-xl-5">
                    <div class="swiper has-shadow th-slider" id="{{ $sliderId }}" data-slider-options='{"autoplay":false,"centeredSlides":true,"paginationType":"progressbar","loop":true,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":1},"768":{"slidesPerView":1},"992":{"slidesPerView":1},"1200":{"slidesPerView":1}}}'>
                        <div class="swiper-wrapper">
                            @forelse($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <div class="testi-block style-2 style-3" dir="ltr">
                                        <div class="top-wrap">
<div class="quote">@if(!empty($sectionConfig['quote_icon_image']))<img src="{{ $sectionConfig['quote_icon_image'] }}" alt="quote">@endif</div>
</div>
                                        <div class="testi-content">
<p class="box-text mb-40">“{{ $testimonial->quote }}”</p>
</div>
                                        <div class="bottom-wrap">
                                            <div class="team-img">@if($testimonial->photo)<img src="{{ Storage::disk('public')->url($testimonial->photo) }}" alt="{{ $testimonial->name }}">@endif</div>
                                            <div class="content">
<h3 class="box-title">{{ $testimonial->name }}</h3>
<p class="text">{{ $testimonial->designation }}@if($testimonial->company), {{ $testimonial->company }}@endif</p>
</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <div class="slider-pagination style-2 mt-5">
</div>
                    </div>
                    <button data-slider-prev="#{{ $sliderId }}" class="slider-arrow testi-3 slider-prev">
<i class="far fa-arrow-left">
</i>
</button>
                    <button data-slider-next="#{{ $sliderId }}" class="slider-arrow testi-3 slider-next">
<i class="far fa-arrow-right">
</i>
</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
