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
    <div class="content-card">
        <div class="table-responsive">
            <table class="table align-middle" data-datatable>
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
                            <td>{{ $p->price ? number_format((float) $p->price, 2) : ($p->price_label ?: '—') }}</td>
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
