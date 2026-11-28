@extends('admin.layouts.app')
@section('title', 'Excel / CSV Rate Import')
@section('content')
    <div class="mb-4"><h1 class="h3 mb-1">Excel / CSV Import</h1><p class="text-muted mb-0">Upload the original rate list, map columns, preview validation and confirm draft imports. Existing verified rates are never overwritten.</p></div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Upload rate list</h2></div><div class="card-body">
            <form method="POST" action="{{ route('admin.circle-rates.imports.upload') }}" enctype="multipart/form-data">@csrf
                <div class="mb-3"><label class="form-label">Financial year *</label><select class="form-select" name="financial_year_id" required><option value="">Select year</option>@foreach($financialYears as $year)<option value="{{ $year->id }}">{{ $year->name }}</option>@endforeach</select></div>
                <div class="mb-3"><label class="form-label">Source document</label><select class="form-select" name="source_id"><option value="">Attach source later</option>@foreach($sources as $source)<option value="{{ $source->id }}">{{ $source->title }}</option>@endforeach</select></div>
                <div class="mb-3"><label class="form-label">Spreadsheet *</label><input class="form-control" type="file" name="file" accept=".csv,.xlsx,.xls" required><small class="text-muted">CSV and XLSX are supported. XLS requires PhpSpreadsheet. Maximum 50 MB.</small></div>
                <button class="btn btn-primary">Upload and inspect</button>
            </form>
        </div></div></div>
        <div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Import history</h2></div>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>File</th><th>Financial year</th><th>Rows</th><th>Valid</th><th>Errors / conflicts</th><th>Status</th><th></th></tr></thead><tbody>
                @forelse($batches as $batch)<tr><td>{{ $batch->original_filename }} @if($batch->is_demo)<span class="badge text-bg-warning">DEMO</span>@endif</td><td>{{ $batch->financialYear?->name ?? '—' }}</td><td>{{ $batch->total_rows }}</td><td>{{ $batch->valid_rows }}</td><td>{{ $batch->invalid_rows + $batch->conflict_rows }}</td><td>{{ ucfirst(str_replace('_', ' ', $batch->status)) }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.circle-rates.imports.show', $batch) }}">Open</a></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">No imports uploaded yet.</td></tr>@endforelse
            </tbody></table></div><div class="card-body">{{ $batches->links() }}</div>
        </div></div>
    </div>
@endsection
