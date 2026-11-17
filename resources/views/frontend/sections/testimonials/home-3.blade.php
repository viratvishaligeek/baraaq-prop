@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $sliderId = 'testiSlide3-'.$section->id;
@endphp

<section class="testi-card-area-3 space overflow-hidden {{ $visibility }}">
    <div class="container">
        <div class="row gy-40 gx-100 justify-content-center align-items-end">
            <div class="col-lg-4">
                <div class="testi-3-rating me-xxl-5">
                    <div class="rating-count"><h2>{{ $sectionConfig['rating'] ?? '4.9' }}</h2><div class="star-shape spin"><img src="{{ asset('theme-assets/img/shape/download-circle.png') }}" alt="Decorative shape"></div></div>
                    <div class="reviews-star">
                        <div class="th-social"><i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i> <i class="fa-solid fa-star"></i></div>
                        <h4 class="review-title"><span class="number">Trust-score on <span class="counter-number">{{ $sectionConfig['review_count'] ?? 0 }}</span>+</span> reviews</h4>
                    </div>
                    <a class="th-btn tab-btn style3 radius" href="{{ $sectionConfig['view_all_url'] ?? '#' }}">{{ $sectionConfig['view_all_label'] ?? 'View All Reviews' }}</a>
                    <div class="testi-bottom-item">
                        <div class="trust-content"><div class="icon"><img src="{{ asset('theme-assets/img/icon/star-icon.svg') }}" alt="Star"></div><h4 class="box-title">{{ $sectionConfig['trust_brand'] ?? '' }}</h4></div>
                        <div class="bottom"><div class="avatar">@if(!empty($sectionConfig['avatar_group_image']))<img src="{{ $sectionConfig['avatar_group_image'] }}" alt="Reviewers">@endif</div></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="testi-3-title-wrap pe-xxl-5 ps-xxl-5">
                    <div class="title-area text-left"><p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p><h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2></div>
                </div>
                <div class="testi-card-slide pe-xl-5 ps-xl-5">
                    <div class="swiper has-shadow th-slider" id="{{ $sliderId }}" data-slider-options='{"autoplay":false,"centeredSlides":true,"paginationType":"progressbar","loop":true,"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":1},"768":{"slidesPerView":1},"992":{"slidesPerView":1},"1200":{"slidesPerView":1}}}'>
                        <div class="swiper-wrapper">
                            @for($i = 1; $i <= min(4, (int) ($sectionConfig['items_limit'] ?? 4)); $i++)
                                @if(!empty($sectionConfig['item_'.$i.'_name']))
                                    <div class="swiper-slide">
                                        <div class="testi-block style-2 style-3" dir="ltr">
                                            <div class="top-wrap"><div class="quote"><img src="{{ asset('theme-assets/img/icon/testi-2quote.svg') }}" alt="quote"></div></div>
                                            <div class="testi-content"><p class="box-text mb-40">“{{ $sectionConfig['item_'.$i.'_quote'] ?? '' }}”</p><p class="box-text">“{{ $sectionConfig['item_'.$i.'_quote_2'] ?? '' }}”</p></div>
                                            <div class="bottom-wrap">
                                                <div class="team-img">@if(!empty($sectionConfig['item_'.$i.'_image']))<img src="{{ $sectionConfig['item_'.$i.'_image'] }}" alt="{{ $sectionConfig['item_'.$i.'_name'] }}">@endif</div>
                                                <div class="content"><h3 class="box-title">{{ $sectionConfig['item_'.$i.'_name'] }}</h3><p class="text">{{ $sectionConfig['item_'.$i.'_role'] ?? '' }}</p></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                        <div class="slider-pagination style-2 mt-5"></div>
                    </div>
                    <button data-slider-prev="#{{ $sliderId }}" class="slider-arrow testi-3 slider-prev"><i class="far fa-arrow-left"></i></button>
                    <button data-slider-next="#{{ $sliderId }}" class="slider-arrow testi-3 slider-next"><i class="far fa-arrow-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</section>
