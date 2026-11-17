@php
    $items = [];
    for ($i = 1; $i <= 7; $i++) {
        $items[] = [
            'number' => $sectionConfig['item_'.$i.'_number'] ?? '',
            'icon' => $sectionConfig['item_'.$i.'_icon'] ?? '',
            'title' => $sectionConfig['item_'.$i.'_title'] ?? '',
            'text' => $sectionConfig['item_'.$i.'_text'] ?? '',
            'button' => $sectionConfig['item_'.$i.'_button'] ?? '',
            'url' => $sectionConfig['item_'.$i.'_url'] ?? '#',
        ];
    }
    $items = array_slice($items, 0, max(1, min(7, (int) ($sectionConfig['items_limit'] ?? 7))));
    $columns = max(3, min(4, (int) ($sectionConfig['columns'] ?? 4)));
    $options = json_encode([
        'breakpoints' => [
            '0' => ['slidesPerView' => 1],
            '576' => ['slidesPerView' => 1],
            '768' => ['slidesPerView' => 2],
            '992' => ['slidesPerView' => 2],
            '1200' => ['slidesPerView' => 3],
            '1400' => ['slidesPerView' => $columns],
        ],
        'autoHeight' => true,
    ], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="why-sec-2 bg-smoke {{ $sectionConfig['vertical_spacing'] ?? 'space' }} {{ $visibility }}" id="why-sec-{{ $section->id }}" @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    <div class="container">
        <div class="row justify-content-lg-between gy-4 text-center text-lg-start justify-content-center align-items-center mb-40">
            <div class="col-lg-7">
                <div class="title-area text-left mb-2">
                    <p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
            @if(!empty($sectionConfig['button_label']))
                <div class="col-lg-auto text-center text-lg-start"><a class="th-btn style3 radius fadeinup wow" href="{{ $sectionConfig['button_url'] ?? '#' }}" data-wow-delay="0.5s">{{ $sectionConfig['button_label'] }}</a></div>
            @endif
        </div>
        <div class="slider-area">
            <div class="swiper th-slider has-shadow" id="whySlider-{{ $section->id }}" data-slider-options='{{ $options }}'>
                <div class="swiper-wrapper">
                    @foreach($items as $item)
                        <div class="swiper-slide">
                            <div class="why-card-1 style-2 style-3" @if(!empty($sectionConfig['card_background'])) data-bg-src="{{ $sectionConfig['card_background'] }}" @endif>
                                <h4 class="count">{{ $item['number'] }}</h4>
                                <div class="why-card-1__icon">@if(!empty($item['icon']))<img src="{{ $item['icon'] }}" alt="image">@endif</div>
                                <div class="why-card-1__content">
                                    <h3 class="box-title">{{ $item['title'] }}</h3>
                                    <p class="box-text">{{ $item['text'] }}</p>
                                </div>
                                <div class="why-card-1__bottom"><a class="th-btn style3 radius bg-white" href="{{ $item['url'] }}">{{ $item['button'] }}</a></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="slider-pagination style-2 mt-5"></div>
            </div>
        </div>
    </div>
</div>
