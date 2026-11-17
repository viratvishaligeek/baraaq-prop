@php
    $items = [];
    for ($i = 1; $i <= 3; $i++) {
        $items[] = [
            'icon' => $sectionConfig['item_'.$i.'_icon'] ?? '',
            'text' => $sectionConfig['item_'.$i.'_text'] ?? '',
            'url' => $sectionConfig['item_'.$i.'_url'] ?? '#',
        ];
    }
    $repeats = max(1, min(8, (int) ($sectionConfig['repeat_count'] ?? 4)));
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="marquee-section overflow-hidden bg-smoke {{ $visibility }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif>
    @if(!empty($sectionConfig['background_image']))
        <div class="marquee-bg-wrap" data-bg-src="{{ $sectionConfig['background_image'] }}"></div>
    @else
        <div class="marquee-bg-wrap"></div>
    @endif
    <div class="marquee-wrapper">
        <div class="marquee" style="animation-duration: {{ max(10, min(90, (int) ($sectionConfig['animation_duration'] ?? 28))) }}s;">
            @for($group = 0; $group < 2; $group++)
                <div class="marquee-group">
                    @for($repeat = 0; $repeat < $repeats; $repeat++)
                        @foreach($items as $item)
                            <div class="text">
                                <a href="{{ $item['url'] }}">
                                    <img src="{{ $item['icon'] }}" alt="img">
                                    {{ $item['text'] }}
                                </a>
                            </div>
                        @endforeach
                    @endfor
                </div>
            @endfor
        </div>
    </div>
</div>
