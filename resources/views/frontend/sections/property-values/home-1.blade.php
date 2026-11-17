@php
    $visibility = trim(
        (!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '') .
        (!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '') .
        (!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : '')
    );
@endphp

<section class="property-values-sec-1 bg-smoke overflow-hidden{{ $visibility }}"
    @if (!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    @if (!empty($sectionConfig['side_image']))
        <div class="shape-mockup d-none d-xxl-block" data-right="0" data-bottom="0">
            <img src="{{ $sectionConfig['side_image'] }}" alt="">
        </div>
    @endif
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6">
                <div class="property-values-1-video">
                    @if (!empty($sectionConfig['video_url']))
                        <a href="{{ $sectionConfig['video_url'] }}" class="play-btn popup-video" aria-label="Play property values video">
                            <i class="fa-sharp fa-solid fa-play"></i>
                        </a>
                    @endif
                    @if (!empty($sectionConfig['video_image']))
                        <div class="video-thumb">
                            <img src="{{ $sectionConfig['video_image'] }}" alt="{{ $sectionConfig['heading'] ?? 'Property values' }}">
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="property-values-1-content space">
                    <div class="title-area text-left">
                        <p class="sub-title text-anime-style-1"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? 'Property Values' }}</p>
                        <h2 class="sec-title text-anime-style-2">{{ $sectionConfig['heading'] ?? 'Market Your Property with Pillar' }}</h2>
                    </div>
                    <div class="property-values-1-counter-wrapper">
                        @for ($i = 1; $i <= 4; $i++)
                            <div class="property-values-1-counter{{ $i % 2 === 1 ? ' fadeinleft wow' : ' fadeinright wow' }}" data-wow-delay="{{ number_format($i * 0.2, 1) }}s">
                                @if (!empty($sectionConfig['counter_'.$i.'_icon']))
                                    <div class="icon"><img src="{{ $sectionConfig['counter_'.$i.'_icon'] }}" alt=""></div>
                                @endif
                                <h2 class="counter"><span class="counter-number">{{ $sectionConfig['counter_'.$i.'_value'] ?? 0 }}</span>{{ $sectionConfig['counter_'.$i.'_suffix'] ?? '' }}</h2>
                                <span>{{ $sectionConfig['counter_'.$i.'_label'] ?? '' }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
