@extends('admin.layouts.app')
@section('title', 'Bulk Location Import')
@section('content')
    <div class="mb-4"><h1 class="h3 mb-1">Bulk Location Import</h1><p class="text-muted mb-0">Import districts, tehsils, area types, location levels and parent-child locations without overwriting existing master data.</p></div>
    <div class="alert alert-info">Use an XLSX or CSV file with at least <strong>District</strong> and <strong>Name</strong> columns. Parent locations must already exist or appear earlier in the file. Duplicate locations are flagged and never overwritten.</div>
    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0">Upload location file</h2></div><div class="card-body">
        <form method="POST" action="{{ route('admin.circle-rates.locations.import.store') }}" enctype="multipart/form-data">@csrf
            <div class="row g-3 align-items-end"><div class="col-md-9"><label class="form-label">XLSX / XLS / CSV file</label><input class="form-control" type="file" name="file" accept=".xlsx,.xls,.csv,.txt" required></div><div class="col-md-3"><button class="btn btn-primary w-100">Upload and map columns</button></div></div>
        </form>
    </div></div>
    <div class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Location import history</h2></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>File</th><th>Rows</th><th>Status</th><th>Uploaded</th><th></th></tr></thead><tbody>
        @forelse($batches as $batch)<tr><td>{{ $batch->original_filename }}</td><td>{{ $batch->total_rows }}</td><td>{{ str_replace('_', ' ', ucfirst($batch->status)) }}</td><td>{{ $batch->created_at?->format('d M Y H:i') }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.circle-rates.locations.import.show', $batch) }}">Review</a></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">No location imports yet.</td></tr>@endforelse
    </tbody></table></div><div class="card-body">{{ $batches->links() }}</div></div>
@endsection
