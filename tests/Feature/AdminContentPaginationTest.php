<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_content_listing_supports_server_side_search_and_pagination(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        Page::create([
            'title' => 'Unique Searchable About',
            'slug' => 'unique-searchable-about',
            'status' => 'draft',
        ]);
        Page::create([
            'title' => 'Unrelated Draft Page',
            'slug' => 'unrelated-draft-page',
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.content.index', ['type' => 'pages', 'q' => 'Unique Searchable']))
            ->assertOk()
            ->assertSee('Unique Searchable About')
            ->assertDontSee('Unrelated Draft Page')
            ->assertSee('data-datatable-paging="false"', false);
    }
}
