@php
    $items = ($featuredProperties ?? collect())->take(max(1, min(8, (int) ($sectionConfig['items_limit'] ?? 3))));
    $sliderId = 'home9FeaturedGallery-'.$section->id;
    $options = json_encode(['effect' => 'fade', 'loop' => true, 'autoHeight' => true], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
@if($items->isNotEmpty())
<div class="gallery-sec-4 {{ $visibility }}" id="featured-gallery-{{ $section->id }}">
    <div class="gallery-video-wrap">
        <div class="video-box1">
            <a href="{{ $sectionConfig['video_url'] ?? 'https://www.youtube.com/watch?v=_sI_Ps7JSEk' }}" class="play-btn popup-video"><i class="fa-sharp fa-solid fa-play"></i></a>
        </div>
    </div>
    <div class="swiper th-slider gallery-slider4" id="{{ $sliderId }}" data-slider-options='{{ $options }}'>
        <div class="swiper-wrapper">
            @foreach($items as $property)
                @php
                    $background = $sectionConfig['background_image_'.min($loop->iteration, 3)] ?? asset('theme-assets/img/hero/hero_bg_1_'.min($loop->iteration, 3).'.jpg');
                    $propertyImage = $property->featured_image ? Storage::disk('public')->url($property->featured_image) : asset('theme-assets/img/popular/popular-1-'.min($loop->iteration, 12).'.jpg');
                @endphp
                <div class="swiper-slide">
                    <div class="gallery-4-inner" data-bg-src="{{ $background }}">
                        <div class="container">
                            <div class="row gy-4 align-items-center">
                                <div class="col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                                    <div class="gallery-4-wrap pe-xl-5 me-xl-2" data-ani="slideinup" data-ani-delay="0.2s">
                                        <div class="popular-list-1 style-3 rounded-0">
                                            <div class="property-content">
                                                <div class="title-area text-left mb-50">
                                                    <p class="sub-title style4 bg-white">{{ $sectionConfig['eyebrow'] ?? 'Popular Properties' }}</p>
                                                    <h2 class="sec-title style-2">{{ $sectionConfig['heading'] ?? 'Featured Properties' }}</h2>
                                                </div>
                                                <div class="media-body">
                                                    <h3 class="box-title"><a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a></h3>
                                                    <div class="box-text"><div class="icon"><img src="{{ asset('theme-assets/img/icon/popular-location.svg') }}" alt="icon"></div>{{ $property->address }}{{ $property->city ? ', '.$property->city : '' }}</div>
                                                </div>
                                                <ul class="property-featured">
                                                    <li><div class="icon"><img src="{{ asset('theme-assets/img/icon/bed.svg') }}" alt="icon"></div>Bed {{ $property->bedrooms ?? '—' }}</li>
                                                    <li><div class="icon"><img src="{{ asset('theme-assets/img/icon/bath.svg') }}" alt="icon"></div>Bath {{ $property->bathrooms ?? '—' }}</li>
                                                    <li><div class="icon"><img src="{{ asset('theme-assets/img/icon/sqft.svg') }}" alt="icon"></div>{{ $property->area ? number_format((float) $property->area).' '.$property->area_unit : '—' }}</li>
                                                </ul>
                                                <div class="property-bottom">
                                                    <h6 class="box-title">{{ $property->price_label ?: number_format((float) $property->price) }}</h6>
                                                    <a class="th-btn sm style3 bg-theme text-white" href="{{ route('properties.show', $property) }}">View More</a>
                                                </div>
                                                @if($property->creator)
                                                    <div class="agent-wrap">
                                                        <div class="thumb"><img src="{{ asset('theme-assets/img/gallery/property-agent.jpg') }}" alt="{{ $property->creator->name }}"></div>
                                                        <div class="content"><h4 class="box-title">{{ $property->creator->name }}</h4><p class="box-text">Property Consultant</p></div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-5 col-xl-6 col-xxl-7">
                                    <a href="{{ $propertyImage }}" class="popular-popup-image"><img class="img-fluid" src="{{ $propertyImage }}" alt="{{ $property->title }}"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="slider-pagination-wrapper">
            <button data-slider-prev="#{{ $sliderId }}" class="slider-arrow default slider-prev style-2">prev</button>
            <div class="slider-pagination2"></div>
            <div class="line"></div>
            <button data-slider-next="#{{ $sliderId }}" class="slider-arrow default style-2">Next</button>
        </div>
    </div>
</div>
@endif
