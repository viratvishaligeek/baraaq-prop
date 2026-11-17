<?php

namespace Tests\\Feature;

use App\\Services\\Widgets\\CmsWidgetRegistry;
use Tests\\TestCase;

class PillerHomepagePresetTest extends TestCase
{
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
