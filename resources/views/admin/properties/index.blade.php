@extends('admin.layouts.app')
@section('title', 'Properties')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Properties</h1>
            <p class="text-muted mb-0">Manage your property listings.</p>
        </div>
        @if (auth()->user()->isAdministrator() ||
                auth()->user()->hasPermission('properties.manage') ||
                auth()->user()->hasPermission('properties.create'))
            <a class="btn btn-primary" href="{{ route('admin.properties.create') }}">
                <i class="fa fa-plus me-2">
                </i>Add property</a>
        @endif
    </div>
    <form method="GET" class="content-card mb-3 row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label">Search</label><input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Title, reference, address or city"></div>
        <div class="col-md-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All statuses</option>@foreach(['draft','published','archived'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Property type</label><select class="form-select" name="property_type"><option value="">All types</option>@foreach($propertyTypes as $type)<option value="{{ $type }}" @selected(($filters['property_type'] ?? '') === $type)>{{ $type }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">Feature</label><input class="form-control" name="feature" value="{{ $filters['feature'] ?? '' }}" placeholder="e.g. parking"></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1">Filter</button><a class="btn btn-light" href="{{ route('admin.properties.index') }}">Reset</a></div>
    </form>
    <div class="content-card">
        <div class="table-responsive">
            <table class="table align-middle" data-datatable data-datatable-paging="false" data-datatable-searching="false">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($properties as $p)
                        <tr>
                            <td>
                                <strong>{{ $p->title }}</strong>
                                <div class="small text-muted">{{ $p->reference ?: 'No reference' }}</div>
                            </td>
                            <td>{{ ucfirst($p->listing_type) }}</td>
                            <td class="price-red-shine">{{ $p->price ? '₹ '.number_format((float)$p->price, 2) : ($p->price_label ?: '—') }}</td>
                            <td>{{ $p->city ?: '—' }}</td>
                            <td>{{ ucfirst($p->status) }}</td>
                            <td class="text-end">
                                @if (auth()->user()->isAdministrator() ||
                                        auth()->user()->hasPermission('properties.manage') ||
                                        auth()->user()->hasPermission('properties.edit'))
                                    <a class="btn btn-sm btn-light" href="{{ route('admin.properties.edit', $p) }}">Edit</a>
                                    @endif @if (auth()->user()->isAdministrator() ||
                                            auth()->user()->hasPermission('properties.manage') ||
                                            auth()->user()->hasPermission('properties.delete'))
                                        <form class="d-inline" method="POST"
                                            action="{{ route('admin.properties.destroy', $p) }}"
                                            data-confirm="Move this property to trash?">@csrf @method('DELETE')<button
                                                class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endif
                            </td>
                    </tr>@empty<tr>
                            <td colspan="6" class="text-center py-5 text-muted">No properties found. Add your first
                                listing.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>{{ $properties->links() }}
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('[data-confirm]').forEach(f => f.addEventListener('submit', e => {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: f.dataset.confirm,
                icon: 'warning',
                showCancelButton: true
            }).then(r => {
                if (r.isConfirmed) f.submit()
            })
        }));
    </script>
@endpush
