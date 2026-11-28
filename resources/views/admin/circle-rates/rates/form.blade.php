@extends('admin.layouts.app')
@section('title', $rate->exists ? 'Edit Circle Rate' : 'Add Circle Rate')
@section('content')
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4"><div><h1 class="h3 mb-1">{{ $rate->exists ? 'Edit Circle Rate' : 'Add Circle Rate' }}</h1><p class="text-muted mb-0">Every rate must be traceable to a source and effective date.</p></div><a class="btn btn-outline-secondary" href="{{ route('admin.circle-rates.rates.index') }}">Back to rates</a></div>
    <form method="POST" action="{{ $rate->exists ? route('admin.circle-rates.rates.update', $rate) : route('admin.circle-rates.rates.store') }}" class="card border-0 shadow-sm p-3 p-md-4">
        @csrf @if($rate->exists) @method('PUT') @endif
        <div class="row g-3">
            @php($selects = [
                'district_id' => ['District', $districts],
                'tehsil_id' => ['Tehsil', $tehsils],
                'location_id' => ['Location', $locations],
                'area_type_id' => ['Area type', $areaTypes ?? collect()],
                'property_type_id' => ['Property type', $propertyTypes],
                'category_id' => ['Category', $categories],
                'road_width_range_id' => ['Road width', $roadWidths],
                'financial_year_id' => ['Financial year', $financialYears],
                'unit_id' => ['Rate unit', $units],
                'source_id' => ['Source document', $sources],
            ])
            @foreach($selects as $name => [$label, $optionsList])
                <div class="col-md-6"><label class="form-label" for="{{ $name }}">{{ $label }} @if(in_array($name, ['district_id','property_type_id','financial_year_id','unit_id']))<span class="text-danger">*</span>@endif</label>
                    <select class="form-select" name="{{ $name }}" id="{{ $name }}" @required(in_array($name, ['district_id','property_type_id','financial_year_id','unit_id']))>
                        <option value="">Select {{ strtolower($label) }} (if applicable)</option>
                        @foreach($optionsList as $option)
                            <option value="{{ $option->id }}" @selected(old($name, $rate->{$name}) == $option->id)>{{ $option->name ?? $option->title }}</option>
                        @endforeach
                    </select>
                    @error($name)<small class="text-danger">{{ $message }}</small>@enderror
                </div>
            @endforeach
            <div class="col-md-4"><label class="form-label">Rate amount *</label><input class="form-control" type="number" min="0.0001" step="0.0001" name="rate" value="{{ old('rate', $rate->rate) }}" required></div>
            <div class="col-md-4"><label class="form-label">Matching priority</label><input class="form-control" type="number" min="0" name="priority" value="{{ old('priority', $rate->priority ?? 0) }}"></div>
            <div class="col-md-4"><label class="form-label">Effective from *</label><input class="form-control" type="date" name="effective_from" value="{{ old('effective_from', $rate->effective_from?->format('Y-m-d')) }}" required></div>
            <div class="col-md-4"><label class="form-label">Effective until</label><input class="form-control" type="date" name="effective_until" value="{{ old('effective_until', $rate->effective_until?->format('Y-m-d')) }}"></div>
            <div class="col-12"><label class="form-label">Rate conditions (JSON array)</label><textarea class="form-control font-monospace" rows="5" name="conditions" placeholder='[{"field":"land_use","operator":"equals","value":"residential"}]'>{{ old('conditions', $rate->conditions ? json_encode($rate->conditions, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '') }}</textarea><small class="text-muted">Supported operators: equals, not_equals, greater_than, less_than, greater_than_or_equal, less_than_or_equal, between, in.</small></div>
            <div class="col-12"><label class="form-label">Administrator notes</label><textarea class="form-control" rows="3" name="admin_notes">{{ old('admin_notes', $rate->admin_notes) }}</textarea></div>
            <div class="col-12"><label class="form-label">Change reason @if($rate->exists)<span class="text-danger">*</span>@else (optional)@endif</label><input class="form-control" name="change_reason" value="{{ old('change_reason') }}" maxlength="2000" @required($rate->exists)></div>
        </div>
        @if($rate->exists)
            <div class="alert alert-warning mt-3 mb-0">Saving changes to a verified or published rate will move it back to draft/pending verification. The previous version is retained.</div>
        @else
            <div class="alert alert-info mt-3 mb-0">New rates are saved as draft/pending. Attach an official source document and verify before publication.</div>
        @endif
        <div class="mt-4"><button class="btn btn-primary" type="submit">Save rate</button></div>
    </form>
@endsection
