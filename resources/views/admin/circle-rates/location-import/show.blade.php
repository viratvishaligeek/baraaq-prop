@extends('admin.layouts.app')
@section('title', 'Review Location Import')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4"><div><h1 class="h3 mb-1">Review location import</h1><p class="text-muted mb-0">{{ $batch->original_filename }} · {{ $batch->total_rows }} rows · {{ str_replace('_', ' ', $batch->status) }}</p></div><a class="btn btn-outline-secondary" href="{{ route('admin.circle-rates.locations.import.index') }}">Back to imports</a></div>
    @if($batch->status === 'location_uploaded')
        <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0">Map spreadsheet columns</h2></div><div class="card-body">
            <form method="POST" action="{{ route('admin.circle-rates.locations.import.preview', $batch) }}">@csrf
                <div class="row g-3">
                    @foreach(['district'=>'District','tehsil'=>'Tehsil','area_type'=>'Area type','level'=>'Location level','parent'=>'Parent location name','name'=>'Location name','slug'=>'Slug','code'=>'Location code'] as $field=>$label)
                        <div class="col-md-6"><label class="form-label">{{ $label }} @if(in_array($field,['district','name']))<span class="text-danger">*</span>@endif</label>
                            <select class="form-select" name="column_mapping[{{ $field }}]" @required(in_array($field,['district','name']))><option value="">Not mapped</option>@foreach($headers as $header)<option value="{{ $header }}" @selected(($batch->column_mapping[$field] ?? '') === $header)>{{ $header }}</option>@endforeach</select>
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-primary mt-4">Validate and preview locations</button>
            </form>
        </div></div>
    @endif
    @if(in_array($batch->status, ['location_previewed','location_imported'], true))
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Valid rows</div><div class="h3 mb-0">{{ $validCount }}</div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Duplicates</div><div class="h3 mb-0">{{ $duplicateCount }}</div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Invalid rows</div><div class="h3 mb-0">{{ $batch->invalid_rows }}</div></div></div>
        </div>
        @if($batch->status === 'location_previewed' && $validCount > 0)
            <form method="POST" action="{{ route('admin.circle-rates.locations.import.confirm', $batch) }}" class="card border-0 shadow-sm p-3 mb-4">@csrf
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="confirm" value="1" id="confirm-locations" required><label class="form-check-label" for="confirm-locations">I reviewed the preview and confirm creating {{ $validCount }} new location records. Existing locations will not be overwritten.</label></div>
                <button class="btn btn-success">Confirm location import</button>
            </form>
        @endif
    @endif
    <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Rows</h2><p class="text-muted small mb-0">{{ $batch->summary }}</p></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Row</th><th>Status</th><th>Normalized location</th><th>Validation details</th></tr></thead><tbody>
        @forelse($rows as $row)<tr><td>{{ $row->row_number }}</td><td><span class="badge {{ $row->status === 'valid' ? 'text-bg-success' : ($row->status === 'duplicate' ? 'text-bg-warning' : 'text-bg-danger') }}">{{ ucfirst($row->status) }}</span></td><td>{{ $row->normalized_data['name'] ?? '—' }}<div class="small text-muted">{{ $row->normalized_data['district_id'] ? 'District #'.$row->normalized_data['district_id'] : 'Unknown district' }} · {{ $row->normalized_data['parent_name'] ?? 'Root location' }}</div></td><td>{{ implode(' ', $row->validation_errors ?? []) }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No preview rows yet.</td></tr>@endforelse
    </tbody></table></div><div class="card-body">{{ $rows->links() }}</div></div>
@endsection
