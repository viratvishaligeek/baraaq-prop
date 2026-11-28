@extends('admin.layouts.app')
@section('title', 'Circle Rates')
@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div><h1 class="h3 mb-1">Circle Rate Records</h1><p class="text-muted mb-0">Rates remain unpublished until a source document is attached and an authorized administrator verifies them.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.circle-rates.rates.create') }}">Add rate</a>
    </div>
    <form method="GET" class="card border-0 shadow-sm p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label">Financial year</label><select class="form-select" name="financial_year_id"><option value="">All years</option>@foreach($financialYears as $year)<option value="{{ $year->id }}" @selected(($filters['financial_year_id'] ?? '') == $year->id)>{{ $year->name }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">District</label><select class="form-select" name="district_id"><option value="">All districts</option>@foreach($districts as $district)<option value="{{ $district->id }}" @selected(($filters['district_id'] ?? '') == $district->id)>{{ $district->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Verification</label><select class="form-select" name="verification_status"><option value="">All</option>@foreach(['pending','verified','rejected'] as $status)<option value="{{ $status }}" @selected(($filters['verification_status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Publication</label><select class="form-select" name="status"><option value="">All</option>@foreach(['draft','published','archived'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </div>
    </form>
    <form id="bulk-rate-form" method="POST" action="{{ route('admin.circle-rates.rates.bulk') }}">@csrf</form>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th><input type="checkbox" id="select-all-rates" aria-label="Select all rates"></th><th>Location</th><th>Property</th><th>Year</th><th>Rate</th><th>Verification</th><th>Publication</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($rates as $rate)
                <tr>
                    <td><input type="checkbox" form="bulk-rate-form" name="ids[]" value="{{ $rate->id }}" class="rate-row-checkbox" aria-label="Select rate {{ $rate->id }}"></td>
                    <td>{{ $rate->location?->name ?? $rate->district?->name }}<div class="small text-muted">{{ $rate->tehsil?->name }}</div></td>
                    <td>{{ $rate->propertyType?->name }}</td><td>{{ $rate->financialYear?->name }}</td>
                    <td>{{ number_format((float)$rate->rate, 2) }} / {{ $rate->unit?->name }}</td>
                    <td><span class="badge {{ $rate->verification_status === 'verified' ? 'text-bg-success' : ($rate->verification_status === 'rejected' ? 'text-bg-danger' : 'text-bg-warning') }}">{{ ucfirst($rate->verification_status) }}</span></td>
                    <td>{{ ucfirst($rate->status) }}</td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.circle-rates.rates.edit', $rate) }}">Edit</a>
                        <form class="d-inline" method="POST" action="{{ route('admin.circle-rates.rates.duplicate', $rate) }}">@csrf<button class="btn btn-sm btn-outline-secondary">Duplicate</button></form>
                        @if($rate->verification_status === 'pending' && $rate->source_id)
                            <form class="d-inline-flex align-items-center gap-1" method="POST" action="{{ route('admin.circle-rates.rates.verify', $rate) }}">@csrf @method('PATCH')<input class="form-control form-control-sm" style="max-width:150px" name="reason" placeholder="Verification reason" required><button class="btn btn-sm btn-success" onclick="return confirm('Verify and publish this rate?')">Verify</button></form>
                            <form class="d-inline-flex align-items-center gap-1" method="POST" action="{{ route('admin.circle-rates.rates.reject', $rate) }}">@csrf @method('PATCH')<input class="form-control form-control-sm" style="max-width:130px" name="reason" placeholder="Rejection reason" required><button class="btn btn-sm btn-outline-danger">Reject</button></form>
                        @endif
                        <form class="d-inline" method="POST" action="{{ route('admin.circle-rates.rates.destroy', $rate) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Archive this rate?')">Archive</button></form>
                    </td>
                </tr>
            @empty<tr><td colspan="8" class="text-center text-muted py-4">No rate records found.</td></tr>@endforelse
            </tbody>
        </table></div>
        <div class="card-body border-top">
            <div class="row g-2 align-items-end">
                <div class="col-md-3"><label class="form-label">Bulk action</label><select class="form-select" name="action" form="bulk-rate-form" id="bulk-rate-action" required><option value="">Select action</option><option value="verify">Verify and publish</option><option value="reject">Reject</option><option value="archive">Archive</option><option value="update_rate">Set new rate amount</option></select></div>
                <div class="col-md-3" id="bulk-new-rate-wrap" hidden><label class="form-label">New rate amount</label><input class="form-control" type="number" min="0.0001" step="0.0001" name="new_rate" form="bulk-rate-form"></div>
                <div class="col-md-4"><label class="form-label">Reason *</label><input class="form-control" name="reason" form="bulk-rate-form" maxlength="2000" required></div>
                <div class="col-md-2"><button class="btn btn-primary w-100" type="submit" form="bulk-rate-form">Apply to selected</button></div>
            </div>
        </div>
        <div class="card-body">{{ $rates->links() }}</div>
    </div>
@push('scripts')
<script>
$(function () {
    $('#select-all-rates').on('change', function () { $('.rate-row-checkbox').prop('checked', this.checked); });
    $('#bulk-rate-action').on('change', function () { $('#bulk-new-rate-wrap').prop('hidden', this.value !== 'update_rate'); });
});
</script>
@endpush
@endsection
