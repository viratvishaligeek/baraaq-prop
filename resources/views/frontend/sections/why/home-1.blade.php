@php
    $visibility = trim(
        (!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '') .
        (!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '') .
        (!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : '')
    );
@endphp

<div class="why-sec-1 space{{ $visibility }}" id="why-sec-{{ $section->id }}"
    @if (!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    <div class="container">
        <div class="row justify-content-xl-between justify-content-center align-items-start">
            <div class="col-xl-6">
                <div class="title-area text-center text-xl-start text-left white mb-4 fadeinup wow">
                    <p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? 'Why Choose Us' }}</p>
                    <h2 class="sec-title text-white mb-0 text-anime-style-3">{{ $sectionConfig['heading'] ?? 'Trusted by 100+ Million Buyers' }}</h2>
                </div>
            </div>
            <div class="col-xl-auto">
                <div class="why-right-review-wrap sec-btn fadeinup wow">
                    <div class="trust-content">
                        @if (!empty($sectionConfig['trust_icon']))
                            <div class="icon"><img src="{{ $sectionConfig['trust_icon'] }}" alt=""></div>
                        @endif
                        <h4 class="box-title">{{ $sectionConfig['trust_label'] ?? 'Trustipilot' }}</h4>
                    </div>
                    <div class="client">
                        @if (!empty($sectionConfig['client_avatar_image']))
                            <div class="avatar"><img src="{{ $sectionConfig['client_avatar_image'] }}" alt="Our clients"></div>
                        @endif
                        <div class="content">
                            <div class="th-social"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                            <h4 class="review-title"><span class="number"><span class="counter-number">{{ $sectionConfig['client_count'] ?? 0 }}</span>{{ $sectionConfig['client_suffix'] ?? '' }}</span> {{ $sectionConfig['client_label'] ?? 'clients' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-30 align-items-center justify-content-center">
            @for ($i = 1; $i <= 3; $i++)
                <div class="col-xl-4 col-lg-6 col-md-6 wow {{ ['fadeinleft', 'fadeinup', 'fadeinright'][$i - 1] }}" data-wow-delay="{{ number_format($i * 0.2, 1) }}s">
                    <div class="why-card-1">
                        @if (!empty($sectionConfig['hover_icon']))
                            <div class="hover-icon"><img src="{{ $sectionConfig['hover_icon'] }}" alt=""></div>
                        @endif
                        @if (!empty($sectionConfig['item_'.$i.'_icon']))
                            <div class="why-card-1__icon"><img src="{{ $sectionConfig['item_'.$i.'_icon'] }}" alt=""></div>
                        @endif
                        <div class="why-card-1__content">
                            <h3 class="box-title">{{ $sectionConfig['item_'.$i.'_title'] ?? '' }}</h3>
                            <div class="box-text">{!! AppSupportHtmlSanitizer::sanitize($sectionConfig['item_'.$i.'_text'] ?? '') !!}</div>
                        </div>
                        @if (!empty($sectionConfig['item_'.$i.'_button']))
                            <div class="why-card-1__bottom">
                                <a class="th-btn style3 pill bg-white" href="{{ $sectionConfig['item_'.$i.'_url'] ?? '#' }}">{{ $sectionConfig['item_'.$i.'_button'] }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
