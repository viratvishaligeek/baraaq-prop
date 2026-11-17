@php
    $columns = max(2, min(4, (int) ($sectionConfig['columns'] ?? 4)));
    $options = json_encode([
        'breakpoints' => [
            '0' => ['slidesPerView' => 1],
            '576' => ['slidesPerView' => 1],
            '768' => ['slidesPerView' => 2],
            '992' => ['slidesPerView' => 2],
            '1200' => ['slidesPerView' => min(3, $columns)],
            '1400' => ['slidesPerView' => $columns],
        ],
        'autoHeight' => true,
    ], JSON_UNESCAPED_SLASHES);
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<section class="team-area-1 {{ $sectionConfig['vertical_spacing'] ?? 'space-bottom' }} overflow-hidden {{ $visibility }}" @if(!empty($sectionConfig['section_background_color'])) style="background-color: {{ $sectionConfig['section_background_color'] }};" @endif @if(!empty($sectionConfig['section_background_image'])) data-bg-src="{{ $sectionConfig['section_background_image'] }}" @endif>
    @if(!empty($sectionConfig['decorative_shape']))
        <div class="shape-mockup jump d-none d-lg-block" style="top: 6%; right: 4%;"><img src="{{ $sectionConfig['decorative_shape'] }}" alt="img"></div>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="title-area text-center mt-1">
                    <p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
        </div>
        <div class="slider-area mb-20">
            <div class="swiper th-slider has-shadow" id="teamSlider-{{ $section->id }}" data-slider-options='{{ $options }}'>
                <div class="swiper-wrapper">
                    @forelse($teamMembers as $member)
                        <div class="swiper-slide">
                            <div class="team-card style-2{{ ($sectionConfig['animate_cards'] ?? true) ? ' wow fadeinup' : '' }}" @if(!empty($sectionConfig['card_background'])) data-bg-src="{{ $sectionConfig['card_background'] }}" @endif>
                                <div class="team-img">
                                    <img src="{{ $member->photo ? Storage::disk('public')->url($member->photo) : asset('theme-assets/img/team/team_2_'.min(6, $loop->iteration).'.png') }}" alt="{{ $member->name }}">
                                </div>
                                <div class="team-content">
                                    <div class="left-contet">
                                        <h3 class="box-title"><a href="{{ url('/contact?agent='.$member->id) }}">{{ $member->name }}</a></h3>
                                        <span class="team-desig">{{ $member->designation }}</span>
                                    </div>
                                    <div class="team-social">
                                        <div class="th-social">
                                            @if(($sectionConfig['show_email'] ?? true) && $member->email)
                                                <a href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i></a>
                                            @endif
                                            @foreach(($member->social_links ?? []) as $network => $url)
                                                @if(is_string($url) && filter_var($url, FILTER_VALIDATE_URL))
                                                    <a target="_blank" rel="noopener noreferrer" href="{{ $url }}"><i class="fab fa-{{ $network }}"></i></a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide"><div class="team-card style-2"><div class="team-content">Team profiles will appear here soon.</div></div></div>
                    @endforelse
                </div>
            </div>
            <button data-slider-prev="#teamSlider-{{ $section->id }}" class="slider-arrow slider-prev"><i class="far fa-arrow-left"></i></button>
            <button data-slider-next="#teamSlider-{{ $section->id }}" class="slider-arrow slider-next"><i class="far fa-arrow-right"></i></button>
        </div>
        @if($sectionConfig['show_chat_text'] ?? true)
            <div class="row gy-40 justify-content-center">
                <div class="col-xl-8">
                    <p class="chat-text-wrap text-center pe-xl-5 ps-xl-5 mb-0{{ ($sectionConfig['animate_cards'] ?? true) ? ' fadeinup wow' : '' }}" data-wow-delay="0.2s">
                        {{ $sectionConfig['chat_text'] ?? '' }}
                        @if(!empty($sectionConfig['chat_link_label']))
                            <a class="text-theme" href="{{ $sectionConfig['chat_link_url'] ?? '#' }}"> {{ $sectionConfig['chat_link_label'] }}</a>
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</section>
