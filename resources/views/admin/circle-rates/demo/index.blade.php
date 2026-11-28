@extends('admin.layouts.app')

@section('title', 'Demo Data Management')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Demo Data Management</h1>
        <p class="text-muted mb-0">Bareilly city-only synthetic data. These locations are illustrative test records, not notified valuation zones.</p>
    </div>
    <span class="badge text-bg-warning fs-6">DEMO MODE</span>
</div>

<div class="alert alert-warning">
    <strong>DEMO DATA – NOT OFFICIAL.</strong>
    Sample rates are synthetic test values. They are never verified official government circle rates and must not be used for registration, stamp duty, lending or legal valuation decisions.
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<div class="row g-3 mb-4">
    @foreach($stats as $label => $count)
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="small text-muted">{{ $label }}</div>
                <div class="fs-3 fw-semibold">{{ number_format($count) }}</div>
                <span class="badge text-bg-warning">DEMO</span>
            </div></div>
        </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white"><h2 class="h5 mb-0">Seed and reset</h2></div>
    <div class="card-body">
        <p class="text-muted">Seeding is idempotent. Re-running it updates only records already carrying the explicit demo marker. Any conflicting non-demo record causes the operation to stop rather than overwrite it.</p>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.circle-rates.demo.seed') }}">@csrf
                <button class="btn btn-primary" type="submit" onclick="return confirm('Seed Bareilly city DEMO data? No official rates will be changed.')">Seed Demo Data</button>
            </form>
            <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#demo-reset-panel">Reset Demo Data</button>
        </div>
        <div class="collapse mt-3" id="demo-reset-panel">
            <div class="border border-danger rounded p-3">
                <h3 class="h6 text-danger">Danger zone – reset demo records only</h3>
                <p class="small text-muted">This permanently deletes rows marked <code>is_demo = true</code> in the circle-rate module, including demo rates, demo calculator fields, demo locations and demo reference records. Official and manually managed records are excluded. Audit history is retained.</p>
                <form method="POST" action="{{ route('admin.circle-rates.demo.reset') }}" onsubmit="return confirm('Permanently delete ONLY demo-marked Bareilly circle-rate records?')">
                    @csrf
                    <label class="form-label" for="demo-reset-confirmation">Type <strong>RESET DEMO DATA</strong> to confirm</label>
                    <input class="form-control mb-2" id="demo-reset-confirmation" name="confirmation" autocomplete="off" required pattern="RESET DEMO DATA">
                    <button class="btn btn-danger" type="submit">Confirm Demo Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between gap-2">
        <h2 class="h5 mb-0">View Demo Data – sample circle rates</h2>
        <span class="badge text-bg-warning">DEMO ONLY</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Location</th><th>Property type</th><th>Financial year</th><th>Sample rate</th><th>Verification</th><th>Source</th></tr></thead>
            <tbody>
            @forelse($rates as $rate)
                <tr>
                    <td>{{ $rate->location?->name ?? 'Bareilly city' }}</td>
                    <td>{{ $rate->propertyType?->name }}</td>
                    <td>{{ $rate->financialYear?->name }}</td>
                    <td>{{ number_format((float)$rate->rate, 2) }} / {{ $rate->unit?->symbol }}</td>
                    <td><span class="badge text-bg-warning">Pending – DEMO</span></td>
                    <td><span class="badge text-bg-warning">DEMO SOURCE</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No demo rates yet. Use Seed Demo Data to create the Bareilly city test dataset.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $rates->links() }}</div>
</div>
@endsection
