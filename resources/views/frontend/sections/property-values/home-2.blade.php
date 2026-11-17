@php
    $image = fn ($path) => $path && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/'))
        ? $path
        : asset($path);
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
            @foreach($items as $item)
                <div class="col-lg-4 col-md-12">
                    <div class="property-values-2-item {{ $loop->first ? 'fadeinleft wow' : ($loop->last ? 'fadeinright wow' : '') }}" @if($loop->first) data-wow-delay="0.2s" @elseif($loop->last) data-wow-delay="0.8s" @endif>
                        <div class="thumb @if($loop->index === 1 || $loop->index === 2) fadeinup wow @endif" @if($loop->index === 1) data-wow-delay="0.4s" @elseif($loop->index === 2) data-wow-delay="0.6s" @endif>
                            <img src="{{ $image($item['image']) }}" alt="{{ $item['title'] }}">
                            <div class="plus-icon"><i class="fa-solid fa-plus"></i></div>
                            <div class="hover-content">
                                <h4 class="box-title"><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></h4>
                                <p class="box-text">{{ $item['text'] }}</p>
                                @if($item['button'])
                                    <a class="th-btn bg-theme pill" href="{{ $item['url'] }}">{{ $item['button'] }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
