@php
    $columns = (int) ($sectionConfig['columns'] ?? 4) === 3 ? 4 : 3;
    $listingFilter = $sectionConfig['listing_filter'] ?? 'all';
    if ($listingFilter === 'sale') $sectionProperties = $properties->where('listing_type', 'sale');
    elseif ($listingFilter === 'rent') $sectionProperties = $properties->where('listing_type', 'rent');
    elseif ($listingFilter === 'featured') $sectionProperties = $properties->where('is_featured', true);
    else $sectionProperties = $properties;
    $sectionProperties = $sectionProperties->take((int) ($sectionConfig['items_limit'] ?? 8));
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<section class="popular-sec-1 {{ $sectionConfig['vertical_spacing'] ?? 'space-bottom' }} {{ $visibility }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="title-area text-center mb-2">
                    <p class="sub-title mt-2 text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title mb-4 text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                    @if($sectionConfig['show_filters'] ?? true)
                        <div class="filter-menu style-2 indicator-active filter-menu-active mt-2 justify-content-center{{ ($sectionConfig['animate_content'] ?? true) ? ' fadeinup wow' : '' }}" data-wow-delay="0.5s">
                            <button data-filter="*" class="th-btn tab-btn active" type="button">View All</button>
                            @foreach(($propertyCategories ?? collect()) as $category)
                                <button data-filter=".cat-{{ $category->slug }}" class="th-btn tab-btn" type="button">{{ $category->name }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row gy-30 justify-content-center filter-active">
            @forelse($sectionProperties as $property)
                @php
                    $images = $property->images->pluck('path')->filter()->values();
                    if ($images->isEmpty() && $property->featured_image) $images = collect([$property->featured_image]);
                    $images = $images->take(5);
                    $firstImagePath = $images->first();
                    $firstImageUrl = $firstImagePath ? Storage::disk('public')->url($firstImagePath) : ($sectionConfig['fallback_image'] ?? null);
                    $categoryClass = $property->category?->slug ?: \Illuminate\Support\Str::slug($property->property_type ?: 'other');
                @endphp
                <div class="col-xxl-{{ $columns }} col-xl-4 col-lg-6 col-md-6 filter-item cat-{{ $categoryClass }}">
                    <div class="popular-list-1">
                        <div class="thumb-wrapper">
                            @if(($sectionConfig['show_image_slider'] ?? true) && $images->count() > 1)
                                <div class="th-slider" data-slider-options='{"loop":false,"autoplay":false,"autoHeight":true,"effect":"fade"}'>
                                    <div class="swiper-wrapper">
                                        @foreach($images as $imagePath)
                                            <div class="swiper-slide"><a class="popular-popup-image" href="{{ Storage::disk('public')->url($imagePath) }}"><img src="{{ Storage::disk('public')->url($imagePath) }}" alt="{{ $property->title }}"></a></div>
                                        @endforeach
                                    </div>
                                    <div class="icon-wrap"><button class="slider-arrow slider-prev"><i class="far fa-arrow-left"></i></button> <button class="slider-arrow slider-next"><i class="far fa-arrow-right"></i></button></div>
                                </div>
                            @else
                                @if($firstImageUrl)<a class="popular-popup-image" href="{{ $firstImageUrl }}"><img src="{{ $firstImageUrl }}" alt="{{ $property->title }}"></a>@endif
                            @endif
                            @if($sectionConfig['show_favorite_action'] ?? true)
                                <div class="actions"><a href="{{ route('properties.show', $property) }}" class="icon-btn" aria-label="View property"><i class="fas fa-heart"></i></a></div>
                            @endif
                            @if($sectionConfig['show_gallery_action'] ?? true)
                                <div class="actions-style-2-wrapper"><div class="actions style-2">@if($firstImageUrl)<a href="{{ $firstImageUrl }}" class="icon-btn popular-popup-image"><span class="action-text">View all img</span><i class="fa-solid fa-camera"></i></a>@endif</div></div>
                            @endif
                            @if($sectionConfig['show_property_badge'] ?? true)
                                <div class="popular-badge"><img src="{{ $sectionConfig['badge_icon'] ?? '' }}" alt="icon"><p>{{ $property->listing_type === 'rent' ? 'For Rent' : 'For Sale' }}</p></div>
                            @endif
                        </div>
                        <div class="property-content">
                            <div class="media-body">
                                <h3 class="box-title"><a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a></h3>
                                <div class="box-text"><div class="icon"><img src="{{ $sectionConfig['location_icon'] ?? '' }}" alt="icon"></div>{{ $property->address }} {{ $property->city }}</div>
                            </div>
                            <ul class="property-featured">
                                <li><div class="icon"><img src="{{ $sectionConfig['bed_icon'] ?? '' }}" alt="icon"></div>Bed {{ $property->bedrooms ?? '—' }}</li>
                                <li><div class="icon"><img src="{{ $sectionConfig['bath_icon'] ?? '' }}" alt="icon"></div>Bath {{ $property->bathrooms ?? '—' }}</li>
                                <li><div class="icon"><img src="{{ $sectionConfig['area_icon'] ?? '' }}" alt="icon"></div>{{ $property->area ? number_format((float) $property->area).' '.$property->area_unit : '—' }}</li>
                            </ul>
                            <div class="property-bottom">
                                <h6 class="box-title">{{ $property->price ? number_format((float) $property->price, 2) : $property->price_label }}</h6>
                                <a class="th-btn sm style3 radius" href="{{ route('properties.show', $property) }}">{{ $sectionConfig['card_button_label'] ?? 'View More' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">Published properties will appear here once available.</div>
            @endforelse
        </div>
    </div>
</section>
