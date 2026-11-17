@php
    $items = [];
    for ($i = 1; $i <= 4; $i++) {
        $items[] = [
            'quote' => $sectionConfig['item_'.$i.'_quote'] ?? '',
            'name' => $sectionConfig['item_'.$i.'_name'] ?? '',
            'role' => $sectionConfig['item_'.$i.'_role'] ?? '',
            'avatar' => $sectionConfig['item_'.$i.'_avatar'] ?? '',
            'rating' => max(1, min(5, (int) ($sectionConfig['item_'.$i.'_rating'] ?? 5))),
        ];
    }
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
                    <p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
        </div>
        <div class="row gy-4 justify-content-center">
            <div class="testi-card-slide">
                <div class="swiper has-shadow th-slider" id="testiSlide-{{ $section->id }}" data-slider-options='{{ $options }}'>
                    <div class="swiper-wrapper">
                        @foreach($items as $item)
                            @continue(!$item['quote'] && !$item['name'])
                            <div class="swiper-slide">
                                <div class="testi-block style-2" dir="ltr">
                                    <div class="top-wrap">
                                        <div class="quote"><img src="{{ $sectionConfig['quote_icon'] ?? '/theme-assets/img/icon/testi-2quote.svg' }}" alt="quote"></div>
                                    </div>
                                    <div class="testi-content pe-xl-5 px-xl-5"><p class="box-text">{{ $item['quote'] }}</p></div>
                                    <div class="bottom-wrap">
                                        <div class="box-review">
                                            @for($star = 1; $star <= $item['rating']; $star++)<i class="fa-sharp fa-solid fa-star"></i>@endfor
                                        </div>
                                        <div class="team-img"><img src="{{ $item['avatar'] }}" alt="{{ $item['name'] }}"></div>
                                        <div class="content"><h3 class="box-title">{{ $item['name'] }}</h3><p class="text">{{ $item['role'] }}</p></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="slider-controller container-width">
                        <button data-slider-prev="#testiSlide-{{ $section->id }}" class="slider-arrow default slider-prev"><i class="far fa-arrow-left"></i></button>
                        <div class="slider-pagination style-2" data-slider-id="#testiSlide-{{ $section->id }}"></div>
                        <button data-slider-next="#testiSlide-{{ $section->id }}" class="slider-arrow default slider-next"><i class="far fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
        @if($sectionConfig['show_trust_summary'] ?? true)
            <div class="row">
                <div class="col-12">
                    <div class="testi-bottom-review-wrap">
                        @for($i = 1; $i <= 2; $i++)
                            <div class="testi-bottom-item{{ $i === 1 ? ' fadeinleft wow' : ' fadeinright wow' }}" data-wow-delay="{{ $i === 1 ? '0.3s' : '0.6s' }}">
                                <div class="trust-content">
                                    <div class="icon"><img src="{{ $sectionConfig['trust_'.$i.'_icon'] ?? '' }}" alt="img"></div>
                                    <h4 class="box-title">{{ $sectionConfig['trust_'.$i.'_name'] ?? '' }}</h4>
                                </div>
                                <div class="bottom">
                                    <div class="avatar"><img src="{{ $sectionConfig['trust_'.$i.'_avatars'] ?? '' }}" alt="img"></div>
                                    <div class="content">
                                        <div class="th-social">@for($star = 1; $star <= 5; $star++)<i class="fa-solid fa-star"></i>@endfor</div>
                                        <h4 class="review-title"><span class="number"><span class="counter-number">{{ $sectionConfig['trust_'.$i.'_score'] ?? '' }}</span>{{ $sectionConfig['trust_'.$i.'_suffix'] ?? '' }}</span> {{ $sectionConfig['trust_'.$i.'_label'] ?? '' }}</h4>
                                    </div>
                                </div>
                            </div>
                            @if($i === 1)<div class="divider"></div>@endif
                        @endfor
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
