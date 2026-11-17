@php
    $items = [];
    for ($i = 1; $i <= min(3, (int) ($sectionConfig['items_limit'] ?? 3)); $i++) {
        if (!empty($sectionConfig['item_'.$i.'_text'])) {
            $items[] = [
                'text' => $sectionConfig['item_'.$i.'_text'],
                'url' => $sectionConfig['item_'.$i.'_url'] ?? '#',
                'icon' => $sectionConfig['item_'.$i.'_icon'] ?? '',
            ];
        }
    }
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="marquee-section-2 overflow-hidden {{ $visibility }}">
    @if(!empty($sectionConfig['shape_image']))
        <div class="shape-mockup movingX d-none d-xl-block" style="bottom: 6%; left: 6%;"><img src="{{ $sectionConfig['shape_image'] }}" alt="Decorative shape"></div>
    @endif
    <div class="marquee-wrapper">
        <div class="marquee">
            @for($group = 0; $group < 2; $group++)
                <div class="marquee-group">
                    @foreach($items as $item)
                        <div class="text">
                            <a href="{{ $item['url'] }}">
                                @if($item['icon'])<img src="{{ $item['icon'] }}" alt="icon">@endif
                                {{ $item['text'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</div>
