@if ($posts->isNotEmpty())
    @php
        $posts = $posts->take(max(1, min(6, (int) ($sectionConfig['items_limit'] ?? 6))))->values();
        $visibility = trim(
            (!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '') .
            (!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '') .
            (!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : '')
        );
        $options = json_encode([
            'breakpoints' => [
                '0' => ['slidesPerView' => 1],
                '576' => ['slidesPerView' => 1],
                '768' => ['slidesPerView' => 1],
                '992' => ['slidesPerView' => 2],
                '1200' => ['slidesPerView' => 2],
                '1300' => ['slidesPerView' => 3],
            ],
            'autoHeight' => true,
        ], JSON_UNESCAPED_SLASHES);
    @endphp

    <section class="blog-area2 space-bottom overflow-hidden{{ $visibility }}" id="blog-sec-{{ $section->id }}">
        <div class="container">
            <div class="row justify-content-lg-between justify-content-center align-items-end">
                <div class="col-lg-7 col-xl-5">
                    <div class="title-area">
                        <p class="sub-title style3 text-anime-style-1">{{ $sectionConfig['eyebrow'] ?? 'Insights & Updates' }}</p>
                        <h2 class="sec-title text-anime-style-2">{{ $sectionConfig['heading'] ?? 'Our Guide to Smarter Living Modern Property Insights' }}</h2>
                    </div>
                </div>
                @if (!empty($sectionConfig['button_label']))
                    <div class="col-lg-auto d-none d-lg-block">
                        <div class="sec-btn">
                            <div class="btn-group2">
                                <a class="btn-circle" href="{{ $sectionConfig['button_url'] ?? route('blog.index') }}"><i class="fa-solid fa-arrow-up-right"></i></a>
                                <a class="th-btn" href="{{ $sectionConfig['button_url'] ?? route('blog.index') }}">{{ $sectionConfig['button_label'] }}</a>
                                <a class="btn-circle" href="{{ $sectionConfig['button_url'] ?? route('blog.index') }}"><i class="fa-solid fa-arrow-up-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="slider-area">
                <div class="swiper th-slider has-shadow" id="blogSlider-{{ $section->id }}" data-slider-options='{{ $options }}'>
                    <div class="swiper-wrapper">
                        @foreach ($posts as $post)
                            @php
                                $fallback = asset('theme-assets/img/blog/blog_5_' . min(3, $loop->iteration) . '.jpg');
                                $image = $post->featured_image ? Storage::disk('public')->url($post->featured_image) : $fallback;
                            @endphp
                            <div class="swiper-slide">
                                <div class="blog-grid">
                                    <div class="blog-img">
                                        <a href="{{ route('blog.show', $post) }}"><img src="{{ $image }}" alt="{{ $post->title }}"></a>
                                    </div>
                                    <div class="blog-content">
                                        <div class="blog-meta">
                                            @if (($sectionConfig['show_date'] ?? true) && $post->published_at)
                                                <a href="{{ route('blog.show', $post) }}">{{ $post->published_at->format('F d, Y') }}</a>
                                            @endif
                                        </div>
                                        <h3 class="box-title"><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h3>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button data-slider-prev="#blogSlider-{{ $section->id }}" class="slider-arrow slider-prev" aria-label="Previous posts"><i class="far fa-arrow-left"></i></button>
                <button data-slider-next="#blogSlider-{{ $section->id }}" class="slider-arrow slider-next" aria-label="Next posts"><i class="far fa-arrow-right"></i></button>
            </div>
        </div>
    </section>
@endif
