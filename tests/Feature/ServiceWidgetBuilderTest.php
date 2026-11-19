<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Services\Pages\PageBuilderService;
use App\Services\Widgets\CmsWidgetRegistry;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ServiceWidgetBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_add_and_reorder_registered_widgets(): void
    {
        $user = $this->administrator();
        $service = Service::create([
            'title' => 'Property management',
            'slug' => 'property-management-builder-test',
            'status' => 'draft',
        ]);

        $key = 'about.home-4';
        $this->actingAs($user)
            ->get(route('admin.services.builder', $service))
            ->assertOk()
            ->assertSee('Service builder');

        $this->actingAs($user)
            ->post(route('admin.services.widgets.add', $service), ['section_key' => $key])
            ->assertRedirect();

        $section = $service->fresh()->sections()->firstOrFail();
        $this->assertSame($key, $section->section_key);
        $this->assertSame($service->id, $section->service_id);
        $this->assertNull($section->page_id);

        $second = app(PageBuilderService::class)->duplicate($service, $section);
        $response = $this->actingAs($user)->patchJson(route('admin.services.widgets.reorder', $service), [
            'sections' => [$second->id, $section->id],
        ]);

        $response->assertOk()->assertJsonPath('saved', true);
        $this->assertSame([$second->id, $section->id], $service->fresh()->sections->pluck('id')->all());
    }

    public function test_published_service_renders_its_registered_widget_instances(): void
    {
        $service = Service::create([
            'title' => 'Property consultation',
            'slug' => 'property-consultation-widget-test',
            'status' => 'published',
            'published_at' => now(),
            'excerpt' => 'Expert property advice.',
        ]);
        $definition = app(CmsWidgetRegistry::class)->find('about.home-4');

        $service->sections()->create([
            'instance_key' => (string) Str::uuid(),
            'section_key' => 'about.home-4',
            'section_type' => $definition['type'],
            'source_design' => $definition['source_design'],
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => array_merge($definition['defaults'] ?? [], ['heading' => 'Service widget content']),
            'data_source' => [],
        ]);

        $this->get(route('services.show', $service))
            ->assertOk()
            ->assertSee('Service widget content');
    }

    public function test_published_service_can_be_copied_to_a_separate_draft_without_changing_original(): void
    {
        $user = $this->administrator();
        $service = Service::create([
            'title' => 'Property valuation',
            'slug' => 'property-valuation-copy-test',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $definition = app(CmsWidgetRegistry::class)->find('about.home-4');
        $service->sections()->create([
            'instance_key' => (string) Str::uuid(),
            'section_key' => 'about.home-4',
            'section_type' => $definition['type'],
            'source_design' => $definition['source_design'],
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => $definition['defaults'] ?? [],
            'data_source' => [],
        ]);

        $response = $this->actingAs($user)
            ->post(route('admin.services.duplicate', $service));

        $draft = Service::query()->where('slug', 'property-valuation-copy-test-draft')->firstOrFail();
        $response->assertRedirect(route('admin.services.builder', $draft));
        $this->assertSame('published', $service->fresh()->status);
        $this->assertSame('draft', $draft->status);
        $this->assertCount(1, $draft->sections);
        $this->assertSame($draft->id, $draft->sections->first()->service_id);
    }

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }
}
