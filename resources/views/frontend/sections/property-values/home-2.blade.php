@php
    $items = [];
    for ($i = 1; $i <= 4; $i++) {
        $items[] = [
            'image' => $sectionConfig['item_'.$i.'_image'] ?? '',
            'title' => $sectionConfig['item_'.$i.'_title'] ?? '',
            'text' => $sectionConfig['item_'.$i.'_text'] ?? '',
            'button' => $sectionConfig['item_'.$i.'_button'] ?? '',
            'url' => $sectionConfig['item_'.$i.'_url'] ?? '#',
        ];
    }
@endphp

<section class="property-values-sec-2 space overflow-hidden">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="title-area text-center">
                    <p class="sub-title text-anime-style-2"><span class="double-line"></span> {{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
        </div>
        <div class="row gy-4 justify-content-center align-items-center">
            <div class="col-lg-4 col-md-12">
                <div class="property-values-2-item fadeinleft wow">
                    @include('frontend.sections.property-values._card', ['item' => $items[0], 'thumbClass' => '', 'delay' => '0.2s'])
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="property-values-2-item">
                    @include('frontend.sections.property-values._card', ['item' => $items[1], 'thumbClass' => 'fadeinup wow', 'delay' => '0.4s'])
                    @include('frontend.sections.property-values._card', ['item' => $items[2], 'thumbClass' => 'fadeinup wow', 'delay' => '0.6s'])
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="property-values-2-item fadeinright wow">
                    @include('frontend.sections.property-values._card', ['item' => $items[3], 'thumbClass' => '', 'delay' => '0.8s'])
                </div>
            </div>
        </div>
    </div>
</section>
