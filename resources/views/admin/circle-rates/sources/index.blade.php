@extends('admin.layouts.app')
@section('title', 'Rate Source Documents')
@section('content')
    <div class="mb-4"><h1 class="h3 mb-1">Source Documents</h1><p class="text-muted mb-0">Attach official rate lists and supporting documents. A rate cannot be verified without a source record.</p></div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Add source</h2></div><div class="card-body">
            <form method="POST" action="{{ route('admin.circle-rates.sources.store') }}" enctype="multipart/form-data">@csrf
                <div class="mb-3"><label class="form-label">Document title *</label><input class="form-control" name="title" required value="{{ old('title') }}"></div>
                <div class="mb-3"><label class="form-label">Reference number</label><input class="form-control" name="reference_number" value="{{ old('reference_number') }}"></div>
                <div class="mb-3"><label class="form-label">Published date</label><input class="form-control" type="date" name="published_on" value="{{ old('published_on') }}"></div>
                <div class="mb-3"><label class="form-label">Official source URL</label><input class="form-control" type="url" name="source_url" value="{{ old('source_url') }}"></div>
                <div class="mb-3"><label class="form-label">Document file</label><input class="form-control" type="file" name="document" accept=".pdf,.xlsx,.xls,.csv,.jpg,.jpeg,.png"><small class="text-muted">Maximum 20 MB. PDF and spreadsheet originals are retained.</small></div>
                <div class="mb-3"><label class="form-label">Notes</label><textarea class="form-control" rows="3" name="notes">{{ old('notes') }}</textarea></div>
                <button class="btn btn-primary">Save source</button>
            </form>
        </div></div></div>
        <div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-header bg-white d-flex justify-content-between align-items-center gap-2"><h2 class="h5 mb-0">Existing sources</h2><form method="GET" class="d-flex gap-2"><select class="form-select form-select-sm" name="demo_filter"><option value="">All sources</option><option value="demo" @selected(($filters['demo_filter'] ?? '') === 'demo')>DEMO only</option><option value="official" @selected(($filters['demo_filter'] ?? '') === 'official')>Official / managed</option></select><button class="btn btn-sm btn-outline-primary">Filter</button></form></div>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Document</th><th>Reference</th><th>Published</th><th>Linked rates</th><th></th></tr></thead><tbody>
                @forelse($sources as $source)<tr><td><strong>{{ $source->title }}</strong> @if($source->is_demo)<span class="badge text-bg-warning">DEMO</span>@endif@if($source->document_path)<div><a href="{{ \Illuminate\Support\Facades\Storage::disk($source->document_disk)->url($source->document_path) }}" target="_blank" rel="noopener">Open document</a></div>@endif</td><td>{{ $source->reference_number ?: '—' }}</td><td>{{ $source->published_on?->format('d M Y') ?: '—' }}</td><td>{{ $source->rates_count }}</td><td class="text-end">@if(!$source->rates_count)<form method="POST" action="{{ route('admin.circle-rates.sources.destroy', $source) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this source?')">Delete</button></form>@endif</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">No source documents added.</td></tr>@endforelse
            </tbody></table></div><div class="card-body">{{ $sources->links() }}</div>
        </div></div>
    </div>
@endsection
