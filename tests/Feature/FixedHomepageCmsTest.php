<?php
namespace Tests\Feature;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
class FixedHomepageCmsTest extends TestCase
{
    use RefreshDatabase;
    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());
        return $user;
    }
    private function homepage(): Homepage
    {
        return Homepage::create(['name' => 'Home 9 — Fixed homepage', 'slug' => 'home-9', 'design_key' => 'home-9', 'status' => 'published', 'is_default' => true, 'is_composition_complete' => true, 'published_at' => now()]);
    }
    private function heroSection(Homepage $homepage): HomepageSection
    {
        $definition = config('cms-sections.sections.hero.home-9');
        return $homepage->sections()->create([
            'instance_key' => (string) Str::uuid(), 'section_key' => 'hero.home-9',
            'section_type' => $definition['type'], 'source_design' => 'home-9',
            'sort_order' => 1, 'is_enabled' => true,
            'config' => array_merge($definition['defaults'] ?? [], [
                'heading_prefix' => 'A fixed Home Nine heading', 'heading_highlight' => 'Made dynamic',
                'heading_suffix' => 'Today', 'description' => 'Managed from the homepage CMS.',
            ]),
            'data_source' => [],
        ]);
    }
    public function test_root_url_always_renders_the_database_backed_home_nine_composition(): void
    {
        $homepage = $this->homepage();
        $this->heroSection($homepage);
        $this->get('/')->assertOk()->assertViewIs('frontend.homepage')
            ->assertSee('A fixed Home Nine heading')->assertSee('Made dynamic')->assertSee('Managed from the homepage CMS.');
    }
    public function test_homepage_admin_lists_fixed_home_nine_sections_and_saves_section_content(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $section = $this->heroSection($homepage);
        $this->actingAs($admin)->get(route('admin.homepages.index'))->assertOk()->assertSee('Homepage content')->assertSee('Hero — Home 9');
        $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
            'config' => ['heading_prefix' => 'Updated from admin'],
                'is_enabled' => '1',
        ])->assertRedirect(route('admin.homepages.index'));
        $this->assertSame('Updated from admin', $section->fresh()->config['heading_prefix']);
        $this->get('/')->assertOk()->assertSee('Updated from admin');
    }
    public function test_homepage_seo_fields_are_editable_from_the_homepage_menu(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $this->actingAs($admin)->patch(route('admin.homepages.update', $homepage), [
            'meta_title' => 'Baraaq Properties | Home',
            'meta_description' => 'Find a property with our local team.',
        ])->assertRedirect(route('admin.homepages.index'));
        $this->assertSame('Baraaq Properties | Home', $homepage->fresh()->meta_title);
        $this->assertSame('Find a property with our local team.', $homepage->fresh()->meta_description);
    }
    public function test_homepage_image_replacement_is_stored_in_media_library_and_attached_to_section(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $section = $this->heroSection($homepage);
        $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
            'config' => ['foreground_image' => UploadedFile::fake()->image('replacement.png')],
        ])->assertRedirect(route('admin.homepages.index'));
        $media = Media::query()->findOrFail((int) $section->fresh()->config['foreground_image']);
        $this->assertSame('replacement.png', $media->original_name);
        $this->assertDatabaseHas('media_attachments', [
            'mediable_type' => HomepageSection::class, 'mediable_id' => $section->id,
            'field_key' => 'foreground_image', 'media_id' => $media->id,
        ]);
        Storage::disk('public')->assertExists($media->path);
    }
    public function test_old_homepage_switching_and_builder_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('admin.page-builder.index'));
        $this->assertFalse(Route::has('admin.sections.index'));
        $this->assertFalse(Route::has('admin.widgets.index'));
        $this->assertFalse(Route::has('admin.pages.homepage'));
        $this->assertFalse(Route::has('admin.homepages.default'));
        $this->get('/home-2.html')->assertNotFound();
        $this->get('/home-9.html')->assertRedirect(route('home'));
    }
    public function test_admin_layout_loads_bootstrap_compatible_datatables_from_the_official_cdn(): void
    {
        $admin = $this->administrator();
        $this->homepage();
        $this->actingAs($admin)->get(route('admin.homepages.index'))->assertOk()
            ->assertSee('cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css')
            ->assertSee('cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js');
    }
}
