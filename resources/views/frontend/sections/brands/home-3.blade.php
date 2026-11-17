@php
    $items = [];
    for ($i = 1; $i <= min(6, (int) ($sectionConfig['items_limit'] ?? 6)); $i++) {
        if (!empty($sectionConfig['item_'.$i.'_image'])) {
            $items[] = ['image' => $sectionConfig['item_'.$i.'_image'], 'url' => $sectionConfig['item_'.$i.'_url'] ?? '#'];
        }
    }
    $options = json_encode(['breakpoints' => [
        '0' => ['slidesPerView' => (int) ($sectionConfig['slides_mobile'] ?? 2)],
        '576' => ['slidesPerView' => (int) ($sectionConfig['slides_mobile'] ?? 2)],
        '768' => ['slidesPerView' => (int) ($sectionConfig['slides_tablet'] ?? 3)],
        '992' => ['slidesPerView' => (int) ($sectionConfig['slides_tablet'] ?? 3)],
        '1200' => ['slidesPerView' => (int) ($sectionConfig['slides_desktop'] ?? 6)],
        '1400' => ['slidesPerView' => (int) ($sectionConfig['slides_desktop'] ?? 6)],
    ]], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="brand-area-1 {{ $sectionConfig['vertical_spacing'] ?? '' }} overflow-hidden {{ $visibility }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif>
    @if(!empty($sectionConfig['shape_image']))
        <div class="shape-mockup spin z-index-1 d-none d-xxl-block z-index-5" style="top: 5%; right: 0%;">
<img src="{{ $sectionConfig['shape_image'] }}" alt="Decorative shape">
</div>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="swiper th-slider" id="brand-slider-{{ $section->id }}" data-slider-options='{{ $options }}'>
                    <div class="swiper-wrapper">
                        @for($repeat = 0; $repeat < 2; $repeat++)
                            @foreach($items as $item)
                                <div class="swiper-slide">
<div class="brand-box">
<a href="{{ $item['url'] }}">
<img src="{{ $item['image'] }}" alt="Brand logo">
</a>
</div>
</div>
                            @endforeach
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
