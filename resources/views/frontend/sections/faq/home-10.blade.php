@php
    $items = [];
    $limit = max(1, min(10, (int) ($sectionConfig['items_limit'] ?? 5)));
    for ($i = 1; $i <= $limit; $i++) {
        $question = trim((string) ($sectionConfig['item_'.$i.'_question'] ?? ''));
        $answer = trim((string) ($sectionConfig['item_'.$i.'_answer'] ?? ''));
        if ($question !== '') $items[] = ['question' => $question, 'answer' => $answer];
    }
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
    $accordionId = 'piller-faq-'.$section->id;
@endphp
@if(count($items))
<section class="faq-area-1 space overflow-hidden {{ $visibility }}" id="{{ $accordionId }}">
    <div class="container"><div class="row justify-content-center"><div class="col-xl-7"><div class="title-area text-center"><p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? 'FAQS' }}</p><h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2><p class="sec-text">{!! \App\Support\HtmlSanitizer::sanitize($sectionConfig['description'] ?? '') !!}</p></div></div></div>
        <div class="row justify-content-center"><div class="col-xl-9"><div class="th-faq-wrapper"><div class="accordion-1 accordion" id="{{ $accordionId }}-accordion">
            @foreach($items as $item)
                <div class="accordion-card {{ $loop->first ? 'active' : '' }} wow fadeinup"><div class="accordion-header" id="{{ $accordionId }}-heading-{{ $loop->index }}"><button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}-collapse-{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $accordionId }}-collapse-{{ $loop->index }}">{{ $item['question'] }}</button></div><div id="{{ $accordionId }}-collapse-{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="{{ $accordionId }}-heading-{{ $loop->index }}" data-bs-parent="#{{ $accordionId }}-accordion"><div class="accordion-body"><p class="faq-text">{!! \App\Support\HtmlSanitizer::sanitize($item['answer']) !!}</p></div></div></div>
            @endforeach
        </div></div></div></div>
    </div>
</section>
@endif