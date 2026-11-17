<?php

namespace Tests\Feature;

use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Banner;
use App\Models\Page;
use App\Models\Testimonial;
use App\Services\Homepage\SectionDataResolver;
use App\Services\Pages\PageBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UniversalPageBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_widgets_reuse_the_existing_homepage_sections_table(): void
    {
        $page = Page::create([
            'title' => 'About us',
            'slug' => 'about-us-builder-test',
            'status' => 'draft',
        ]);

        $builder = app(PageBuilderService::class);
        $section = $builder->add($page, 'hero.index');

        $this->assertDatabaseHas('homepage_sections', [
            'id' => $section->id,
            'page_id' => $page->id,
            'homepage_id' => null,
            'section_key' => 'hero.index',
        ]);

        $builder->save($page, $section, [
            'config' => ['heading' => 'A configurable page heading'],
            'data_source' => ['limit' => 5, 'sort_direction' => 'asc'],
        ]);

        $this->assertSame('A configurable page heading', $section->fresh()->config['heading']);
        $this->assertSame('asc', $section->fresh()->data_source['sort_direction']);
    }

    public function test_page_widget_instances_can_be_duplicated_and_reordered_without_touching_homepage_sections(): void
    {
        $homepage = Homepage::create([
            'name' => 'Existing homepage',
            'slug' => 'existing-homepage-builder-test',
            'design_key' => 'index',
            'status' => 'draft',
        ]);
        $homepageSection = $homepage->sections()->create([
            'instance_key' => (string) Str::uuid(),
            'section_key' => 'hero.index',
            'section_type' => 'hero',
            'source_design' => 'index',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => [],
        ]);

        $page = Page::create([
            'title' => 'Services',
            'slug' => 'services-builder-test',
            'status' => 'draft',
        ]);
        $builder = app(PageBuilderService::class);
        $first = $builder->add($page, 'hero.index');
        $second = $builder->duplicate($page, $first);
        $builder->reorder($page, [$second->id, $first->id]);

        $this->assertSame([$second->id, $first->id], $page->fresh()->sections->pluck('id')->all());
        $this->assertSame($homepageSection->id, $homepage->fresh()->sections->first()->id);
        $this->assertCount(2, $page->fresh()->sections);
    }

    public function test_testimonial_widget_query_honours_selected_records_limit_and_sort_direction(): void
    {
        $older = Testimonial::create([
            'name' => 'Zara',
            'quote' => 'Excellent service.',
            'sort_order' => 2,
            'rating' => 5,
            'is_active' => true,
            'status' => 'published',
        ]);
        $newer = Testimonial::create([
            'name' => 'Aarav',
            'quote' => 'Very helpful team.',
            'sort_order' => 1,
            'rating' => 5,
            'is_active' => true,
            'status' => 'published',
        ]);

        $section = new HomepageSection([
            'section_key' => 'testimonials.home-2',
            'section_type' => 'testimonial-slider',
            'data_source' => [
                'selected_ids' => [$older->id, $newer->id],
                'limit' => 1,
                'sort_by' => 'name',
                'sort_direction' => 'asc',
            ],
        ]);

        $resolved = app(SectionDataResolver::class)->resolve($section, ['items_limit' => 4]);

        $this->assertCount(1, $resolved['testimonials']);
        $this->assertSame($newer->id, $resolved['testimonials']->first()->id);
    }

    public function test_page_widget_can_switch_between_original_piller_designs_in_the_same_family(): void
    {
        $page = Page::create([
            'title' => 'About design switch',
            'slug' => 'about-design-switch-test',
            'status' => 'draft',
        ]);

        $builder = app(PageBuilderService::class);
        $section = $builder->add($page, 'about.home-4');
        $builder->save($page, $section, [
            'section_key' => 'about.home-7',
            'config' => ['heading' => 'Preserved editorial content'],
            'data_source' => [],
        ]);

        $saved = $section->fresh();

        $this->assertSame('about.home-7', $saved->section_key);
        $this->assertSame('home-7', $saved->source_design);
        $this->assertSame('Preserved editorial content', $saved->config['heading']);
        $this->assertSame('about-composition', $saved->section_type);
    }

    public function test_published_cms_page_renders_its_registered_widget_on_the_frontend(): void
    {
        $page = Page::create([
            'title' => 'Builder render test',
            'slug' => 'builder-render-test',
            'status' => 'draft',
        ]);

        app(PageBuilderService::class)->add($page, 'hero.index');
        Banner::create([
            'title' => 'Database hero title',
            'subtitle' => 'Database hero subtitle',
            'description' => 'A banner loaded from the database.',
            'button_text' => 'Explore listings',
            'button_url' => '/properties',
            'placement' => 'home',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $page->update(['status' => 'published']);

        $this->get('/pages/builder-render-test')
            ->assertOk()
            ->assertSee('hero-1', false)
            ->assertSee('Database hero title')
            ->assertSee('Database hero subtitle');
    }
}
