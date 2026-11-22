<?php
namespace Tests\Feature;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\Role;
use App\Models\Setting;
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
        return Homepage::create(['name' => 'Home 9 — Fixed homepage', 'slug' => 'home-9', 'status' => 'published', 'published_at' => now()]);
    }
    private function heroSection(Homepage $homepage): HomepageSection
    {
        $definition = config('cms-sections.sections.hero.home-9');
        return $homepage->sections()->create([
            'section_key' => 'hero.home-9',
            'section_type' => $definition['type'],
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
            ->assertSee('header-layout9')->assertDontSee('header-layout1')
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
    public function test_administrator_can_hide_a_fixed_homepage_section_without_removing_its_content(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $section = $this->heroSection($homepage);

        $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
            'config' => ['heading_prefix' => 'Retained while hidden'],
            'is_enabled' => '0',
        ])->assertRedirect(route('admin.homepages.index'));

        $this->assertFalse($section->fresh()->is_enabled);
        $this->assertSame('Retained while hidden', $section->fresh()->config['heading_prefix']);
        $this->get('/')->assertOk()->assertDontSee('Retained while hidden');
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

        $this->actingAs($admin)->patch(route('admin.homepages.update', $homepage), [
            'meta_title' => '',
            'meta_description' => '',
        ])->assertRedirect(route('admin.homepages.index'));
        $this->assertNull($homepage->fresh()->meta_title);
        $this->assertNull($homepage->fresh()->meta_description);
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
    public function test_header_contact_and_footer_content_are_editable_from_the_homepage_menu(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        $homepage = $this->homepage();

        $this->actingAs($admin)->patch(route('admin.homepages.update', $homepage), [
            'site_title' => 'Baraaq Property Group',
            'header_menu_location' => 'primary',
            'site_phone' => '+91 98765 43210',
            'site_email' => 'hello@baraaq.example',
            'site_address' => 'Meerut, Uttar Pradesh',
            'footer_about_heading' => 'About Baraaq',
            'footer_rights_text' => 'Copyright 2026 Baraaq.',
            'footer_map_embed_url' => 'https://maps.google.com/maps?q=Meerut',
            'logo' => UploadedFile::fake()->image('brand.png'),
            'footer_gallery_images' => [UploadedFile::fake()->image('gallery-one.png')],
        ])->assertRedirect(route('admin.homepages.index'));

        $this->assertSame('Baraaq Property Group', Setting::read('site_title'));
        $this->assertSame('primary', Setting::read('header_menu_location'));
        $this->assertSame('+91 98765 43210', Setting::read('site_phone'));
        $this->assertSame('hello@baraaq.example', Setting::read('site_email'));
        $this->assertSame('About Baraaq', Setting::read('footer_about_heading'));
        $this->assertSame('Copyright 2026 Baraaq.', Setting::read('footer_rights_text'));
        $this->assertNotEmpty(Setting::read('logo'));
        $this->assertCount(1, Setting::read('footer_gallery_images'));
    }

    public function test_homepage_header_button_rejects_unsafe_javascript_urls(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();

        $this->actingAs($admin)->patch(route('admin.homepages.update', $homepage), [
            'header_cta_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('header_cta_url');
    }

    public function test_old_homepage_switching_and_builder_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('admin.page-builder.index'));
        $this->assertFalse(Route::has('admin.sections.index'));
        $this->assertFalse(Route::has('admin.widgets.index'));
        $this->assertFalse(Route::has('admin.pages.homepage'));
        $this->assertFalse(Route::has('admin.homepages.default'));
        $this->assertFalse(Route::has('theme.page'));
        $this->assertFalse(view()->exists('admin.homepages.builder'));
        $this->assertFalse(view()->exists('frontend.home'));
        $this->assertFalse(view()->exists('frontend.theme-page'));
        $this->get('/home-2.html')->assertNotFound();
        $this->get('/about.html')->assertNotFound();
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


    private function createFixedSection(Homepage $homepage, string $key, int $sortOrder = 1): HomepageSection
    {
        $definition = config('cms-sections.sections.'.$key);

        return $homepage->sections()->create([
            'section_key' => $key,
            'section_type' => $definition['type'],
            'sort_order' => $sortOrder,
            'is_enabled' => true,
            'config' => $definition['defaults'] ?? [],
            'data_source' => [],
        ]);
    }

    public function test_all_ten_fixed_homepage_sections_save_through_the_registered_admin_route(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();

        foreach (array_keys(config('cms-sections.sections', [])) as $index => $key) {
            $definition = config('cms-sections.sections.'.$key);
            $fieldKey = null;

            foreach ($definition['fields'] ?? [] as $candidateKey => $field) {
                if (in_array($field['type'] ?? 'text', ['text', 'textarea'], true)) {
                    $fieldKey = $candidateKey;
                    break;
                }
            }

            $this->assertNotNull($fieldKey, "Section [{$key}] must expose at least one editable text field.");
            $section = $this->createFixedSection($homepage, $key, $index + 1);
            $value = 'Saved section '.($index + 1);

            $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
                'config' => [$fieldKey => $value],
            ])->assertRedirect(route('admin.homepages.index'));

            $this->assertSame($value, $section->fresh()->config[$fieldKey], "Section [{$key}] did not persist [{$fieldKey}].");
        }
    }

    public function test_every_registered_homepage_image_field_can_be_uploaded_and_attached(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $checkedFields = 0;

        foreach (array_keys(config('cms-sections.sections', [])) as $index => $key) {
            $definition = config('cms-sections.sections.'.$key);
            $section = $this->createFixedSection($homepage, $key, $index + 1);

            foreach ($definition['fields'] ?? [] as $fieldKey => $field) {
                if (($field['type'] ?? null) !== 'image') {
                    continue;
                }

                $fileName = Str::slug($key.'-'.$fieldKey).'.png';

                $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
                    'config' => [$fieldKey => UploadedFile::fake()->image($fileName)],
                ])->assertRedirect(route('admin.homepages.index'));

                $mediaId = (int) ($section->fresh()->config[$fieldKey] ?? 0);
                $this->assertGreaterThan(0, $mediaId, "Image field [{$key}.{$fieldKey}] was not saved.");
                $media = Media::query()->findOrFail($mediaId);

                $this->assertDatabaseHas('media_attachments', [
                    'mediable_type' => HomepageSection::class,
                    'mediable_id' => $section->id,
                    'field_key' => $fieldKey,
                    'media_id' => $mediaId,
                ]);
                Storage::disk('public')->assertExists($media->path);
                $checkedFields++;
            }
        }

        $this->assertGreaterThan(0, $checkedFields, 'The fixed homepage registry must expose uploadable image fields.');
    }

    public function test_section_links_accept_relative_anchor_telephone_email_and_http_links(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $section = $this->heroSection($homepage);

        foreach (['/contact', '#services', 'tel:+911234567890', 'mailto:info@example.com', 'https://example.com'] as $url) {
            $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
                'config' => ['primary_button_url' => $url],
            ])->assertRedirect(route('admin.homepages.index'));

            $this->assertSame($url, $section->fresh()->config['primary_button_url']);
        }

        foreach (['javascript:alert(1)', '//example.com/path'] as $unsafeUrl) {
            $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
                'config' => ['primary_button_url' => $unsafeUrl],
            ])->assertSessionHasErrors('config.primary_button_url');
        }
    }

    public function test_featured_gallery_video_url_requires_an_absolute_http_url(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $section = $this->createFixedSection($homepage, 'featured-gallery.home-9', 1);

        foreach (['/contact', '#services', 'mailto:info@example.com', 'tel:+911234567890'] as $invalidUrl) {
            $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
                'config' => ['video_url' => $invalidUrl],
            ])->assertSessionHasErrors('config.video_url');
        }

        $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
            'config' => ['video_url' => 'https://www.youtube.com/watch?v=example'],
        ])->assertRedirect(route('admin.homepages.index'));

        $this->assertSame('https://www.youtube.com/watch?v=example', $section->fresh()->config['video_url']);
    }

    public function test_partial_legacy_section_config_uses_registered_defaults_in_frontend_and_admin(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $definition = config('cms-sections.sections.hero.home-9');

        $section = $homepage->sections()->create([
            'section_key' => 'hero.home-9',
            'section_type' => $definition['type'],
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => ['heading_prefix' => 'Partial saved config'],
            'data_source' => [],
        ]);

        $this->get('/')->assertOk()
            ->assertSee('Partial saved config')
            ->assertSee('Most Suitable')
            ->assertSee('Explore Properties');

        $this->actingAs($admin)->get(route('admin.homepages.index'))
            ->assertOk()
            ->assertSee('value="Most Suitable"', false)
            ->assertSee('inputmode="url"', false)
            ->assertSee('/contact, #services, tel:+911234567890, mailto:info@example.com or https://example.com', false);
    }

    public function test_rich_text_section_content_is_sanitized_before_it_is_saved(): void
    {
        $admin = $this->administrator();
        $homepage = $this->homepage();
        $section = $this->heroSection($homepage);

        $this->actingAs($admin)->patch(route('admin.homepages.sections.update', [$homepage, $section]), [
            'config' => [
                'description' => '<p>Safe copy</p><script>alert(1)</script><a href="javascript:alert(2)">Read more</a>',
            ],
        ])->assertRedirect(route('admin.homepages.index'));

        $saved = $section->fresh()->config['description'];
        $this->assertStringContainsString('Safe copy', $saved);
        $this->assertStringNotContainsString('<script', $saved);
        $this->assertStringNotContainsString('javascript:', $saved);
    }

}
