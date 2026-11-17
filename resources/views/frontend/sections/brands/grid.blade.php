@php
    $logos = [];
    for ($i=1; $i<=min(12,(int)($sectionConfig['items_limit'] ?? 6)); $i++) if(!empty($sectionConfig['item_'.$i.'_image'])) $logos[] = ['image'=>$sectionConfig['item_'.$i.'_image'],'url'=>$sectionConfig['item_'.$i.'_url'] ?? '#'];
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
@if(count($logos))
<section class="brand-area-1 space overflow-hidden {{ $visibility }}" id="brands-{{ $section->id }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif><div class="container"><div class="row justify-content-center"><div class="col-xl-7"><div class="title-area text-center"><p class="sub-title">{{ $sectionConfig['eyebrow'] ?? '' }}</p><h2 class="sec-title">{{ $sectionConfig['heading'] ?? '' }}</h2></div></div></div><div class="row g-3 justify-content-center">@foreach($logos as $logo)<div class="col-6 col-sm-4 col-lg-2"><div class="brand-box"><a href="{{ $logo['url'] }}"><img src="{{ $logo['image'] }}" alt="Client logo" loading="lazy"></a></div></div>@endforeach</div></div></section>
@endif