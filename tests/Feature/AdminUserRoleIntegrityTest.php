<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserRoleIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_role_and_user_create_update_operations_persist_permissions_and_credentials(): void
    {
        $admin = $this->administrator();
        $pageView = Permission::query()->where('slug', 'pages.view')->firstOrFail();
        $pageEdit = Permission::query()->where('slug', 'pages.edit')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.roles.store'), [
            'name' => 'Content Operator',
            'description' => 'Manages public content.',
            'permissions' => [$pageView->id],
        ])->assertRedirect(route('admin.roles.index'));

        $role = Role::query()->where('slug', 'content-operator')->firstOrFail();
        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $pageView->id,
        ]);

        $this->actingAs($admin)->put(route('admin.roles.update', $role), [
            'name' => 'Content Manager',
            'description' => 'Manages and edits public content.',
            'permissions' => [$pageView->id, $pageEdit->id],
        ])->assertRedirect();

        $role->refresh();
        $this->assertSame('Content Manager', $role->name);
        $this->assertEqualsCanonicalizing(
            [$pageView->id, $pageEdit->id],
            $role->permissions()->pluck('permissions.id')->all()
        );

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Content Operator User',
            'email' => 'content.operator@example.test',
            'phone' => '+919876543210',
            'password' => 'Strong-Test-Password-123!',
            'is_active' => '1',
            'roles' => [$role->id],
        ])->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'content.operator@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('Strong-Test-Password-123!', $user->password));
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->roles()->whereKey($role->id)->exists());

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Content Operator Updated',
            'email' => 'content.operator@example.test',
            'phone' => '+919876500000',
            'password' => '',
            'is_active' => '1',
            'roles' => [$role->id],
        ])->assertRedirect();

        $user->refresh();
        $this->assertSame('Content Operator Updated', $user->name);
        $this->assertSame('+919876500000', $user->phone);
        $this->assertTrue(Hash::check('Strong-Test-Password-123!', $user->password));
        $this->assertTrue($user->roles()->whereKey($role->id)->exists());
    }

    public function test_user_and_role_deletion_guards_preserve_builtin_administrator_access(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin)->delete(route('admin.roles.destroy', Role::where('slug', 'administrator')->firstOrFail()))
            ->assertSessionHasErrors();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertDatabaseHas('roles', ['slug' => 'administrator']);
    }
}
