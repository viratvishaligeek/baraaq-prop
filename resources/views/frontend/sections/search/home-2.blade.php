@php
    $minOptions = collect(explode(',', (string) ($sectionConfig['min_area_options'] ?? '500,1000,1500')))->map(fn ($value) => trim($value))->filter();
    $maxOptions = collect(explode(',', (string) ($sectionConfig['max_area_options'] ?? '2000,3000,4000')))->map(fn ($value) => trim($value))->filter();
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="advance-search-wrapper stye-2 {{ $visibility }}" id="advanced-search-{{ $section->id }}">
    @if($sectionConfig['show_location'] ?? true)
        <div class="form-group">
            <label for="location-{{ $section->id }}">{{ $sectionConfig['location_label'] ?? 'Location' }}</label>
            <select name="location" id="location-{{ $section->id }}" form="heroSearchForm" class="form-select nice-select">
                <option value="" disabled selected hidden>{{ $sectionConfig['location_placeholder'] ?? 'Select Location' }}</option>
                @foreach(($cities ?? collect()) as $city)
                    <option value="{{ $city }}">{{ $city }}</option>
                @endforeach
            </select>
        </div>
    @endif
    @if($sectionConfig['show_min_area'] ?? true)
        <div class="form-group">
            <label for="mini-area-{{ $section->id }}">{{ $sectionConfig['min_area_label'] ?? 'Mini Area (Sqft)' }}</label>
            <select name="mini_area" id="mini-area-{{ $section->id }}" form="heroSearchForm" class="form-select nice-select">
                <option value="" disabled selected hidden>{{ $sectionConfig['min_area_placeholder'] ?? 'Select Area' }}</option>
                @foreach($minOptions as $area)
                    <option value="{{ $area }}">{{ number_format((float) $area) }} Sqft</option>
                @endforeach
            </select>
        </div>
    @endif
    @if($sectionConfig['show_max_area'] ?? true)
        <div class="form-group">
            <label for="max-area-{{ $section->id }}">{{ $sectionConfig['max_area_label'] ?? 'Max Area (Sqft)' }}</label>
            <select name="max_area" id="max-area-{{ $section->id }}" form="heroSearchForm" class="form-select nice-select">
                <option value="" disabled selected hidden>{{ $sectionConfig['max_area_placeholder'] ?? 'Select Max Area' }}</option>
                @foreach($maxOptions as $area)
                    <option value="{{ $area }}">{{ number_format((float) $area) }} Sqft</option>
                @endforeach
            </select>
        </div>
    @endif
</div>
