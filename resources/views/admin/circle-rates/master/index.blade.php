@extends('admin.layouts.app')

@section('title', $definition['title'])

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div><h1 class="h3 mb-1">{{ $definition['title'] }}</h1><p class="text-muted mb-0">Configure the master data used by the Bareilly property valuation calculator.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.circle-rates.dashboard') }}">Back to dashboard</a>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h2 class="h5 mb-0">{{ $record ? 'Edit record' : 'Add new record' }}</h2></div>
                <div class="card-body">
                    <form method="POST" action="{{ $record ? route('admin.circle-rates.master.update', [$module, $record->id]) : route('admin.circle-rates.master.store', $module) }}">
                        @csrf
                        @if ($record) @method('PUT') @endif
                        <div class="row g-3">
                            @foreach ($definition['fields'] as $field)
                                @php
                                    $name = $field['name'];
                                    $value = old($name, $record?->{$name});
                                @endphp
                                <div class="col-12">
                                    @if ($field['type'] === 'boolean')
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="{{ $name }}" value="1" id="field-{{ $name }}" @checked((bool) $value)>
                                            <label class="form-check-label" for="field-{{ $name }}">{{ $field['label'] }}</label>
                                        </div>
                                    @else
                                        <label class="form-label" for="field-{{ $name }}">{{ $field['label'] }} @if(!empty($field['required']))<span class="text-danger">*</span>@endif</label>
                                        @if ($field['type'] === 'select')
                                            <select class="form-select" name="{{ $name }}" id="field-{{ $name }}" @required(!empty($field['required']))>
                                                <option value="">Select {{ strtolower($field['label']) }}</option>
                                                @foreach (($options[$name] ?? []) as $option)
                                                    <option value="{{ $option->id }}" @selected((string) $value === (string) $option->id)>{{ $option->name }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($field['type'] === 'multi-select')
                                            <select class="form-select" name="{{ $name }}[]" id="field-{{ $name }}" multiple size="5">
                                                @foreach (($options[$name] ?? []) as $option)
                                                    <option value="{{ $option->id }}" @selected(in_array((string) $option->id, array_map('strval', (array) $value), true))>{{ $option->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Hold Ctrl (Windows/Linux) or Command (Mac) to select multiple entries.</small>
                                        @elseif ($field['type'] === 'textarea')
                                            <textarea class="form-control" name="{{ $name }}" id="field-{{ $name }}" rows="3">{{ $value }}</textarea>
                                        @elseif ($field['type'] === 'json')
                                            <textarea class="form-control font-monospace" name="{{ $name }}" id="field-{{ $name }}" rows="4" placeholder='{"key":"value"}'>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : $value }}</textarea>
                                            @if($module === 'property-types' && $name === 'formula_configuration')
                                                <small class="text-muted d-block mt-2">Safe formula variables: area, converted_area, input_area, rate and numeric custom fields such as custom.depreciation_percent. Supported operators: +, -, *, / and parentheses. Example: converted_area * rate * (1 - custom.depreciation_percent / 100). PHP, function calls and executable code are not allowed.</small>
                                            @elseif($module === 'rules' && $name === 'conditions')
                                                <small class="text-muted d-block mt-2">Use a JSON list of field/operator/value conditions. Operators: equals, not_equals, greater_than, less_than, greater_than_or_equal, less_than_or_equal, between, in.</small>
                                            @elseif($module === 'rules' && $name === 'actions')
                                                <small class="text-muted d-block mt-2">Supported source-backed actions: require_fields, deny_calculation, rate_multiplier and fixed_adjustment. Financial adjustments are applied only after verification with a source document.</small>
                                            @endif
                                        @else
                                            <input class="form-control" type="{{ $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') }}" name="{{ $name }}" id="field-{{ $name }}" value="{{ $value }}" @required(!empty($field['required']) && $field['type'] !== 'number')>
                                        @endif
                                    @endif
                                    @error($name)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                        <div class="col-12 mt-3"><label class="form-label" for="change_reason">Change reason (optional)</label><input class="form-control" id="change_reason" name="change_reason" maxlength="2000" value="{{ old('change_reason') }}"></div>
                        <div class="d-flex gap-2 mt-4">
                            <button class="btn btn-primary" type="submit">{{ $record ? 'Save changes' : 'Create record' }}</button>
                            @if ($record)<a class="btn btn-outline-secondary" href="{{ route('admin.circle-rates.master.index', $module) }}">Cancel</a>@endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2"><h2 class="h5 mb-0">Existing records</h2><form method="GET" class="d-flex gap-2"><select class="form-select form-select-sm" name="demo_filter"><option value="">All records</option><option value="demo" @selected(request('demo_filter') === 'demo')>DEMO only</option><option value="official" @selected(request('demo_filter') === 'official')>Official / managed</option></select><button class="btn btn-sm btn-outline-primary">Filter</button></form><span class="text-muted small">{{ $records->total() }} total</span></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>ID</th>@foreach ($definition['columns'] as $column)<th>{{ \Illuminate\Support\Str::headline($column) }}</th>@endforeach<th class="text-end">Actions</th></tr></thead>
                        <tbody>
                            @forelse ($records as $item)
                                <tr>
                                    <td>{{ $item->id }} @if($item->is_demo)<span class="badge text-bg-warning">DEMO</span>@endif</td>
                                    @foreach ($definition['columns'] as $column)
                                        <td>
                                            @php($cell = $item->{$column})
                                            @if (is_bool($cell))<span class="badge {{ $cell ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $cell ? 'Yes' : 'No' }}</span>
                                            @elseif (is_array($cell))<code>{{ \Illuminate\Support\Str::limit(json_encode($cell), 55) }}</code>
                                            @else{{ \Illuminate\Support\Str::limit((string) ($cell ?? '—'), 55) }}@endif
                                        </td>
                                    @endforeach
                                    <td class="text-end text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.circle-rates.master.index', [$module, 'edit' => $item->id]) }}">Edit</a>
                                        <form class="d-inline" method="POST" action="{{ route('admin.circle-rates.master.destroy', [$module, $item->id]) }}" onsubmit="return confirm('Delete this record? Referenced records may prevent deletion.');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ count($definition['columns']) + 2 }}" class="text-center text-muted py-4">No records have been added yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-body">{{ $records->links() }}</div>
            </div>
        </div>
    </div>
@endsection
