@php
    $options = json_encode(['effect' => 'fade', 'loop' => true, 'autoHeight' => true], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="gallery-sec-4 {{ $sectionConfig['vertical_spacing'] ?? '' }} {{ $visibility }}" id="gallery-{{ $section->id }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif>
    @if(($sectionConfig['show_video_button'] ?? true) && !empty($sectionConfig['video_url']))
        <div class="gallery-video-wrap">
<div class="video-box1">
<a href="{{ $sectionConfig['video_url'] }}" class="play-btn popup-video">
<i class="fa-sharp fa-solid fa-play">
</i>
</a>
</div>
</div>
    @endif
    <div class="swiper th-slider gallery-slider4" id="gallerySlider4-{{ $section->id }}" data-slider-options='{{ $options }}'>
        <div class="swiper-wrapper">
            @forelse($featuredProperties as $property)
                @php
                    $propertyImagePath = $property->featured_image ?: $property->images->first()?->path;
                    $propertyImage = $propertyImagePath ? Storage::disk('public')->url($propertyImagePath) : null;
                    $slideBackground = $sectionConfig['slide_background_'.($loop->iteration)] ?? null;
                @endphp
                <div class="swiper-slide">
                    <div class="gallery-4-inner" @if($slideBackground) data-bg-src="{{ $slideBackground }}" @elseif($propertyImage) data-bg-src="{{ $propertyImage }}" @endif>
                        <div class="container">
                            <div class="row gy-4 align-items-center">
                                <div class="col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                                    <div class="gallery-4-wrap pe-xl-5 me-xl-2" @if($sectionConfig['animate_slides'] ?? true) data-ani="slideinup" data-ani-delay="0.2s" @endif>
                                        <div class="popular-list-1 style-3">
                                            <div class="property-content">
                                                <div class="title-area text-left mb-50">
                                                    <p class="sub-title">
<span class="double-line">
</span>{{ $sectionConfig['eyebrow'] ?? 'Popular Properties' }}</p>
                                                    <h2 class="sec-title">{{ $sectionConfig['heading'] ?? 'Featured Properties' }}</h2>
                                                </div>
                                                <div class="media-body">
                                                    <h3 class="box-title">
<a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
</h3>
                                                    <div class="box-text">
<div class="icon">@if(!empty($sectionConfig['location_icon']))<img src="{{ $sectionConfig['location_icon'] }}" alt="icon">@endif</div>{{ $property->address }}, {{ $property->city }}</div>
                                                </div>
                                                <ul class="property-featured">
                                                    <li>
<div class="icon">@if(!empty($sectionConfig['bed_icon']))<img src="{{ $sectionConfig['bed_icon'] }}" alt="icon">@endif</div>Bed {{ $property->bedrooms ?? '—' }}</li>
                                                    <li>
<div class="icon">@if(!empty($sectionConfig['bath_icon']))<img src="{{ $sectionConfig['bath_icon'] }}" alt="icon">@endif</div>Bath {{ $property->bathrooms ?? '—' }}</li>
                                                    <li>
<div class="icon">@if(!empty($sectionConfig['area_icon']))<img src="{{ $sectionConfig['area_icon'] }}" alt="icon">@endif</div>{{ $property->area ? number_format((float) $property->area).' '.$property->area_unit : '—' }}</li>
                                                </ul>
                                                <div class="property-bottom">
                                                    <h6 class="box-title">{{ $property->price ? number_format((float) $property->price, 2) : $property->price_label }}</h6>
                                                    <a class="th-btn sm style3 radius bg-theme text-white" href="{{ route('properties.show', $property) }}">{{ $sectionConfig['card_button_label'] ?? 'View More' }}</a>
                                                </div>
                                                @if(($sectionConfig['show_agent'] ?? true) && $property->creator)
                                                    <div class="agent-wrap">
<div class="thumb">@if(!empty($sectionConfig['agent_avatar_image']))<img src="{{ $sectionConfig['agent_avatar_image'] }}" alt="{{ $property->creator->name }}">@endif</div>
<div class="content">
<h4 class="box-title">{{ $property->creator->name }}</h4>
<p class="box-text">{{ $sectionConfig['agent_role_label'] ?? 'Sale Agent' }}</p>
</div>
</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="swiper-slide">
<div class="gallery-4-inner">
<div class="container">
<div class="gallery-4-wrap">
<div class="popular-list-1 style-3">
<div class="property-content">
<h2 class="sec-title">Featured Properties</h2>
<p class="box-text">Featured published listings will appear here.</p>
</div>
</div>
</div>
</div>
</div>
</div>
            @endforelse
        </div>
        <div class="slider-pagination-wrapper">
            <button data-slider-prev="#gallerySlider4-{{ $section->id }}" class="slider-arrow default slider-prev style-2">prev</button>
            <div class="slider-pagination2">
</div>
<div class="line">
</div>
            <button data-slider-next="#gallerySlider4-{{ $section->id }}" class="slider-arrow default style-2">Next</button>
        </div>
    </div>
</div>
