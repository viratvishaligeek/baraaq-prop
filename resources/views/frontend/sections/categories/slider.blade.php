@php
    $variant = $section->source_design === 'home-3' ? 'style-2' : '';
    $wrapperClass = $section->source_design === 'home-3'
        ? 'category-sec-2 position-relative bg-white space-extra-bottom'
        : 'category-sec-1 position-relative bg-smoke';
    $alignment = $sectionConfig['alignment'] ?? 'center';
    $columns = max(1, min(6, (int) ($sectionConfig['columns'] ?? 6)));
    $breakpoints = [
        '0' => ['slidesPerView' => 1],
        '400' => ['slidesPerView' => 2],
        '768' => ['slidesPerView' => 3],
        '992' => ['slidesPerView' => min(4, $columns)],
        '1200' => ['slidesPerView' => min(5, $columns)],
        '1300' => ['slidesPerView' => $columns],
    ];
    $sliderOptions = json_encode(['breakpoints' => $breakpoints], JSON_UNESCAPED_SLASHES);
    $visibility = trim(
        (!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-sm-block' : '').
        (!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').
        (!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : '')
    );
    $backgroundImage = $sectionConfig['background_image'] ?? null;
@endphp

<section
    id="category-sec"
    class="{{ $wrapperClass }} {{ $sectionConfig['vertical_spacing'] ?? 'space' }} {{ $visibility }}"
    @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif
    @if($backgroundImage) data-bg-src="{{ $backgroundImage }}" @endif
>
    @if($section->source_design !== 'home-3')
        <div class="shape-mockup moving d-none d-md-block" data-top="12%" data-left="7%"><img src="{{ asset('theme-assets/img/shape/category-1-left.png') }}" alt=""></div>
        <div class="shape-mockup jump d-none d-md-block" data-top="14%" data-right="2%"><img src="{{ asset('theme-assets/img/shape/category-1-right.png') }}" alt=""></div>
    @endif
    <div class="container">
        <div class="title-area text-{{ $alignment }} mb-50">
            @if(!empty($sectionConfig['eyebrow']))
                <h3 class="sub-title text-anime-style-2"><span class="double-line"></span> {{ $sectionConfig['eyebrow'] }}</h3>
            @endif
            @if(!empty($sectionConfig['heading']))
                <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] }}</h2>
            @endif
            @if(!empty($sectionConfig['description']))
                <p class="mt-3">{{ $sectionConfig['description'] }}</p>
            @endif
        </div>
        <div class="slider-area">
            <div class="swiper th-slider" id="categorySlider-{{ $section->id }}" data-slider-options='{{ $sliderOptions }}'>
                <div class="swiper-wrapper">
                    @foreach(($categories ?? collect())->take((int) ($sectionConfig['items_limit'] ?? 7)) as $category)
                        <div class="swiper-slide">
                            <div class="category-card {{ $variant }}">
                                <div class="box-icon">
                                    <img src="{{ $category->icon_path ? asset('storage/'.$category->icon_path) : asset('theme-assets/img/icon/category_card_'.min(6, $loop->iteration).'.svg') }}" alt="{{ $category->name }}">
                                </div>
                                <h3 class="box-title"><a href="{{ url('/properties?category='.$category->slug) }}">{{ $category->name }}</a></h3>
                                @if($sectionConfig['show_property_count'] ?? true)
                                    <p class="box-subtitle">{{ $category->properties_count }} {{ \Illuminate\Support\Str::plural('Property', $category->properties_count) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($section->source_design === 'home-3')
                    <div class="slider-pagination style-2"></div>
                @endif
            </div>
        </div>
    </div>
</section>
