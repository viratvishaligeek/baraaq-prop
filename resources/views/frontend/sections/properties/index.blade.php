<section class="popular-sec-1 space">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="title-area text-center">
                    <p class="sub-title text-anime-style-2">
<span class="double-line">
</span> {{ $sectionConfig['eyebrow'] ?? 'Listing Property' }}</p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? 'Featured Luxury Properties' }}</h2>
                    @if(!empty($sectionConfig['description']))
                        <p class="mt-3">{{ $sectionConfig['description'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        @php
            $sectionProperties = $properties;
            $listingFilter = $sectionConfig['listing_filter'] ?? 'all';

            if ($listingFilter === 'sale') {
                $sectionProperties = $sectionProperties->where('listing_type', 'sale');
            } elseif ($listingFilter === 'rent') {
                $sectionProperties = $sectionProperties->where('listing_type', 'rent');
            } elseif ($listingFilter === 'featured') {
                $sectionProperties = $sectionProperties->where('is_featured', true);
            }

            $sectionProperties = $sectionProperties->take((int) ($sectionConfig['items_limit'] ?? 4));
        @endphp
        <div class="row gy-30 justify-content-center">
            @forelse($sectionProperties as $property)
                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6">
                    <div class="popular-list-1">
                        <div class="thumb-wrapper">
                            <a class="popular-popup-image" href="{{ $property->featured_image ? Storage::disk('public')->url($property->featured_image) : asset('theme-assets/img/popular/popular-1-1.jpg') }}">
                                <img src="{{ $property->featured_image ? Storage::disk('public')->url($property->featured_image) : asset('theme-assets/img/popular/popular-1-1.jpg') }}" alt="{{ $property->title }}">
                            </a>
                            <div class="popular-badge">
                                <img src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}" alt="icon">
                                <p>{{ $property->listing_type === 'rent' ? ($sectionConfig['rent_label'] ?? 'For Rent') : ($sectionConfig['sale_label'] ?? 'For Sale') }}</p>
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
</div>{{ $sectionConfig['bed_label'] ?? 'Bed' }} {{ $property->bedrooms ?? '—' }}</li>
                                <li>
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/bath.svg') }}" alt="icon">
</div>{{ $sectionConfig['bath_label'] ?? 'Bath' }} {{ $property->bathrooms ?? '—' }}</li>
                                <li>
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/sqft.svg') }}" alt="icon">
</div>{{ $property->area ? number_format((float) $property->area).' '.$property->area_unit : '—' }}</li>
                            </ul>
                            <div class="property-bottom">
                                <h6 class="box-title">{{ $property->price ? number_format((float) $property->price, 2) : $property->price_label }}</h6>
                                <a class="th-btn sm style3 pill" href="{{ route('properties.show', $property) }}">{{ $sectionConfig['card_button_label'] ?? 'View More' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">{{ $sectionConfig['empty_state_text'] ?? 'Featured properties will appear here once published.' }}</div>
            @endforelse
        </div>
        @if(!empty($sectionConfig['button_label']))
            <div class="text-center mt-4">
                <a class="th-btn pill style3" href="{{ $sectionConfig['button_url'] ?? route('properties.index') }}">{{ $sectionConfig['button_label'] }}</a>
            </div>
        @endif
    </div>
</section>
