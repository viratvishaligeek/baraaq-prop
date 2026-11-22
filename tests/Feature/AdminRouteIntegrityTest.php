<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminRouteIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_routes_referenced_by_admin_and_frontend_navigation_are_registered(): void
    {
        $names = [
            'home', 'contact', 'about', 'sitemap', 'robots', 'blog.index', 'blog.show', 'properties.index', 'properties.show',
            'services.index', 'services.show', 'team.index', 'testimonials.index', 'enquiries.store', 'newsletter.store',
            'admin.login.store', 'admin.logout', 'admin.dashboard', 'admin.analytics.index',
            'admin.properties.index', 'admin.properties.create', 'admin.properties.types', 'admin.properties.amenities',
            'admin.properties.store', 'admin.properties.update', 'admin.properties.edit', 'admin.properties.destroy',
            'admin.properties.images.destroy', 'admin.properties.images.reorder', 'admin.properties.images.feature',
            'admin.enquiries.index', 'admin.enquiries.update', 'admin.enquiries.destroy',
            'admin.comments.index', 'admin.comments.update', 'admin.comments.destroy',
            'admin.content.index', 'admin.content.create', 'admin.content.store', 'admin.content.edit',
            'admin.content.update', 'admin.content.destroy', 'admin.content.preview',
            'admin.tags.index', 'admin.tags.create', 'admin.tags.store', 'admin.tags.edit', 'admin.tags.update', 'admin.tags.destroy',
            'admin.appearance.index', 'admin.homepages.index', 'admin.homepages.update', 'admin.homepages.sections.update',
            'admin.menus.index', 'admin.menus.create', 'admin.menus.store', 'admin.menus.edit', 'admin.menus.update', 'admin.menus.destroy',
            'admin.media.index', 'admin.media.store', 'admin.media.picker', 'admin.media.picker.upload',
            'admin.media.edited-copy', 'admin.media.update', 'admin.media.destroy', 'admin.media.bulk-destroy',
            'admin.seo.index', 'admin.seo.indexing', 'admin.settings.edit', 'admin.settings.update',
            'admin.newsletters.index', 'admin.newsletters.destroy',
            'admin.users.index', 'admin.users.create', 'admin.users.store', 'admin.users.edit', 'admin.users.update', 'admin.users.destroy',
            'admin.roles.index', 'admin.roles.create', 'admin.roles.store', 'admin.roles.edit', 'admin.roles.update', 'admin.roles.destroy',
        ];

        foreach ($names as $name) {
            $this->assertTrue(Route::has($name), "Expected route [{$name}] to be registered.");
        }
    }

    public function test_route_names_are_unique_and_legacy_theme_page_route_is_absent(): void
    {
        $names = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values();

        $this->assertCount($names->count(), $names->unique());
        $this->assertFalse(Route::has('theme.page'));
        $this->assertFalse(Route::has('admin.homepages.builder'));
        $this->assertFalse(Route::has('admin.widgets.index'));
        $this->assertFalse(Route::has('admin.page-builder.index'));
        $this->assertFalse(Route::has('admin.section-library.index'));
        $this->assertFalse(Route::has('admin.reusable-sections.index'));
    }

    public function test_admin_data_tables_are_enabled_on_the_listing_screens(): void
    {
        $admin = $this->administrator();

        $screens = [
            ['admin.dashboard', []],
            ['admin.analytics.index', []],
            ['admin.comments.index', []],
            ['admin.content.index', ['pages']],
            ['admin.enquiries.index', []],
            ['admin.menus.index', []],
            ['admin.newsletters.index', []],
            ['admin.properties.index', []],
            ['admin.properties.types', []],
            ['admin.properties.amenities', []],
            ['admin.roles.index', []],
            ['admin.seo.index', []],
            ['admin.tags.index', []],
            ['admin.users.index', []],
        ];

        $serverPaginated = ['admin.comments.index', 'admin.enquiries.index', 'admin.menus.index', 'admin.newsletters.index', 'admin.properties.index', 'admin.tags.index', 'admin.users.index'];

        foreach ($screens as [$name, $parameters]) {
            $response = $this->actingAs($admin)->get(route($name, $parameters));
            $response->assertOk();
            $response->assertSee('data-datatable', false, "Admin listing [{$name}] must opt its table into DataTables.");
            $response->assertSee('cdn.datatables.net/2.3.4/js/dataTables.min.js', false);
            if (in_array($name, $serverPaginated, true)) {
                $response->assertSee('data-datatable-paging="false"', false);
            }
            $response->assertSee('responsive: true', false);
            $response->assertSee('DataTable.isDataTable(table)', false);
        }
    }

    public function test_homepage_section_update_route_uses_patch_and_expected_parameters(): void
    {
        $route = Route::getRoutes()->getByName('admin.homepages.sections.update');

        $this->assertNotNull($route);
        $this->assertContains('PATCH', $route->methods());
        $this->assertSame('admin/homepages/{homepage}/sections/{section}', $route->uri());
    }
}
