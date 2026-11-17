@if ($testimonials->isNotEmpty())
    @php
        $visibility = trim(
            (!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '') .
            (!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '') .
            (!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : '')
        );
        $options = json_encode([
            'centeredSlides' => true,
            'loop' => (bool) ($sectionConfig['loop'] ?? true),
            'breakpoints' => [
                '0' => ['slidesPerView' => 1],
                '576' => ['slidesPerView' => 1],
                '768' => ['slidesPerView' => 1],
                '992' => ['slidesPerView' => 2],
                '1200' => ['slidesPerView' => 2],
                '1300' => ['slidesPerView' => 3],
            ],
        ], JSON_UNESCAPED_SLASHES);
    @endphp

    <section class="bg-title {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden{{ $visibility }}"
        @if (!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
        <div class="container">
            <div class="row justify-content-lg-between justify-content-center align-items-end">
                <div class="col-lg-6">
                    <div class="title-area text-left text-lg-start">
                        <p class="sub-title text-anime-style-1"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? 'Testimonials' }}</p>
                        <h2 class="sec-title text-anime-style-2 text-white">{{ $sectionConfig['heading'] ?? 'What Clients Say About Pillar' }}</h2>
                    </div>
                </div>
                @if (!empty($sectionConfig['button_label']))
                    <div class="col-lg-auto d-none d-lg-block">
                        <div class="sec-btn">
                            <a href="{{ $sectionConfig['button_url'] ?? '/testimonials' }}" class="th-btn radius2 fadeinup wow" data-wow-delay=".4s">
                                {{ $sectionConfig['button_label'] }} <i class="fas fa-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row gy-4 justify-content-center">
                <div class="testi-card-slide">
                    <div class="swiper has-shadow th-slider testiSlide6" id="testiSlide-{{ $section->id }}" data-slider-options='{{ $options }}'>
                        <div class="swiper-wrapper">
                            @foreach ($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <div class="testi-item style2">
                                        <div class="box-profile">
                                            <div class="box-author">
                                                @if ($testimonial->photo)
                                                    <img src="{{ Storage::disk('public')->url($testimonial->photo) }}" alt="{{ $testimonial->name }}">
                                                @else
                                                    <span class="box-author-fallback">{{ mb_substr($testimonial->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="box-info">
                                                <h3 class="box-title">{{ $testimonial->name }}</h3>
                                                <p class="box-desig">{{ $testimonial->designation ?: ($testimonial->company ?: 'Client') }}</p>
                                            </div>
                                        </div>
                                        <p class="box-text">“{{ $testimonial->quote }}”</p>
                                        <div class="box-content">
                                            <div class="quote">
                                                @if (!empty($sectionConfig['quote_icon']))
                                                    <img src="{{ $sectionConfig['quote_icon'] }}" alt="Quote">
                                                @endif
                                            </div>
                                            @if (($sectionConfig['show_rating'] ?? true) && $testimonial->rating)
                                                <div class="box-review">
                                                    <i class="fa-sharp fa-solid fa-star"></i>
                                                    <div class="rating"><span>{{ number_format((float) $testimonial->rating, 1) }}</span></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="slider-controller container-width">
                            <button data-slider-prev="#testiSlide-{{ $section->id }}" class="slider-arrow default slider-prev" aria-label="Previous testimonial">
                                <i class="far fa-arrow-left"></i>
                            </button>
                            <div class="slider-pagination" data-slider-id="#testiSlide-{{ $section->id }}"></div>
                            <button data-slider-next="#testiSlide-{{ $section->id }}" class="slider-arrow default slider-next" aria-label="Next testimonial">
                                <i class="far fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
