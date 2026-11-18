@php
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp
<section class="space-bottom overflow-hidden {{ $visibility }}" id="statistics-{{ $section->id }}">
    <div class="container">
        <div class="row gy-4 gx-90">
            @for($i = 1; $i <= 4; $i++)
                <div class="col-lg-3 col-md-6">
                    <div class="counter-box style-2">
                        <div class="media-body">
                            <h4 class="box-number"><span class="counter-number">{{ $sectionConfig['stat_'.$i.'_value'] ?? ['98','162','150','2.6'][$i-1] }}</span><span class="plus-simple">{{ $sectionConfig['stat_'.$i.'_suffix'] ?? ['+','+','+','M'][$i-1] }}</span></h4>
                            <p class="box-text">{{ $sectionConfig['stat_'.$i.'_label'] ?? ['offices worldwide','completed projects','products available','Total Project Expenditure'][$i-1] }}</p>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
