@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="text-muted mb-0">Your property website at a glance · Traffic for the last 30 days.</p>
        </div>
        <div class="d-flex gap-2">
            @if (auth()->user()->isAdministrator() || auth()->user()->hasPermission('analytics.view'))
                <a class="btn btn-outline-primary" href="{{ route('admin.analytics.index') }}">Detailed analytics</a>
            @endif
            <a class="btn btn-primary" href="{{ route('admin.properties.create') }}">
                <i class="fa fa-plus me-2"></i>Add property
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Properties', $propertyCount, 'fa-house', route('admin.properties.index')],
            ['Published properties', $publishedPropertyCount, 'fa-check-circle', route('admin.properties.index', ['status' => 'published'])],
            ['Blog posts', $postCount, 'fa-newspaper', route('admin.content.index', 'posts')],
            ['Pages', $pageCount, 'fa-file-lines', route('admin.content.index', 'pages')],
            ['Users', $userCount, 'fa-users', route('admin.users.index')],
            ['New enquiries', $newEnquiryCount, 'fa-inbox', route('admin.enquiries.index', ['status' => 'new'])],
            ['Visits · 30 days', $totalVisits, 'fa-chart-line', route('admin.analytics.index')],
            ['Page views · 30 days', $totalPageViews, 'fa-eye', route('admin.analytics.index')],
        ] as [$label, $value, $icon, $destination])
            <div class="col-6 col-xl-3">
                <a class="stat-card h-100 d-block text-decoration-none text-reset" href="{{ $destination }}" aria-label="View {{ $label }}">
                    <div class="stat-icon"><i class="fa {{ $icon }}"></i></div>
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="stat-value">{{ number_format($value) }}</div>
                </a>
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
                        <thead><tr><th>Property</th><th>City</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($recentProperties as $property)
                                <tr>
                                    <td class="fw-semibold">{{ $property->title }}</td>
                                    <td>{{ $property->city ?: '—' }}</td>
                                    <td>{{ ucfirst($property->status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No properties yet.</td></tr>
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
                @forelse($recentEnquiries as $enquiry)
                    <div class="enquiry-row">
                        <div class="fw-semibold">{{ $enquiry->name }}</div>
                        <div class="small text-muted">
                            {{ $enquiry->property?->title ?? 'General enquiry' }} · {{ $enquiry->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No enquiries received.</p>
                @endforelse
            </div>
        </div>

        <div class="col-12">
            <div class="content-card">
                <div class="card-heading">
                    <h2>Recent admin activity</h2>
                    <span class="text-muted small">Latest content and configuration changes</span>
                </div>
                @forelse($recentActivities as $activity)
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom py-2">
                        <div>
                            <div class="fw-semibold">{{ $activity->description }}</div>
                            <div class="small text-muted">{{ $activity->causer?->name ?? 'System' }} · {{ ucfirst($activity->event) }}</div>
                        </div>
                        <time class="small text-muted" datetime="{{ $activity->created_at->toIso8601String() }}">
                            {{ $activity->created_at->diffForHumans() }}
                        </time>
                    </div>
                @empty
                    <p class="text-muted mb-0">No activity recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
