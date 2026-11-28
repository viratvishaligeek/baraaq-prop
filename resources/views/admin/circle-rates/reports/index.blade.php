@extends('admin.layouts.app')
@section('title', 'Valuation Reports')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h3 mb-1">Valuation Reports</h1><p class="text-muted mb-0">Historical public calculator results and their linked rate records.</p></div><a class="btn btn-outline-primary" href="{{ route('admin.circle-rates.reports.export', request()->query()) }}">Export CSV</a></div>
    <form method="GET" class="card border-0 shadow-sm p-3 mb-3"><div class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label">Financial year</label><select class="form-select" name="financial_year_id"><option value="">All years</option>@foreach($financialYears as $year)<option value="{{ $year->id }}" @selected(($filters['financial_year_id'] ?? '') == $year->id)>{{ $year->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">From</label><input class="form-control" type="date" name="from" value="{{ $filters['from'] ?? '' }}"></div>
        <div class="col-md-3"><label class="form-label">Until</label><input class="form-control" type="date" name="until" value="{{ $filters['until'] ?? '' }}"></div>
        <div class="col-md-3"><label class="form-label">Data type</label><select class="form-select" name="demo_filter"><option value="">All reports</option><option value="demo" @selected(($filters['demo_filter'] ?? '') === 'demo')>DEMO only</option><option value="official" @selected(($filters['demo_filter'] ?? '') === 'official')>Official / managed</option></select></div>
        <div class="col-md-3"><button class="btn btn-primary w-100">Filter reports</button></div>
    </div></form>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Reference</th><th>Location</th><th>Property type</th><th>Year</th><th>Area</th><th>Estimated value</th><th>Time</th></tr></thead><tbody>
        @forelse($records as $record)<tr><td><code>{{ $record->reference }}</code></td><td>{{ $record->rate?->location?->name ?? $record->rate?->district?->name ?? '—' }}</td><td>{{ $record->rate?->propertyType?->name ?? '—' }} @if($record->is_demo)<span class="badge text-bg-warning">DEMO</span>@endif</td><td>{{ $record->financialYear?->name }}</td><td>{{ $record->property_area }}</td><td>₹{{ number_format((float)$record->estimated_value, 2) }}</td><td>{{ $record->valued_at?->format('d M Y H:i') }}</td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">No valuation records found.</td></tr>@endforelse
    </tbody></table></div><div class="card-body">{{ $records->links() }}</div></div>
@endsection
