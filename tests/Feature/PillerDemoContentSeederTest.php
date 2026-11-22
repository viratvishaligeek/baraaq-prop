<?php
namespace Tests\Feature;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Enquiry;
use App\Models\Homepage;
use App\Models\Media;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class PillerDemoContentSeederTest extends TestCase
{
    use RefreshDatabase;
    public function test_database_seeder_creates_home_nine_and_realistic_cms_content_without_legacy_builder_tables(): void
    {
        Storage::fake('public');
        $this->seed();
        $homepage = Homepage::query()->where('slug', 'home-9')->firstOrFail();
        $expected = ['hero.home-9', 'about.home-9', 'statistics.home-9', 'services.home-9', 'properties.index', 'featured-gallery.home-9', 'home-details.home-9', 'team.home-2', 'testimonials.home-3', 'blog.home-2'];
        $this->assertSame(1, Homepage::query()->count());
        $this->assertTrue($homepage->is_default);
        $this->assertTrue($homepage->is_composition_complete);
        $this->assertSame($expected, $homepage->sections()->orderBy('sort_order')->pluck('section_key')->all());
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
        $this->assertGreaterThan(0, Category::query()->count());
        $this->assertGreaterThan(0, Page::query()->count());
        $this->assertGreaterThan(0, Banner::query()->count());
        $this->assertGreaterThan(0, PropertyImage::query()->count());
        $this->assertGreaterThan(0, Menu::query()->count());
        $this->assertGreaterThan(0, MenuItem::query()->count());
        $this->assertGreaterThan(0, Role::query()->count());
        $this->assertGreaterThan(0, Setting::query()->count());
        $this->assertGreaterThan(0, DB::table('categorizables')->count());
        $this->assertGreaterThan(0, DB::table('page_views')->count());
        $this->assertGreaterThan(0, DB::table('activity_logs')->count());
        $this->assertFalse(Schema::hasTable('widgets'));
        $this->assertFalse(Schema::hasTable('widget_areas'));
        $this->assertFalse(Schema::hasTable('widget_area_items'));
        $this->assertFalse(Schema::hasTable('reusable_sections'));
        $this->assertFalse(Schema::hasColumn('pages', 'homepage_key'));
        $this->assertFalse(Schema::hasColumn('homepage_sections', 'page_id'));
        $this->assertFalse(Schema::hasColumn('homepage_sections', 'service_id'));
        $this->assertFalse(Schema::hasColumn('homepage_sections', 'reusable_section_id'));
        $this->get('/')->assertOk()->assertViewIs('frontend.homepage')
            ->assertSee('Discover')->assertSee('Most Suitable')->assertSee('Property')
            ->assertSee('Create Your Story')->assertSee('Best Properties Sale')
            ->assertSee('Meet Our Pillar Agents')->assertSee('What Clients Say About Pillar')
            ->assertSee('Helpful Real Estate Articles')->assertSee('header-layout9')
            ->assertSee('Pillar Location')->assertSee('Pillar moments');
    }
}
