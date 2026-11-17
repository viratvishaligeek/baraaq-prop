@extends('admin.layouts.app')
@section('title', 'Widgets')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="page-title">Widgets</h1><p class="text-muted mb-0">Create reusable content blocks and assign them to website areas.</p></div>
    <a class="btn btn-primary" href="{{ route('admin.widgets.create') }}">Create widget</a>
</div>
<div class="row g-3 mb-4">
@foreach($areas as $area)
    <div class="col-md-3"><div class="content-card h-100"><div class="fw-semibold">{{ $area->name }}</div><div class="small text-muted">{{ $area->location }}</div><div class="mt-2">{{ $area->items()->count() }} assigned widgets</div></div></div>
@endforeach
</div>
<div class="content-card"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Name</th><th>Type</th><th>Areas</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($widgets as $widget)
<tr><td class="fw-semibold">{{ $widget->name }}</td><td><code>{{ str_replace('_',' ',$widget->type) }}</code></td><td>{{ $widget->areas->pluck('name')->join(', ') ?: '—' }}</td><td><span class="badge {{ $widget->status==='published'?'text-bg-success':'text-bg-secondary' }}">{{ ucfirst($widget->status) }}</span></td><td class="text-end"><a class="btn btn-sm btn-light" href="{{ route('admin.widgets.edit',$widget) }}">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.widgets.destroy',$widget) }}" onsubmit="return confirm('Delete this widget?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="5" class="text-center text-muted py-5">No widgets created yet.</td></tr>@endforelse
</tbody></table></div>{{ $widgets->links() }}</div>
@endsection
