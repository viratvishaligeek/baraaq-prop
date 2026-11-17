@php
    $items = $categories->take(max(1, min(20, (int) ($sectionConfig['items_limit'] ?? 7))));
    $tabPrefix = 'category-tabs-'.$section->id;
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
@if($items->isNotEmpty())
<section class="category-sec-3 position-relative {{ $sectionConfig['vertical_spacing'] ?? 'space' }} bg-smoke2 overflow-hidden {{ $visibility }}" id="{{ $tabPrefix }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif>
    <div class="shape-mockup spin d-none d-xxl-block" data-top="4%" data-right="12%"><img src="{{ asset('theme-assets/img/shape/10-3.png') }}" alt=""></div>
    <div class="container">
        <div class="row mb-3 mb-xxl-5 mb-xl-4"><div class="col-xl-3"><div class="title-area text-left mb-1"><p class="sub-title style4 style2 bg-white text-anime-style-1">{{ $sectionConfig['eyebrow'] ?? 'Our Property Type' }}</p></div></div><div class="col-xl-5"><div class="pe-xxl-5 ps-xxl-4"><h3 class="sec-title style-2">{{ $sectionConfig['heading'] ?? 'Shaping the Future of Modern Living' }}</h3>@if(!empty($sectionConfig['description']))<p>{{ $sectionConfig['description'] }}</p>@endif</div></div></div>
        <div class="category3-wrapper">
            <div class="nav category3-tab-btn" role="tablist">
                @foreach($items as $category)
                    <button class="tab-btn {{ $loop->first ? 'active' : '' }}" id="{{ $tabPrefix }}-tab-{{ $category->id }}" data-bs-toggle="tab" data-bs-target="#{{ $tabPrefix }}-panel-{{ $category->id }}" role="tab" aria-controls="{{ $tabPrefix }}-panel-{{ $category->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"><h4 class="count">{{ str_pad((string)($category->properties_count ?? 0), 2, '0', STR_PAD_LEFT) }}</h4>{{ $category->name }}</button>
                @endforeach
            </div>
            <div class="tab-content">
                @foreach($items as $category)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabPrefix }}-panel-{{ $category->id }}" role="tabpanel" aria-labelledby="{{ $tabPrefix }}-tab-{{ $category->id }}">
                        <div class="row gy-4 justify-content-center">
                            @forelse($category->properties as $property)
                                @php $imagePath = $property->featured_image ?: $property->images->first()?->path; @endphp
                                <div class="col-xl-4 col-lg-4 col-md-6"><div class="category3-card"><div class="thumb">
                                    @if($imagePath)<img src="{{ Storage::disk('public')->url($imagePath) }}" alt="{{ $property->title }}">@else<img src="{{ asset('theme-assets/img/normal/category3-1.jpg') }}" alt="{{ $property->title }}">@endif
                                    <div class="cate3-content"><h4 class="box-title"><a href="{{ route('properties.show', $property->slug) }}">{{ $property->title }}</a></h4><p class="box-text">{{ $property->city }}@if($property->price) · {{ number_format((float)$property->price) }}@endif</p></div>
                                </div></div></div>
                            @empty
                                <div class="col-12"><div class="text-center py-5"><p class="mb-2">No published properties in {{ $category->name }} yet.</p><a href="{{ url('/properties?category='.$category->slug) }}" class="th-btn">{{ $sectionConfig['button_label'] ?? 'Browse properties' }}</a></div></div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif