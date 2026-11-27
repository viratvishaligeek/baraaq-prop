@extends('frontend.layouts.theme')
@section('title', $property->meta_title ?: $property->title)
@section('meta_description', $property->meta_description ?:
    \Illuminate\Support\Str::limit(strip_tags($property->description ?? ''), 155))
@section('canonical_url', $property->canonical_url ?: route('properties.show', $property))
@section('content')
    @php
        $propertyMedia = collect();
        if ($property->featured_image) {
            $propertyMedia->push((object) ['path' => $property->featured_image, 'alt_text' => $property->title]);
        }
        foreach ($property->images as $propertyImage) {
            if ($propertyImage->path !== $property->featured_image) {
                $propertyMedia->push($propertyImage);
            }
        }
    @endphp


    <style>
        .property-detail-vibrant {
            --pd-navy: #102b61;
            --pd-blue: #123f91;
            --pd-gold: #f2b632;
            --pd-red: #f12d2d;
            --pd-ink: #14213d;
        }
        .property-detail-vibrant .property-meta-wrap {
            display:flex; align-items:center; justify-content:space-between; gap:14px;
            flex-wrap:wrap; margin-bottom:22px !important;
        }
        .property-detail-vibrant .property-meta {
            display:flex; align-items:center; gap:12px 18px; flex-wrap:wrap;
        }
        .property-detail-vibrant .property-meta > span {
            display:inline-flex; align-items:center; gap:8px; color:var(--pd-ink);
            font-size:14px; font-weight:600;
        }
        .property-detail-vibrant .property-meta > span i { color:var(--pd-red); }
        .property-detail-vibrant .property-meta .property-tag {
            background:linear-gradient(135deg,#ff514b,#e91f28); color:#fff;
            border-radius:30px; padding:9px 19px; font-weight:700;
            box-shadow:0 6px 15px rgba(241,45,45,.18);
        }
        .property-detail-vibrant .wishlist-icon .icon-btn {
            border:2px solid #e8e8e8; background:#fff; color:#444;
            width:48px; height:48px; border-radius:50%;
        }
        .property-detail-vibrant .property-contact-highlight {
            display:flex; align-items:center; gap:14px; padding:14px 18px;
            border:2px solid #f4c64e; border-radius:18px;
            background:linear-gradient(110deg,#fff9e8,#fffdf7);
            color:var(--pd-navy); margin:0 0 18px;
        }
        .property-detail-vibrant .property-contact-highlight .contact-icon {
            flex:0 0 48px; width:48px; height:48px; display:grid; place-items:center;
            border-radius:50%; background:var(--pd-navy); color:#fff; font-size:21px;
        }
        .property-detail-vibrant .property-contact-highlight strong { color:var(--pd-blue); }
        .property-detail-vibrant .property-contact-actions {
            display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;
            margin-bottom:30px;
        }
        .property-detail-vibrant .property-contact-btn {
            min-height:66px; display:flex; align-items:center; justify-content:center;
            gap:12px; border-radius:18px; color:#fff !important; font-size:20px;
            font-weight:800; box-shadow:0 9px 18px rgba(16,43,97,.15);
            transition:transform .2s ease,box-shadow .2s ease;
        }
        .property-detail-vibrant .property-contact-btn:hover { transform:translateY(-2px); color:#fff; }
        .property-detail-vibrant .property-contact-btn:only-child { grid-column:1 / -1; }
        .property-detail-vibrant .property-contact-btn.call {
            background:linear-gradient(135deg,#17499e,#09265f);
        }
        .property-detail-vibrant .property-contact-btn.whatsapp {
            background:linear-gradient(135deg,#18c653,#079b3c);
        }
        .property-detail-vibrant .property-heading-row {
            display:flex; align-items:center; gap:16px; margin-bottom:10px;
        }
        .property-detail-vibrant .property-heading-row .page-title {
            color:var(--pd-ink); margin:0; white-space:nowrap;
        }
        .property-detail-vibrant .property-heading-line {
            height:4px; flex:1; border-radius:5px; background:var(--pd-gold);
        }
        .property-detail-vibrant .property-price {
            display:block; color:var(--pd-ink) !important; font-size:clamp(28px,4vw,44px);
            font-weight:800; line-height:1.15; margin:0 0 20px;
        }
        .property-detail-vibrant .property-price::after { display:none; }
        .property-detail-vibrant .page-features {
            padding:17px 18px; border:1px solid #e8edf5; border-radius:16px;
            background:#f8faff; margin-bottom:22px;
        }
        .property-detail-vibrant .page-features .box-text {
            display:flex; align-items:center; gap:9px; color:var(--pd-navy);
            font-weight:700; margin-bottom:14px;
        }
        .property-detail-vibrant .page-features .box-text .icon { flex:0 0 20px; }
        .property-detail-vibrant .property-featured {
            display:flex; flex-wrap:wrap; gap:12px 20px; margin:0; padding:0;
        }
        .property-detail-vibrant .property-featured li {
            display:flex; align-items:center; gap:7px; color:#526078; font-weight:600;
        }
        .property-detail-vibrant .property-featured .icon img { width:19px; height:19px; }
        @media(max-width:575px) {
            .property-detail-vibrant .property-meta-wrap { gap:10px; margin-bottom:18px !important; }
            .property-detail-vibrant .property-meta { gap:9px 12px; }
            .property-detail-vibrant .property-meta > span { font-size:12px; }
            .property-detail-vibrant .property-meta .property-tag { padding:7px 13px; }
            .property-detail-vibrant .wishlist-icon .icon-btn { width:40px; height:40px; }
            .property-detail-vibrant .property-contact-highlight { padding:11px 12px; gap:10px; border-radius:14px; }
            .property-detail-vibrant .property-contact-highlight .contact-icon { width:40px; height:40px; flex-basis:40px; font-size:18px; }
            .property-detail-vibrant .property-contact-highlight span { font-size:13px; line-height:1.45; }
            .property-detail-vibrant .property-contact-actions { gap:9px; margin-bottom:25px; }
            .property-detail-vibrant .property-contact-btn { min-height:54px; gap:7px; border-radius:13px; font-size:14px; }
            .property-detail-vibrant .property-contact-btn i { font-size:18px; }
            .property-detail-vibrant .property-heading-row { gap:9px; }
            .property-detail-vibrant .property-heading-row .page-title { font-size:21px; white-space:normal; }
            .property-detail-vibrant .property-heading-line { height:3px; min-width:20px; }
            .property-detail-vibrant .property-price { font-size:30px; margin-bottom:16px; }
            .property-detail-vibrant .page-features { padding:13px; border-radius:13px; }
            .property-detail-vibrant .property-featured { gap:10px 14px; }
            .property-detail-vibrant .property-featured li { font-size:12px; }
        }
    </style>

    <div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/blog/breadcrumb-bg.jpg') }}">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $property->title }}</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>{{ $property->title }}</li>
                </ul>
            </div>
        </div>
    </div>
    <section class="property-details space overflow-hidden">
        <div class="container">
            <div class="row gy-40 gx-50">
                <div class="col-xxl-8 col-lg-7">
                    <div class="row gy-30">
                        <div class="slider-area property-slider1">
                            <div class="swiper th-slider panoramaSlide2 mb-4" id="panoramaSlide2"
                                data-slider-options='{"autoplay":false,"effect":"fade","loop":true, "simulateTouch": false,"thumbs":{"swiper":".property-thumb-slider"},"autoplayDisableOnInteraction":"true"}'>
                                <div class="swiper-wrapper">
                                    @forelse($propertyMedia as $index => $image)
                                        <div class="swiper-slide">
                                            <div class="property-slider-img propery-single-slide"
                                                id="panorama{{ $index + 1 }}"><img
                                                    src="{{ Storage::disk('public')->url($image->path) }}"
                                                    alt="{{ $image->alt_text ?: $property->title }}"></div>
                                        </div>
                                    @empty
                                        <div class="swiper-slide">
                                            <div
                                                class="property-slider-img propery-single-slide bg-smoke d-flex align-items-center justify-content-center">
                                                <p class="text-muted p-4 mb-0">Property images will be added after
                                                    verification.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="swiper th-slider property-thumb-slider slider-tab"
                                data-slider-options='{"effect":"slide","loop":true, "simulateTouch": true,"breakpoints":{"0":{"slidesPerView":2},"576":{"slidesPerView":"2"},"768":{"slidesPerView":"3"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"4"}},"autoplayDisableOnInteraction":"true"}'>
                                <div class="swiper-wrapper">
                                    @forelse($propertyMedia as $image)
                                        <div class="swiper-slide">
                                            <div class="tab-btn property-slider-img"><img
                                                    src="{{ Storage::disk('public')->url($image->path) }}"
                                                    alt="{{ $image->alt_text ?: $property->title }}"></div>
                                        </div>
                                    @empty
                                        <div class="swiper-slide">
                                            <div
                                                class="tab-btn property-slider-img bg-smoke d-flex align-items-center justify-content-center">
                                                <span class="text-muted">No photos</span></div>
                                        </div>
                                    @endforelse
                                </div>
                            </div><button data-slider-prev="#panoramaSlide2" class="slider-arrow slider-prev"><i
                                    class="far fa-arrow-left"></i></button> <button data-slider-next="#panoramaSlide2"
                                class="slider-arrow slider-next"><i class="far fa-arrow-right"></i></button>
                        </div>
                        <div class="property-page-single property-detail-vibrant">
                            <div class="page-content">
                                <div class="property-meta-wrap fadeinup wow">
                                    <div class="property-meta">
                                        @if ($property->is_featured)
                                            <span class="property-tag">Featured</span>
                                        @endif
                                        <span><i class="fa-solid fa-calendar-days"></i>
                                            {{ $property->published_at?->format('d M, Y') ?? '—' }}</span>
                                        <span><i class="fa-solid fa-location-dot"></i>
                                            {{ $property->city ?: 'Bareilly' }}</span>
                                    </div>
                                    <div class="wishlist-icon">
                                        <a href="#" class="icon-btn" aria-label="Add property to favourites"><i class="fa-solid fa-heart"></i></a>
                                    </div>
                                </div>

                                @php
                                    $propertyPhone = preg_replace('/\\D+/', '', (string) ($siteSettings['site_phone'] ?? ''));
                                    $propertyWhatsapp = preg_replace('/\\D+/', '', (string) ($siteSettings['whatsapp_number'] ?? ''));
                                    $propertyContactNumber = $propertyPhone ?: $propertyWhatsapp;
                                @endphp
                                @if ($propertyContactNumber || $propertyWhatsapp)
                                    <div class="property-contact-highlight fadeinup wow">
                                        <div class="contact-icon"><i class="fa-solid fa-headset"></i></div>
                                        <span>For more details about this property, <strong>contact our team directly.</strong></span>
                                    </div>
                                    <div class="property-contact-actions fadeinup wow">
                                        @if ($propertyContactNumber)
                                            <a class="property-contact-btn call" href="tel:{{ $propertyContactNumber }}">
                                                <i class="fa-solid fa-phone"></i><span>Call Now</span>
                                            </a>
                                        @endif
                                        @if ($propertyWhatsapp)
                                            <a class="property-contact-btn whatsapp" href="https://wa.me/{{ $propertyWhatsapp }}?text={{ rawurlencode('Hello, I am interested in ' . $property->title . ' (' . route('properties.show', $property) . ')') }}" target="_blank" rel="noopener noreferrer">
                                                <i class="fa-brands fa-whatsapp"></i><span>WhatsApp</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <div class="page-title-wrap fadeinup wow mb-0">
                                    <div class="property-heading-row">
                                        <h2 class="page-title">About This Property</h2>
                                        <span class="property-heading-line"></span>
                                    </div>
                                    <h4 class="property-price">
                                        {{ $property->price ? '₹ ' . number_format((float) $property->price, 2) : $property->price_label ?? 'Price on request' }}
                                    </h4>
                                </div>
                                <div class="page-features fadeinup wow">
                                    <div class="box-text">
                                        <div class="icon"><img
                                                src="{{ asset('theme-assets/img/icon/popular-location.svg') }}"
                                                alt="icon"></div>
                                        {{ trim(($property->address ?? '') . ' ' . ($property->city ?? '') . ' ' . ($property->state ?? '')) }}
                                    </div>
                                    <ul class="property-featured">
                                        <li>
                                            <div class="icon"><img src="{{ asset('theme-assets/img/icon/bed.svg') }}" alt="Bedrooms"></div>
                                            Bed {{ $property->bedrooms ?? '—' }}
                                        </li>
                                        <li>
                                            <div class="icon"><img src="{{ asset('theme-assets/img/icon/bath.svg') }}" alt="Bathrooms"></div>
                                            Bath {{ $property->bathrooms ?? '—' }}
                                        </li>
                                        <li>
                                            <div class="icon"><img src="{{ asset('theme-assets/img/icon/sqft.svg') }}" alt="Area"></div>
                                            {{ $property->area ? number_format((float) $property->area) . ' ' . ($property->area_unit ?? 'sqft') : '—' }}
                                        </li>
                                    </ul>
                                </div>
                                <div class="mb-30 fadeinup wow">{!! \App\Support\HtmlSanitizer::sanitize($property->description ?? '') !!}</div>
                                <div class="hightlighes-title-wrap mb-25 fadeinup wow">
                                    <h2 class="page-title mb-2">Property Hightlights</h2>
                                    <h5 class="house-sell">
                                        {{ $property->listing_type === 'rent' ? 'House for rent' : ($property->listing_type === 'lease' ? 'Property for lease' : 'House for sale') }}
                                    </h5>
                                </div>
                                <ul class="property-grid-list fadeinup wow">
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-1.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">ID NO.</h4>
                                            <p class="property-grid-list-text">{{ $property->reference ?: $property->id }}
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-2.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Type</h4>
                                            <p class="property-grid-list-text">{{ $property->property_type ?: '—' }}</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-3.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Room</h4>
                                            <p class="property-grid-list-text">—</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-4.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Bedroom</h4>
                                            <p class="property-grid-list-text">{{ $property->bedrooms ?? '—' }}</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-5.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Bath</h4>
                                            <p class="property-grid-list-text">{{ $property->bathrooms ?? '—' }}</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-6.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Big Yard</h4>
                                            <p class="property-grid-list-text">—</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-7.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Purpose</h4>
                                            <p class="property-grid-list-text">
                                                {{ ucfirst($property->listing_type ?? '—') }}</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-8.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Sqft</h4>
                                            <p class="property-grid-list-text">
                                                {{ $property->area ?  number_format((float) $property->area) : '—' }}
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-9.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Parking</h4>
                                            <p class="property-grid-list-text">—</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-10.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Elevator</h4>
                                            <p class="property-grid-list-text">—</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-11.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Wifi</h4>
                                            <p class="property-grid-list-text">—</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="property-grid-list-icon"><img
                                                src="{{ asset('theme-assets/img/icon/property-single-icon1-12.svg') }}"
                                                alt="img"></div>
                                        <div class="property-grid-list-details">
                                            <h4 class="property-grid-list-title">Built in</h4>
                                            <p class="property-grid-list-text">—</p>
                                        </div>
                                    </li>
                                </ul>
                                <h3 class="page-title mt-50 mb-30 fadeinup wow">From Amazing Gallery</h3>
                                <div class="row gy-4 fadeinup wow">
                                    @forelse($property->images as $image)
                                        <div class="col-xl-{{ $loop->odd ? 7 : 5 }}">
                                            <div class="property-gallery-card">
                                                <div class="property-gallery-card-img"><img class="w-100"
                                                        src="{{ Storage::disk('public')->url($image->path) }}"
                                                        alt="{{ $image->alt_text ?: $property->title }}"></div><a
                                                    class="icon-btn popup-image"
                                                    href="{{ Storage::disk('public')->url($image->path) }}"><i
                                                        class="fa-solid fa-eye"></i></a>
                                            </div>
                                        </div>@empty @if ($property->featured_image)
                                            <div class="col-12">
                                                <div class="property-gallery-card">
                                                    <div class="property-gallery-card-img"><img class="w-100"
                                                            src="{{ Storage::disk('public')->url($property->featured_image) }}"
                                                            alt="{{ $property->title }}"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforelse
                                </div>
                                <h3 class="page-title mt-50 mb-25 fadeinup wow">Features & amenities</h3>
                                <div class="fea-anim-checklist fadeinup wow">
                                    <div class="checklist style2 list-six-column">
                                        <ul>
                                            @forelse(collect($property->features ?? [])->values() as $feature)
                                                @if (is_string($feature) && trim($feature) !== '')
                                                    <li>{{ $feature }}</li>
                                                @elseif(is_array($feature))
                                                    <li>{{ $feature['name'] ?? ($feature['label'] ?? '') }}</li>
                                                @endif @empty<li>Property features will be added by our
                                                    team.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                                <div
                                    class="row gy-30 align-items-center justify-content-center justify-content-between fadeinup wow mb-40 mt-40">
                                    <div class="col-lg-auto">
                                        <h3 class="page-title mb-0">Floor Plan</h3>
                                    </div>
                                    <div class="col-lg-auto">
                                        <ul class="nav nav-tabs property-tab mb-0" role="tablist">
                                            @forelse($property->accommodations as $space)
                                                <li class="nav-item" role="presentation"><button
                                                        class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                        id="floor-tab-{{ $space->id }}" data-bs-toggle="tab"
                                                        data-bs-target="#floor-pane-{{ $space->id }}" type="button"
                                                        role="tab" aria-controls="floor-pane-{{ $space->id }}"
                                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $space->name }}</button>
                                                </li>
                                            @empty
                                                <li class="nav-item" role="presentation"><span
                                                        class="nav-link disabled">Floor plans pending</span></li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                                <div class="tab-content">
                                    @forelse($property->accommodations as $space)
                                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                            id="floor-pane-{{ $space->id }}" role="tabpanel"
                                            aria-labelledby="floor-tab-{{ $space->id }}" tabindex="0">
                                            <div class="property-grid-plan fadeinup wow">
                                                @if ($space->imageMedia)
                                                    <div class="property-grid-thumb"><img
                                                            src="{{ $space->imageMedia->url }}"
                                                            alt="{{ $space->name }}"></div>
                                                @endif
                                                <div class="property-grid-details">
                                                    <h4 class="property-grid-title">{{ $space->name }}</h4>
                                                    <p class="property-grid-text mb-25">{{ $space->description }}</p>
                                                    @if ($space->area)
                                                        <p class="text-desc">{{ number_format((float) $space->area) }}
                                                            {{ $space->area_unit }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="tab-pane fade show active" role="tabpanel">
                                            <p class="text-muted">Floor plan details will be published when verified.</p>
                                        </div>
                                    @endforelse
                                </div>
                                <h3 class="page-title mt-50 mb-30 fadeinup wow">Location</h3>
                                <div class="location-map fadeinup wow">
                                    <div class="contact-map">
                                        @if ($property->latitude && $property->longitude)
                                            <iframe
                                                src="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}&output=embed"
                                                allowfullscreen="" loading="lazy"
                                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        @endif
                                    </div>
                                    <div class="location-map-address">
                                        @if ($property->featured_image)
                                            <div class="thumb"><img
                                                    src="{{ Storage::disk('public')->url($property->featured_image) }}"
                                                    alt="{{ $property->title }}"></div>
                                        @endif
                                        <div class="media-body">
                                            <h4 class="title">Address:</h4>
                                            <p class="text">
                                                {{ trim(($property->address ?? '') . ' ' . ($property->city ?? '') . ' ' . ($property->state ?? '') . ' ' . ($property->postal_code ?? '') . ' ' . ($property->country ?? '')) }}
                                            </p>
                                            <h4 class="title">Post Code:</h4>
                                            <p class="text">{{ $property->postal_code ?: '—' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-lg-5">
                    <aside class="sidebar-area">
                        <div class="widget widget_search">
                            <h3 class="widget_title">search</h3>
                            <form class="search-form" method="GET" action="{{ route('properties.index') }}"><input
                                    type="search" name="q" value="{{ request('q') }}"
                                    placeholder="Enter Keyword"> <button type="submit"><i
                                        class="far fa-search"></i></button></form>
                        </div>
                        <div class="widget">
                            <h3 class="widget_title" style="border: 1px solid #0000006b; text-align: center; padding: 10px; border-radius: 7px;">Featured Listings</h3>
                            <div class="recent-post-wrap featured-listing">
                                @forelse($related as $relatedProperty)
                                    <div class="recent-post">
                                        <div class="media-img"><a href="{{ route('properties.show', $relatedProperty) }}">
                                                @if ($relatedProperty->featured_image)
                                                    <img src="{{ Storage::disk('public')->url($relatedProperty->featured_image) }}"
                                                        alt="{{ $relatedProperty->title }}">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <h4 class="post-title"><a class="text-inherit"
                                                    href="{{ route('properties.show', $relatedProperty) }}">{{ $relatedProperty->title }}</a>
                                            </h4>
                                            <div class="property-features-wrap">
                                                <div class="property-features-item">
                                                    <div class="thumb"><img
                                                            src="{{ asset('theme-assets/img/icon/bed.svg') }}"
                                                            alt="Bedrooms"></div>
                                                    <h5 class="feature-title">Bed {{ $relatedProperty->bedrooms ?? '—' }}
                                                    </h5>
                                                </div>
                                                <div class="divider"></div>
                                                <div class="property-features-item">
                                                    <div class="thumb"><img
                                                            src="{{ asset('theme-assets/img/icon/bath.svg') }}"
                                                            alt="Bathrooms"></div>
                                                    <h5 class="feature-title">Bath
                                                        {{ $relatedProperty->bathrooms ?? '—' }}</h5>
                                                </div>
                                                <div class="divider"></div>
                                                <div class="property-features-item">
                                                    <div class="thumb"><img
                                                            src="{{ asset('theme-assets/img/icon/sqft.svg') }}"
                                                            alt="Area"></div>
                                                    <h5 class="feature-title">
                                                        {{ $relatedProperty->area ? number_format((float) $relatedProperty->area) . ' ' . ($relatedProperty->area_unit ?? 'sqft') : '— sqft' }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="recent-post-meta price-red-shine">
                                                {{ $relatedProperty->price ? '₹ ' . number_format((float) $relatedProperty->price, 2) : $relatedProperty->price_label ?? 'Price on request' }}
                                            </div>
                                        </div>
                                </div>@empty<p class="text-muted">No related listings are currently available.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="widget sidebar-contact-form">
                            <h3 class="widget_title">Contact Us</h3>
                            <div class="contact-form-widget">
                                <form action="{{ route('enquiries.store') }}" method="POST" class="newsletter-form">
                                    @csrf
                                    <input type="hidden" name="subject" value="Property enquiry">
                                    <div class="form-group"><input class="form-control" type="text" name="name"
                                            value="{{ old('name') }}" placeholder="Your Name" required
                                            maxlength="150"></div>
                                    <div class="form-group"><input class="form-control" type="email" name="email"
                                            value="{{ old('email') }}" placeholder="Your Email" required></div>
                                    <div class="form-group"><input class="form-control" type="text" name="phone"
                                            value="{{ old('phone') }}" placeholder="Your Phone" required
                                            maxlength="30"></div>
                                    <div class="form-group mb-4">
                                        <textarea name="message" id="property-enquiry-message" cols="30" rows="3" class="form-control"
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
    </section>
@endsection
