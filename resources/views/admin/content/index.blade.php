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
            <div class="col-md-6">
                <input class="form-control" name="search" value="{{ request('search') }}"
                    placeholder="Search {{ $type }}...">
            </div>
            @if (in_array($type, ['posts', 'pages', 'services', 'team', 'testimonials']))
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All statuses</option>
                        @foreach (in_array($type, ['team', 'testimonials']) ? ['draft', 'published'] : ['draft', 'published', 'archived'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-auto">
                <button class="btn btn-outline-primary">Filter</button>
                <a class="btn btn-link" href="{{ route('admin.content.index', $type) }}">Reset</a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table align-middle">
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
                                @if ($type === 'pages' && $item->homepage_key === 'primary')
                                    <span class="badge bg-success ms-1">Homepage</span>
                                @endif
                            </td>
                            <td>{{ $item->slug ?? ($item->designation ?? ($item->placement ?? '—')) }}</td>
                            <td>{{ isset($item->status) ? ucfirst($item->status) : (isset($item->is_active) ? ($item->is_active ? 'Active' : 'Inactive') : '—') }}</td>
                            <td class="text-end">
                                @if ($type === 'pages' && $item->status === 'published' && $item->homepage_key !== 'primary' &&
                                        (auth()->user()->isAdministrator() || auth()->user()->hasPermission('pages.manage')))
                                    <form class="d-inline" method="POST"
                                        action="{{ route('admin.pages.homepage', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success">Set as homepage</button>
                                    </form>
                                @endif
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
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">No {{ $type }} found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
@endsection
