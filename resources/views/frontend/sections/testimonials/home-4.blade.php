@php
    $items = $testimonials->take(max(1, min(24, (int) ($sectionConfig['items_limit'] ?? 6))));
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
@if($items->isNotEmpty())
<section class="space overflow-hidden {{ $visibility }}" id="testimonials-{{ $section->id }}">
    <div class="container"><div class="row gx-60 gy-4 flex-row-reverse">
        <div class="col-xxl-4"><div class="title-area text-left"><p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? 'Testimonials' }}</p><h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2><p class="sec-text fadeinup wow">{{ $sectionConfig['description'] ?? '' }}</p></div>@if(!empty($sectionConfig['button_label']))<div class="btn"><a href="{{ $sectionConfig['button_url'] ?? '/contact' }}" class="th-btn style7">{{ $sectionConfig['button_label'] }}</a></div>@endif</div>
        <div class="col-xxl-8"><div class="row gy-4">@foreach($items as $testimonial)<div class="col-lg-6"><div class="testi-item wow fadeinleft"><div class="box-profile"><div class="profile-img"><img src="{{ $testimonial->photo ? Storage::disk('public')->url($testimonial->photo) : asset('theme-assets/img/testimonial/testi_1_1.jpg') }}" alt="{{ $testimonial->name }}"></div><div class="profile-content"><h3 class="box-title">{{ $testimonial->name }}</h3><span class="team-desig">{{ $testimonial->designation }}</span></div></div><p class="box-text">“{{ $testimonial->quote }}”</p>@if(($sectionConfig['show_rating'] ?? true) && $testimonial->rating)<div class="reviews-star"><span class="rating">{{ number_format($testimonial->rating, 1) }}</span> <i class="fa-solid fa-star"></i></div>@endif</div></div>@endforeach</div></div>
    </div></div>
</section>
@endif