@extends('admin.layouts.app')
@section('title', 'Circle Rate Audit Logs')
@section('content')
    <div class="mb-4"><h1 class="h3 mb-1">Audit Logs</h1><p class="text-muted mb-0">Trace who changed rates, master data, imports and verification states.</p></div>
    <form method="GET" class="card border-0 shadow-sm p-3 mb-3"><div class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label">Event</label><input class="form-control" name="event" value="{{ $filters['event'] ?? '' }}" placeholder="e.g. verified_and_published"></div>
        <div class="col-md-3"><label class="form-label">Subject type</label><input class="form-control" name="subject_type" value="{{ $filters['subject_type'] ?? '' }}" placeholder="Model class"></div>
        <div class="col-md-2"><label class="form-label">From</label><input class="form-control" type="date" name="from" value="{{ $filters['from'] ?? '' }}"></div>
        <div class="col-md-2"><label class="form-label">Until</label><input class="form-control" type="date" name="until" value="{{ $filters['until'] ?? '' }}"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
    </div></form>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>When</th><th>Admin</th><th>Event</th><th>Subject</th><th>Reason</th><th>Source</th><th>Changes</th></tr></thead><tbody>
        @forelse($logs as $log)<tr><td>{{ $log->created_at?->format('d M Y H:i:s') }}</td><td>{{ $log->user?->name ?? 'System / deleted user' }}</td><td>{{ $log->event }}</td><td>{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td><td>{{ $log->reason ?: '—' }}</td><td>{{ $log->source?->title ?? '—' }}</td><td><details><summary>View</summary><pre class="small mt-2">{{ json_encode(['old' => $log->old_values, 'new' => $log->new_values], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></details></td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">No audit events found.</td></tr>@endforelse
    </tbody></table></div><div class="card-body">{{ $logs->links() }}</div></div>
@endsection
