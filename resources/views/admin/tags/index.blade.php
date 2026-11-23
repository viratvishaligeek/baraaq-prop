@extends('admin.layouts.app')
@section('title', 'Blog tags')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div><h1 class="page-title mb-1">Blog tags</h1><p class="text-muted mb-0">Manage reusable tags assigned to blog posts.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.tags.create') }}">Add tag</a>
    </div>
    <form method="GET" class="content-card mb-3 row g-2 align-items-end">
        <div class="col-md-5"><label class="form-label">Search tags</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Tag name or slug"></div>
        <div class="col-auto"><button class="btn btn-primary">Search</button><a class="btn btn-link" href="{{ route('admin.tags.index') }}">Reset</a></div>
    </form>
    <div class="content-card">
        <div class="table-responsive"><table class="table align-middle" data-datatable data-datatable-paging="false" data-datatable-searching="false"><thead><tr><th>Name</th><th>Slug</th><th>Posts</th><th></th></tr></thead><tbody>
            @forelse($tags as $tag)
                <tr><td class="fw-semibold">{{ $tag->name }}</td><td><code>{{ $tag->slug }}</code></td><td>{{ $tag->posts_count }}</td><td class="text-end"><a class="btn btn-sm btn-light" href="{{ route('admin.tags.edit', $tag) }}">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('Delete this tag? Posts will remain intact.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-5">No tags created yet.</td></tr>
            @endforelse
        </tbody></table></div>
        {{ $tags->links() }}
    </div>
@endsection
