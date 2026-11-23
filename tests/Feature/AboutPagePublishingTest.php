<?php

namespace Tests\\Feature;

use App\\Models\\Page;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

class AboutPagePublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_route_renders_the_published_cms_page_with_its_own_canonical_url(): void
    {
        Page::create([
            'title' => 'About Buraaqh Properties',
            'slug' => 'about',
            'content' => '<p>Local property advisory and real estate guidance.</p>',
            'status' => 'published',
            'meta_title' => 'About Buraaqh Properties | Bareilly',
            'meta_description' => 'Learn about our property advisory services in Bareilly.',
        ]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('About Buraaqh Properties')
            ->assertSee('Local property advisory and real estate guidance.')
            ->assertSee('<link rel="canonical" href="'.route('about').'"', false)
            ->assertDontSee('/pages/about');
    }

    public function test_about_route_does_not_publish_a_draft_cms_page(): void
    {
        Page::create([
            'title' => 'Unpublished About Page',
            'slug' => 'about',
            'content' => 'Draft-only content.',
            'status' => 'draft',
        ]);

        $this->get(route('about'))->assertNotFound();
    }
}
