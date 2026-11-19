@extends('admin.layouts.app')
@section('title', 'Universal page builder')
@section('content')
    <div class="mb-4"><h1 class="page-title mb-1">Universal page builder</h1><p class="text-muted mb-0">Choose an existing page or service to edit its registered widget layout. Published content remains unchanged until you create and publish a draft.</p></div>
    <div class="content-card mb-4">
        <h2 class="h5 mb-3">Pages</h2>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Page</th><th>Status</th><th>Updated</th><th></th></tr></thead><tbody>
            @forelse($pages as $page)
                <tr><td class="fw-semibold">{{ $page->title }}</td><td>{{ ucfirst($page->status) }}</td><td>{{ $page->updated_at?->format('d M Y') }}</td><td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('admin.pages.builder', $page) }}">Open builder</a></td></tr>
            @empty<tr><td colspan="4" class="text-center text-muted py-4">No pages yet. <a href="{{ route('admin.content.index', 'pages') }}">Create a page</a>.</td></tr>@endforelse
        </tbody></table></div>
        {{ $pages->links() }}
    </div>
    <div class="content-card">
        <h2 class="h5 mb-3">Services</h2>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Service</th><th>Status</th><th>Updated</th><th></th></tr></thead><tbody>
            @forelse($services as $service)
                <tr><td class="fw-semibold">{{ $service->title }}</td><td>{{ ucfirst($service->status) }}</td><td>{{ $service->updated_at?->format('d M Y') }}</td><td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('admin.services.builder', $service) }}">Open builder</a></td></tr>
            @empty<tr><td colspan="4" class="text-center text-muted py-4">No services yet. <a href="{{ route('admin.content.index', 'services') }}">Create a service</a>.</td></tr>@endforelse
        </tbody></table></div>
        {{ $services->links() }}
    </div>
@endsection
