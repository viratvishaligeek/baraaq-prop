@extends('admin.layouts.app')
@section('title', 'Review Import Batch')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h3 mb-1">Review import batch #{{ $batch->id }}</h1><p class="text-muted mb-0">{{ $batch->original_filename }} · {{ $batch->financialYear?->name }}</p></div><a class="btn btn-outline-secondary" href="{{ route('admin.circle-rates.imports.index') }}">Back to imports</a></div>
    @if($batch->summary)<div class="alert alert-info">{{ $batch->summary }}</div>@endif
    @if($batch->status === 'uploaded' || $batch->status === 'previewed')
        <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0">Map spreadsheet columns</h2></div><div class="card-body">
            <form method="POST" action="{{ route('admin.circle-rates.imports.preview', $batch) }}">@csrf
                <div class="row g-3">
                    @foreach($mapFields as $key => $label)
                        <div class="col-md-6"><label class="form-label">{{ $label }} @if(in_array($key, ['district','property_type','rate','unit','effective_from']))<span class="text-danger">*</span>@endif</label>
                            <select class="form-select" name="mapping[{{ $key }}]" @required(in_array($key, ['district','property_type','rate','unit','effective_from']))>
                                <option value="">Not mapped</option>
                                @foreach($headers as $header)<option value="{{ $header }}" @selected(($batch->column_mapping[$key] ?? '') === $header)>{{ $header }}</option>@endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-primary mt-3">Validate and preview records</button>
            </form>
        </div></div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="small text-muted">Total rows</div><div class="fs-4 fw-semibold">{{ $batch->total_rows }}</div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="small text-muted">Valid</div><div class="fs-4 fw-semibold text-success">{{ $batch->valid_rows }}</div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="small text-muted">Invalid</div><div class="fs-4 fw-semibold text-danger">{{ $batch->invalid_rows }}</div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="small text-muted">Conflicts</div><div class="fs-4 fw-semibold text-warning">{{ $batch->conflict_rows }}</div></div></div></div>
    </div>

    @if($batch->status === 'previewed' && ($validCount + $duplicateCount) > 0)
        <form method="POST" action="{{ route('admin.circle-rates.imports.confirm', $batch) }}" class="card border-0 shadow-sm p-3 mb-4">@csrf
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="confirm" value="1" id="confirm-import" required><label class="form-check-label" for="confirm-import">I reviewed the preview and confirm importing {{ $validCount }} new rows as unverified drafts. Existing verified rates will not be overwritten.</label></div>
            @if($duplicateCount > 0)
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="update_unverified" value="1" id="update-unverified"><label class="form-check-label" for="update-unverified">Also update {{ $duplicateCount }} matching unverified records. Their previous versions will be retained. This option never updates verified records.</label></div>
            @endif
            <button class="btn btn-success">Confirm import</button>
        </form>
    @endif
    @if($batch->invalid_rows + $batch->conflict_rows > 0)
        <a class="btn btn-outline-danger mb-3" href="{{ route('admin.circle-rates.imports.errors', $batch) }}">Download validation / conflict report</a>
    @endif
    <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Preview rows</h2></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Row</th><th>Status</th><th>Location / property</th><th>Rate</th><th>Validation messages</th></tr></thead><tbody>
        @forelse($rows as $row)<tr><td>{{ $row->row_number }}</td><td><span class="badge {{ $row->status === 'valid' ? 'text-bg-success' : (in_array($row->status, ['conflict','duplicate']) ? 'text-bg-warning' : 'text-bg-danger') }}">{{ ucfirst($row->status) }}</span></td><td>{{ $row->normalized_data['location_id'] ?? $row->normalized_data['district_id'] ?? '—' }} / {{ $row->normalized_data['property_type_id'] ?? '—' }}</td><td>{{ $row->normalized_data['rate'] ?? '—' }} / {{ $row->normalized_data['unit_id'] ?? '—' }}</td><td class="small">{{ implode(' ', $row->validation_errors ?? []) }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">Upload preview has not been generated yet.</td></tr>@endforelse
    </tbody></table></div><div class="card-body">{{ $rows->links() }}</div></div>
@endsection
