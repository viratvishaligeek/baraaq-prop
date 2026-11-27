@extends('frontend.layouts.theme')
@section('title', \App\Models\Setting::read('properties_meta_title', 'Properties | ' . config('app.name')))
@section('meta_description',
    \App\Models\Setting::read(
    'properties_meta_description',
    'Browse published property
    listings.',
    ))
@section('canonical_url', route('properties.index'))
@section('content')
    <div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/blog/breadcrumb-bg.jpg') }}">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Properties</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Properties</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="search-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-10">
                    <div class="search-wrap">
                        <div class="search-wrap-area-top">
                            <ul class="nav nav-tabs custom" id="propertySearchTabs" role="tablist">
                                <li class="nav-item" role="presentation"><button class="nav-link active" type="button" data-listing-type="sale" aria-pressed="true">Buy</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" type="button" data-listing-type="sale" aria-pressed="false">Sell</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" type="button" data-listing-type="rent" aria-pressed="false">Rent</button></li>
                            </ul>
                        </div>
                        <form method="GET" action="{{ route('properties.index') }}" id="property-search-form">
                            <input type="hidden" name="listing_type" id="property-listing-type" value="{{ request('listing_type', 'sale') }}">
                            <div class="select-group-wrapper">
                                <div class="form-group">
                                    <label for="property-keyword">Keyword</label>
                                    <input class="form-control" type="search" id="property-keyword" name="q" value="{{ request('q') }}" placeholder="Property name, reference or keyword">
                                </div>
                                <div class="form-group">
                                    <label for="property_type">Property Type</label>
                                    <select name="property_type" id="property_type" class="form-select nice-select">
                                        <option value="">All Property Types</option>
                                        @foreach ($propertyTypes as $propertyType)
                                            <option value="{{ $propertyType }}" @selected(request('property_type') === $propertyType)>{{ \Illuminate\Support\Str::headline($propertyType) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="property-city">Location</label>
                                    <select name="city" id="property-city" class="form-select nice-select">
                                        <option value="">All Locations</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city }}" @selected(request('city', request('location')) === $city)>{{ $city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="min_bedrooms">Min Bedrooms</label>
                                    <select name="min_bedrooms" id="min_bedrooms" class="form-select nice-select">
                                        <option value="">Any</option>
                                        @foreach ($bedroomOptions as $bedroom)
                                            <option value="{{ $bedroom }}" @selected((string) request('min_bedrooms') === (string) $bedroom)>{{ $bedroom }}+</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="max_bedrooms">Max Bedrooms</label>
                                    <select name="max_bedrooms" id="max_bedrooms" class="form-select nice-select">
                                        <option value="">Any</option>
                                        @foreach ($bedroomOptions as $bedroom)
                                            <option value="{{ $bedroom }}" @selected((string) request('max_bedrooms') === (string) $bedroom)>{{ $bedroom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="min_area">Min Area (Sqft)</label>
                                    <input class="form-control" type="number" min="0" step="1" name="min_area" id="min_area" value="{{ request('min_area', request('mini_area')) }}" placeholder="Any">
                                </div>
                                <div class="form-group">
                                    <label for="max_area">Max Area (Sqft)</label>
                                    <input class="form-control" type="number" min="0" step="1" name="max_area" id="max_area" value="{{ request('max_area') }}" placeholder="Any">
                                </div>
                                <div class="form-group price-filter-group">
                                    <p class="price-range-title">Price Range</p>
                                    <div class="price_slider_wrapper property-price-range" data-min="{{ $priceMin }}" data-max="{{ $priceMax }}">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="price_label">₹ <span id="property-min-price-label">{{ number_format((float) request('min_price', $priceMin)) }}</span></span>
                                            <span class="price_label">₹ <span id="property-max-price-label">{{ number_format((float) request('max_price', $priceMax)) }}</span></span>
                                        </div>
                                        <div class="property-range-inputs">
                                            <input type="range" id="property-min-price-range" min="{{ $priceMin }}" max="{{ $priceMax }}" step="1000" value="{{ request('min_price', $priceMin) }}">
                                            <input type="range" id="property-max-price-range" min="{{ $priceMin }}" max="{{ $priceMax }}" step="1000" value="{{ request('max_price', $priceMax) }}">
                                        </div>
                                        <input type="hidden" name="min_price" id="property-min-price" value="{{ request('min_price') }}">
                                        <input type="hidden" name="max_price" id="property-max-price" value="{{ request('max_price') }}">
                                    </div>
                                </div>
                                <div class="advance-btn-wrapper">
                                    <div class="advance-search-btn">
                                        <div class="search-btn-item">
                                            <button type="submit" class="th-btn radius w-100"><i class="fa-regular fa-magnifying-glass me-2"></i> Search Property</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="advance-btn-wrapper">
                                    <a href="{{ route('properties.index') }}" class="th-btn radius outline w-100">Reset Filters</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .property-price-range { width: 100%; }
        .property-range-inputs { display: grid; gap: 8px; }
        .property-range-inputs input[type="range"] { width: 100%; accent-color: var(--theme-color, #b18b5e); }
        #property-search-form .select-group-wrapper { align-items: end; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('property-search-form');
            if (!form) return;

            const listingInput = document.getElementById('property-listing-type');
            let initialTabSelected = false;
            document.querySelectorAll('#propertySearchTabs [data-listing-type]').forEach(function (tab) {
                const active = !initialTabSelected && tab.dataset.listingType === listingInput.value;
                if (active) initialTabSelected = true;
                tab.classList.toggle('active', active);
                tab.setAttribute('aria-pressed', active ? 'true' : 'false');
                tab.addEventListener('click', function () {
                    listingInput.value = tab.dataset.listingType;
                    document.querySelectorAll('#propertySearchTabs [data-listing-type]').forEach(function (item) {
                        const selected = item === tab;
                        item.classList.toggle('active', selected);
                        item.setAttribute('aria-pressed', selected ? 'true' : 'false');
                    });
                });
            });

            const minRange = document.getElementById('property-min-price-range');
            const maxRange = document.getElementById('property-max-price-range');
            const minField = document.getElementById('property-min-price');
            const maxField = document.getElementById('property-max-price');
            const minLabel = document.getElementById('property-min-price-label');
            const maxLabel = document.getElementById('property-max-price-label');
            const money = value => new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(value);

            function syncPriceRange(changed, updateFields) {
                let low = Number(minRange.value);
                let high = Number(maxRange.value);
                if (low > high) {
                    if (changed === 'min') low = high;
                    else high = low;
                }
                minRange.value = low;
                maxRange.value = high;
                if (updateFields) {
                    minField.value = low;
                    maxField.value = high;
                }
                minLabel.textContent = money(low);
                maxLabel.textContent = money(high);
            }
            minRange.addEventListener('input', () => syncPriceRange('min', true));
            maxRange.addEventListener('input', () => syncPriceRange('max', true));
            syncPriceRange(null, false);

            const minBedrooms = document.getElementById('min_bedrooms');
            const maxBedrooms = document.getElementById('max_bedrooms');
            minBedrooms.addEventListener('change', function () {
                if (minBedrooms.value && maxBedrooms.value && Number(minBedrooms.value) > Number(maxBedrooms.value)) {
                    maxBedrooms.value = minBedrooms.value;
                }
            });
            maxBedrooms.addEventListener('change', function () {
                if (minBedrooms.value && maxBedrooms.value && Number(maxBedrooms.value) < Number(minBedrooms.value)) {
                    minBedrooms.value = maxBedrooms.value;
                }
            });
        });
    </script>
    <section class="th-blog-wrapper space-top space-extra-bottom">
        <div class="container">
            <div class="th-sort-bar property-style">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md">
                        <h4 class="box-title text-start fadeinup wow" data-wow-duration="1.5s" data-wow-delay="0.1s">
                            Property Listing</h4>
                    </div>
                    <div class="col-md-auto">
                        <div class="sorting-filter-wrap fadeinup wow" data-wow-duration="1.5s" data-wow-delay="0.3s">
                            <div class="nav" role="tablist"><a class="active" href="property.html#"
                                    id="tab-shop-list" data-bs-toggle="tab" data-bs-target="#tab-list" role="tab"
                                    aria-controls="tab-grid" aria-selected="false"><i class="fa-solid fa-list"></i></a>
                                <a href="property.html#" id="tab-shop-grid" data-bs-toggle="tab"
                                    data-bs-target="#tab-grid" role="tab" aria-controls="tab-grid"
                                    aria-selected="true"><i class="fa-light fa-grid-2"></i></a>
                            </div>
                            <form class="woocommerce-ordering" method="get"><select name="orderby" class="orderby"
                                    aria-label="Shop order" onchange="this.form.submit()">
                                    @foreach (['menu_order' => 'Default Sorting', 'popularity' => 'Featured first', 'date' => 'Sort by latest', 'price' => 'Sort by price: low to high', 'price-desc' => 'Sort by price: high to low'] as $sortValue => $sortLabel)
                                        <option value="{{ $sortValue }}" @selected(request('orderby', 'menu_order') === $sortValue)>
                                            {{ $sortLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                @foreach (request()->except(['orderby', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade" id="tab-grid" role="tabpanel" aria-labelledby="tab-shop-grid">
                    <div class="row gy-40">
                        @forelse ($properties as $property)
                            <div class="col-xl-4 col-lg-6 col-md-6 fadeinup wow">
                                <div class="popular-list-1 grid-style">@php
                                    $listingImages = collect();
                                    if ($property->featured_image) {
                                        $listingImages->push(
                                            (object) [
                                                'path' => $property->featured_image,
                                                'alt_text' => $property->title,
                                            ],
                                        );
                                    }
                                    foreach ($property->images as $listingImage) {
                                        if ($listingImage->path !== $property->featured_image) {
                                            $listingImages->push($listingImage);
                                        }
                                    }
                                @endphp
                                    <div class="thumb-wrapper">
                                        <div class="th-slider"
                                            data-slider-options='{"loop":false, "autoplay": false,"autoHeight": true, "effect":"fade"}'>
                                            <div class="swiper-wrapper">
                                                @forelse($listingImages as $listingImage)
                                                    <div class="swiper-slide"><a class="popular-popup-image"
                                                            href="{{ Storage::disk('public')->url($listingImage->path) }}"><img
                                                                src="{{ Storage::disk('public')->url($listingImage->path) }}"
                                                                alt="{{ $listingImage->alt_text ?: $property->title }}"></a>
                                                    </div>
                                                @empty
                                                    <div class="swiper-slide">
                                                        <div
                                                            class="property-image-placeholder bg-smoke d-flex align-items-center justify-content-center">
                                                            <span class="text-muted">Image pending</span>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                            <div class="icon-wrap"><button class="slider-arrow slider-prev"><i
                                                        class="far fa-arrow-left"></i></button> <button
                                                    class="slider-arrow slider-next"><i
                                                        class="far fa-arrow-right"></i></button></div>
                                        </div>
                                        <div class="actions"><a href="#" class="icon-btn"><i
                                                    class="fas fa-heart"></i></a></div>
                                        <div class="actions-style-2-wrapper">
                                            <div class="actions style-2"><a href="property.html#" class="icon-btn"><span
                                                        class="action-text">Add To Favorite</span> <i
                                                        class="fa-solid fa-bookmark"></i> </a><a
                                                    href="{{ $listingImages->isNotEmpty() ? Storage::disk('public')->url($listingImages->first()->path) : route('properties.show', $property) }}"
                                                    class="icon-btn {{ $listingImages->isNotEmpty() ? 'popular-popup-image' : '' }}"><span
                                                        class="action-text">View all img</span> <i
                                                        class="fa-solid fa-camera"></i></a></div>
                                        </div>
                                        <div class="popular-badge"><img
                                                src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}"
                                                alt="icon">
                                            <p>{{ $property->listing_type === 'rent' ? 'For Rent' : ($property->listing_type === 'lease' ? 'For Lease' : 'For Sale') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="property-content">
                                        <div class="media-body">
                                            <h3 class="box-title"><a
                                                    href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                            </h3>
                                            <div class="box-text">
                                                <div class="icon"><img
                                                        src="{{ asset('theme-assets/img/icon/popular-location.svg') }}"
                                                        alt="icon"></div>
                                                {{ trim(($property->address ? $property->address . ', ' : '') . $property->city) }}
                                            </div>
                                        </div>
                                        <ul class="property-featured">
                                            <li>
                                                <div class="icon"><img
                                                        src="{{ asset('theme-assets/img/icon/bed.svg') }}"
                                                        alt="icon"></div>Bed {{ $property->bedrooms ?? '—' }}
                                            </li>
                                            <li>
                                                <div class="icon"><img
                                                        src="{{ asset('theme-assets/img/icon/bath.svg') }}"
                                                        alt="icon"></div>Bath {{ $property->bathrooms ?? '—' }}
                                            </li>
                                            <li>
                                                <div class="icon"><img
                                                        src="{{ asset('theme-assets/img/icon/sqft.svg') }}"
                                                        alt="icon"></div>
                                                {{ $property->area ? number_format((float) $property->area) . ' ' . ($property->area_unit ?? 'sqft') : '—' }}
                                            </li>
                                        </ul>
                                        <div class="property-bottom">
                                            <h6 class="box-title price-red-shine">
                                                {{ $property->price ? '₹ ' . number_format((float) $property->price, 2) : $property->price_label ?? 'Price on request' }}
                                            </h6><a class="th-btn sm style3 pill"
                                                href="{{ route('properties.show', $property) }}">View More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">No matching properties are available.</div>
                        @endforelse
                    </div>
                </div>
                <div class="tab-pane fade active show" id="tab-list" role="tabpanel" aria-labelledby="tab-shop-list">
                    <div class="row gy-40 justify-content-center">
                        <div class="col-xl-8 col-lg-12">
                            <div class="row gy-30">
                                @forelse ($properties as $property)
                                    <div class="col-xl-12 fadeinup wow">
                                        <div class="popular-list-1 list-style">@php
                                            $listingImages = collect();
                                            if ($property->featured_image) {
                                                $listingImages->push(
                                                    (object) [
                                                        'path' => $property->featured_image,
                                                        'alt_text' => $property->title,
                                                    ],
                                                );
                                            }
                                            foreach ($property->images as $listingImage) {
                                                if ($listingImage->path !== $property->featured_image) {
                                                    $listingImages->push($listingImage);
                                                }
                                            }
                                        @endphp
                                            <div class="thumb-wrapper">
                                                <div class="th-slider"
                                                    data-slider-options='{"loop":false, "autoplay": false,"autoHeight": true, "effect":"fade"}'>
                                                    <div class="swiper-wrapper">
                                                        @forelse($listingImages as $listingImage)
                                                            <div class="swiper-slide"><a class="popular-popup-image"
                                                                    href="{{ Storage::disk('public')->url($listingImage->path) }}"><img
                                                                        src="{{ Storage::disk('public')->url($listingImage->path) }}"
                                                                        alt="{{ $listingImage->alt_text ?: $property->title }}"></a>
                                                            </div>
                                                        @empty
                                                            <div class="swiper-slide">
                                                                <div
                                                                    class="property-image-placeholder bg-smoke d-flex align-items-center justify-content-center">
                                                                    <span class="text-muted">Image pending</span>
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                    <div class="icon-wrap"><button class="slider-arrow slider-prev"><i
                                                                class="far fa-arrow-left"></i></button> <button
                                                            class="slider-arrow slider-next"><i
                                                                class="far fa-arrow-right"></i></button></div>
                                                </div>
                                                <div class="actions"><a href="#" class="icon-btn"><i
                                                            class="fas fa-heart"></i></a></div>
                                                <div class="actions-style-2-wrapper">
                                                    <div class="actions style-2"><a href="property.html#"
                                                            class="icon-btn"><span class="action-text">Add To
                                                                Favorite</span> <i class="fa-solid fa-bookmark"></i> </a><a
                                                            href="{{ $listingImages->isNotEmpty() ? Storage::disk('public')->url($listingImages->first()->path) : route('properties.show', $property) }}"
                                                            class="icon-btn {{ $listingImages->isNotEmpty() ? 'popular-popup-image' : '' }}"><span
                                                                class="action-text">View all img</span> <i
                                                                class="fa-solid fa-camera"></i></a></div>
                                                </div>
                                                <div class="popular-badge"><img
                                                        src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}"
                                                        alt="icon">
                                                    <p>{{ $property->listing_type === 'rent' ? 'For Rent' : ($property->listing_type === 'lease' ? 'For Lease' : 'For Sale') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="property-content">
                                                <div class="media-body">
                                                    <h3 class="box-title"><a
                                                            href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                                    </h3>
                                                    <div class="box-text">
                                                        <div class="icon"><img
                                                                src="{{ asset('theme-assets/img/icon/popular-location.svg') }}"
                                                                alt="icon"></div>
                                                        {{ trim(($property->address ? $property->address . ', ' : '') . $property->city) }}
                                                    </div>
                                                </div>
                                                <ul class="property-featured">
                                                    <li>
                                                        <div class="icon"><img
                                                                src="{{ asset('theme-assets/img/icon/bed.svg') }}"
                                                                alt="icon"></div>Bed {{ $property->bedrooms ?? '—' }}
                                                    </li>
                                                    <li>
                                                        <div class="icon"><img
                                                                src="{{ asset('theme-assets/img/icon/bath.svg') }}"
                                                                alt="icon"></div>Bath
                                                        {{ $property->bathrooms ?? '—' }}
                                                    </li>
                                                    <li>
                                                        <div class="icon"><img
                                                                src="{{ asset('theme-assets/img/icon/sqft.svg') }}"
                                                                alt="icon"></div>
                                                        {{ $property->area ? number_format((float) $property->area) . ' ' . ($property->area_unit ?? 'sqft') : '—' }}
                                                    </li>
                                                </ul>
                                                <div class="property-bottom">
                                                    <h6 class="box-title price-red-shine">
                                                        {{ $property->price ? '₹ ' . number_format((float) $property->price, 2) : $property->price_label ?? 'Price on request' }}
                                                    </h6><a class="th-btn sm style3 pill"
                                                        href="{{ route('properties.show', $property) }}">View More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted">No matching properties are available.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6">
                            <aside class="sidebar-area">
<div class="widget">
                                    <h3 class="widget_title">Featured Properties</h3>
                                    <div class="recent-post-wrap featured-listing">
                                        @forelse($featuredProperties as $featuredProperty)
                                            <div class="recent-post">
                                                <div class="media-img"><a
                                                        href="{{ route('properties.show', $featuredProperty) }}">
                                                        @if ($featuredProperty->featured_image)
                                                            <img src="{{ Storage::disk('public')->url($featuredProperty->featured_image) }}"
                                                                alt="{{ $featuredProperty->title }}">
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="post-title"><a class="text-inherit"
                                                            href="{{ route('properties.show', $featuredProperty) }}">{{ $featuredProperty->title }}</a>
                                                    </h4>
                                                    <div class="property-features-wrap">
                                                        <div class="property-features-item">
                                                            <div class="thumb"><img
                                                                    src="{{ asset('theme-assets/img/icon/bed.svg') }}"
                                                                    alt="Bedrooms"></div>
                                                            <h5 class="feature-title">Bed
                                                                {{ $featuredProperty->bedrooms ?? '—' }}</h5>
                                                        </div>
                                                        <div class="divider"></div>
                                                        <div class="property-features-item">
                                                            <div class="thumb"><img
                                                                    src="{{ asset('theme-assets/img/icon/bath.svg') }}"
                                                                    alt="Bathrooms"></div>
                                                            <h5 class="feature-title">Bath
                                                                {{ $featuredProperty->bathrooms ?? '—' }}</h5>
                                                        </div>
                                                        <div class="divider"></div>
                                                        <div class="property-features-item">
                                                            <div class="thumb"><img
                                                                    src="{{ asset('theme-assets/img/icon/sqft.svg') }}"
                                                                    alt="Area"></div>
                                                            <h5 class="feature-title">
                                                                {{ $featuredProperty->area ? number_format((float) $featuredProperty->area) . ' ' . ($featuredProperty->area_unit ?? 'sqft') : '— sqft' }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="recent-post-meta price-red-shine">
                                                        {{ $featuredProperty->price ? '₹ ' . number_format((float) $featuredProperty->price, 2) : $featuredProperty->price_label ?? 'Price on request' }}
                                                    </div>
                                                </div>
                                        </div>@empty<p class="text-muted">No featured properties are currently
                                                available.</p>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="widget sidebar-contact-form">
                                    <h3 class="widget_title">Contact Us</h3>
                                    <div class="contact-form-widget">
                                        <form action="{{ route('enquiries.store') }}" method="POST"
                                            class="newsletter-form">
                                            @csrf
                                            <input type="hidden" name="subject" value="Property enquiry">
                                            <div class="form-group"><input class="form-control" type="text"
                                                    name="name" value="{{ old('name') }}" placeholder="Your Name"
                                                    required maxlength="150"></div>
                                            <div class="form-group"><input class="form-control" type="email"
                                                    name="email" value="{{ old('email') }}" placeholder="Your Email"
                                                    required></div>
                                            <div class="form-group"><input class="form-control" type="text"
                                                    name="phone" value="{{ old('phone') }}" placeholder="Your Phone"
                                                    required maxlength="30"></div>
                                            <div class="form-group mb-4">
                                                <textarea name="message" id="property-list-enquiry-message" cols="30" rows="3" class="form-control"
                                                    placeholder="Your Message">{{ old('message') }}</textarea>
                                            </div>
                                            <div class="form-btn pt-2"><button type="submit" class="th-btn radius">Send
                                                    Us</button></div>
                                        </form>
                                    </div>
                                </div>
                                <div class="widget widget_banner"
                                    data-bg-src="{{ asset('theme-assets/img/bg/widget_banner.jpg') }}">
                                    <div class="widget-banner">
                                        <h2 class="title">We can help you to find real estate agency</h2><a
                                            href="{{ route('contact') }}" class="th-btn radius">Contact with Agent</a>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="container mt-4">{{ $properties->withQueryString()->links('vendor.pagination.buraaqh') }}</div>
@endsection
