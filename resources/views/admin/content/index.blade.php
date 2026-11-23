@extends('admin.layouts.app')
@section('title', ucfirst($type))
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">{{ ucfirst($type) }}</h1>
            <p class="text-muted mb-0">Manage {{ $type }} content.</p>
        </div>
        @if (auth()->user()->isAdministrator() ||
                auth()->user()->hasPermission($type . '.manage') ||
                auth()->user()->hasPermission($type . '.create'))
            <a class="btn btn-primary" href="{{ route('admin.content.create', $type) }}">
                <i class="fa fa-plus me-2"></i>Add new
            </a>
        @endif
    </div>
    <div class="content-card">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input class="form-control" type="search" name="q" value="{{ request('q') }}"
                    placeholder="Search {{ $type }}..." aria-label="Search {{ $type }}">
            </div>
            @if (in_array($type, ['posts', 'pages', 'services', 'team', 'testimonials']))
                <div class="col-md-3">
                    <select class="form-select" name="status" aria-label="Filter by publication status">
                        <option value="">All statuses</option>
                        @foreach (in_array($type, ['team', 'testimonials']) ? ['draft', 'published'] : ['draft', 'published', 'archived'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if ($type === 'categories')
                <div class="col-md-3">
                    <select class="form-select" name="category_type" aria-label="Filter category type">
                        <option value="">All category types</option>
                        <option value="property" @selected(request('category_type') === 'property')>Property</option>
                        <option value="post" @selected(request('category_type') === 'post')>Blog</option>
                    </select>
                </div>
            @endif
            <div class="col-md-auto">
                <button class="btn btn-outline-primary" type="submit">Search / filter</button>
                <a class="btn btn-link" href="{{ route('admin.content.index', $type) }}">Reset</a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle" data-datatable data-datatable-paging="false" data-datatable-searching="false">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug / detail</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="fw-semibold">
                                {{ $item->title ?? $item->name }}
                            </td>
                            <td>{{ $item->slug ?? ($item->designation ?? ($item->placement ?? '—')) }}</td>
                            <td>{{ isset($item->status) ? ucfirst($item->status) : (isset($item->is_active) ? ($item->is_active ? 'Active' : 'Inactive') : '—') }}</td>
                            <td class="text-end">
                                @if (auth()->user()->isAdministrator() ||
                                        auth()->user()->hasPermission($type . '.manage') ||
                                        auth()->user()->hasPermission($type . '.edit'))
                                    <a class="btn btn-sm btn-light"
                                        href="{{ route('admin.content.edit', [$type, $item->id]) }}">Edit</a>
                                @endif
                                @if (auth()->user()->isAdministrator() ||
                                        auth()->user()->hasPermission($type . '.manage') ||
                                        auth()->user()->hasPermission($type . '.delete'))
                                    <form class="d-inline" method="POST"
                                        action="{{ route('admin.content.destroy', [$type, $item->id]) }}"
                                        onsubmit="return confirm('Delete this item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
            <small class="text-muted">Showing {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} of {{ $items->total() }} records</small>
            {{ $items->links() }}
        </div>
    </div>
@endsection
