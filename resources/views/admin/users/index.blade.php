@extends('admin.layouts.app') @section('title', 'Users') @section('content')<div
    class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Users</h1>
        <p class="text-muted">Manage administrator and editor accounts.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Add user</a>
</div>
<div class="content-card">
    <table class="table align-middle" data-datatable>
        <thead>
            <tr>
                <th>User</th>
                <th>Roles</th>
                <th>Status</th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                        <strong>{{ $user->name }}</strong>
                        <div class="small text-muted">{{ $user->email }}</div>
                    </td>
                    <td>{{ $user->roles->pluck('name')->join(', ') ?: 'No role' }}</td>
                    <td>{{ $user->is_active ? 'Active' : 'Disabled' }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-light" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                        @if (!$user->is(auth()->user()))
                            <form class="d-inline" method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE')<button
                                    class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>{{ $users->links() }}
</div>@endsection
