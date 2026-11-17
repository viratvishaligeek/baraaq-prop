@php
    $items = [];
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($sectionConfig['item_' . $i . '_question'])) {
            $items[] = [
                'question' => $sectionConfig['item_' . $i . '_question'],
                'answer' => $sectionConfig['item_' . $i . '_answer'] ?? '',
            ];
        }
    }
    $items = array_slice($items, 0, max(1, min(5, (int) ($sectionConfig['items_limit'] ?? 5))));
    $visibility = trim(
        (!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '') .
            (!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '') .
            (!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''),
    );
    $accordionId = 'home3-faq-' . $section->id;
@endphp

<div class="{{ $sectionConfig['vertical_spacing'] ?? 'space' }} bg-smoke {{ $visibility }}" id="{{ $accordionId }}"
    @if (!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif
    @if (!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    @if (!empty($sectionConfig['shape_image']))
        <div class="shape-mockup" style="bottom: 0; left: 0%;">
            <img src="{{ $sectionConfig['shape_image'] }}" alt="Decorative shape">
        </div>
    @endif
    @if (!empty($sectionConfig['right_shape_image']))
        <div class="shape-mockup movingX d-none d-lg-block" style="top: 18%; right: 7%;">
            <img src="{{ $sectionConfig['right_shape_image'] }}" alt="Decorative shape">
        </div>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-xl-6 col-xxl-5">
                <div class="title-area text-center pe-xl-5 ps-xl-5 me-xxl-5 ms-xxl-5">
                    <p class="sub-title text-anime-style-2">
                        <span class="double-line">
                        </span>{{ $sectionConfig['eyebrow'] ?? '' }}
                    </p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center gy-4">
            <div class="col-lg-10">
                <div class="accordion-1 accordion" id="{{ $accordionId }}-items">
                    @foreach ($items as $index => $item)
                        @php
                            $itemId = $accordionId . '-item-' . ($index + 1);
                        @endphp
                        <div class="accordion-card style-2 {{ $loop->first ? 'active' : '' }} {{ $sectionConfig['animate_items'] ?? true ? 'wow fadeinup' : '' }}"
                            @if ($sectionConfig['animate_items'] ?? true) data-wow-delay=".{{ ($index + 1) * 2 }}s" @endif>
                            <div class="accordion-header" id="{{ $itemId }}-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#{{ $itemId }}"
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-controls="{{ $itemId }}">
                                    <span class="serial-numb">{{ $index + 1 }}. </span>{{ $item['question'] }}
                                </button>
                            </div>
                            <div id="{{ $itemId }}"
                                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                aria-labelledby="{{ $itemId }}-header"
                                data-bs-parent="#{{ $accordionId }}-items">
                                <div class="accordion-body">
                                    <p class="faq-text">{!! \\App\\Support\\HtmlSanitizer::sanitize($item['answer']) !!}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
