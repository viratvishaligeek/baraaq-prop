<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\HomepageTemplateSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_homepage_identity_navigation_and_footer_are_not_duplicated_in_general_settings(): void
    {
        $admin = $this->administrator();
        $this->seed(HomepageTemplateSeeder::class);

        $this->actingAs($admin)->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('Global SEO defaults')
            ->assertSee('name="favicon"', false)
            ->assertDontSee('name="site_title"', false)
            ->assertDontSee('name="header_cta_url"', false)
            ->assertDontSee('name="footer_menu_location"', false)
            ->assertDontSee('name="social_facebook"', false)
            ->assertDontSee('name="logo"', false);

        $this->actingAs($admin)->get(route('admin.homepages.index'))
            ->assertOk()
            ->assertSee('name="site_title"', false)
            ->assertSee('name="header_menu_location"', false)
            ->assertSee('name="header_cta_url"', false)
            ->assertSee('name="footer_menu_location"', false)
            ->assertSee('name="social_facebook"', false)
            ->assertSee('name="logo"', false);
    }

    public function test_general_settings_save_only_global_seo_appearance_and_behavior_options(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->from(route('admin.settings.edit'))->put(route('admin.settings.update'), [
            'sticky_header' => '0',
            'meta_title' => 'Default site SEO title',
            'meta_description' => 'Default site SEO description.',
            'meta_keywords' => 'property, real estate',
            'meta_robots' => 'noindex,nofollow',
            'robots_rules' => "User-agent: *\nAllow: /\nDisallow: /private",
            'primary_color' => '#123456',
            'secondary_color' => '#654321',
            'head_scripts' => '<script>window.siteHeadReady = true;</script>',
            'body_scripts' => '<script>window.siteBodyReady = true;</script>',
        ])->assertRedirect(route('admin.settings.edit'));

        $this->assertFalse(Setting::read('sticky_header', true));
        $this->assertSame('Default site SEO title', Setting::read('meta_title'));
        $this->assertSame('Default site SEO description.', Setting::read('meta_description'));
        $this->assertSame('noindex,nofollow', Setting::read('meta_robots'));
        $this->assertSame('#123456', Setting::read('primary_color'));
        $this->assertSame('#654321', Setting::read('secondary_color'));
        $this->assertSame("User-agent: *\nAllow: /\nDisallow: /private", Setting::read('robots_rules'));
        $this->assertSame('<script>window.siteHeadReady = true;</script>', Setting::read('head_scripts'));
        $this->assertSame('<script>window.siteBodyReady = true;</script>', Setting::read('body_scripts'));
    }

    public function test_favicon_and_default_social_image_uploads_are_registered_in_media_library(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();

        $this->actingAs($admin)->from(route('admin.settings.edit'))->put(route('admin.settings.update'), [
            'meta_robots' => 'index,follow',
            'favicon' => UploadedFile::fake()->image('favicon.png'),
            'default_og_image' => UploadedFile::fake()->image('default-og.png'),
        ])->assertRedirect(route('admin.settings.edit'));

        $faviconPath = Setting::read('favicon');
        $socialImagePath = Setting::read('default_og_image');

        $this->assertNotEmpty($faviconPath);
        $this->assertNotEmpty($socialImagePath);
        $this->assertDatabaseHas('media', ['path' => $faviconPath, 'original_name' => 'favicon.png']);
        $this->assertDatabaseHas('media', ['path' => $socialImagePath, 'original_name' => 'default-og.png']);
        Storage::disk('public')->assertExists($faviconPath);
        Storage::disk('public')->assertExists($socialImagePath);
        $this->assertCount(2, Media::query()->get());
    }
}
