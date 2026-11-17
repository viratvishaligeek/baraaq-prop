@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $mapProperties = ($mapProperties ?? collect())->take(max(1, min(4, (int) ($sectionConfig['map_card_limit'] ?? 4))));
    $tabId = 'home3-search-'.$section->id;
    $animateContent = $sectionConfig['animate_content'] ?? true;
@endphp

<div class="th-hero-wrapper bg-smoke hero-3 {{ $visibility }}" id="hero-{{ $section->id }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif>
    @if(!empty($sectionConfig['right_shape_image']))<div class="shape-mockup d-none d-xl-block" data-top="0" data-right="0"><img src="{{ $sectionConfig['right_shape_image'] }}" alt="img"></div>@endif
    @if(!empty($sectionConfig['left_shape_image']))<div class="shape-mockup spin d-none d-xxl-block" data-left="3%" data-bottom="5%"><img src="{{ $sectionConfig['left_shape_image'] }}" alt="img"></div>@endif
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="{{ ($sectionConfig['show_map'] ?? true) ? 'col-xl-6' : 'col-xl-12' }} col-lg-12">
                <div class="hero-style3">
                    <div class="title-area text-left mb-50">
                        <p class="sub-title{{ $animateContent ? ' fadeinup wow' : '' }}" data-wow-delay="0.2s"><span class="double-line"></span> {{ $sectionConfig['eyebrow'] ?? '' }}</p>
                        <h1 class="hero-title{{ $animateContent ? ' fadeinup wow' : '' }}" data-wow-delay="0.3s">{{ $sectionConfig['heading_prefix'] ?? '' }} <span class="title2 text-theme">{{ $sectionConfig['heading_highlight'] ?? '' }}</span> {{ $sectionConfig['heading_suffix'] ?? '' }}</h1>
                    </div>
                    @if($sectionConfig['show_search_form'] ?? true)
                    <div class="hero-3-search-wrap">
                        <ul class="nav nav-tabs custom{{ $animateContent ? ' fadeinup wow' : '' }}" id="{{ $tabId }}" role="tablist" data-wow-delay="0.5s">
                            @foreach(['buy' => 'buy_tab_label', 'sell' => 'sell_tab_label', 'rent' => 'rent_tab_label'] as $tab => $labelKey)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $tabId }}-{{ $tab }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $tabId }}-{{ $tab }}" type="button" role="tab" aria-controls="{{ $tabId }}-{{ $tab }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $sectionConfig[$labelKey] ?? ucfirst($tab) }}</button>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content{{ $animateContent ? ' fadeinup wow' : '' }}" id="{{ $tabId }}-content" data-wow-delay="0.7s">
                            @foreach(['buy' => 'sale', 'sell' => 'sale', 'rent' => 'rent'] as $tab => $listingType)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}-{{ $tab }}" role="tabpanel" aria-labelledby="{{ $tabId }}-{{ $tab }}-tab">
                                    <div class="hero-search-wrapper">
                                        <form action="{{ route('properties.index') }}" method="GET">
                                            @if($listingType)<input type="hidden" name="listing_type" value="{{ $listingType }}">@endif
                                            <div class="hero-search-group-wrapper">
                                                <div class="form-group">
                                                    <label for="{{ $tabId }}-keyword-{{ $tab }}">{{ $sectionConfig['keyword_label'] ?? 'Keyword' }}</label>
                                                    <div class="style-border style-radius col-12"><input type="text" class="form-control" name="q" id="{{ $tabId }}-keyword-{{ $tab }}" placeholder="{{ $sectionConfig['keyword_placeholder'] ?? 'Looking For?' }}"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="{{ $tabId }}-location-{{ $tab }}">{{ $sectionConfig['location_label'] ?? 'Location' }}</label>
                                                    <div class="style-border style-radius col-12"><input type="text" class="form-control" name="city" id="{{ $tabId }}-location-{{ $tab }}" placeholder="{{ $sectionConfig['location_placeholder'] ?? 'Find a location' }}" list="{{ $tabId }}-cities"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="{{ $tabId }}-price-{{ $tab }}">{{ $sectionConfig['pricing_label'] ?? 'Pricing' }}</label>
                                                    <div class="style-border style-radius col-12"><input type="number" min="0" step="1" class="form-control" name="max_price" id="{{ $tabId }}-price-{{ $tab }}" placeholder="{{ $sectionConfig['pricing_placeholder'] ?? 'Maximum price' }}"></div>
                                                </div>
                                                <div class="advance-btn-wrapper">
                                                    <div class="advance-search-btn advance-search-active"><div class="th-btn radius style3 bg-smoke search-btn-item">{{ $sectionConfig['advanced_label'] ?? 'Advanced' }} <i class="fa-solid fa-sliders-up"></i></div></div>
                                                    <button class="th-btn radius bg-theme"><i class="far fa-search me-2"></i> {{ $sectionConfig['search_button_label'] ?? 'Search' }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                            <datalist id="{{ $tabId }}-cities">
                                @foreach(($cities ?? collect()) as $city)<option value="{{ $city }}">@endforeach
                            </datalist>
                            <div class="advance-search-wrapper stye-2 hero-3-ad-search">
                                <form action="{{ route('properties.index') }}" method="GET" class="row gy-3">
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-listing-type">Listing status</label>
                                        <select name="listing_type" id="{{ $tabId }}-listing-type" class="form-select nice-select">
                                            <option value="">Any listing status</option>
                                            @foreach(($listingTypes ?? collect()) as $listingTypeOption)
                                                <option value="{{ $listingTypeOption }}">{{ ucfirst($listingTypeOption) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-type">{{ $sectionConfig['advanced_type_label'] ?? 'Property Type' }}</label>
                                        <select name="property_type" id="{{ $tabId }}-type" class="form-select nice-select">
                                            <option value="">{{ $sectionConfig['advanced_type_label'] ?? 'Any property type' }}</option>
                                            @foreach(($propertyTypes ?? collect()) as $type)<option value="{{ $type }}">{{ ucfirst($type) }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-category">Category</label>
                                        <select name="category" id="{{ $tabId }}-category" class="form-select nice-select">
                                            <option value="">All categories</option>
                                            @foreach(($propertyCategories ?? collect()) as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-bedrooms">{{ $sectionConfig['advanced_bedrooms_label'] ?? 'Maximum bedrooms' }}</label>
                                        <select name="max_bedrooms" id="{{ $tabId }}-bedrooms" class="form-select nice-select">
                                            <option value="">Any bedroom count</option>
                                            @foreach(($bedroomOptions ?? collect()) as $bedrooms)<option value="{{ $bedrooms }}">{{ $bedrooms }} {{ $bedrooms === 1 ? 'Bedroom' : 'Bedrooms' }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-advanced-location">{{ $sectionConfig['advanced_location_label'] ?? 'Location' }}</label>
                                        <select name="city" id="{{ $tabId }}-advanced-location" class="form-select nice-select">
                                            <option value="">All locations</option>
                                            @foreach(($cities ?? collect()) as $city)<option value="{{ $city }}">{{ $city }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-min-price">Minimum price</label>
                                        <input class="form-control" type="number" min="0" step="0.01" name="min_price" id="{{ $tabId }}-min-price" placeholder="No minimum">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-max-price">Maximum price</label>
                                        <input class="form-control" type="number" min="0" step="0.01" name="max_price" id="{{ $tabId }}-max-price" placeholder="No maximum">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-min-area">Minimum area</label>
                                        <input class="form-control" type="number" min="0" step="0.01" name="min_area" id="{{ $tabId }}-min-area" placeholder="Any size">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="{{ $tabId }}-max-area">Maximum area</label>
                                        <input class="form-control" type="number" min="0" step="0.01" name="max_area" id="{{ $tabId }}-max-area" placeholder="Any size">
                                    </div>
                                    <div class="col-12 text-end"><button type="submit" class="th-btn radius bg-theme"><i class="far fa-search me-2"></i> {{ $sectionConfig['search_button_label'] ?? 'Search' }}</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($sectionConfig['show_counters'] ?? true)
                    <div class="hero-3-counter-wrap{{ $animateContent ? ' fadeinup wow' : '' }}" data-wow-delay="0.9s">
                        @for($i = 1; $i <= 4; $i++)
                            <div class="hero-3-counter-item">
                                <h2 class="counter"><span class="counter-number">{{ $sectionConfig['counter_'.$i.'_value'] ?? 0 }}</span> {{ $sectionConfig['counter_'.$i.'_suffix'] ?? '' }}</h2>
                                <span class="box-text">{{ $sectionConfig['counter_'.$i.'_label'] ?? '' }}</span>
                            </div>
                        @endfor
                    </div>
                    @endif
                </div>
            </div>
            @if($sectionConfig['show_map'] ?? true)
            <div class="col-xl-6 col-lg-12">
                <div class="map-wrapper{{ $animateContent ? ' fadeinright wow' : '' }}" data-wow-delay="0.5s">
                    @if(!empty($sectionConfig['map_image']))<img src="{{ $sectionConfig['map_image'] }}" alt="Property locations map">@endif
                    <ul class="map-marker-wrap">
                        @foreach($mapProperties as $index => $property)
                            @php
                                $markerIndex = $index + 1;
                                $latSpan = (float) ($mapBounds['max_lat'] ?? 0) - (float) ($mapBounds['min_lat'] ?? 0);
                                $lngSpan = (float) ($mapBounds['max_lng'] ?? 0) - (float) ($mapBounds['min_lng'] ?? 0);
                                $markerLeft = abs($lngSpan) < 0.000001 ? 50 : 12 + (((float) $property->longitude - (float) $mapBounds['min_lng']) / $lngSpan) * 76;
                                $markerTop = abs($latSpan) < 0.000001 ? 50 : 14 + (((float) $mapBounds['max_lat'] - (float) $property->latitude) / $latSpan) * 72;
                            @endphp
                            <li class="map-icon map-icon{{ $markerIndex }}" style="left: {{ number_format($markerLeft, 2, '.', '') }}%;top: {{ number_format($markerTop, 2, '.', '') }}%">
                                @if(!empty($sectionConfig['map_marker_image']))<img src="{{ $sectionConfig['map_marker_image'] }}" alt="{{ $property->title }}">@endif
                            </li>
                        @endforeach
                    </ul>
                    @foreach($mapProperties as $index => $property)
                        @php
                            $mapIndex = $index + 1;
                            $propertyImagePath = $property->featured_image ?: $property->images->first()?->path;
                            $propertyImage = $propertyImagePath ? Storage::disk('public')->url($propertyImagePath) : ($sectionConfig['map_property_fallback_image'] ?? null);
                        @endphp
                        <div class="map-popular-list map-popular-list{{ $mapIndex }} {{ ($mapIndex === 4 || ($loop->last && $mapProperties->count() < 4)) ? 'show' : '' }}">
                            <div class="popular-list-1">
                                <div class="thumb-wrapper">
                                    @if($propertyImage)<img src="{{ $propertyImage }}" alt="{{ $property->title }}">@endif
                                    <div class="actions"><a href="{{ route('properties.show', $property) }}" class="icon-btn" aria-label="View {{ $property->title }}"><i class="fas fa-heart"></i></a></div>
                                    <div class="price"><h6 class="box-title">{{ $property->price ? number_format((float) $property->price, 2) : $property->price_label }}</h6></div>
                                </div>
                                <div class="property-content">
                                    <div class="media-body">
                                        <h3 class="box-title"><a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a></h3>
                                        <div class="box-text"><div class="icon">@if(!empty($sectionConfig['map_location_icon']))<img src="{{ $sectionConfig['map_location_icon'] }}" alt="icon">@endif</div>{{ $property->address }}, {{ $property->city }}</div>
                                    </div>
                                    <ul class="property-featured">
                                        <li><div class="icon">@if(!empty($sectionConfig['map_bed_icon']))<img src="{{ $sectionConfig['map_bed_icon'] }}" alt="icon">@endif</div>Bed {{ $property->bedrooms ?? '—' }}</li>
                                        <li><div class="icon">@if(!empty($sectionConfig['map_bath_icon']))<img src="{{ $sectionConfig['map_bath_icon'] }}" alt="icon">@endif</div>Bath {{ $property->bathrooms ?? '—' }}</li>
                                        <li><div class="icon">@if(!empty($sectionConfig['map_area_icon']))<img src="{{ $sectionConfig['map_area_icon'] }}" alt="icon">@endif</div>{{ $property->area ? number_format((float) $property->area).' '.$property->area_unit : '—' }}</li>
                                    </ul>
                                </div>
                                <div class="arrow-bottom"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
