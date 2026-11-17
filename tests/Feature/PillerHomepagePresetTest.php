<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\HomepageController;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Role;
use App\Models\User;
use App\Services\Widgets\CmsWidgetRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PillerHomepagePresetTest extends TestCase
{
    use RefreshDatabase;
    public function test_each_of_the_ten_original_homepages_has_a_registered_draft_composition(): void
    {
        $presets = config('cms-homepage-presets', []);
        $registry = app(CmsWidgetRegistry::class);
        $expectedDesigns = ['index', 'home-2', 'home-3', 'home-4', 'home-5', 'home-6', 'home-7', 'home-8', 'home-9', 'home-10'];

        $this->assertSame($expectedDesigns, array_keys($presets));

        foreach ($presets as $design => $sectionKeys) {
            $hero = $registry->find('hero.' . $design);

            $this->assertNotNull($hero, $design . ' must retain its original Piller hero');
            $this->assertTrue(view()->exists($hero['view']), $design . ' hero view must exist');

            foreach ($sectionKeys as $key) {
                $definition = $registry->find($key);

                $this->assertNotNull($definition, $design . ' contains unregistered section ' . $key);
                $this->assertTrue(view()->exists($definition['view']), $design . ' section ' . $key . ' must resolve to a real Blade view');
            }
        }
    }

    public function test_duplicating_a_partial_homepage_preserves_existing_sections_and_adds_missing_preset_sections(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::create(['name' => 'Administrator', 'slug' => 'administrator']));
        $this->actingAs($admin);

        $homepage = Homepage::create([
            'name' => 'Home 4 source',
            'slug' => 'home-four-partial-source',
            'design_key' => 'home-4',
            'status' => 'published',
            'is_default' => false,
            'is_composition_complete' => false,
            'published_at' => now(),
        ]);
        $homepage->sections()->create([
            'instance_key' => (string) Str::uuid(),
            'section_key' => 'hero.home-4',
            'section_type' => 'hero',
            'source_design' => 'home-4',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => ['heading' => 'Preserve the existing hero'],
        ]);

        app(HomepageController::class)->duplicate($homepage);

        $draft = Homepage::query()->where('slug', 'like', 'home-four-partial-source-draft-%')->firstOrFail();
        $keys = $draft->sections->pluck('section_key')->all();
        $required = array_merge(['hero.home-4'], config('cms-homepage-presets.home-4'));

        $this->assertSame('Preserve the existing hero', $draft->sections->firstWhere('section_key', 'hero.home-4')->config['heading']);
        foreach ($required as $key) $this->assertContains($key, $keys);
        $this->assertCount(count($keys), array_unique($keys));
        $this->assertFalse($draft->is_composition_complete);

        app(HomepageController::class)->verifyComposition($draft);

        $this->assertTrue($draft->fresh()->is_composition_complete);
    }

    public function test_all_ten_presets_include_non_hero_content_and_are_not_implicitly_marked_complete(): void
    {
        $presets = config('cms-homepage-presets', []);

        $this->assertCount(10, $presets);

        foreach ($presets as $design => $sectionKeys) {
            $this->assertNotEmpty($sectionKeys, $design . ' must include original non-hero content');
            $this->assertNotContains('hero.' . $design, $sectionKeys, 'The controller adds each hero exactly once.');
        }
    }
}
