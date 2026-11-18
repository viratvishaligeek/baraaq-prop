@php
    $items = ($properties ?? collect())->take(max(1, min(16, (int) ($sectionConfig['items_limit'] ?? 8))));
    $categories = ($propertyCategories ?? collect())->values();
    $sliderOptions = json_encode(['loop' => false, 'autoplay' => false, 'autoHeight' => true, 'effect' => 'fade'], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
<section class="popular-sec-1 overflow-hidden space {{ $visibility }}" id="properties-{{ $section->id }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="title-area text-center mb-2">
                    <p class="sub-title style4 text-anime-style-1">{{ $sectionConfig['eyebrow'] ?? 'Popular Properties' }}</p>
                    <h2 class="sec-title style-2 mb-4 text-anime-style-3">{{ $sectionConfig['heading'] ?? 'Best Properties Sale' }}</h2>
                    @if(!empty($sectionConfig['description']))
                        <p class="sec-text">{{ $sectionConfig['description'] }}</p>
                    @endif
                    @if(($sectionConfig['show_filters'] ?? true) && $categories->isNotEmpty())
                        <div class="filter-menu style-2 indicator-active filter-menu-active mt-2 justify-content-center fadeinup wow" data-wow-duration="1.3s" data-wow-delay="0.4s">
                            <button data-filter="*" class="th-btn tab-btn active" type="button">View All</button>
                            @foreach($categories as $category)
                                <button data-filter=".cat{{ $loop->iteration }}" class="th-btn tab-btn" type="button">{{ $category->name }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row gy-30 justify-content-center filter-active">
            @forelse($items as $property)
                @php
                    $categoryIndex = $categories->search(fn($category) => (int) $category->id === (int) $property->category_id);
                    $filterClass = $categoryIndex === false ? '' : 'cat'.($categoryIndex + 1);
                    $gallery = $property->images->isNotEmpty() ? $property->images : collect();
                    $fallbackImage = $property->featured_image ? Storage::disk('public')->url($property->featured_image) : asset('theme-assets/img/popular/popular-1-'.(($loop->index % 12) + 1).'.jpg');
                    $sliderId = 'home9PropertyImages-'.$section->id.'-'.$property->id;
                @endphp
                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 filter-item {{ $filterClass }}">
                    <div class="popular-list-1 style-4">
                        <div class="thumb-wrapper">
                            @if($gallery->isNotEmpty())
                                <div class="th-slider" id="{{ $sliderId }}" data-slider-options='{{ $sliderOptions }}'>
                                    <div class="swiper-wrapper">
                                        @foreach($gallery->take(4) as $image)
                                            @php $imageUrl = Storage::disk('public')->url($image->path); @endphp
                                            <div class="swiper-slide">
                                                <a class="popular-popup-image" href="{{ $imageUrl }}"><img src="{{ $imageUrl }}" alt="{{ $image->alt_text ?: $property->title }}"></a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="icon-wrap">
                                        <button class="slider-arrow slider-prev" type="button" data-slider-prev="#{{ $sliderId }}"><i class="far fa-arrow-left"></i></button>
                                        <button class="slider-arrow slider-next" type="button" data-slider-next="#{{ $sliderId }}"><i class="far fa-arrow-right"></i></button>
                                    </div>
                                </div>
                            @else
                                <a class="popular-popup-image" href="{{ $fallbackImage }}"><img src="{{ $fallbackImage }}" alt="{{ $property->title }}"></a>
                            @endif
                            <div class="actions-style-2-wrapper">
                                <div class="actions style-2">
                                    <a href="{{ route('properties.show', $property) }}" class="icon-btn"><span class="action-text">View Property</span><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                    <a href="{{ $fallbackImage }}" class="icon-btn popular-popup-image"><span class="action-text">View image</span><i class="fa-solid fa-camera"></i></a>
                                </div>
                            </div>
                            <div class="popular-badge">
                                <img src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}" alt="icon">
                                <p>{{ $property->listing_type === 'rent' ? 'For Rent' : 'For Sale' }}</p>
                            </div>
                        </div>
                        <div class="property-content">
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
                                <h6 class="box-title">{{ $property->price_label ?: '₹'.number_format((float) $property->price) }}</h6>
                                <a class="th-btn sm style3 pill" href="{{ route('properties.show', $property) }}">View More</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center"><p>Properties will appear here once published.</p></div>
            @endforelse
        </div>
        @if(!empty($sectionConfig['button_label']))
            <div class="text-center mt-4"><a class="th-btn pill style3" href="{{ $sectionConfig['button_url'] ?? route('properties.index') }}">{{ $sectionConfig['button_label'] }}</a></div>
        @endif
    </div>
</section>
