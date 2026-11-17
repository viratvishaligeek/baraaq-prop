@php
    $items = [];
    for ($i = 1; $i <= 8; $i++) {
        $items[] = [
            'image' => $sectionConfig['item_'.$i.'_image'] ?? '',
            'count' => $sectionConfig['item_'.$i.'_count'] ?? '',
            'title' => $sectionConfig['item_'.$i.'_title'] ?? '',
            'url' => $sectionConfig['item_'.$i.'_url'] ?? '#',
        ];
    }
    $items = array_slice($items, 0, max(1, min(8, (int) ($sectionConfig['items_limit'] ?? 8))));
    $slides = max(2, min(4, (int) ($sectionConfig['columns'] ?? 4)));
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $backgroundImage = $sectionConfig['background_image'] ?? null;
    $options = json_encode([
        'centeredSlides' => false,
        'loop' => (bool) ($sectionConfig['loop'] ?? true),
        'breakpoints' => [
            '0' => ['slidesPerView' => 1],
            '576' => ['slidesPerView' => 1],
            '768' => ['slidesPerView' => 2],
            '992' => ['slidesPerView' => 3],
            '1200' => ['slidesPerView' => min(3, $slides)],
            '1400' => ['slidesPerView' => $slides],
        ],
    ], JSON_UNESCAPED_SLASHES);
@endphp

<div class="gallery-sec-2 overflow-hidden bg-smoke {{ $sectionConfig['vertical_spacing'] ?? 'space' }} {{ $visibility }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if($backgroundImage) data-bg-src="{{ $backgroundImage }}" @endif>
    <div class="shape-mockup moving d-none d-md-block" style="top: 12%; left: 7%;">
<img src="{{ $sectionConfig['shape_image'] ?? '/theme-assets/img/shape/gallery-sec-1-left.png' }}" alt="img">
</div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="title-area text-center">
                    <p class="sub-title text-anime-style-1">
<span class="double-line">
</span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title mb-2 text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2>
                    <p class="box-text text-anime-style-3">{{ $sectionConfig['description'] ?? '' }}</p>
                </div>
            </div>
        </div>
        <div class="gallery-2-slider-wrap">
            <div class="slider-area">
                <div class="swiper th-slider has-shadow gallery-1" id="neighborhoodSlider-{{ $section->id }}" data-slider-options='{{ $options }}'>
                    <div class="swiper-wrapper">
                        @foreach($items as $item)
                            <div class="swiper-slide">
                                <div class="gallery-card style-2">
                                    <div class="gallery-img">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
                                        <div class="gallery-content">
                                            <div class="gallery-content-wrapper">
                                                <div class="left-content">
                                                    <p class="box-text">{{ $item['count'] }}</p>
                                                    <h2 class="box-title">
<a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
</h2>
                                                </div>
                                                <div class="link">
<a href="{{ $item['url'] }}">
<i class="far fa-arrow-right">
</i>
</a>
</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button data-slider-prev="#neighborhoodSlider-{{ $section->id }}" class="slider-arrow slider-prev style2">
<i class="far fa-arrow-left">
</i>
</button>
                    <button data-slider-next="#neighborhoodSlider-{{ $section->id }}" class="slider-arrow slider-next style2">
<i class="far fa-arrow-right">
</i>
</button>
                </div>
            </div>
        </div>
    </div>
</div>
