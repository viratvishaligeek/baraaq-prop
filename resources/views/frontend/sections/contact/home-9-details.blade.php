@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
<section class="space overflow-hidden {{ $visibility }}" id="home-details-{{ $section->id }}">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 text-center text-xl-start">
                <div class="title-area text-left pe-xl-5 me-xl-5">
                    <p class="sub-title style4 text-anime-style-2">{{ $sectionConfig['eyebrow'] ?? 'Home Details' }}</p>
                    <h2 class="sec-title style-2 text-anime-style-3 pe-xl-5 me-xl-5">{{ $sectionConfig['heading'] ?? 'Luxury Real Estate Home Details at a Glance' }}</h2>
                </div>
                <div class="contact-list">
                    <ul>
                        <li>Location <span>{{ $sectionConfig['location'] ?? '' }}</span></li>
                        <li>Total Area <span>{{ $sectionConfig['total_area'] ?? '' }}</span></li>
                        <li>Living Space <span>{{ $sectionConfig['living_space'] ?? '' }}</span></li>
                        <li>Bedrooms <span>{{ $sectionConfig['bedrooms'] ?? '' }}</span></li>
                        <li>Bathrooms <span>{{ $sectionConfig['bathrooms'] ?? '' }}</span></li>
                        <li>Floors <span>{{ $sectionConfig['floors'] ?? '' }}</span></li>
                    </ul>
                    @if(!empty($sectionConfig['button_label']))
                        <div class="btn"><a href="{{ $sectionConfig['button_url'] ?? '/about' }}" class="th-btn style7">{{ $sectionConfig['button_label'] }}</a></div>
                    @endif
                </div>
            </div>
            <div class="col-xl-6 mt-40 mt-xl-0">
                <div class="img-box4 fadeinup wow" data-wow-duration="1.5s" data-wow-delay="0.3s">
                    <img src="{{ $sectionConfig['image'] ?? asset('theme-assets/img/about/contact-img3.png') }}" alt="{{ $sectionConfig['heading'] ?? 'Property details' }}">
                </div>
            </div>
        </div>
    </div>
</section>
