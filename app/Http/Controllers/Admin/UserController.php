<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function authorizeAssignedRoles(array $roleIds): void
    {
        $actor = request()->user();

        if ($actor->isAdministrator()) {
            return;
        }

        $roles = Role::query()->with('permissions')->whereIn('id', $roleIds)->get();
        $requestedPermissions = $roles
            ->flatMap(fn($role) => $role->permissions->pluck('slug'))
            ->unique()
            ->all();

        abort_if(
            $roles->contains(fn($role) => $role->slug === 'administrator')
                || array_diff($requestedPermissions, $actor->permissionSlugs()) !== [],
            403,
            'You cannot assign a role that grants permissions you do not have.'
        );
    }

    public function index()
    {
        return view('admin.users.index', [
            'users' => User::with('roles')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:12'],
            'is_active' => ['sometimes', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $roleIds = $data['roles'] ?? [];
        $this->authorizeAssignedRoles($roleIds);

        DB::transaction(function () use ($data, $request, $roleIds) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'is_active' => $request->boolean('is_active'),
            ]);
            $user->roles()->sync($roleIds);
        });

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_if(
            $user->isAdministrator() && !$request->user()->isAdministrator(),
            403,
            'Only an Administrator can modify an Administrator account.'
        );

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:12'],
            'is_active' => ['sometimes', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $roleIds = $data['roles'] ?? [];
        $this->authorizeAssignedRoles($roleIds);

        if ($user->is($request->user())) {
            abort_if(!$request->boolean('is_active'), 422, 'You cannot disable your own account.');

            $assignedPermissions = Role::query()
                ->with('permissions')
                ->whereIn('id', $roleIds)
                ->get()
                ->flatMap(fn($role) => $role->permissions->pluck('slug'));

            abort_unless($assignedPermissions->contains('dashboard.view'), 422, 'Your account must retain dashboard access.');

            if ($request->user()->isAdministrator()) {
                $administratorRoleId = Role::where('slug', 'administrator')->value('id');
                abort_unless(in_array($administratorRoleId, $roleIds), 422, 'You cannot remove your own Administrator role.');
            }
        }

        if ($user->isAdministrator() && !in_array('administrator', Role::whereIn('id', $roleIds)->pluck('slug')->all(), true)) {
            abort_if(
                User::whereHas('roles', fn($query) => $query->where('slug', 'administrator'))->count() <= 1,
                422,
                'The last Administrator account cannot lose its role.'
            );
        }

        DB::transaction(function () use ($user, $data, $request, $roleIds) {
            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();
            $user->roles()->sync($roleIds);
        });

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        abort_if(
            $user->isAdministrator() && !request()->user()->isAdministrator(),
            403,
            'Only an Administrator can delete an Administrator account.'
        );
        abort_if($user->is(request()->user()), 422, 'You cannot delete your own account.');

        if ($user->isAdministrator()) {
            abort_if(
                User::whereHas('roles', fn($query) => $query->where('slug', 'administrator'))->count() <= 1,
                422,
                'The last Administrator account cannot be deleted.'
            );
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
