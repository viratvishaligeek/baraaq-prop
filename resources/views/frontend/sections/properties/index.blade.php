<section class="popular-sec-1 space">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="title-area text-center">
                    <p class="sub-title text-anime-style-2">
                        <span class="double-line">
                        </span> {{ $sectionConfig['eyebrow'] ?? '' }}
                    </p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                    @if (!empty($sectionConfig['description']))
                        <p class="mt-3">{{ $sectionConfig['description'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        @php
            $sectionProperties = $properties;
            $listingFilter = $sectionConfig['listing_filter'] ?? '';

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
                            @if ($property->featured_image)
                                <a class="popular-popup-image"
                                    href="{{ Storage::disk('public')->url($property->featured_image) }}">
                                    <img src="{{ Storage::disk('public')->url($property->featured_image) }}"
                                        alt="{{ $property->title }}">
                                </a>
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                                    style="min-height: 180px">Image not provided</div>
                            @endif
                            <div class="popular-badge">
                                <img src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}" alt="icon">
                                <p>{{ $property->listing_type === 'rent' ? $sectionConfig['rent_label'] ?? '' : $sectionConfig['sale_label'] ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="property-content">
                            <div class="media-body">
                                <h3 class="box-title">
                                    <a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                </h3>
                                <div class="box-text">
                                    <div class="icon">
                                        <img src="{{ asset('theme-assets/img/icon/popular-location.svg') }}"
                                            alt="icon">
                                    </div>{{ $property->address }} {{ $property->city }}
                                </div>
                            </div>
                            <ul class="property-featured">
                                <li>
                                    <div class="icon">
                                        <img src="{{ asset('theme-assets/img/icon/bed.svg') }}" alt="icon">
                                    </div>{{ $sectionConfig['bed_label'] ?? '' }} {{ $property->bedrooms ?? '—' }}
                                </li>
                                <li>
                                    <div class="icon">
                                        <img src="{{ asset('theme-assets/img/icon/bath.svg') }}" alt="icon">
                                    </div>{{ $sectionConfig['bath_label'] ?? '' }} {{ $property->bathrooms ?? '—' }}
                                </li>
                                <li>
                                    <div class="icon">
                                        <img src="{{ asset('theme-assets/img/icon/sqft.svg') }}" alt="icon">
                                    </div>
                                    {{ $property->area ? number_format((float) $property->area) . ' ' . $property->area_unit : '—' }}
                                </li>
                            </ul>
                            <div class="property-bottom">
                                <h6 class="box-title price-red-shine">
                                    {{ $property->price ? '₹ ' . number_format((float) $property->price, 2) : $property->price_label }}
                                </h6>
                                <a class="th-btn sm style3 pill"
                                    href="{{ route('properties.show', $property) }}">{{ $sectionConfig['card_button_label'] ?? '' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">{{ $sectionConfig['empty_state_text'] ?? '' }}</div>
            @endforelse
        </div>
        @if (!empty($sectionConfig['button_label']))
            <div class="text-center mt-4">
                <a class="th-btn pill style3"
                    href="{{ $sectionConfig['button_url'] ?? route('properties.index') }}">{{ $sectionConfig['button_label'] }}</a>
            </div>
        @endif
    </div>
</section>
