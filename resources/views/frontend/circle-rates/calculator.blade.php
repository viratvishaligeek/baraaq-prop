@extends('frontend.layouts.theme')

@section('title', 'Bareilly Property Circle Rate Calculator')
@section('meta_description', 'Estimate property valuation using administrator-verified Bareilly circle rate records and transparent calculation details.')

@section('content')
    <section class="space overflow-hidden" style="padding: 70px 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="text-center mb-4">
                        <span class="sub-title">Bareilly, Uttar Pradesh</span>
                        <h1 class="h2">Property Circle Rate Calculator</h1>
                        <p class="text-muted">Choose the applicable location, property type and area to calculate an estimate from published, verified rate records.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>We could not calculate this valuation.</strong>
                            <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('circle-rates.calculate') }}" class="card shadow-sm border-0 p-3 p-md-4" id="circle-rate-calculator">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="cr-district">District <span class="text-danger">*</span></label>
                                <select class="form-select" id="cr-district" name="district_id" required>
                                    <option value="">Select district</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" @selected(old('district_id') == $district->id)>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-tehsil">Tehsil</label>
                                <select class="form-select" id="cr-tehsil" name="tehsil_id"><option value="">Select tehsil (if applicable)</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-area-type">Area type</label>
                                <select class="form-select" id="cr-area-type" name="area_type_id">
                                    <option value="">Select area type (if applicable)</option>
                                    @foreach ($areaTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-location-root">Location / village / mohalla</label>
                                <select class="form-select" id="cr-location-root" data-location-select><option value="">Select district first</option></select>
                                <div id="cr-location-children" class="mt-2"></div>
                                <input type="hidden" name="location_id" id="cr-location-id" value="{{ old('location_id') }}">
                                <small class="text-muted">Further location levels appear only when configured for the selected area.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-property-type">Property type <span class="text-danger">*</span></label>
                                <select class="form-select" id="cr-property-type" name="property_type_id" required>
                                    <option value="">Select property type</option>
                                    @foreach ($propertyTypes as $type)<option value="{{ $type->id }}" @selected(old('property_type_id') == $type->id)>{{ $type->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-category">Property category</label>
                                <select class="form-select" id="cr-category" name="category_id"><option value="">Select category (if applicable)</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-road-width">Road width</label>
                                <select class="form-select" id="cr-road-width" name="road_width_range_id"><option value="">Select road width (if applicable)</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-financial-year">Financial year <span class="text-danger">*</span></label>
                                <select class="form-select" id="cr-financial-year" name="financial_year_id" required>
                                    <option value="">Select financial year</option>
                                    @foreach ($financialYears as $year)<option value="{{ $year->id }}" @selected(old('financial_year_id', $financialYears->firstWhere('is_current', true)?->id) == $year->id)>{{ $year->name }}</option>@endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-valuation-date">Valuation date <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" id="cr-valuation-date" name="valuation_date" value="{{ old('valuation_date', now()->toDateString()) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-area">Property area <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" id="cr-area" name="area" min="0.000001" step="0.000001" value="{{ old('area') }}" placeholder="Enter measured area" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cr-area-unit">Area unit <span class="text-danger">*</span></label>
                                <select class="form-select" id="cr-area-unit" name="area_unit_id" required>
                                    <option value="">Select unit</option>
                                    @foreach ($units as $unit)<option value="{{ $unit->id }}" @selected(old('area_unit_id') == $unit->id)>{{ $unit->name }} ({{ $unit->symbol }})</option>@endforeach
                                </select>
                            </div>
                            @foreach ($fields as $field)
                                @php($fieldName = 'custom['.$field->name.']')
                                <div class="col-md-6" data-custom-field="{{ $field->name }}" data-applicability='@json($field->applicability ?? [])'>
                                    <label class="form-label">{{ $field->label }} @if($field->is_required)<span class="text-danger">*</span>@endif</label>
                                    @if (in_array($field->input_type, ['select', 'dropdown']))
                                        <select class="form-select" name="{{ $fieldName }}" @required($field->is_required)>
                                            <option value="">{{ $field->placeholder ?: 'Select '.$field->label }}</option>
                                            @foreach ($field->options as $option)<option value="{{ $option->value }}">{{ $option->label }}</option>@endforeach
                                        </select>
                                    @elseif ($field->input_type === 'multi-select')
                                        <select class="form-select" name="{{ $fieldName }}[]" multiple @required($field->is_required)>
                                            @foreach ($field->options as $option)<option value="{{ $option->value }}">{{ $option->label }}</option>@endforeach
                                        </select>
                                    @elseif ($field->input_type === 'radio')
                                        <div class="d-flex flex-wrap gap-3">@foreach ($field->options as $option)<label class="form-check"><input class="form-check-input" type="radio" name="{{ $fieldName }}" value="{{ $option->value }}" @required($field->is_required)> <span class="form-check-label">{{ $option->label }}</span></label>@endforeach</div>
                                    @elseif ($field->input_type === 'checkbox')
                                        <input class="form-check-input" type="checkbox" name="{{ $fieldName }}" value="1">
                                    @elseif ($field->input_type === 'date')
                                        <input class="form-control" type="date" name="{{ $fieldName }}" @required($field->is_required)>
                                    @elseif ($field->input_type === 'number' || $field->input_type === 'range')
                                        <input class="form-control" type="{{ $field->input_type === 'range' ? 'range' : 'number' }}" name="{{ $fieldName }}" placeholder="{{ $field->placeholder }}" @required($field->is_required)>
                                    @else
                                        <input class="form-control" type="text" name="{{ $fieldName }}" placeholder="{{ $field->placeholder }}" @required($field->is_required)>
                                    @endif
                                    @if($field->help_text)<small class="text-muted">{{ $field->help_text }}</small>@endif
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
                            <p class="small text-muted mb-0">Calculation is available only when a matching published and verified rate exists.</p>
                            <button class="th-btn" type="submit">Calculate valuation <i class="fa-regular fa-arrow-right ms-2"></i></button>
                        </div>
                    </form>

                    <div class="alert alert-warning mt-4 mb-0">
                        {{ $disclaimer }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
$(function () {
    const $district = $('#cr-district');
    const $tehsil = $('#cr-tehsil');
    const $areaType = $('#cr-area-type');
    const $root = $('#cr-location-root');
    const $children = $('#cr-location-children');
    const $locationId = $('#cr-location-id');
    const $propertyType = $('#cr-property-type');
    const $category = $('#cr-category');
    const $roadWidth = $('#cr-road-width');

    function fillSelect($select, rows, placeholder) {
        $select.empty().append($('<option>', { value: '', text: placeholder }));
        $.each(rows || [], function (_, row) {
            $select.append($('<option>', { value: row.id, text: row.name }));
        });
    }
    function loadTehsils() {
        fillSelect($tehsil, [], 'Loading tehsils...');
        if (!$district.val()) return fillSelect($tehsil, [], 'Select tehsil (if applicable)');
        $.getJSON('{{ route('circle-rates.data.tehsils') }}', { district_id: $district.val() })
            .done(rows => fillSelect($tehsil, rows, 'Select tehsil (if applicable)'))
            .fail(() => fillSelect($tehsil, [], 'Unable to load tehsils'));
    }
    function loadLocations($select, parentId) {
        const params = { district_id: $district.val() };
        if ($tehsil.val()) params.tehsil_id = $tehsil.val();
        if ($areaType.val()) params.area_type_id = $areaType.val();
        if (parentId) params.parent_id = parentId;
        $select.prop('disabled', true);
        $.getJSON('{{ route('circle-rates.data.locations') }}', params)
            .done(rows => fillSelect($select, rows, 'Select location (if applicable)'))
            .fail(() => fillSelect($select, [], 'Unable to load locations'))
            .always(() => $select.prop('disabled', false));
    }
    function loadCategories() {
        fillSelect($category, [], 'Loading categories...');
        if (!$propertyType.val()) return fillSelect($category, [], 'Select category (if applicable)');
        $.getJSON('{{ route('circle-rates.data.categories') }}', {
            property_type_id: $propertyType.val(), location_id: $locationId.val()
        }).done(rows => fillSelect($category, rows, 'Select category (if applicable)'))
          .fail(() => fillSelect($category, [], 'Unable to load categories'));
    }
    function loadRoadWidths() {
        fillSelect($roadWidth, [], 'Loading road widths...');
        if (!$propertyType.val()) return fillSelect($roadWidth, [], 'Select road width (if applicable)');
        $.getJSON('{{ route('circle-rates.data.road-widths') }}', { property_type_id: $propertyType.val() })
          .done(rows => fillSelect($roadWidth, rows, 'Select road width (if applicable)'))
          .fail(() => fillSelect($roadWidth, [], 'Unable to load road widths'));
    }
    function resetLocationChildren() {
        $children.empty();
        $locationId.val('');
        loadCategories();
    }
    function appendChildLocations(parentId, $parentWrap) {
        const $wrap = $('<div class="mt-2"></div>');
        const $select = $('<select class="form-select" aria-label="More specific location"><option value="">Loading sub-locations...</option></select>');
        $wrap.append($select);
        $parentWrap.append($wrap);
        const params = { district_id: $district.val(), parent_id: parentId };
        if ($tehsil.val()) params.tehsil_id = $tehsil.val();
        if ($areaType.val()) params.area_type_id = $areaType.val();
        $.getJSON('{{ route('circle-rates.data.locations') }}', params)
            .done(function (rows) {
                fillSelect($select, rows, 'Select a more specific location (optional)');
                if (!rows.length) $wrap.remove();
            })
            .fail(function () { $wrap.remove(); });
        $select.on('change', function () {
            $wrap.children('.mt-2').remove();
            $wrap.nextAll().remove();
            if ($(this).val()) {
                $locationId.val($(this).val());
                loadCategories();
                appendChildLocations($(this).val(), $wrap);
            } else {
                const parentSelect = $wrap.prev().find('select').last();
                $locationId.val(parentId);
                loadCategories();
            }
        });
    }
    $district.on('change', function () {
        loadTehsils();
        fillSelect($root, [], 'Loading locations...');
        resetLocationChildren();
        if ($district.val()) loadLocations($root, null);
        else fillSelect($root, [], 'Select district first');
    });
    $tehsil.add($areaType).on('change', function () {
        resetLocationChildren();
        if ($district.val()) loadLocations($root, null);
    });
    $root.on('change', function () {
        resetLocationChildren();
        $locationId.val($(this).val() || '');
        if ($(this).val()) appendChildLocations($(this).val(), $children);
    });
    function updateCustomFields() {
        const selected = {
            district_id: $district.val(),
            tehsil_id: $tehsil.val(),
            area_type_id: $areaType.val(),
            location_id: $locationId.val(),
            property_type_id: $propertyType.val()
        };
        $('[data-custom-field]').each(function () {
            let applicability = {};
            try { applicability = JSON.parse($(this).attr('data-applicability') || '{}'); } catch (e) {}
            const map = {
                district_ids: 'district_id',
                tehsil_ids: 'tehsil_id',
                area_type_ids: 'area_type_id',
                location_ids: 'location_id',
                property_type_ids: 'property_type_id'
            };
            let visible = true;
            $.each(map, function (key, field) {
                if (Array.isArray(applicability[key]) && applicability[key].length) {
                    if (!applicability[key].map(String).includes(String(selected[field] || ''))) visible = false;
                }
            });
            $(this).toggle(visible);
            $(this).find('input,select,textarea').prop('disabled', !visible);
        });
    }
    $district.add($tehsil).add($areaType).add($propertyType).on('change', updateCustomFields);
    $propertyType.on('change', function () { loadCategories(); loadRoadWidths(); updateCustomFields(); });
    updateCustomFields();
    loadRoadWidths();
});
</script>
@endpush
