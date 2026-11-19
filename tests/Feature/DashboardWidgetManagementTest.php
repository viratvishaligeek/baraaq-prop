<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWidgetManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_configure_enabled_dashboard_widgets_destinations_and_order(): void
    {
        $user = $this->administratorWithSettingsPermission();

        $this->actingAs($user)
            ->get(route('admin.dashboard.widgets.edit'))
            ->assertOk()
            ->assertSee('Dashboard widgets')
            ->assertSee('Published properties');

        $widgets = [
            'properties' => ['enabled' => '0', 'destination' => 'properties', 'order' => 1],
            'published_properties' => ['enabled' => '1', 'destination' => 'properties', 'order' => 2],
            'posts' => ['enabled' => '0', 'destination' => 'posts', 'order' => 3],
            'pages' => ['enabled' => '0', 'destination' => 'pages', 'order' => 4],
            'users' => ['enabled' => '0', 'destination' => 'users', 'order' => 5],
            'new_enquiries' => ['enabled' => '1', 'destination' => 'enquiries', 'order' => 6],
            'visits' => ['enabled' => '0', 'destination' => 'analytics', 'order' => 7],
            'page_views' => ['enabled' => '0', 'destination' => 'analytics', 'order' => 8],
        ];

        $this->put(route('admin.dashboard.widgets.update'), ['widgets' => $widgets])
            ->assertRedirect(route('admin.dashboard'));

        $saved = Setting::read('dashboard_widgets');
        $this->assertFalse($saved['properties']['enabled']);
        $this->assertSame('properties', $saved['published_properties']['destination']);
        $this->assertSame('enquiries', $saved['new_enquiries']['destination']);
        $this->assertSame(6, $saved['new_enquiries']['order']);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Published properties')
            ->assertSee('New enquiries')
            ->assertDontSee('Blog posts');
    }

    public function test_dashboard_widget_configuration_requires_settings_permission(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $role = Role::create(['name' => 'Content editor', 'slug' => 'content-editor']);
        $user->roles()->attach($role);

        $this->actingAs($user)->get(route('admin.dashboard.widgets.edit'))->assertForbidden();
    }

    private function administratorWithSettingsPermission(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $role = Role::create(['name' => 'Administrator', 'slug' => 'administrator']);
        $permission = Permission::create(['name' => 'Manage settings', 'slug' => 'settings.manage', 'group' => 'settings']);
        $dashboardPermission = Permission::create(['name' => 'View dashboard', 'slug' => 'dashboard.view', 'group' => 'dashboard']);
        $role->permissions()->attach([$permission->id, $dashboardPermission->id]);
        $user->roles()->attach($role);

        return $user;
    }
}
