@php
    $statistics = collect(range(1, 4))->filter(fn ($index) => filled($sectionConfig['stat_'.$index.'_value'] ?? null));
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
@if ($statistics->isNotEmpty())
<section class="space-bottom overflow-hidden {{ $visibility }}" id="statistics-{{ $section->id }}">
    <div class="container">
        <div class="row gy-4 gx-90">
            @foreach ($statistics as $i)
                <div class="col-lg-3 col-md-6">
                    <div class="counter-box style-2">
                        <div class="media-body">
                            <h4 class="box-number"><span class="counter-number">{{ $sectionConfig['stat_'.$i.'_value'] }}</span><span class="plus-simple">{{ $sectionConfig['stat_'.$i.'_suffix'] ?? '' }}</span></h4>
                            <p class="box-text">{{ $sectionConfig['stat_'.$i.'_label'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
