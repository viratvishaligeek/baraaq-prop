@php
    $items = [];
    for ($i = 1; $i <= 4; $i++) {
        $items[] = [
            'image' => $sectionConfig['item_'.$i.'_image'] ?? '',
            'count' => $sectionConfig['item_'.$i.'_count'] ?? '',
            'title' => $sectionConfig['item_'.$i.'_title'] ?? '',
            'url' => $sectionConfig['item_'.$i.'_url'] ?? '#',
        ];
    }
    $animation = ($sectionConfig['animate_cards'] ?? true);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="gallery-sec-3 overflow-hidden bg-smoke {{ $sectionConfig['vertical_spacing'] ?? 'space' }} {{ $visibility }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    @if(!empty($sectionConfig['shape_image']))
        <div class="shape-mockup animation-infinite" style="left: 0; bottom: 0;" data-bg-src="{{ $sectionConfig['shape_image'] }}"></div>
    @endif
    <div class="container">
        <div class="row justify-content-lg-between text-center text-lg-start gy-4 justify-content-center align-items-center mb-40">
            <div class="col-lg-7">
                <div class="title-area text-left mb-2">
                    <p class="sub-title text-anime-style-1"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2>
                    <p class="box-text text-anime-style-3">{{ $sectionConfig['description'] ?? '' }}</p>
                </div>
            </div>
            @if(!empty($sectionConfig['button_label']))
                <div class="col-lg-auto text-center text-lg-start{{ $animation ? ' fadeinup wow' : '' }}" @if($animation) data-wow-duration="1.5s" data-wow-delay="0.4s" @endif>
                    <a class="th-btn style3 radius" href="{{ $sectionConfig['button_url'] ?? '#' }}">{{ $sectionConfig['button_label'] }}</a>
                </div>
            @endif
        </div>
        <div class="row gy-4 justify-content-center align-items-center">
            <div class="col-lg-6">
                <div class="gallery-card style-2{{ $animation ? ' fadeinleft wow' : '' }}" @if($animation) data-wow-delay="0.3s" @endif>
                    @include('frontend.sections.gallery._city-card', ['item' => $items[0]])
                </div>
            </div>
            <div class="col-lg-6">
                <div class="property-values-2-item">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="gallery-card style-2{{ $animation ? ' fadeinup wow' : '' }}" @if($animation) data-wow-delay="0.5s" @endif>
                                @include('frontend.sections.gallery._city-card', ['item' => $items[1]])
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-lg-6">
                            <div class="gallery-card style-2{{ $animation ? ' fadeinleft wow' : '' }}" @if($animation) data-wow-delay="0.7s" @endif>
                                @include('frontend.sections.gallery._city-card', ['item' => $items[2]])
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="gallery-card style-2{{ $animation ? ' fadeinright wow' : '' }}" @if($animation) data-wow-delay="0.7s" @endif>
                                @include('frontend.sections.gallery._city-card', ['item' => $items[3]])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
