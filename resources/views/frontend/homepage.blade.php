@extends('frontend.layouts.theme')
@section('title', $homepage->meta_title ?: config('app.name'))
@section('meta_description', $homepage->meta_description ?: \App\Models\Setting::read('meta_description', ''))
@section('canonical_url', route('home'))
@section('content')
    @php
        $heroConfig = $homepageSectionConfigs['hero.home-9'] ?? [];
        $aboutConfig = $homepageSectionConfigs['about.home-9'] ?? [];
        $statsConfig = $homepageSectionConfigs['statistics.home-9'] ?? [];
        $servicesConfig = $homepageSectionConfigs['services.home-9'] ?? [];
        $propertiesConfig = $homepageSectionConfigs['properties.index'] ?? [];
        $galleryConfig = $homepageSectionConfigs['featured-gallery.home-9'] ?? [];
        $detailsConfig = $homepageSectionConfigs['home-details.home-9'] ?? [];
        $teamConfig = $homepageSectionConfigs['team.home-2'] ?? [];
        $testimonialsConfig = $homepageSectionConfigs['testimonials.home-3'] ?? [];
        $blogConfig = $homepageSectionConfigs['blog.home-2'] ?? [];
    @endphp

    <div class="th-hero-wrapper hero-9 position-relative" @unless ($homepageSectionEnabled['hero.home-9'] ?? true) hidden @endunless>
        <div class="hero-inner">
            <div class="th-hero-bg"
                data-bg-src="{{ $heroConfig['background_image'] ?? asset('theme-assets/img/hero/hero-9-bg.png') }}">
            </div>
            <div class="container">
                <div class="row gy-50 gx-40 align-items-center">
                    <div class="col-xl-6 col-lg-6">
                        <div class="hero-style9">
                            <span class="hero-subtitle">{{ $heroConfig['eyebrow'] ?? 'About Buraaqh' }}</span>
                            <h1 class="hero-title wow fadeinup" data-wow-duration="1.5s" data-wow-delay="0.2s">
                                <span class="title1">{{ $heroConfig['heading_prefix'] ?? 'Discover' }}</span> <span
                                    class="title2">{{ $heroConfig['heading_highlight'] ?? 'Most Suitable' }}</span> <span
                                    class="title2 text-theme">{{ $heroConfig['heading_suffix'] ?? 'Property' }}</span>
                            </h1>
                            <p class="hero-text wow fadeinup" data-wow-duration="1.5s" data-wow-delay="0.3s">
                                {!! \App\Support\HtmlSanitizer::sanitize($heroConfig['description'] ?? '') !!}</p>
                            <div class="btn-group wow fadeinup" data-wow-duration="1.5s" data-wow-delay="0.4s">
                                <a href="{{ $heroConfig['primary_button_url'] ?? route('properties.index') }}"
                                    class="th-btn big-btn style2">{{ $heroConfig['primary_button_label'] ?? 'Explore Properties' }}</a>
                                <a href="{{ $heroConfig['secondary_button_url'] ?? route('contact') }}"
                                    class="th-btn big-btn style3">{{ $heroConfig['secondary_button_label'] ?? 'Contact Us' }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="hero-9-img">
                            <img src="{{ $heroConfig['foreground_image'] ?? asset('theme-assets/img/hero/hero-9-right.png') }}"
                                alt="{{ $homepage->name }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="about-7 space-bottom overflow-hidden pt-2" id="about-sec"
        @unless ($homepageSectionEnabled['about.home-9'] ?? true) hidden @endunless>
        <div class="container">
            <div class="row justify-content-center mb-35">
                <div class="col-xl-6">
                    <div class="title-area text-center mb-1">
                        <p class="sub-title style4 text-anime-style-1">{{ $aboutConfig['eyebrow'] ?? 'About Us' }}</p>
                        <h2 class="sec-title style-2 text-anime-style-2">
                            {{ $aboutConfig['heading'] ?? 'Property guidance built around your goals' }}</h2>
                        <a href="{{ $aboutConfig['button_url'] ?? route('about') }}"
                            class="th-btn big-btn bg-theme">{{ $aboutConfig['button_label'] ?? 'More About Us' }}</a>
                    </div>
                </div>
            </div>
            <div class="row gy-40 justify-content-center">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="about7-left">
                        <img class="d-none d-xl-block"
                            src="{{ $aboutConfig['image_primary'] ?? asset('theme-assets/img/about/about7-1.jpg') }}"
                            alt="">
                        <div class="about7-content-box">
                            <h4 class="box-title">{{ $aboutConfig['mission_title'] ?? 'Our Mission' }}</h4>
                            <p class="box-text">{{ $aboutConfig['mission_text'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6 d-none d-xl-block">
                    <div class="about7-middle">
                        <img src="{{ $aboutConfig['image_secondary'] ?? asset('theme-assets/img/about/about7-2.jpg') }}"
                            alt="">
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="about7-right">
                        <img class="d-none d-xl-block"
                            src="{{ $aboutConfig['image_tertiary'] ?? asset('theme-assets/img/about/about7-3.jpg') }}"
                            alt="">
                        <div class="about7-content-box">
                            <h4 class="box-title">{{ $aboutConfig['vision_title'] ?? 'Our Vision' }}</h4>
                            <p class="box-text">{{ $aboutConfig['vision_text'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (
        ($homepageSectionEnabled['statistics.home-9'] ?? true) &&
            collect([
                $statsConfig['stat_1_value'] ?? '',
                $statsConfig['stat_2_value'] ?? '',
                $statsConfig['stat_3_value'] ?? '',
                $statsConfig['stat_4_value'] ?? '',
            ])->filter(fn($value) => $value !== '')->isNotEmpty())
        <div class="space-bottom overflow-hidden">
            <div class="container">
                <div class="row gy-4 gx-90">
                    <div class="col-lg-3 col-md-6">
                        <div class="counter-box style-2">
                            <div class="media-body">
                                <h4 class="box-number">
                                    <span class="counter-number">{{ $statsConfig['stat_1_value'] ?? '' }} </span>
                                    <span class="plus-simple">{{ $statsConfig['stat_1_suffix'] ?? '' }}</span>
                                </h4>
                                <p class="box-text">{{ $statsConfig['stat_1_label'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="counter-box style-2">
                            <div class="media-body">
                                <h4 class="box-number">
                                    <span class="counter-number">{{ $statsConfig['stat_2_value'] ?? '' }} </span>
                                    <span class="plus-simple">{{ $statsConfig['stat_2_suffix'] ?? '' }}</span>
                                </h4>
                                <p class="box-text">{{ $statsConfig['stat_2_label'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="counter-box style-2">
                            <div class="media-body">
                                <h4 class="box-number">
                                    <span class="counter-number">{{ $statsConfig['stat_3_value'] ?? '' }} </span>
                                    <span class="plus-simple">{{ $statsConfig['stat_3_suffix'] ?? '' }}</span>
                                </h4>
                                <p class="box-text">{{ $statsConfig['stat_3_label'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="counter-box style-2">
                            <div class="media-body">
                                <h4 class="box-number">
                                    <span class="counter-number">{{ $statsConfig['stat_4_value'] ?? '' }} </span>
                                    <span class="plus-simple">{{ $statsConfig['stat_4_suffix'] ?? '' }}</span>
                                </h4>
                                <p class="box-text">{{ $statsConfig['stat_4_label'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <section class="th-service-2 bg-smoke overflow-hidden space overflow-hidden" id="service-sec"
        @unless ($homepageSectionEnabled['services.home-9'] ?? true) hidden @endunless>
        <div class="container">
            <div class="row justify-content-center mb-40">
                <div class="col-xl-8 col-lg-10">
                    <div class="title-area text-center mb-1 ps-xxl-5 pe-xxl-5">
                        <p class="sub-title style4 bg-white text-anime-style-1">
                            {{ $servicesConfig['eyebrow'] ?? 'Our Services' }}</p>
                        <h2 class="sec-title style-2 mb-1 text-anime-style-3">
                            {{ $servicesConfig['heading'] ?? 'Property services and support' }}</h2>
                        @if (!empty($servicesConfig['description']))
                            <div class="sec-text">{!! \App\Support\HtmlSanitizer::sanitize($servicesConfig['description']) !!}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row gy-4 justify-content-center">
                @forelse ($featuredServices as $service)
                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6">
                        <div class="service-card-2">
                            <div class="box-icon">
                                <img src="{{ asset('theme-assets/img/icon/service_card_2_1.svg') }}" alt="Icon">
                            </div>
                            <div class="box-content">
                                <h3 class="box-title">
                                    <a href="{{ route('services.show', $service) }}">{{ $service->title }}</a>
                                </h3>
                                <p class="box-text">{{ $service->excerpt }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">No services are currently published.</div>
                @endforelse
            </div>
    </section>
    <section class="popular-sec-1 overflow-hidden space" @unless ($homepageSectionEnabled['properties.index'] ?? true) hidden @endunless>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7">
                    <div class="title-area text-center mb-2">
                        <p class="sub-title style4 text-anime-style-1">
                            {{ $propertiesConfig['eyebrow'] ?? 'Popular Properties' }}</p>
                        <h2 class="sec-title style-2 mb-4 text-anime-style-3">
                            {{ $propertiesConfig['heading'] ?? 'Best Properties Sale' }}</h2>
                        <div class="filter-menu style-2 indicator-active filter-menu-active mt-2 justify-content-center fadeinup wow"
                            data-wow-duration="1.3s" data-wow-delay="0.4s">
                            <button data-filter="*" class="th-btn tab-btn active" type="button">View All</button>
                            <button data-filter=".cat1" class="th-btn tab-btn" type="button">Apartment</button> <button
                                data-filter=".cat2" class="th-btn tab-btn" type="button">Commercial</button> <button
                                data-filter=".cat3" class="th-btn tab-btn" type="button">Land or Plot</button> <button
                                data-filter=".cat4" class="th-btn tab-btn" type="button">Farm</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row gy-30 justify-content-center filter-active">
                @forelse ($featuredProperties as $property)
                    @php
                        $listingImages = collect();
                        if ($property->featured_image) {
                            $listingImages->push(
                                (object) ['path' => $property->featured_image, 'alt_text' => $property->title],
                            );
                        }
                        foreach ($property->images as $listingImage) {
                            if ($listingImage->path !== $property->featured_image) {
                                $listingImages->push($listingImage);
                            }
                        }
                        $propertyFilterClass = match (\Illuminate\Support\Str::slug($property->category?->name ?? '')) {
                            'apartment' => 'cat1',
                            'commercial' => 'cat2',
                            'land-or-plot' => 'cat3',
                            'farm' => 'cat4',
                            default => '',
                        };
                    @endphp
                    <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 filter-item {{ $propertyFilterClass }}">
                        <div class="popular-list-1 style-4">
                            <div class="thumb-wrapper">
                                <div class="th-slider"
                                    data-slider-options='{"loop":false, "autoplay": false,"autoHeight": true, "effect":"fade"}'>
                                    <div class="swiper-wrapper">
                                        @forelse($listingImages as $listingImage)
                                            <div class="swiper-slide">
                                                <a class="popular-popup-image"
                                                    href="{{ Storage::disk('public')->url($listingImage->path) }}">
                                                    <img src="{{ Storage::disk('public')->url($listingImage->path) }}"
                                                        alt="{{ $listingImage->alt_text ?: $property->title }}">
                                                </a>
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
                                    <div class="icon-wrap">
                                        <button class="slider-arrow slider-prev">
                                            <i class="far fa-arrow-left">
                                            </i>
                                        </button> <button class="slider-arrow slider-next">
                                            <i class="far fa-arrow-right">
                                            </i>
                                        </button>
                                    </div>
                                </div>
                                <div class="actions">
                                    <a href="#" class="icon-btn">
                                        <i class="fas fa-heart">
                                        </i>
                                    </a>
                                </div>
                                <div class="actions-style-2-wrapper">
                                    <div class="actions style-2">
                                        <a href="home-9.html#" class="icon-btn">
                                            <span class="action-text">Add To Favorite</span> <i
                                                class="fa-solid fa-bookmark">
                                            </i> </a>
                                        <a href="{{ $listingImages->isNotEmpty() ? Storage::disk('public')->url($listingImages->first()->path) : route('properties.show', $property) }}"
                                            class="icon-btn {{ $listingImages->isNotEmpty() ? 'popular-popup-image' : '' }}">
                                            <span class="action-text">View all img</span> <i class="fa-solid fa-camera">
                                            </i>
                                        </a>
                                    </div>
                                </div>
                                <div class="popular-badge">
                                    <img src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}" alt="icon">
                                    <p>{{ $property->listing_type === 'rent' ? 'For Rent' : ($property->listing_type === 'lease' ? 'For Lease' : 'For Sale') }}
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
                                        </div>
                                        {{ trim(($property->address ? $property->address . ', ' : '') . $property->city) }}
                                    </div>
                                </div>
                                <ul class="property-featured">
                                    <li>
                                        <div class="icon">
                                            <img src="{{ asset('theme-assets/img/icon/bed.svg') }}" alt="icon">
                                        </div>Bed {{ $property->bedrooms ?? '—' }}
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <img src="{{ asset('theme-assets/img/icon/bath.svg') }}" alt="icon">
                                        </div>Bath {{ $property->bathrooms ?? '—' }}
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <img src="{{ asset('theme-assets/img/icon/sqft.svg') }}" alt="icon">
                                        </div>
                                        {{ $property->area ? number_format((float) $property->area) . ' ' . ($property->area_unit ?? 'sqft') : '—' }}
                                    </li>
                                </ul>
                                <div class="property-bottom">
                                    <h6 class="box-title">
                                        {{ $property->price ? number_format((float) $property->price, 2) : $property->price_label ?? 'Price on request' }}
                                    </h6>
                                    <a class="th-btn sm style3 pill"
                                        href="{{ route('properties.show', $property) }}">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">No published property listings yet.</div>
                @endforelse
            </div>
        </div>
        </div>
    </section>
    <div class="gallery-sec-4" id="gallery" @unless ($homepageSectionEnabled['featured-gallery.home-9'] ?? true) hidden @endunless>
        <div class="gallery-video-wrap">
            <div class="video-box1">
                <a href="{{ $galleryConfig['video_url'] ?? '#' }}" class="play-btn popup-video">
                    <i class="fa-sharp fa-solid fa-play">
                    </i>
                </a>
            </div>
        </div>
        <div class="swiper th-slider gallery-slider4" id="gallerySlider4"
            data-slider-options='{"effect":"fade", "loop":true, "autoHeight": "true"}'>
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="gallery-4-inner"
                        data-bg-src="{{ $galleryItems->get(0)?->image ? Storage::disk('public')->url($galleryItems->get(0)->image) : '' }}"
                        style="background-color: #f4f5f6">
                        <div class="container">
                            <div class="row gy-4 align-items-center">
                                <div class="col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                                    <div class="gallery-4-wrap pe-xl-5 me-xl-2" data-ani="slideinup"
                                        data-ani-delay="0.2s">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gallery-4-inner"
                        data-bg-src="{{ $galleryItems->get(1)?->image ? Storage::disk('public')->url($galleryItems->get(1)->image) : '' }}"
                        style="background-color: #f4f5f6">
                        <div class="container">
                            <div class="row gy-4 align-items-center">
                                <div class="col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                                    <div class="gallery-4-wrap pe-xl-5 me-xl-2" data-ani="slideinup"
                                        data-ani-delay="0.2s">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="gallery-4-inner"
                        data-bg-src="{{ $galleryItems->get(2)?->image ? Storage::disk('public')->url($galleryItems->get(2)->image) : '' }}"
                        style="background-color: #f4f5f6">
                        <div class="container">
                            <div class="row gy-4 align-items-center">
                                <div class="col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                                    <div class="gallery-4-wrap pe-xl-5 me-xl-2" data-ani="slideinup"
                                        data-ani-delay="0.2s">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-pagination-wrapper">
                <button data-slider-prev="#gallerySlider4" class="slider-arrow default slider-prev style-2">prev</button>
                <div class="slider-pagination2">

                </div>
                <div class="line">
                </div>
                <button data-slider-next="#gallerySlider4" class="slider-arrow default style-2">Next</button>
            </div>
        </div>
    </div>
    <div class="space overflow-hidden" id="contact-sec" @unless ($homepageSectionEnabled['home-details.home-9'] ?? true) hidden @endunless>
        <div class="container">
            <div class="row">
                <div class="col-xl-6 text-center text-xl-start">
                    <div class="title-area text-left pe-xl-5 me-xl-5">
                        <p class="sub-title style4 text-anime-style-2">{{ $detailsConfig['eyebrow'] ?? 'Home Details' }}
                        </p>
                        <h2 class="sec-title style-2 text-anime-style-3 pe-xl-5 me-xl-5">
                            {{ $detailsConfig['heading'] ?? 'Property overview' }}</h2>
                    </div>
                    <div class="contact-list">
                        <ul>
                            <li>{{ $detailsConfig['location_label'] ?? 'Location' }}
                                <span>{{ $detailsConfig['location'] ?? '' }}</span>
                            </li>
                            <li>{{ $detailsConfig['total_area_label'] ?? 'Total Area' }}
                                <span>{{ $detailsConfig['total_area'] ?? '' }}</span>
                            </li>
                            <li>{{ $detailsConfig['living_space_label'] ?? 'Living Space' }}
                                <span>{{ $detailsConfig['living_space'] ?? '' }}</span>
                            </li>
                            <li>{{ $detailsConfig['bedrooms_label'] ?? 'Bedrooms' }}
                                <span>{{ $detailsConfig['bedrooms'] ?? '' }}</span>
                            </li>
                            <li>{{ $detailsConfig['bathrooms_label'] ?? 'Bathrooms' }}
                                <span>{{ $detailsConfig['bathrooms'] ?? '' }}</span>
                            </li>
                            <li>{{ $detailsConfig['floors_label'] ?? 'Floors' }}
                                <span>{{ $detailsConfig['floors'] ?? '' }}</span>
                            </li>
                        </ul>
                        <div class="btn">
                            <a href="{{ $detailsConfig['button_url'] ?? route('about') }}"
                                class="th-btn style7">{{ $detailsConfig['button_label'] ?? 'Discover More' }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 mt-40 mt-xl-0">
                    <div class="img-box4 fadeinup wow" data-wow-duration="1.5s" data-wow-delay="0.3s">
                        <img src="{{ $detailsConfig['image'] ?? asset('theme-assets/img/about/contact-img3.png') }}"
                            alt="{{ $detailsConfig['heading'] ?? 'Property overview' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="team-area-1 space-bottom overflow-hidden" @unless ($homepageSectionEnabled['team.home-2'] ?? true) hidden @endunless>
        <div class="shape-mockup jump d-none d-lg-block" style="top: 6%; right: 4%;">
            <img src="{{ asset('theme-assets/img/shape/team-2-shape.png') }}" alt="img">
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7">
                    <div class="title-area text-center mt-1">
                        <p class="sub-title style4 text-anime-style-2">{{ $teamConfig['eyebrow'] ?? 'Team Members' }}</p>
                        <h2 class="sec-title style-2 text-anime-style-3">
                            {{ $teamConfig['heading'] ?? 'Meet Our Pillar Agents' }}</h2>
                    </div>
                </div>
            </div>
            <div class="slider-area mb-20">
                <div class="swiper th-slider has-shadow" id="teamSlider1"
                    data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"2"},"1200":{"slidesPerView":"3"},"1400":{"slidesPerView":"4"}}, "autoHeight": "true"}'>
                    <div class="swiper-wrapper">
                        @forelse ($members as $member)
                            <div class="swiper-slide">
                                <div class="team-card style-2"
                                    data-bg-src="{{ asset('theme-assets/img/shape/team-2-bg-shape.png') }}">
                                    <div class="team-img">
                                        @if ($member->photo)
                                            <img src="{{ Storage::disk('public')->url($member->photo) }}"
                                            alt="{{ $member->name }}">@else<div class="team-avatar-placeholder"
                                                aria-label="{{ $member->name }}">
                                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($member->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="team-content">
                                        <div class="left-contet">
                                            <h3 class="box-title">{{ $member->name }}</h3>
                                            <span class="team-desig">{{ $member->designation }}</span>
                                        </div>
                                        <div class="team-social">
                                            <div class="th-social">
                                                @foreach ($member->social_links ?? [] as $network => $url)
                                                    @if (\App\Rules\SafeLink::isHttpUrl($url))
                                                        <a target="_blank" rel="noopener noreferrer"
                                                            href="{{ \App\Rules\SafeLink::sanitizeHttp($url) }}"
                                                            aria-label="{{ ucfirst($network) }}">
                                                            <i class="fab fa-{{ $network }}">
                                                            </i>
                                                        </a>
                                                    @endif
                                                    @endforeach @if ($member->email)
                                                        <a href="mailto:{{ $member->email }}"
                                                            aria-label="Email {{ $member->name }}">
                                                            <i class="fa-solid fa-envelope">
                                                            </i>
                                                        </a>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">Team profiles will be published soon.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <button data-slider-prev="#teamSlider1" class="slider-arrow slider-prev">
                <i class="far fa-arrow-left">
                </i>
            </button> <button data-slider-next="#teamSlider1" class="slider-arrow slider-next">
                <i class="far fa-arrow-right">
                </i>
            </button>
        </div>
        <div class="row gy-40 justify-content-center">
            <div class="col-xl-8">
                <p class="chat-text-wrap text-center pe-xl-5 ps-xl-5 mb-0 fadeinup wow" data-wow-delay="0.2s">Explore
                    Property agents are here to help with all your buying, renting and selling goals. Find the home of your
                    dreams with an expert you can trust. <a class="text-theme" href="{{ route('contact') }}"> Let’s
                        chat</a>
                </p>
            </div>
        </div>
        </div>
    </section>
    <section class="space-bottom space-extra-bottom" @unless ($homepageSectionEnabled['testimonials.home-3'] ?? true) hidden @endunless>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7">
                    <div class="title-area text-center mt-1">
                        <p class="sub-title style4 text-anime-style-2">Testimonials</p>
                        <h2 class="sec-title style-2 text-anime-style-3">
                            {{ $testimonialsConfig['heading'] ?? 'What Clients Say About Pillar' }}</h2>
                    </div>
                </div>
            </div>
            <div class="row gy-4">
                <div class="col-lg-4">
                    <div class="testi-3-rating me-xxl-5">
                        <div class="rating-count">
                            <h2>{{ $averageRating ?: '—' }}</h2>
                            <div class="star-shape spin">
                                <img src="{{ asset('theme-assets/img/shape/download-circle.png') }}" alt="img">
                            </div>
                        </div>
                        <div class="reviews-star">
                            <div class="th-social">
                                <i class="fa-solid fa-star">
                                </i> <i class="fa-solid fa-star">
                                </i> <i class="fa-solid fa-star">
                                </i> <i class="fa-solid fa-star">
                                </i> <i class="fa-solid fa-star">
                                </i>
                            </div>
                            <h4 class="review-title">
                                <span class="number">{{ $testimonialsConfig['trust_score_prefix'] ?? 'Client rating' }}
                                    <span class="counter-number">{{ $reviewCount ?? 0 }}</span>
                                    {{ $testimonialsConfig['reviews_suffix'] ?? 'reviews' }}
                            </h4>
                        </div>
                        <a class="th-btn tab-btn style3 radius"
                            href="{{ route('testimonials.index') }}">{{ $testimonialsConfig['view_all_label'] ?? 'View All Reviews' }}</a>
                        <div class="testi-bottom-item">
                            <div class="trust-content">
                                <div class="icon">
                                    <img src="{{ asset('theme-assets/img/icon/star-icon.svg') }}" alt="img">
                                </div>
                                <h4 class="box-title">{{ $testimonialsConfig['trust_brand'] ?? 'Client reviews' }}</h4>
                            </div>
                            <div class="bottom">
                                <div class="avatar">
                                    <img src="{{ asset('theme-assets/img/hero/heror-1-avator-group.png') }}"
                                        alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="top-slider-wrapper-main style-2">
                        <div class="top-slider-wrapper">
                            <div class="row gy-4">
                                @php
                                    $testimonialColumns = collect($testimonials)->chunk(
                                        (int) ceil(max(1, collect($testimonials)->count()) / 2),
                                    );
                                @endphp
                                @forelse($testimonialColumns as $testimonialColumn)
                                    <div class="col-lg-6">
                                        <div class="top-slider-item-wrapper">
                                            @foreach ($testimonialColumn as $testimonial)
                                                <div class="testi-block bg-smoke">
                                                    <div class="top-wrap">
                                                        <div class="quote">
                                                            <img src="{{ asset('theme-assets/img/icon/quote.svg') }}"
                                                                alt="quote">
                                                        </div>
                                                        <div class="logo">
                                                            <img src="{{ asset('theme-assets/img/icon/brand-logo-1.png') }}"
                                                                alt="Team">
                                                        </div>
                                                    </div>
                                                    <div class="testi-content">
                                                        <p class="box-text">{{ $testimonial->quote }}</p>
                                                    </div>
                                                    <div class="bottom-wrap">
                                                        <div class="content">
                                                            <h3 class="box-title">{{ $testimonial->name }}</h3>
                                                            <p class="text">
                                                                {{ trim(($testimonial->designation ?? '') . ' ' . ($testimonial->company ?? '')) }}
                                                            </p>
                                                        </div>
                                                        <div class="team-img">
                                                            @if ($testimonial->photo)
                                                                <img src="{{ Storage::disk('public')->url($testimonial->photo) }}"
                                                                alt="{{ $testimonial->name }}">@else<div
                                                                    class="team-avatar-placeholder"
                                                                    aria-label="{{ $testimonial->name }}">
                                                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($testimonial->name, 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <div class="box-review">
                                                                <i class="fa-sharp fa-solid fa-star">

                                                                </i>
                                                                <div class="rating">
                                                                    <span>{{ $testimonial->rating ? number_format((float) $testimonial->rating, 1) : '—' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted">Client testimonials will appear here.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
    </section>
    <section class="space bg-smoke overflow-hidden" id="blog-sec"
        @unless ($homepageSectionEnabled['blog.home-2'] ?? true) hidden @endunless>
        <div class="container">
            <div class="row justify-content-lg-between justify-content-center align-items-end">
                <div class="col-lg-6">
                    <div class="title-area text-left text-lg-start">
                        <p class="sub-title style4 bg-white text-anime-style-1">
                            {{ $blogConfig['eyebrow'] ?? 'Latest News' }}</p>
                        <h2 class="sec-title style-2 text-anime-style-2">
                            {{ $blogConfig['heading'] ?? 'Helpful Real Estate Articles' }}</h2>
                        @if (!empty($blogConfig['description']))
                            <p>{{ $blogConfig['description'] }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-auto d-none d-lg-block">
                    <div class="sec-btn">
                        <a href="{{ route('blog.index') }}" class="th-btn style3 fadeinup wow"
                            data-wow-delay=".4s">{{ $blogConfig['button_label'] ?? 'Explore All' }} <i
                                class="fas fa-arrow-up-right">
                            </i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="slider-area">
                <div class="swiper th-slider has-shadow" id="blogSlider1"
                    data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"1"},"992":{"slidesPerView":"2"},"1200":{"slidesPerView":"2"},"1300":{"slidesPerView":"3"}}, "autoHeight": "true"}'>
                    <div class="swiper-wrapper">
                        @forelse ($posts as $post)
                            <div class="swiper-slide">
                                <div class="blog-card style-5 bg-smoke">
                                    <div class="blog-img">
                                        <a href="{{ route('blog.show', $post) }}">
                                            @if ($post->featured_image)
                                                <img src="{{ Storage::disk('public')->url($post->featured_image) }}"
                                                alt="{{ $post->title }}">@else<div
                                                    class="blog-image-placeholder bg-smoke d-flex align-items-center justify-content-center">
                                                    <span class="text-muted">Image pending</span>
                                                </div>
                                            @endif
                                        </a>
                                        <div class="date">
                                            <a
                                                href="{{ route('blog.show', $post) }}">{{ $post->published_at?->format('d M') }}</a>
                                        </div>
                                    </div>
                                    <div class="blog-content">
                                        <div class="blog-meta">
                                            <a href="{{ route('blog.show', $post) }}">
                                                <i class="fa-solid fa-user">
                                                </i>
                                                {{ $post->author?->name ?? 'Buraaqh Properties' }}</a> <a
                                                href="{{ route('blog.show', $post) }}">
                                                <i class="fa-solid fa-comments">
                                                </i>
                                                {{ $post->approved_comments_count ?? 0 }} Comments</a>
                                        </div>
                                        <h3 class="box-title">
                                            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                                        </h3>
                                        <a href="{{ route('blog.show', $post) }}" class="th-btn style3">Read More</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">No articles have been published yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <button data-slider-prev="#blogSlider1" class="slider-arrow slider-prev">
                <i class="far fa-arrow-left">
                </i>
            </button> <button data-slider-next="#blogSlider1" class="slider-arrow slider-next">
                <i class="far fa-arrow-right">
                </i>
            </button>
        </div>
    </section>
@endsection
