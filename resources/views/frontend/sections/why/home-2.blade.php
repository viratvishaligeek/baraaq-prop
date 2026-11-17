@php
    $items = [];
    for ($i = 1; $i <= 3; $i++) {
        $items[] = [
            'number' => $sectionConfig['item_'.$i.'_number'] ?? '',
            'icon' => $sectionConfig['item_'.$i.'_icon'] ?? '',
            'title' => $sectionConfig['item_'.$i.'_title'] ?? '',
            'text' => $sectionConfig['item_'.$i.'_text'] ?? '',
            'button' => $sectionConfig['item_'.$i.'_button'] ?? '',
            'url' => $sectionConfig['item_'.$i.'_url'] ?? '#',
        ];
    }
    $columns = (int) ($sectionConfig['columns'] ?? 3) === 2 ? 6 : 4;
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $backgroundImage = $sectionConfig['background_image'] ?? null;
@endphp

<div class="why-sec-2 bg-smoke {{ $sectionConfig['vertical_spacing'] ?? 'space' }} {{ $visibility }}" id="why-sec-{{ $section->id }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if($backgroundImage) data-bg-src="{{ $backgroundImage }}" @endif>
    @if(!empty($sectionConfig['shape_image']))
        <div class="shape-mockup jump d-none d-md-block" style="top: 20%; right: 5%;"><img src="{{ $sectionConfig['shape_image'] }}" alt="img"></div>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="title-area text-center">
                    <p class="sub-title{{ ($sectionConfig['animate_cards'] ?? true) ? ' fadeinup wow' : '' }}" data-wow-duration="1.5s" data-wow-delay="0.1s"><span class="double-line"></span> {{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title{{ ($sectionConfig['animate_cards'] ?? true) ? ' fadeinup wow' : '' }}" data-wow-duration="1.5s" data-wow-delay="0.3s">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
        </div>
        <div class="row gy-30 align-items-center justify-content-center">
            @foreach($items as $item)
                <div class="col-xl-{{ $columns }} col-lg-6 col-md-6{{ ($sectionConfig['animate_cards'] ?? true) ? [' fadeinleft wow',' fadeinup wow',' fadeinright wow'][$loop->index] : '' }}" @if($sectionConfig['animate_cards'] ?? true) data-wow-delay="{{ ['.2s','.4s','.6s'][$loop->index] }}" @endif>
                    <div class="why-card-1 style-2">
                        @if(!empty($sectionConfig['hover_icon']))
                            <div class="hover-icon"><img src="{{ $sectionConfig['hover_icon'] }}" alt="img"></div>
                        @endif
                        <h4 class="count">{{ $item['number'] }}</h4>
                        <div class="why-card-1__icon"><img src="{{ $item['icon'] }}" alt="image"></div>
                        <div class="why-card-1__content">
                            <h3 class="box-title">{{ $item['title'] }}</h3>
                            <p class="box-text">{{ $item['text'] }}</p>
                        </div>
                        <div class="why-card-1__bottom"><a class="th-btn style3 pill bg-white w-100" href="{{ $item['url'] }}">{{ $item['button'] }}</a></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
