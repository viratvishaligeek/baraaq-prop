@extends('admin.layouts.app')

@section('title', 'Property Circle Rate Management')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Property Circle Rate Management</h1>
            <p class="text-muted mb-0">Manage verified Bareilly valuation data, financial years and calculation rules.</p>
        </div>
        <span class="badge text-bg-light border">Current financial year: {{ $currentYear?->name ?? 'Not configured' }}</span>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Districts', $districtCount],
            ['Tehsils', $tehsilCount],
            ['Locations', $locationCount],
            ['Property types', $propertyTypeCount],
            ['Rate records', $rateCount],
            ['Verified & published', $verifiedRateCount],
            ['Pending verification', $pendingRateCount],
        ] as [$label, $value])
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">{{ $label }}</div>
                        <div class="fs-3 fw-semibold">{{ number_format($value) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="alert alert-info">
        <strong>Data integrity:</strong> No government circle rates are prefilled by this module. Only administrator-entered or source-documented rates can be verified and published.
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h2 class="h5 mb-0">Recently added rates</h2>
            <span class="small text-muted">Latest 8 records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Location</th><th>Property type</th><th>Financial year</th><th>Rate</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($latestRates as $rate)
                        <tr>
                            <td>{{ $rate->location?->name ?? $rate->district?->name ?? 'District level' }}</td>
                            <td>{{ $rate->propertyType?->name ?? '—' }}</td>
                            <td>{{ $rate->financialYear?->name ?? '—' }}</td>
                            <td>{{ number_format((float) $rate->rate, 2) }} / {{ $rate->unit?->name ?? 'unit' }}</td>
                            <td><span class="badge {{ $rate->verification_status === 'verified' ? 'text-bg-success' : 'text-bg-warning' }}">{{ ucfirst($rate->verification_status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No circle rate records yet. Add an official source and configure rates before enabling public valuation.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h2 class="h5 mb-0">Recent imports</h2></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>File</th><th>Rows</th><th>Valid</th><th>Invalid</th><th>Status</th><th>Uploaded</th></tr></thead>
                <tbody>
                    @forelse ($recentImports as $batch)
                        <tr>
                            <td>{{ $batch->original_filename }}</td>
                            <td>{{ number_format($batch->total_rows) }}</td>
                            <td>{{ number_format($batch->valid_rows) }}</td>
                            <td>{{ number_format($batch->invalid_rows) }}</td>
                            <td>{{ ucfirst($batch->status) }}</td>
                            <td>{{ $batch->created_at?->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No imports have been uploaded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
