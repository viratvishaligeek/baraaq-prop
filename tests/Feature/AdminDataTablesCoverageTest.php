<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDataTablesCoverageTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_every_generic_content_listing_uses_the_shared_responsive_datatable(): void
    {
        $admin = $this->administrator();

        foreach (['posts', 'pages', 'categories', 'banners', 'team', 'services', 'testimonials'] as $type) {
            $response = $this->actingAs($admin)->get(route('admin.content.index', $type));

            $response->assertOk()
                ->assertSee('data-datatable', false)
                ->assertSee('responsive: true', false)
                ->assertSee('DataTable.isDataTable(table)', false)
                ->assertSee('cdn.datatables.net/2.3.4/js/dataTables.min.js', false);
        }
    }

    public function test_media_library_uses_its_paged_visual_grid_instead_of_an_unpaged_table(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->get(route('admin.media.index'))
            ->assertOk()
            ->assertDontSee('data-datatable', false)
            ->assertSee('pagination', false);
    }
}
