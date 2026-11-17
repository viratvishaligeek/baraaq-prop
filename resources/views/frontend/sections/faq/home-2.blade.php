@php
    $items = [];
    for ($i = 1; $i <= 5; $i++) {
        $items[] = [
            'question' => $sectionConfig['item_' . $i . '_question'] ?? '',
            'answer' => $sectionConfig['item_' . $i . '_answer'] ?? '',
        ];
    }
    $visibility = trim(
        (!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '') .
            (!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '') .
            (!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''),
    );
@endphp

<div class="{{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden bg-smoke {{ $visibility }}"
    id="faq-section-{{ $section->id }}">
    @if (!empty($sectionConfig['shape_image']))
        <div class="shape-mockup" style="bottom: 0; left: 0%;">
            <img src="{{ $sectionConfig['shape_image'] }}" alt="img">
        </div>
    @endif
    @if (!empty($sectionConfig['circle_image']))
        <div class="shape-mockup spin d-none d-lg-block" style="bottom: 11%; left: 7%;">
            <img src="{{ $sectionConfig['circle_image'] }}" alt="img">
        </div>
    @endif
    <div class="container">
        <div class="row justify-content-between gy-4">
            <div class="col-lg-5 col-xl-5 col-xxl-4">
                <div class="title-area text-left pe-xxl-5">
                    <p class="sub-title text-anime-style-1">
                        <span class="double-line">
                        </span>{{ $sectionConfig['eyebrow'] ?? '' }}
                    </p>
                    <h2 class="sec-title text-anime-style-2">{{ $sectionConfig['heading'] ?? '' }}</h2>
                    <p class="sec-text text-anime-style-3">{!! \\App\\Support\\HtmlSanitizer::sanitize($sectionConfig['description'] ?? '') !!}</p>
                    @if (!empty($sectionConfig['button_label']))
                        <a href="{{ $sectionConfig['button_url'] ?? '#' }}"
                            class="th-btn bg-black pill text-anime-style-2">{{ $sectionConfig['button_label'] }}</a>
                    @endif
                </div>
            </div>
            <div class="col-lg-7 col-xl-7 col-xxl-7">
                <div class="accordion-1 accordion" id="faqAccordion-{{ $section->id }}">
                    @foreach ($items as $item)
                        @continue(!$item['question'] && !$item['answer'])
                        @php
                            $itemId = 'faq-' . $section->id . '-' . $loop->iteration;
                        @endphp
                        <div class="accordion-card{{ $loop->first ? ' active' : '' }}{{ $sectionConfig['animate_items'] ?? true ? ' wow fadeinup' : '' }}"
                            @if ($sectionConfig['animate_items'] ?? true) data-wow-delay="{{ ['.1s', '.3s', '.5s', '.7s', '.9s'][$loop->index] }}" @endif>
                            <div class="accordion-header" id="{{ $itemId }}-header">
                                <button class="accordion-button{{ $loop->first ? '' : ' collapsed' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#{{ $itemId }}"
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-controls="{{ $itemId }}">
                                    <span class="serial-numb">{{ $loop->iteration }}. </span>{{ $item['question'] }}
                                </button>
                            </div>
                            <div id="{{ $itemId }}"
                                class="accordion-collapse collapse{{ $loop->first ? ' show' : '' }}"
                                aria-labelledby="{{ $itemId }}-header"
                                data-bs-parent="#faqAccordion-{{ $section->id }}">
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
