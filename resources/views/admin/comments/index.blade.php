@extends('admin.layouts.app')
@section('title', 'Comment moderation')
@section('content')
<div class="mb-4"><h1 class="page-title">Comment moderation</h1><p class="text-muted">Review and moderate comments submitted on published blog posts.</p></div>
<div class="content-card">
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-6"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search author, email, comment or post"></div>
        <div class="col-md-3"><select class="form-select" name="status"><option value="">All statuses</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="col-auto"><button class="btn btn-outline-primary">Filter</button><a class="btn btn-link" href="{{ route('admin.comments.index') }}">Reset</a></div>
    </form>
    <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Comment</th><th>Post</th><th>Status</th><th>Moderate</th></tr></thead><tbody>
    @forelse($comments as $comment)
        <tr><td><strong>{{ $comment->name }}</strong><div class="small text-muted">{{ $comment->email }}</div><p class="mb-0 mt-2">{{ $comment->body }}</p>@if(auth()->user()->isAdministrator() || auth()->user()->hasPermission('comments.manage') || auth()->user()->hasPermission('comments.delete'))<form method="POST" action="{{ route('admin.comments.destroy',$comment) }}" class="mt-2" onsubmit="return confirm('Delete this comment?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>@endif</td>
        <td><a href="{{ route('blog.show',$comment->post) }}" target="_blank" rel="noopener">{{ $comment->post?->title ?? 'Post deleted' }}</a><div class="small text-muted">{{ $comment->created_at->format('d M Y H:i') }}</div></td>
        <td><span class="badge text-bg-{{ $comment->status === 'approved' ? 'success' : ($comment->status === 'rejected' ? 'secondary' : 'warning') }}">{{ ucfirst($comment->status) }}</span></td>
        <td>@if(auth()->user()->isAdministrator() || auth()->user()->hasPermission('comments.manage') || auth()->user()->hasPermission('comments.edit') || auth()->user()->hasPermission('comments.publish'))<form method="POST" action="{{ route('admin.comments.update',$comment) }}" class="d-flex gap-2">@csrf @method('PATCH')<select class="form-select form-select-sm" name="status">@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected($comment->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select><button class="btn btn-sm btn-primary">Save</button></form>@else<span class="text-muted">View only</span>@endif</td></tr>
    @empty<tr><td colspan="4" class="text-center text-muted py-5">No comments match these filters.</td></tr>@endforelse
    </tbody></table></div>{{ $comments->links() }}
</div>
@endsection
