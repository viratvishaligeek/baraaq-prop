@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
<section class="feature-sec-5 {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden {{ $visibility }}" id="services-{{ $section->id }}">
    <div class="container"><div class="row gy-4"><div class="col-xl-4"><div class="title-area pe-xxl-5"><span class="sub-title style2 text-anime-style-1">{{ $sectionConfig['eyebrow'] ?? 'Our Services' }}</span><h2 class="sec-title text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2><p>{{ $sectionConfig['description'] ?? '' }}</p></div></div>
        @foreach($services as $service)<div class="col-xl-4 col-md-6"><a href="{{ route('services.show', $service->slug) }}" class="feature-grid d-block"><div class="box-wrapp"><div class="box-icon"><img src="{{ $service->featured_image ? Storage::disk('public')->url($service->featured_image) : asset('theme-assets/img/icon/feature_1_1.svg') }}" alt=""></div><h3 class="box-title">{{ $service->title }}</h3></div><p class="box-text">{{ $service->excerpt }}</p><span class="text-theme">{{ $sectionConfig['card_link_label'] ?? 'Explore service' }} <i class="far fa-arrow-right"></i></span></a></div>@endforeach
    </div></div>
</section>