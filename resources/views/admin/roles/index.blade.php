@extends('admin.layouts.app') @section('title', 'Roles & permissions') @section('content')<div
    class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Roles & permissions</h1>
        <p class="text-muted">Control access to CMS modules.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('admin.roles.create') }}">Create role</a>
</div>
<div class="content-card">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Role</th>
                <th>Users</th>
                <th>Permissions</th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>
                        <strong>{{ $role->name }}</strong>
                        <div class="small text-muted">{{ $role->description }}</div>
                    </td>
                    <td>{{ $role->users_count }}</td>
                    <td>{{ $role->permissions_count }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-light" href="{{ route('admin.roles.edit', $role) }}">Manage</a>
                        @if ($role->slug !== 'administrator')
                            <form class="d-inline" method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                onsubmit="return confirm('Delete this role?')">@csrf @method('DELETE')<button
                                    class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>@endsection
