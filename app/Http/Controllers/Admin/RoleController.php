<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    private function authorizePermissionAssignments(array $permissionIds): void
    {
        $user = request()->user();

        if ($user->isAdministrator()) {
            return;
        }

        $requested = Permission::query()
            ->whereIn('id', $permissionIds)
            ->pluck('slug')
            ->all();

        abort_if(
            array_diff($requested, $user->permissionSlugs()) !== [],
            403,
            'You cannot grant permissions that you do not have.'
        );
    }

    public function index()
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount('users')->withCount('permissions')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.roles.form', [
            'role' => new Role,
            'permissions' => Permission::orderBy('group')->orderBy('name')->get()->groupBy('group'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $permissionIds = $data['permissions'] ?? [];
        $this->authorizePermissionAssignments($permissionIds);

        $base = Str::slug($data['name']) ?: Str::random(8);
        $slug = $base;
        $suffix = 2;

        while (Role::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        DB::transaction(function () use ($data, $permissionIds, $slug) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
            ]);
            $role->permissions()->sync($permissionIds);
        });

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.form', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::orderBy('group')->orderBy('name')->get()->groupBy('group'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->slug === 'administrator' && !$request->user()->isAdministrator(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $permissionIds = $data['permissions'] ?? [];
        $this->authorizePermissionAssignments($permissionIds);

        DB::transaction(function () use ($role, $data, $permissionIds) {
            $role->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);
            $role->permissions()->sync($permissionIds);
        });

        return back()->with('success', 'Role permissions updated.');
    }

    public function destroy(Role $role)
    {
        abort_if($role->slug === 'administrator', 422, 'The built-in Administrator role cannot be deleted.');
        abort_if($role->users()->exists(), 422, 'Remove this role from all users before deleting it.');

        $role->delete();

        return back()->with('success', 'Role deleted.');
    }
}
