<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', $siteSettings['meta_title'] ?? ($siteSettings['site_title'] ?? config('app.name')))</title>
    <meta name="author"
        content="{{ $siteSettings['business_name'] ?? ($siteSettings['site_title'] ?? config('app.name')) }}">
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::read('meta_description', ''))">
    <meta name="keywords" content="@yield('meta_keywords', $siteSettings['meta_keywords'] ?? '')">
    <meta name="robots" content="@yield('meta_robots', $siteSettings['meta_robots'] ?? 'index,follow')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', $siteSettings['meta_title'] ?? ($siteSettings['site_title'] ?? config('app.name')))">
    <meta property="og:description" content="@yield('meta_description', $siteSettings['meta_description'] ?? '')">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    @php
        $defaultSocialPath = ltrim((string) ($siteSettings['default_og_image'] ?? ''), '/');
        $defaultSocialImage = $defaultSocialPath === ''
            ? asset('theme-assets/img/hero/hero-9-right.png')
            : (\App\Rules\SafeLink::isHttpUrl($defaultSocialPath)
                ? $defaultSocialPath
                : (\Illuminate\Support\Str::startsWith($defaultSocialPath, 'theme-assets/')
                    ? asset($defaultSocialPath)
                    : Storage::disk('public')->url($defaultSocialPath)));
    @endphp
    <meta property="og:image" content="@yield('og_image', $defaultSocialImage)">
    <meta property="og:site_name" content="{{ $siteSettings['site_title'] ?? config('app.name') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $siteSettings['meta_title'] ?? ($siteSettings['site_title'] ?? config('app.name')))">
    <meta name="twitter:description" content="@yield('meta_description', $siteSettings['meta_description'] ?? '')">
    <meta name="twitter:image" content="@yield('og_image', $defaultSocialImage)">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    @if (!empty($siteSettings['favicon']))
        <link rel="icon" href="{{ asset('storage/' . $siteSettings['favicon']) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $siteSettings['favicon']) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme-assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme-assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme-assets/css/style.css') }}">
    <style>
        :root {
            --theme-color:
                {{ $siteSettings['primary_color'] ?? '#068FFF' }};
            --theme-color2:
                {{ $siteSettings['secondary_color'] ?? '#044DBC' }};
        }

        .site-brand-wordmark {
            display: inline-block;
            color: var(--title-color, #17202a);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: clamp(18px, 2vw, 24px);
            font-weight: 800;
            letter-spacing: -.045em;
            line-height: 1.05;
            white-space: nowrap;
        }
        .site-brand-wordmark--inverse { color: #fff; }
        .mobile-logo .site-brand-wordmark { font-size: 20px; }

        html, body {
            overflow-x: clip;
        }

        .sticky-wrapper[data-sticky-enabled="false"] {
            position: relative !important;
            top: auto !important
        }
    </style>@php echo $siteSettings['head_scripts'] ?? ''; @endphp

    @php
        $organization = [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateAgent',
            'name' => $siteSettings['business_name'] ?? ($siteSettings['site_title'] ?? config('app.name')),
            'url' => url('/'),
            'areaServed' => ['@type' => 'City', 'name' => $siteSettings['service_area_city'] ?? 'Bareilly'],
            'logo' => !empty($siteSettings['logo']) ? Storage::disk('public')->url($siteSettings['logo']) : null,
            'email' => $siteSettings['site_email'] ?? null,
            'telephone' => $siteSettings['site_phone'] ?? null,
            'address' => !empty($siteSettings['site_address'])
                ? [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $siteSettings['site_address'],
                ]
                : null,
            'sameAs' => array_values(
                array_filter([
                    $siteSettings['social_facebook'] ?? null,
                    $siteSettings['social_instagram'] ?? null,
                    $siteSettings['social_linkedin'] ?? null,
                    $siteSettings['social_youtube'] ?? null,
                    $siteSettings['social_x'] ?? null,
                ]),
            ),
        ];

        // Remove null and empty values
        $organization = array_filter($organization, function ($value) {
            return $value !== null && $value !== '';
        });
    @endphp

    <script type="application/ld+json">
    {!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
        </script>

    @yield('structured_data')
</head>

<body>
    <div class="preloader">
        <button class="th-btn preloaderCls">Cancel Preloader</button>
        <div id="preloader" class="preloader-inner">
            <div class="txt-loading" aria-label="{{ $siteSettings['site_title'] ?? 'Buraaqh Properties' }}">
                @foreach (mb_str_split((string) ($siteSettings['site_title'] ?? 'Buraaqh Properties')) as $letter)
                    <span data-text-preloader="{{ e($letter) }}" class="letters-loading">{{ $letter }}</span>
                @endforeach
            </div>
        </div>
    </div>
    <div class="color-scheme">
        <button class="switchIcon">
            <i class="fa-solid fa-palette">
            </i>
        </button>
        <h4 class="color-scheme-title">
            <i class="far fa-palette">
            </i> Color Switcher
        </h4>
        <p class="color-scheme-text">Check template with your color</p>
        <div class="color-switch-btns">
            <button data-color="#6240CF">
                <i class="fa-solid fa-droplet">
                </i>
            </button>
            <button data-color="#068FFF">
                <i class="fa-solid fa-droplet">
                </i>
            </button> <button data-color="#044DBC">
                <i class="fa-solid fa-droplet">
                </i>
            </button> <button data-color="#FFAF00">
                <i class="fa-solid fa-droplet">
                </i>
            </button> <button data-color="#F80000">
                <i class="fa-solid fa-droplet">
                </i>
            </button> <button data-color="#231E7A">
                <i class="fa-solid fa-droplet">
                </i>
            </button>
        </div>
        <p class="color-scheme-text">Or custom color..</p>
        <input type="color" id="thcolorpicker" value="#068FFF">
    </div>
    <div class="popup-search-box d-none d-lg-block">
        <button class="searchClose">
            <i class="fal fa-times">
            </i>
        </button>
        <form action="{{ route('properties.index') }}" method="GET">
            <input type="search" name="q" placeholder="Search properties..." aria-label="Search properties">
            <button type="submit">
                <i class="fal fa-search">
                </i>
            </button>
        </form>
    </div>
    @php
        // Resolve the configured menu before both mobile and desktop navigation render.
        $primaryMenu = \App\Models\Menu::cached($siteSettings['header_menu_location'] ?? 'primary');
    @endphp
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle">
                <i class="fal fa-times">
                </i>
            </button>
            <div class="mobile-logo">
                <a href="{{ route('home') }}">
                    @if (!empty($siteSettings['logo']))
                        <img src="{{ asset('storage/' . $siteSettings['logo']) }}"
                        alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">@else<span class="site-brand-wordmark">{{ $siteSettings['site_title'] ?? config('app.name') }}</span>
                    @endif
                </a>
            </div>
            <div class="th-mobile-menu">
                <ul>@include('frontend.partials.navigation')</ul>
            </div>
        </div>
    </div>
    <header class="th-header header-default {{ request()->routeIs('home') ? 'header-layout9' : '' }}">
        @unless (request()->routeIs('home'))
            <div class="header-top">
                <div class="container">
                    <div class="row justify-content-center justify-content-lg-between align-items-center gy-2">
                        <div class="col-auto d-none d-lg-block">
                            <div class="header-links"><ul>
                                @if (!empty($siteSettings['site_email']))
                                    <li><i class="fa-solid fa-envelope"></i> <a href="mailto:{{ $siteSettings['site_email'] }}">{{ $siteSettings['site_email'] }}</a></li>
                                @endif
                                @if (!empty($siteSettings['site_phone']))
                                    <li><i class="fa-solid fa-phone"></i> <a href="tel:{{ preg_replace('/\D+/', '', $siteSettings['site_phone']) }}">{{ $siteSettings['site_phone'] }}</a></li>
                                @endif
                            </ul></div>
                        </div>
                        <div class="col-auto">
                            <div class="header-links"><ul>
                                <li><div class="th-social">
                                    @foreach (['facebook' => 'social_facebook', 'twitter' => 'social_x', 'linkedin' => 'social_linkedin', 'whatsapp' => 'whatsapp_number'] as $network => $setting)
                                        @if (!empty($siteSettings[$setting]))
                                            <a href="{{ $network === 'whatsapp' ? 'https://wa.me/' . preg_replace('/\D+/', '', $siteSettings[$setting]) : \App\Rules\SafeLink::sanitizeHttp($siteSettings[$setting]) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network) }}"><i class="fab fa-{{ $network === 'facebook' ? 'facebook-f' : ($network === 'linkedin' ? 'linkedin-in' : $network) }}"></i></a>
                                        @endif
                                    @endforeach
                                </div></li>
                                <li class="lang-wrapper"><div class="lang-menu">
                                    <div class="icon"><img src="{{ asset('theme-assets/img/icon/english.png') }}" alt="Language"></div>
                                    <select class="form-select nice-select" aria-label="Language">
                                        <option selected>English</option><option>Spanish</option><option>Hindi</option>
                                    </select>
                                </div></li>
                            </ul></div>
                        </div>
                    </div>
                </div>
            </div>
        @endunless
        <div class="sticky-wrapper" data-sticky-enabled="{{ $siteSettings['sticky_header'] ?? true ? 'true' : 'false' }}">
            <div class="menu-area">
                <div class="container">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="header-logo">
                                <a href="{{ route('home') }}">
                                    @if (!empty($siteSettings['logo']))
                                        <img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">
                                    @else
                                        <span class="site-brand-wordmark">{{ $siteSettings['site_title'] ?? config('app.name') }}</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <nav class="main-menu d-none d-lg-inline-block"><ul>@include('frontend.partials.navigation')</ul></nav>
                            <button type="button" class="th-menu-toggle d-block d-lg-none" aria-label="Open navigation"><i class="far fa-bars"></i></button>
                        </div>
                        <div class="col-auto d-none d-xl-block">
                            <div class="header-button">
                                <a href="{{ !empty($siteSettings['header_cta_url']) ? \App\Rules\SafeLink::sanitize($siteSettings['header_cta_url']) : route('contact') }}" class="th-btn bg-black {{ request()->routeIs('home') ? '' : 'radius' }}">
                                    <i class="fa-regular fa-house-chimney me-2"></i> {{ $siteSettings['header_cta_label'] ?? 'Add Listing' }}
                                </a>
                                <button type="button" class="sidemenu-bar sideMenuInfo {{ request()->routeIs('home') ? 'pill' : '' }}" aria-label="Open information panel"><span></span><span></span><span></span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    <footer class="footer-wrapper {{ request()->routeIs('home') ? 'footer-layout1' : 'footer-default' }}">
        @if (request()->routeIs('home'))
            <div class="footer-top-shape animation-infinite" data-bg-src="{{ asset('theme-assets/img/icon/footer-top-shape.png') }}"></div>
        @endif
            <div class="widget-area">
            <div class="container">
                @php
                    $footerMenu = \App\Models\Menu::cached($siteSettings['footer_menu_location'] ?? '');
                    $footerQuickLinks = \App\Models\Menu::cached($siteSettings['footer_quick_links_menu_location'] ?? '');
                    $footerSupport = \App\Models\Menu::cached($siteSettings['footer_support_menu_location'] ?? '');
                @endphp

                <div class="footer-all-widget-wrapper">
                    <div class="footer-all-widget-item">
                        

                        <div class="widget footer-widget">
                            @if (request()->routeIs('home'))
                                <h3 class="widget_title">{{ $siteSettings['footer_about_heading'] ?? ('About ' . ($siteSettings['site_title'] ?? config('app.name'))) }}</h3>
                            @endif
                            <div class="th-widget-about">
                                @unless (request()->routeIs('home'))
                                    <div class="about-logo">
                                        <a href="{{ route('home') }}">
                                            @if (!empty($siteSettings['logo']))
                                                <img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">
                                            @else
                                                <span class="site-brand-wordmark site-brand-wordmark--inverse">{{ $siteSettings['site_title'] ?? config('app.name') }}</span>
                                            @endif
                                        </a>
                                    </div>
                                @endunless
                                <p class="about-text">{{ $siteSettings['footer_description'] ?? '' }}</p>
                                <div class="footer-info-wrap">
                                    <div class="footer-info">
                                        <i class="fas fa-phone">

                                        </i>
                                        <p class="info-box_link">
                                            <a
                                                href="tel:{{ preg_replace('/\D+/', '', $siteSettings['site_phone'] ?? '') }}">{{ $siteSettings['site_phone'] ?? '' }}</a>
                                        </p>
                                    </div>
                                    <div class="footer-info">
                                        <i class="fas fa-envelope">

                                        </i>
                                        <p class="info-box_link">
                                            <a
                                                href="mailto:{{ $siteSettings['site_email'] ?? '' }}">{{ $siteSettings['site_email'] ?? '' }}</a>
                                        </p>
                                    </div>
                                    <div class="footer-info">
                                        <i class="fas fa-location-dot">

                                        </i>
                                        <p class="info-box_link">
                                            <span>{{ $siteSettings['site_address'] ?? '' }}</span>
                                        </p>
                                    </div>
                                </div>
                                @if (request()->routeIs('home'))
                                    <div class="about-logo">
                                        <a href="{{ route('home') }}">
                                            @if (!empty($siteSettings['logo']))
                                                <img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">
                                            @else
                                                <span class="site-brand-wordmark site-brand-wordmark--inverse">{{ $siteSettings['site_title'] ?? config('app.name') }}</span>
                                            @endif
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    
                        
                    </div>
                    <div class="footer-all-widget-item">
                        <div class="footer-right-wrap">
                            <div class="footer-item-wrap">
                                @php
                                    $footerFeaturedCategories = \App\Models\Category::query()
                                        ->where('type', 'property')->where('is_active', true)->orderBy('name')->limit(5)->get();
                                    $footerFeaturedProperties = \App\Models\Property::published()->where('is_featured', true)->latest()->limit(5)->get();
                                @endphp
                                <div class="footer-item">
                                    <div class="widget widget_nav_menu footer-widget">
                                        <h3 class="widget_title">{{ $siteSettings['footer_featured_heading'] ?? 'Featured Houses' }}</h3>
                                        <div class="menu-all-pages-container">
                                            <ul class="menu">
                                                @if ($footerMenu && $footerMenu->items->isNotEmpty())
                                                    @include('frontend.partials.menu-items', ['items' => $footerMenu->items])
                                                @elseif ($footerFeaturedCategories->isNotEmpty())
                                                    @foreach ($footerFeaturedCategories as $footerCategory)
                                                        <li><a href="{{ route('properties.index', ['category' => $footerCategory->slug]) }}">#{{ $footerCategory->name }}</a></li>
                                                    @endforeach
                                                @else
                                                    @foreach ($footerFeaturedProperties as $footerProperty)
                                                        <li><a href="{{ route('properties.show', $footerProperty) }}">{{ $footerProperty->title }}</a></li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer-item">
                                    

                                    <div class="widget widget_nav_menu footer-widget">
                                        <h3 class="widget_title">{{ $siteSettings['footer_quick_links_heading'] ?? 'Quick Links' }}</h3>
                                        <div class="menu-all-pages-container">
                                            <ul class="menu">
                                                @if ($footerQuickLinks && $footerQuickLinks->items->isNotEmpty())
                                                    @include('frontend.partials.menu-items', ['items' => $footerQuickLinks->items])
                                                @else
                                                    <li><a href="{{ route('properties.index') }}">Properties</a></li>
                                                    <li><a href="{{ route('services.index') }}">Services</a></li>
                                                    <li><a href="{{ route('about') }}">About Us</a></li>
                                                    <li><a href="{{ route('blog.index') }}">Latest News</a></li>
                                                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                
                                    
                                </div>
                                <div class="footer-item">
                                    

                                    <div class="widget widget_nav_menu footer-widget">
                                        <h3 class="widget_title">{{ $siteSettings['footer_support_heading'] ?? 'Support' }}</h3>
                                        <div class="menu-all-pages-container">
                                            <ul class="menu">
                                                @if ($footerSupport && $footerSupport->items->isNotEmpty())
                                                    @include('frontend.partials.menu-items', ['items' => $footerSupport->items])
                                                @else
                                                    <li><a href="{{ route('faq.index') }}">FAQs</a></li>
                                                    <li><a href="{{ route('neighborhood-guides.index') }}">Neighborhood Guide</a></li>
                                                    <li><a href="{{ route('gallery.index') }}">Gallery</a></li>
                                                    <li><a href="{{ route('contact') }}">Help Center</a></li>
                                                    <li><a href="{{ route('contact') }}">Contact Support</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                
                                    
                                </div>
                                @if (\App\Rules\SafeLink::isHttpUrl($siteSettings['footer_map_embed_url'] ?? null) || !empty($siteSettings['site_address']))
                                    <div class="footer-item">
                                        <div class="widget widget_banner footer-widget">
                                            <h3 class="widget_title">{{ $siteSettings['footer_location_heading'] ?? 'Pillar Location' }}</h3>
                                            <div class="widget-map">
                                                <iframe src="{{ \App\Rules\SafeLink::isHttpUrl($siteSettings['footer_map_embed_url'] ?? null) ? \App\Rules\SafeLink::sanitizeHttp($siteSettings['footer_map_embed_url']) : 'https://www.google.com/maps?q=' . urlencode($siteSettings['site_address']) . '&output=embed' }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @unless (request()->routeIs('home'))
                            <div class="footer-right-bottom-wrap">
                                <div class="footer-right-bottom-item help">
                                    <div class="footer-right-bottom-item__thumb help"><img src="{{ asset('theme-assets/img/icon/footer-default-icon-1-1.png') }}" alt=""></div>
                                    <div class="footer-right-bottom-item__content"><h4 class="box-title"><a href="{{ route('contact') }}">{{ $siteSettings['footer_cta_text'] ?? 'Need help buying or selling?' }}</a></h4></div>
                                </div>
                                @if (\App\Rules\SafeLink::isHttpUrl($siteSettings['app_store_url'] ?? null))
                                <div class="footer-right-bottom-item scan">
                                    <div class="footer-right-bottom-item__thumb"><img src="{{ asset('theme-assets/img/icon/footer-default-icon-1-2.png') }}" alt=""></div>
                                    <div class="footer-right-bottom-item__content"><p>Download on the</p><h4 class="box-title"><a href="{{ \App\Rules\SafeLink::sanitizeHttp($siteSettings['app_store_url']) }}" target="_blank" rel="noopener noreferrer">App Store</a></h4></div><div class="footer-right-bottom-item__right"><img src="{{ asset('theme-assets/img/icon/footer-default-icon-1-2-scan.png') }}" alt="App Store QR code"></div>
                                </div>
                                @endif
                                @if (\App\Rules\SafeLink::isHttpUrl($siteSettings['play_store_url'] ?? null))
                                <div class="footer-right-bottom-item scan">
                                    <div class="footer-right-bottom-item__thumb"><img src="{{ asset('theme-assets/img/icon/footer-default-icon-1-3.png') }}" alt=""></div>
                                    <div class="footer-right-bottom-item__content"><p>GET IT ON</p><h4 class="box-title"><a href="{{ \App\Rules\SafeLink::sanitizeHttp($siteSettings['play_store_url']) }}" target="_blank" rel="noopener noreferrer">Google Play</a></h4></div><div class="footer-right-bottom-item__right"><img src="{{ asset('theme-assets/img/icon/footer-default-icon-1-2-scan.png') }}" alt="Google Play QR code"></div>
                                </div>
                                @endif
                            </div>
                            @endunless
                            @if (request()->routeIs('home') && !empty($siteSettings['footer_gallery_images']))
                            <div class="footer-bottom-community">
                                <div class="content">
                                    <p>{{ $siteSettings['footer_gallery_handle'] ?? '' }}</p>
                                    <h4 class="box-title">{{ $siteSettings['footer_gallery_title'] ?? '' }}</h4>
                                </div>
                                <div class="footer-gallery-wrapper">
                    @foreach(($siteSettings['footer_gallery_images'] ?? []) as $galleryImage)
                        @php
                            $galleryMedia = is_numeric($galleryImage) ? \App\Models\Media::query()->find((int) $galleryImage) : null;
                            $galleryImageUrl = $galleryMedia?->url
                                ?? (\Illuminate\Support\Str::startsWith((string) $galleryImage, ['http://', 'https://'])
                                    ? $galleryImage
                                    : asset(ltrim((string) $galleryImage, '/')));
                        @endphp
                        <div class="simple-gallery-card">
                            <div class="gallery-img">
                                <img src="{{ $galleryImageUrl }}" alt="{{ $siteSettings['footer_gallery_title'] ?? ($siteSettings['site_title'] ?? '') }}">
                                <a href="{{ $galleryImageUrl }}" class="icon-btn popup-image"><i class="fa-brands fa-instagram"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-wrap">
            <div class="footer-bottom-top-shape animation-infinite"
                data-bg-src="{{ asset('theme-assets/img/icon/footer-bottom-top-shape.png') }}">
            </div>
            <div class="container">
                <div class="row gy-3 justify-content-lg-between justify-content-center align-items-center">
                    <div class="col-lg-7">
                        <p class="copyright-text">
                            Copyright <i class="fal fa-copyright"></i> {{ date('Y') }} {{ $siteSettings['footer_copyright'] ?? ($siteSettings['site_title'] ?? config('app.name')) }}. {{ $siteSettings['footer_rights_text'] ?? 'All Rights Reserved.' }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <div class="footer-default-copy-right">
                            <p>{{ $siteSettings['footer_social_heading'] ?? 'Social Media:' }}</p>
                            <div class="th-social">
                                @if (\App\Rules\SafeLink::isHttpUrl($siteSettings['social_facebook'] ?? null))
                                    <a href="{{ \App\Rules\SafeLink::sanitizeHttp($siteSettings['social_facebook']) }}" target="_blank" rel="noopener noreferrer">
                                        <i class="fab fa-facebook-f">
                                        </i>
                                    </a>
                                    @endif @if (\App\Rules\SafeLink::isHttpUrl($siteSettings['social_instagram'] ?? null))
                                        <a href="{{ \App\Rules\SafeLink::sanitizeHttp($siteSettings['social_instagram']) }}" target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="fab fa-instagram">
                                            </i>
                                        </a>
                                        @endif @if (\App\Rules\SafeLink::isHttpUrl($siteSettings['social_linkedin'] ?? null))
                                            <a href="{{ \App\Rules\SafeLink::sanitizeHttp($siteSettings['social_linkedin']) }}" target="_blank"
                                                rel="noopener noreferrer">
                                                <i class="fab fa-linkedin-in">
                                                </i>
                                            </a>
                                            @endif @if (\App\Rules\SafeLink::isHttpUrl($siteSettings['social_youtube'] ?? null))
                                                <a href="{{ \App\Rules\SafeLink::sanitizeHttp($siteSettings['social_youtube']) }}" target="_blank"
                                                    rel="noopener noreferrer">
                                                    <i class="fab fa-youtube">
                                                    </i>
                                                </a>
                                                @endif @if (\App\Rules\SafeLink::isHttpUrl($siteSettings['social_x'] ?? null))
                                                    <a href="{{ \App\Rules\SafeLink::sanitizeHttp($siteSettings['social_x']) }}" target="_blank"
                                                        rel="noopener noreferrer">
                                                        <i class="fab fa-x-twitter">
                                                        </i>
                                                    </a>
                                                    @endif @if (!empty($siteSettings['whatsapp_number']))
                                                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $siteSettings['whatsapp_number']) }}"
                                                            target="_blank" rel="noopener noreferrer">
                                                            <i class="fab fa-whatsapp">
                                                            </i>
                                                        </a>
                                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <div class="scroll-top">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;">
            </path>
        </svg>
    </div>
    <script src="{{ asset('theme-assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('theme-assets/js/app.min.js') }}"></script>
    <script src="{{ asset('theme-assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('theme-assets/js/nice-select.min.js') }}"></script>
    <script src="{{ asset('theme-assets/js/main.js') }}"></script>@php echo $siteSettings['body_scripts'] ?? ''; @endphp
</body>

</html>
