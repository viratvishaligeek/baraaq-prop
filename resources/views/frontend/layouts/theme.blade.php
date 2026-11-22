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
        $defaultSocialImage = !empty($siteSettings['default_og_image'])
            ? asset('storage/' . $siteSettings['default_og_image'])
            : asset('theme-assets/img/hero/hero-10-1.jpg');
    @endphp
    <meta property="og:image" content="@yield('og_image', $defaultSocialImage)">
    <meta property="og:site_name" content="{{ $siteSettings['site_title'] ?? config('app.name') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $siteSettings['meta_title'] ?? ($siteSettings['site_title'] ?? config('app.name')))">
    <meta name="twitter:description" content="@yield('meta_description', $siteSettings['meta_description'] ?? '')">
    <meta name="twitter:image" content="@yield('og_image', $defaultSocialImage)">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('theme-assets/img/favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('theme-assets/img/favicons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('theme-assets/img/favicons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('theme-assets/img/favicons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('theme-assets/img/favicons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('theme-assets/img/favicons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('theme-assets/img/favicons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('theme-assets/img/favicons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180"
        href="{{ asset('theme-assets/img/favicons/apple-icon-180x180.png') }}">
    <link rel="icon"
        href="{{ !empty($siteSettings['favicon']) ? asset('storage/' . $siteSettings['favicon']) : asset('theme-assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('theme-assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96"
        href="{{ asset('theme-assets/img/favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('theme-assets/img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('theme-assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('theme-assets/img/favicons/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
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
            '@type' => 'Organization',
            'name' => $siteSettings['business_name'] ?? ($siteSettings['site_title'] ?? config('app.name')),
            'url' => url('/'),
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
            <div class="txt-loading">
                <span data-text-preloader="P" class="letters-loading">P </span>
                <span data-text-preloader="I" class="letters-loading">I </span>
                <span data-text-preloader="L" class="letters-loading">L </span>
                <span data-text-preloader="L" class="letters-loading">L
                </span>
                <span data-text-preloader="E" class="letters-loading">E </span>
                <span data-text-preloader="R" class="letters-loading">R</span>
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
                        alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">@else<img
                            src="{{ asset('theme-assets/img/logo.svg') }}"
                            alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">
                    @endif
                </a>
            </div>
            <div class="th-mobile-menu">
                <ul>@include('frontend.partials.navigation')</ul>
            </div>
        </div>
    </div>
    @if (isset($homepage) && $homepage?->design_key === 'home-9')
        <header class="th-header header-default header-layout9">
            <div class="sticky-wrapper" data-sticky-enabled="{{ $siteSettings['sticky_header'] ?? true ? 'true' : 'false' }}">
                <div class="menu-area">
                    <div class="container">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-auto">
                                <div class="header-logo">
                                    <a href="{{ route('home') }}">
                                        @if (!empty($siteSettings['logo']))
                                            <img src="{{ asset('storage/'.$siteSettings['logo']) }}" alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">
                                        @else
                                            <img src="{{ asset('theme-assets/img/logo.svg') }}" alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">
                                        @endif
                                    </a>
                                </div>
                            </div>
                            <div class="col-auto">
                                <nav class="main-menu d-none d-lg-inline-block">
                                    <ul>@include('frontend.partials.navigation')</ul>
                                </nav>
                                <button type="button" class="th-menu-toggle d-block d-lg-none"><i class="far fa-bars"></i></button>
                            </div>
                            <div class="col-auto d-none d-xl-block">
                                <div class="header-button">
                                    <a href="{{ $siteSettings['header_cta_url'] ?? route('properties.index') }}" class="th-btn bg-black">
                                        <i class="fa-regular fa-house-chimney me-2"></i> {{ $siteSettings['header_cta_label'] ?? 'Add Listing' }}
                                    </a>
                                    <button type="button" class="icon-btn searchBoxToggler"><i class="far fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    @else
    <header class="th-header header-default header-layout1">
        <div class="header-top">
            <div class="container">
                <div class="row justify-content-center justify-content-lg-between align-items-center gy-2">
                    <div class="col-auto d-none d-lg-block">
                        <div class="header-links">
                            <ul>
                                <li>
                                    <i class="fa-solid fa-envelope">
                                    </i> <a
                                        href="mailto:{{ $siteSettings['site_email'] ?? '' }}">{{ \App\Models\Setting::read('site_email', '') }}</a>
                                </li>
                                <li>
                                    <i class="fa-solid fa-phone">
                                    </i> <a
                                        href="tel:{{ preg_replace('/\D+/', '', \App\Models\Setting::read('site_phone', '')) }}">{{ $siteSettings['site_phone'] ?? '' }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="header-links">
                            <ul>
                                <li>
                                    <div class="th-social">
                                        @if (!empty($siteSettings['social_facebook']))
                                            <a href="{{ $siteSettings['social_facebook'] }}" target="_blank"
                                                rel="noopener">
                                                <i class="fab fa-facebook-f">
                                                </i>
                                            </a>
                                            @endif @if (!empty($siteSettings['social_instagram']))
                                                <a href="{{ $siteSettings['social_instagram'] }}" target="_blank"
                                                    rel="noopener">
                                                    <i class="fab fa-instagram">
                                                    </i>
                                                </a>
                                                @endif @if (!empty($siteSettings['social_linkedin']))
                                                    <a href="{{ $siteSettings['social_linkedin'] }}" target="_blank"
                                                        rel="noopener">
                                                        <i class="fab fa-linkedin-in">
                                                        </i>
                                                    </a>
                                                    @endif @if (!empty($siteSettings['social_youtube']))
                                                        <a href="{{ $siteSettings['social_youtube'] }}"
                                                            target="_blank" rel="noopener">
                                                            <i class="fab fa-youtube">
                                                            </i>
                                                        </a>
                                                        @endif @if (!empty($siteSettings['social_x']))
                                                            <a href="{{ $siteSettings['social_x'] }}" target="_blank"
                                                                rel="noopener">
                                                                <i class="fab fa-x-twitter">
                                                                </i>
                                                            </a>
                                                            @endif @if (!empty($siteSettings['whatsapp_number']))
                                                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $siteSettings['whatsapp_number']) }}"
                                                                    target="_blank" rel="noopener">
                                                                    <i class="fab fa-whatsapp">
                                                                    </i>
                                                                </a>
                                                            @endif
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-wrapper"
            data-sticky-enabled="{{ $siteSettings['sticky_header'] ?? true ? 'true' : 'false' }}">
            <div class="menu-area">
                <div class="container">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="header-logo">
                                <a href="{{ route('home') }}">
                                    @if (!empty($siteSettings['logo']))
                                        <img src="{{ asset('storage/' . $siteSettings['logo']) }}"
                                        alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">@else<img
                                            src="{{ asset('theme-assets/img/logo-white.svg') }}"
                                            alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">
                                    @endif
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            @php
                                $primaryMenu = \App\Models\Menu::cached(
                                    $siteSettings['header_menu_location'] ?? 'primary',
                                );
                            @endphp
                            @if ($primaryMenu)
                                <nav class="main-menu d-none d-lg-inline-block">
                                    <ul>@include('frontend.partials.navigation')</ul>
                                </nav>
                            @else
                                <nav class="main-menu d-none d-lg-inline-block">
                                    <ul>@include('frontend.partials.navigation')</ul>
                                </nav>
                            @endif
                            <button type="button" class="th-menu-toggle d-block d-lg-none">
                                <i class="far fa-bars">
                                </i>
                            </button>
                        </div>
                        <div class="col-auto d-none d-xl-block">
                            <div class="header-button">
                                <a href="{{ $siteSettings['header_cta_url'] ?? route('properties.index') }}" class="th-btn outline pill text-white">
                                    <i class="fa-regular fa-house-chimney me-2">
                                    </i> {{ $siteSettings['header_cta_label'] ?? 'Browse Properties' }} </a>
                                <button type="button" class="icon-btn searchBoxToggler text-white">
                                    <i class="far fa-search">
                                    </i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
 
    @endif
    <main>
        @yield('content')
    </main>
    <footer class="footer-wrapper footer-layout1">
        <div class="footer-top-shape animation-infinite"
            data-bg-src="{{ asset('theme-assets/img/icon/footer-top-shape.png') }}">
        </div>
        <div class="widget-area">
            <div class="container">
                @php
                    $footerMenu = \App\Models\Menu::cached($siteSettings['footer_menu_location'] ?? 'footer');
                @endphp
                @if ($footerMenu && $footerMenu->items->isNotEmpty())
                    <nav class="cms-footer-navigation" aria-label="Footer navigation">
                        <ul class="list-inline">@include('frontend.partials.menu-items', ['items' => $footerMenu->items])
                        </ul>
                    </nav>
                @endif
                <div class="footer-all-widget-wrapper">
                    <div class="footer-all-widget-item">
                        

                        <div class="widget footer-widget">
                            <h3 class="widget_title">{{ $siteSettings['footer_about_heading'] ?? 'About' }} {{ $siteSettings['site_title'] ?? config('app.name') }}
                            </h3>
                            <div class="th-widget-about">
                                <p class="about-text">
                                    {{ $siteSettings['footer_description'] ?? ($siteSettings['site_tagline'] ?? '') }}
                                </p>
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
                                <div class="about-logo">
                                    <a href="{{ route('home') }}">
                                        @if (!empty($siteSettings['logo']))
                                            <img src="{{ asset('storage/' . $siteSettings['logo']) }}"
                                            alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">@else<img
                                                src="{{ asset('theme-assets/img/logo-white.svg') }}"
                                                alt="{{ $siteSettings['site_title'] ?? config('app.name') }}">
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    
                        
                    </div>
                    <div class="footer-all-widget-item">
                        <div class="footer-right-wrap">
                            <div class="footer-item-wrap">
                                <div class="footer-item">
                                    

                                    <div class="widget widget_nav_menu footer-widget">
                                        <h3 class="widget_title">{{ $siteSettings['footer_services_heading'] ?? 'Services' }}</h3>
                                        <div class="menu-all-pages-container">
                                            <ul class="menu">
                                                @foreach (\App\Models\Service::published()->orderBy('title')->limit(5)->get() as $footerService)
                                                    <li>
                                                        <a
                                                            href="{{ route('services.show', $footerService) }}">{{ $footerService->title }}</a>
                                                    </li>
                                                @endforeach
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
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                
                                    
                                </div>
                                <div class="footer-item">
                                    <div class="widget widget_banner footer-widget">
                                        <h3 class="widget_title">{{ $siteSettings['footer_location_heading'] ?? 'Pillar Location' }}</h3>
                                        <div class="widget-map">
                                            @if (!empty($siteSettings['footer_map_embed_url']))
                                                <iframe src="{{ $siteSettings['footer_map_embed_url'] }}"></iframe>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                <img src="{{ $galleryImageUrl }}" alt="Pillar property gallery">
                                <a href="{{ $galleryImageUrl }}" class="icon-btn popup-image"><i class="fa-brands fa-instagram"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
                            </div>
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
                            <i class="fal fa-copyright">
                            </i> {{ date('Y') }}
                            {{ $siteSettings['footer_copyright'] ?? ($siteSettings['site_title'] ?? config('app.name')) }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <div class="footer-default-copy-right">
                            <p>{{ $siteSettings['footer_social_heading'] ?? 'Social Media:' }}</p>
                            <div class="th-social">
                                @if (!empty($siteSettings['social_facebook']))
                                    <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" rel="noopener">
                                        <i class="fab fa-facebook-f">
                                        </i>
                                    </a>
                                    @endif @if (!empty($siteSettings['social_instagram']))
                                        <a href="{{ $siteSettings['social_instagram'] }}" target="_blank"
                                            rel="noopener">
                                            <i class="fab fa-instagram">
                                            </i>
                                        </a>
                                        @endif @if (!empty($siteSettings['social_linkedin']))
                                            <a href="{{ $siteSettings['social_linkedin'] }}" target="_blank"
                                                rel="noopener">
                                                <i class="fab fa-linkedin-in">
                                                </i>
                                            </a>
                                            @endif @if (!empty($siteSettings['social_youtube']))
                                                <a href="{{ $siteSettings['social_youtube'] }}" target="_blank"
                                                    rel="noopener">
                                                    <i class="fab fa-youtube">
                                                    </i>
                                                </a>
                                                @endif @if (!empty($siteSettings['social_x']))
                                                    <a href="{{ $siteSettings['social_x'] }}" target="_blank"
                                                        rel="noopener">
                                                        <i class="fab fa-x-twitter">
                                                        </i>
                                                    </a>
                                                    @endif @if (!empty($siteSettings['whatsapp_number']))
                                                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $siteSettings['whatsapp_number']) }}"
                                                            target="_blank" rel="noopener">
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
