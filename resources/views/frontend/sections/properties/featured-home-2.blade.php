@php
    $listingFilter = $sectionConfig['listing_filter'] ?? 'all';
    if ($listingFilter === 'sale') $sectionProperties = $properties->where('listing_type', 'sale');
    elseif ($listingFilter === 'rent') $sectionProperties = $properties->where('listing_type', 'rent');
    elseif ($listingFilter === 'featured') $sectionProperties = $properties->where('is_featured', true);
    else $sectionProperties = $properties;
    $sectionProperties = $sectionProperties->take((int) ($sectionConfig['items_limit'] ?? 4));
    $options = json_encode([
        'breakpoints' => [
            '0' => ['slidesPerView' => 1],
            '576' => ['slidesPerView' => 1],
            '768' => ['slidesPerView' => 1],
            '992' => ['slidesPerView' => 1],
            '1200' => ['slidesPerView' => 2],
            '1400' => ['slidesPerView' => 2],
        ],
        'autoplay' => (bool) ($sectionConfig['autoplay'] ?? true),
        'autoHeight' => true,
        'spaceBetween' => 60,
    ], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<section class="popular-sec-1 {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden {{ $visibility }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    <div class="container">
        <div class="row justify-content-lg-between gy-4 justify-content-center align-items-center mb-40">
            <div class="col-lg-6">
                <div class="title-area text-left mb-2">
                    <p class="sub-title feature-sec-v2 text-anime-style-2">
<span class="double-line">
</span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title feature-sec-v2 text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
            @if(!empty($sectionConfig['button_label']))
                <div class="col-lg-auto">
<a href="{{ $sectionConfig['button_url'] ?? '#' }}" class="th-btn tab-btn style3 pill feature-sec-v2 fadeinup wow" data-wow-delay="0.4s">{{ $sectionConfig['button_label'] }}</a>
</div>
            @endif
        </div>
        <div class="slider-area">
            <div class="swiper th-slider has-shadow" id="featuresSlider-{{ $section->id }}" data-slider-options='{{ $options }}'>
                <div class="swiper-wrapper">
                    @forelse($sectionProperties as $property)
                        @php
                            $imagePath = $property->featured_image ?: $property->images->first()?->path;
                        @endphp
                        <div class="swiper-slide">
                            <div class="popular-list-1 style-2{{ ($sectionConfig['animate_cards'] ?? true) ? ' fadeinup wow' : '' }}">
                                <div class="thumb-wrapper">
                                    <img src="{{ $imagePath ? Storage::disk('public')->url($imagePath) : asset('theme-assets/img/popular/feature-1-1.jpg') }}" alt="{{ $property->title }}">
                                    <div class="popular-badge">
<img src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}" alt="icon">
<p>{{ $property->listing_type === 'rent' ? 'For Rent' : 'For Sale' }}</p>
</div>
                                </div>
                                <div class="property-content">
                                    <div class="media-body">
                                        <h3 class="box-title">
<a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
</h3>
                                        <div class="box-text">
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/popular-location.svg') }}" alt="icon">
</div>{{ $property->address }} {{ $property->city }}</div>
                                    </div>
                                    <ul class="property-featured">
                                        <li>
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/bed.svg') }}" alt="icon">
</div>Bed {{ $property->bedrooms ?? '—' }}</li>
                                        <li>
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/bath.svg') }}" alt="icon">
</div>Bath {{ $property->bathrooms ?? '—' }}</li>
                                        <li>
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/sqft.svg') }}" alt="icon">
</div>{{ $property->area ? number_format((float) $property->area).' '.$property->area_unit : '—' }}</li>
                                    </ul>
                                    <div class="property-bottom">
                                        <h6 class="box-title">{{ $property->price ? number_format((float) $property->price, 2) : $property->price_label }}</h6>
                                        <a class="th-btn sm style3 pill" href="{{ route('properties.show', $property) }}">{{ $sectionConfig['card_button_label'] ?? 'View More' }}</a>
                                    </div>
                                    @if($sectionConfig['show_agent'] ?? true)
                                        <div class="agent-wrap">
                                            <div class="agent-thumb">
<img src="{{ asset('theme-assets/img/popular/agent-thumb-'.($loop->iteration % 2 ? '1' : '2').'.jpg') }}" alt="Agent">
</div>
                                            <div class="agent-content">
<h4 class="box-title">{{ $property->creator?->name ?? 'Property Consultant' }}</h4>
<p class="box-text">{{ $property->listing_type === 'rent' ? 'Rental Agent' : 'Sale Agent' }}</p>
</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
<div class="popular-list-1 style-2">
<div class="property-content">Featured properties will appear here once published.</div>
</div>
</div>
                    @endforelse
                </div>
            </div>
            <button data-slider-prev="#featuresSlider-{{ $section->id }}" class="slider-arrow slider-prev">
<i class="far fa-arrow-left">
</i>
</button>
            <button data-slider-next="#featuresSlider-{{ $section->id }}" class="slider-arrow slider-next">
<i class="far fa-arrow-right">
</i>
</button>
        </div>
    </div>
</section>
