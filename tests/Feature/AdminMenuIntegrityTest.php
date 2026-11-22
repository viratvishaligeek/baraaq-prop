<?php

namespace Tests\Feature;

use App\Models\Homepage;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\HomepageTemplateSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMenuIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_service_menu_items_can_be_selected_saved_and_resolved(): void
    {
        $admin = $this->administrator();
        $service = Service::create([
            'title' => 'Property valuation',
            'slug' => 'property-valuation',
            'status' => 'published',
            'published_at' => now(),
            'content' => 'Professional property valuation.',
        ]);

        $this->actingAs($admin)->post(route('admin.menus.store'), [
            'name' => 'Footer services',
            'location' => 'footer-services',
            'items' => [[
                'label' => 'Property valuation',
                'link_type' => 'service',
                'link_id' => (string) $service->id,
                'url' => '',
                'target' => '_self',
                'is_active' => '1',
            ]],
        ])->assertRedirect();

        $menu = Menu::query()->where('location', 'footer-services')->firstOrFail();
        $item = $menu->allItems()->firstOrFail();

        $this->assertSame(route('services.show', $service->slug), $item->resolved_url);

        $this->actingAs($admin)->get(route('admin.menus.edit', $menu))
            ->assertOk()
            ->assertSee('Service: Property valuation');

        $cachedMenu = Menu::cached('footer-services');
        $this->assertNotNull($cachedMenu);
        $this->assertSame(route('services.show', $service->slug), $cachedMenu->items->first()->resolved_url);
    }

    public function test_menu_custom_links_reject_unsafe_schemes(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->post(route('admin.menus.store'), [
            'name' => 'Unsafe menu',
            'location' => 'unsafe-menu',
            'items' => [[
                'label' => 'Unsafe link',
                'link_type' => 'custom',
                'url' => 'javascript:alert(1)',
                'target' => '_self',
                'is_active' => '1',
            ]],
        ])->assertSessionHasErrors('items.0.url');
    }

    public function test_legacy_unsafe_menu_urls_render_as_non_executable_links(): void
    {
        $menu = Menu::create(['name' => 'Legacy menu', 'location' => 'legacy-menu']);
        $item = $menu->allItems()->create([
            'label' => 'Legacy link',
            'link_type' => 'custom',
            'url' => 'javascript:alert(1)',
            'target' => '_self',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->assertSame('#', $item->resolved_url);
    }

    public function test_frontend_navigation_fallback_uses_a_registered_contact_route(): void
    {
        $this->seed(HomepageTemplateSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Contact')
            ->assertSee(route('pages.show', 'contact'), false);
    }
}
