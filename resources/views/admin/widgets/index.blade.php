@extends('admin.layouts.app')
@section('title', 'Widgets')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Widgets</h1>
            <p class="text-muted mb-0">Create reusable content blocks and assign them to website areas.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.widgets.create') }}">Create widget</a>
    </div>
    <div class="row g-3 mb-4">
        @foreach ($areas as $area)
            <div class="col-lg-6">
                <div class="content-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="fw-semibold">{{ $area->name }}</div>
                            <div class="small text-muted">{{ $area->location }}</div>
                        </div>
                        <span class="badge text-bg-light">{{ $area->items->count() }} assigned</span>
                    </div>
                    <div class="widget-sort-list list-group"
                        data-reorder-url="{{ route('admin.widget-areas.reorder', $area) }}">
                        @forelse($area->items as $item)
                            <div class="list-group-item d-flex align-items-center gap-2" draggable="true"
                                data-item-id="{{ $item->id }}">
                                <span class="text-muted" style="cursor:move">↕</span>
                                <span class="flex-grow-1">{{ $item->widget?->name ?? 'Deleted widget' }}</span>
                                <span
                                    class="small text-muted">{{ $item->is_active && $item->widget?->status === 'published' ? 'Enabled' : 'Disabled' }}</span>
                            </div>
                        @empty
                            <div class="small text-muted py-2">No widgets assigned to this area.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="content-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Areas</th>
                        <th>Status</th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($widgets as $widget)
                        <tr>
                            <td class="fw-semibold">{{ $widget->name }}</td>
                            <td>
                                <code>{{ str_replace('_', ' ', $widget->type) }}</code>
                            </td>
                            <td>{{ $widget->areas->pluck('name')->join(', ') ?: '—' }}</td>
                            <td>
                                <span
                                    class="badge {{ $widget->status === 'published' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($widget->status) }}</span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-light" href="{{ route('admin.widgets.edit', $widget) }}">Edit</a>
                                <form class="d-inline" method="POST" action="{{ route('admin.widgets.destroy', $widget) }}"
                                    onsubmit="return confirm('Delete this widget?')">@csrf @method('DELETE')<button
                                        class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty<tr>
                            <td colspan="5" class="text-center text-muted py-5">No widgets created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>{{ $widgets->links() }}
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
            document.querySelectorAll('.widget-sort-list').forEach(function(list) {
                var dragged = null;
                list.addEventListener('dragstart', function(event) {
                    var row = event.target.closest('[data-item-id]');
                    if (!row) return;
                    dragged = row;
                    event.dataTransfer.effectAllowed = 'move';
                });
                list.addEventListener('dragover', function(event) {
                    event.preventDefault();
                    var target = event.target.closest('[data-item-id]');
                    if (dragged && target && target !== dragged) {
                        var rect = target.getBoundingClientRect();
                        list.insertBefore(dragged, event.clientY < rect.top + rect.height / 2 ? target :
                            target.nextSibling);
                    }
                });
                list.addEventListener('drop', async function(event) {
                    event.preventDefault();
                    var ids = Array.from(list.querySelectorAll('[data-item-id]')).map(function(
                    row) {
                        return Number(row.dataset.itemId);
                    });
                    var response = await fetch(list.dataset.reorderUrl, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            items: ids
                        })
                    });
                    if (!response.ok) alert('Could not save widget order. Refresh and try again.');
                });
                list.addEventListener('dragend', function() {
                    dragged = null;
                });
            });
        })();
    </script>
@endpush
