@php
    $animate = $sectionConfig['animate_content'] ?? true;
    $animationClass = $animate ? ' fadeinup wow' : '';
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $quickTypes = [];
    for ($i = 1; $i <= 4; $i++) {
        $quickTypes[] = [
            'label' => $sectionConfig['quick_type_'.$i.'_label'] ?? '',
            'icon' => $sectionConfig['quick_type_'.$i.'_icon'] ?? '',
        ];
    }
@endphp

<div class="th-hero-wrapper hero-2 {{ $visibility }}" id="hero">
    <div class="slider-2-scroll-down scroll-down">
        <a href="#about-sec" class="scroll-wrap"><span></span></a>
        <span class="scroll-text">{{ $sectionConfig['scroll_label'] ?? 'Scroll' }}</span>
    </div>
    <div class="hero-2-social">
        <div class="th-social">
            @foreach(['facebook','twitter','instagram','linkedin'] as $network)
                @if(!empty($sectionConfig['social_'.$network.'_url']))
                    <a target="_blank" rel="noopener noreferrer" href="{{ $sectionConfig['social_'.$network.'_url'] }}"><i class="fab fa-{{ $network === 'facebook' ? 'facebook-f' : ($network === 'twitter' ? 'twitter' : ($network === 'instagram' ? 'instagram' : 'linkedin-in')) }}"></i></a>
                @endif
            @endforeach
        </div>
    </div>
    <div class="th-container22">
        <div class="hero-inner hero-style2" @if(!empty($sectionConfig['background_mask_image'])) data-mask-src="{{ $sectionConfig['background_mask_image'] }}" @endif>
            @if(!empty($sectionConfig['hero_video_url']))
                <video class="hero-video" id="video" src="{{ $sectionConfig['hero_video_url'] }}" loop muted autoplay playsinline></video>
            @endif
            <div class="row justify-content-center gy-50 gx-40 align-items-center">
                <div class="col-xxl-8 col-xl-9 col-lg-11">
                    <div class="hero-2-content text-center">
                        <div class="hero-img-shape-2{{ $animationClass }}" @if($animate) data-wow-duration="1.5s" data-wow-delay="0.1s" @endif>
                            <div class="logo-icon-wrap">
                                <a href="{{ $sectionConfig['logo_video_url'] ?? '#' }}" class="logo-icon popup-video" aria-label="Play introduction"><i class="fa-sharp fa-solid fa-play"></i></a>
                                <div class="logo-icon-wrap__text"><span class="logo-animation">{{ $sectionConfig['logo_text'] ?? '' }}</span></div>
                            </div>
                        </div>
                        <h1 class="hero-title{{ $animationClass }}" @if($animate) data-wow-duration="1.5s" data-wow-delay="0.3s" @endif>
                            {{ $sectionConfig['heading_prefix'] ?? '' }}
                            <span class="title2 text-theme">{{ $sectionConfig['heading_highlight'] ?? '' }}</span>
                            {{ $sectionConfig['heading_suffix'] ?? '' }}
                        </h1>
                        @if($sectionConfig['show_search_form'] ?? true)
                            <div class="hero-search-wrapper{{ $animationClass }}" @if($animate) data-wow-duration="1.5s" data-wow-delay="0.5s" @endif>
                                <form action="{{ route('properties.index') }}" method="GET" id="heroSearchForm">
                                    <div class="hero-search-group-wrapper">
                                        <div class="form-group">
                                            <label for="select_make1">{{ $sectionConfig['search_property_type_label'] ?? 'Property Type' }}</label>
                                            <select name="property_type" id="select_make1" class="form-select nice-select">
                                                <option value="" disabled selected hidden>{{ $sectionConfig['search_property_type_placeholder'] ?? 'Select Make' }}</option>
                                                @for($i = 1; $i <= 4; $i++)
                                                    @if(!empty($sectionConfig['search_type_'.$i.'_value']))
                                                        <option value="{{ $sectionConfig['search_type_'.$i.'_value'] }}">{{ $sectionConfig['search_type_'.$i.'_label'] ?? $sectionConfig['search_type_'.$i.'_value'] }}</option>
                                                    @endif
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="name11">{{ $sectionConfig['search_location_label'] ?? 'Location' }}</label>
                                            <div class="style-border style-radius col-12">
                                                <input type="text" class="form-control" name="city" id="name11" placeholder="{{ $sectionConfig['search_location_placeholder'] ?? 'Find a location' }}">
                                                <div class="icon"><img src="{{ asset('theme-assets/img/icon/location-icon.svg') }}" alt="icon"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Looking">{{ $sectionConfig['search_keyword_label'] ?? 'Keyword' }}</label>
                                            <div class="style-border style-radius col-12">
                                                <input type="text" class="form-control" name="q" id="Looking" placeholder="{{ $sectionConfig['search_keyword_placeholder'] ?? 'Looking For?' }}">
                                            </div>
                                        </div>
                                        <div class="advance-btn-wrapper">
                                            <div class="advance-search-btn advance-search-active">
                                                <div class="th-btn pill style3 search-btn-item">{{ $sectionConfig['advanced_button_label'] ?? 'Advanced' }} <i class="fa-solid fa-sliders-up"></i></div>
                                            </div>
                                            <button type="submit" class="th-btn pill bg-black"><i class="far fa-search me-2"></i> {{ $sectionConfig['search_button_label'] ?? 'Search' }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                        @if($sectionConfig['show_property_types'] ?? true)
                            <div class="property-type-wrap{{ $animationClass }}" @if($animate) data-wow-duration="1.5s" data-wow-delay="0.7s" @endif>
                                @foreach($quickTypes as $type)
                                    @if($type['label'])
                                        <div class="property-type-item">
                                            <div class="icon"><img src="{{ $type['icon'] }}" alt="{{ $type['label'] }}"></div>
                                            <span>{{ $type['label'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
