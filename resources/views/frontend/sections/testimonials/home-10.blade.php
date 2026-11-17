@php
    $items = $testimonials->take(max(1, min(24, (int) ($sectionConfig['items_limit'] ?? 6))));
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
@if($items->isNotEmpty())
<section class="space-top {{ $visibility }}" id="testimonials-{{ $section->id }}">
    <div class="container"><div class="row justify-content-center"><div class="col-xl-7"><div class="title-area text-center mt-1"><p class="sub-title style4 style2 text-anime-style-2">{{ $sectionConfig['eyebrow'] ?? 'Testimonials' }}</p><h2 class="sec-title style-2 text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2></div></div></div>
        <div class="row gy-4 justify-content-center">@foreach($items as $testimonial)<div class="col-xl-4 col-lg-6"><div class="testi-block bg-smoke"><div class="top-wrap"><div class="quote"><img src="{{ $sectionConfig['quote_icon'] ?? asset('theme-assets/img/icon/quote.svg') }}" alt="quote"></div><div class="logo"><img src="{{ asset('theme-assets/img/icon/brand-logo-'.(($loop->index % 3)+1).'.png') }}" alt=""></div></div><div class="testi-content"><p class="box-text">“{{ $testimonial->quote }}”</p></div><div class="bottom-wrap"><div class="content"><h3 class="box-title">{{ $testimonial->name }}</h3><p class="text">{{ $testimonial->designation }}</p></div><div class="team-img"><img src="{{ $testimonial->photo ? Storage::disk('public')->url($testimonial->photo) : asset('theme-assets/img/testimonial/testi_1_1.jpg') }}" alt="{{ $testimonial->name }}">@if(($sectionConfig['show_rating'] ?? true) && $testimonial->rating)<div class="box-review"><i class="fa-sharp fa-solid fa-star"></i><div class="rating"><span>{{ number_format($testimonial->rating, 1) }}</span></div></div>@endif</div></div></div></div></div>@endforeach</div>
        @if(!empty($sectionConfig['button_label']))<div class="text-center mt-4"><a href="{{ $sectionConfig['button_url'] ?? '/contact' }}" class="th-btn">{{ $sectionConfig['button_label'] }}</a></div>@endif
    </div>
</section>
@endif