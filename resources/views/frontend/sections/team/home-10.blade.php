@if ($teamMembers->isNotEmpty())
    @php
        $visibility = trim(
            (!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '') .
            (!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '') .
            (!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : '')
        );
        $members = $teamMembers->take(4)->values();
    @endphp

    <section class="team-area-6 space overflow-hidden{{ $visibility }}">
        <div class="container">
            <div class="row justify-content-end">
                <div class="col-xl-5">
                    <div class="title-area mt-1">
                        <p class="sub-title style4 style2 text-anime-style-2">{{ $sectionConfig['eyebrow'] ?? 'Team Members' }}</p>
                        <h2 class="sec-title style-2 text-anime-style-3">{{ $sectionConfig['heading'] ?? 'Meet Our Pillar Agents' }}</h2>
                    </div>
                </div>
            </div>
            <div class="team6-row-wrapper">
                @if (!empty($sectionConfig['background_shape']))
                    <div class="team6-bg-shape"><img src="{{ $sectionConfig['background_shape'] }}" alt=""></div>
                @endif
                <div class="row">
                    <div class="col-lg-5 col-xl-5 d-none d-xl-block">
                        <div class="testi8-img-left">
                            @if (!empty($sectionConfig['side_image']))
                                <div class="img1" data-mask-src="{{ asset('theme-assets/img/bg/about-8-left-mask.png') }}">
                                    <img src="{{ $sectionConfig['side_image'] }}" alt="Our real estate team">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-7">
                        <div class="row gy-4">
                            @foreach ($members->take(2) as $member)
                                <div class="col-lg-6 col-md-6">
                                    <div class="team-card style-2 card-{{ $loop->iteration }}" @if(!empty($sectionConfig['card_background'])) data-bg-src="{{ $sectionConfig['card_background'] }}" @endif>
                                        <div class="team-img">
                                            <img src="{{ $member->photo ? Storage::disk('public')->url($member->photo) : asset('theme-assets/img/team/team_2_'.($loop->iteration).'.png') }}" alt="{{ $member->name }}">
                                        </div>
                                        <div class="team-content">
                                            <div class="left-contet">
                                                <h3 class="box-title"><a href="{{ url('/contact?agent='.$member->id) }}">{{ $member->name }}</a></h3>
                                                <span class="team-desig">{{ $member->designation }}</span>
                                            </div>
                                            <div class="team-social"><div class="th-social">
                                                @if (($sectionConfig['show_email'] ?? true) && $member->email)
                                                    <a href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i></a>
                                                @endif
                                                @foreach (($member->social_links ?? []) as $network => $url)
                                                    @if (is_string($url) && filter_var($url, FILTER_VALIDATE_URL))
                                                        <a target="_blank" rel="noopener noreferrer" href="{{ $url }}"><i class="fab fa-{{ $network }}"></i></a>
                                                    @endif
                                                @endforeach
                                            </div></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row gy-4 justify-content-center mt-1">
                    @foreach ($members->skip(2) as $member)
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="team-card style-2 card-{{ $loop->iteration + 2 }}" @if(!empty($sectionConfig['card_background'])) data-bg-src="{{ $sectionConfig['card_background'] }}" @endif>
                                <div class="team-img">
                                    <img src="{{ $member->photo ? Storage::disk('public')->url($member->photo) : asset('theme-assets/img/team/team_2_'.($loop->iteration + 2).'.png') }}" alt="{{ $member->name }}">
                                </div>
                                <div class="team-content">
                                    <div class="left-contet">
                                        <h3 class="box-title"><a href="{{ url('/contact?agent='.$member->id) }}">{{ $member->name }}</a></h3>
                                        <span class="team-desig">{{ $member->designation }}</span>
                                    </div>
                                    <div class="team-social"><div class="th-social">
                                        @if (($sectionConfig['show_email'] ?? true) && $member->email)
                                            <a href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i></a>
                                        @endif
                                        @foreach (($member->social_links ?? []) as $network => $url)
                                            @if (is_string($url) && filter_var($url, FILTER_VALIDATE_URL))
                                                <a target="_blank" rel="noopener noreferrer" href="{{ $url }}"><i class="fab fa-{{ $network }}"></i></a>
                                            @endif
                                        @endforeach
                                    </div></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
