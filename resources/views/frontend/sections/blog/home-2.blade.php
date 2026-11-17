@php
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
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<section class="{{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden {{ $visibility }}" id="blog-sec-{{ $section->id }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    <div class="container">
        <div class="row justify-content-lg-between justify-content-center align-items-end">
            <div class="col-lg-6">
                <div class="title-area text-left text-lg-start">
                    <p class="sub-title text-anime-style-1"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2>
                    <p class="sec-text pe-xl-4 text-anime-style-3">{{ $sectionConfig['description'] ?? '' }}</p>
                </div>
            </div>
            @if(!empty($sectionConfig['button_label']))
                <div class="col-lg-auto d-none d-lg-block">
                    <div class="sec-btn"><a href="{{ $sectionConfig['button_url'] ?? route('blog.index') }}" class="th-btn pill style3 fadeinup wow" data-wow-delay=".4s">{{ $sectionConfig['button_label'] }} <i class="fas fa-arrow-up-right"></i></a></div>
                </div>
            @endif
        </div>
        <div class="slider-area">
            <div class="swiper th-slider has-shadow" id="blogSlider-{{ $section->id }}" data-slider-options='{{ $options }}'>
                <div class="swiper-wrapper">
                    @forelse($posts as $post)
                        <div class="swiper-slide">
                            <div class="blog-card">
                                <div class="blog-img">
                                    <a href="{{ route('blog.show', $post) }}"><img src="{{ $post->featured_image ? Storage::disk('public')->url($post->featured_image) : asset('theme-assets/img/blog/blog_1_1.jpg') }}" alt="{{ $post->title }}"></a>
                                    @if($sectionConfig['show_date'] ?? true)
                                        <div class="date"><a href="{{ route('blog.show', $post) }}">{{ $post->published_at?->format('d M') }}</a></div>
                                    @endif
                                </div>
                                <div class="blog-content">
                                    <div class="blog-meta">
                                        @if($sectionConfig['show_author'] ?? true)
                                            <a href="{{ route('blog.show', $post) }}"><i class="fa-solid fa-user"></i> {{ $post->author?->name ?? config('app.name') }}</a>
                                        @endif
                                    </div>
                                    <h3 class="box-title"><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h3>
                                    <a href="{{ route('blog.show', $post) }}" class="th-btn pill style3">{{ $sectionConfig['card_button_label'] ?? 'Read More' }}</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide"><div class="blog-card"><div class="blog-content"><h3 class="box-title">News and articles will appear here once published.</h3></div></div></div>
                    @endforelse
                </div>
            </div>
            <button data-slider-prev="#blogSlider-{{ $section->id }}" class="slider-arrow slider-prev"><i class="far fa-arrow-left"></i></button>
            <button data-slider-next="#blogSlider-{{ $section->id }}" class="slider-arrow slider-next"><i class="far fa-arrow-right"></i></button>
        </div>
    </div>
</section>
