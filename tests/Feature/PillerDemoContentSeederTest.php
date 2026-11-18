<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Enquiry;
use App\Models\Homepage;
use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PillerDemoContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_realistic_cms_content_and_selects_dynamic_home_nine(): void
    {
        Storage::fake('public');

        $this->seed();

        $homepage = Homepage::query()->where('slug', 'home-9')->firstOrFail();
        $expectedSections = array_merge(
            ['hero.home-9'],
            config('cms-homepage-presets.home-9', [])
        );

        $this->assertTrue($homepage->is_default);
        $this->assertTrue($homepage->is_composition_complete);
        $this->assertSame(
            $expectedSections,
            $homepage->sections()->orderBy('sort_order')->pluck('section_key')->all()
        );

        $this->assertGreaterThanOrEqual(12, Property::published()->count());
        $this->assertGreaterThanOrEqual(8, Post::published()->count());
        $this->assertGreaterThanOrEqual(8, Service::published()->count());
        $this->assertGreaterThanOrEqual(6, TeamMember::query()->where('status', 'published')->count());
        $this->assertGreaterThanOrEqual(6, Testimonial::query()->where('status', 'published')->count());
        $this->assertGreaterThanOrEqual(8, Comment::query()->count());
        $this->assertGreaterThanOrEqual(6, Enquiry::query()->count());
        $this->assertGreaterThanOrEqual(10, NewsletterSubscriber::query()->count());
        $this->assertGreaterThan(0, Media::query()->count());
        $this->assertGreaterThan(0, $homepage->sections()->whereHas('mediaAttachments')->count());

        $this->get('/')
            ->assertOk()
            ->assertViewIs('frontend.home-builder')
            ->assertSee('Discover')
            ->assertSee('Most Suitable')
            ->assertSee('Property')
            ->assertSee('Create Your Story')
            ->assertSee('Best Properties Sale')
            ->assertSee('Meet Our Pillar Agents')
            ->assertSee('What Clients Say About Pillar')
            ->assertSee('Helpful Real Estate Articles');
    }
}
