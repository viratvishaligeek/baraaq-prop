@extends('admin.layouts.app') @section('title', 'Dashboard') @section('content')<div
    class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="text-muted mb-0">A snapshot of your property website.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('admin.properties.create') }}">
        <i class="fa fa-plus me-2">
        </i>Add property</a>
</div>
<div class="row g-3 mb-4">
    @foreach ([['Properties', $propertyCount, 'fa-house'], ['Published', $publishedPropertyCount, 'fa-check-circle'], ['Blog posts', $postCount, 'fa-newspaper'], ['New enquiries', $newEnquiryCount, 'fa-inbox']] as [$label, $value, $icon])
        <div class="col-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa {{ $icon }}">
                    </i>
                </div>
                <div class="text-muted small">{{ $label }}</div>
                <div class="stat-value">{{ number_format($value) }}</div>
            </div>
        </div>
    @endforeach
</div>
<div class="row g-4">
    <div class="col-xl-7">
        <div class="content-card">
            <div class="card-heading">
                <h2>Recent properties</h2>
                <a href="{{ route('admin.properties.index') }}">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>City</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProperties as $p)
                            <tr>
                                <td class="fw-semibold">{{ $p->title }}</td>
                                <td>{{ $p->city ?: '—' }}</td>
                                <td>{{ ucfirst($p->status) }}</td>
                        </tr>@empty<tr>
                                <td colspan="3" class="text-center text-muted py-4">No properties yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="content-card">
            <div class="card-heading">
                <h2>Latest enquiries</h2>
                <a href="{{ route('admin.enquiries.index') }}">View all</a>
            </div>
            @forelse($recentEnquiries as $e)
                <div class="enquiry-row">
                    <div class="fw-semibold">{{ $e->name }}</div>
                    <div class="small text-muted">{{ $e->property?->title ?? 'General enquiry' }} ·
                        {{ $e->created_at->diffForHumans() }}</div>
            </div>@empty<p class="text-muted">No enquiries received.</p>
            @endforelse
        </div>
    </div>
</div>@endsection
