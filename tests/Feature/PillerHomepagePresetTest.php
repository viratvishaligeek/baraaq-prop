<?php
namespace Tests\Feature;
use App\Models\Homepage;
use Database\Seeders\HomepageTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PillerHomepagePresetTest extends TestCase
{
    use RefreshDatabase;
    public function test_only_home_nine_is_seeded_as_the_fixed_homepage(): void
    {
        $this->seed(HomepageTemplateSeeder::class);
        $this->assertSame(1, Homepage::query()->count());
        $homepage = Homepage::query()->firstOrFail();
        $this->assertSame('home-9', $homepage->slug);
        $this->assertSame('home-9', $homepage->design_key);
        $this->assertTrue($homepage->is_default);
        $this->assertSame('published', $homepage->status);
    }
    public function test_active_section_registry_contains_only_the_fixed_home_nine_composition(): void
    {
        $expected = ['hero.home-9', 'about.home-9', 'statistics.home-9', 'services.home-9', 'properties.index', 'featured-gallery.home-9', 'home-details.home-9', 'team.home-2', 'testimonials.home-3', 'blog.home-2'];
        $this->assertSame($expected, array_keys(config('cms-sections.sections')));
        foreach ($expected as $key) {
            $definition = config('cms-sections.sections.' . $key);
            $this->assertNotEmpty($definition['view']);
            $this->assertTrue(view()->exists($definition['view']));
        }
    }
}
