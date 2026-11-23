<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use App\Models\Banner;
use App\Models\Comment;
use App\Models\Enquiry;
use App\Models\Media;
use App\Models\GalleryItem;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\MediaAttachment;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\ServiceFeature;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\NeighborhoodGuide;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use Database\Seeders\BuraaqhContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuraaqhContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_buraaqh_seeder_is_repeatable_and_does_not_publish_fabricated_property_inventory(): void
    {
        Storage::fake('public');

        config(['app.env' => 'production']);
        $this->seed();

        $this->assertSame(1, Homepage::query()->count());
        $this->assertSame(0, Property::published()->count());
        $this->assertSame(0, Service::published()->count());
        $this->assertSame(0, GalleryItem::query()->where('status', 'published')->count());
        $this->assertSame(0, Testimonial::query()->count());
        $this->assertSame(0, TeamMember::query()->count());
        $this->assertSame(10, HomepageSection::query()->count());
        $this->assertSame(6, MediaAttachment::query()->count());
        $this->assertSame(7, NeighborhoodGuide::query()->count());
        $this->assertSame(18, FaqItem::query()->count());
        $this->assertSame(7, Post::query()->count());
        foreach (Post::query()->get() as $post) {
            $this->assertGreaterThan(700, str_word_count(strip_tags($post->content)), $post->slug.' should be long-form content.');
            $this->assertGreaterThan(5, preg_match_all('/<h2\\b/i', $post->content), $post->slug.' should have a useful heading structure.');
            $this->assertStringContainsString('Conclusion', $post->content);
            $this->assertNotEmpty($post->meta_title);
            $this->assertLessThanOrEqual(60, mb_strlen($post->meta_title), $post->slug.' SEO title should fit the configured limit.');
            $this->assertNotEmpty($post->meta_description);
            $this->assertLessThanOrEqual(155, mb_strlen($post->meta_description), $post->slug.' SEO description should fit the configured limit.');
            $this->assertMatchesRegularExpression('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $post->slug);
            preg_match_all('/<p>(.*?)<\\/p>/si', $post->content, $paragraphMatches);
            $paragraphs = array_map(
                fn (string $paragraph): string => trim(preg_replace('/\\s+/', ' ', strip_tags($paragraph))),
                $paragraphMatches[1]
            );
            $this->assertCount(count(array_unique($paragraphs)), $paragraphs, $post->slug.' should not pad its word count with repeated paragraphs.');
            $this->assertGreaterThan(0, $post->tags()->count(), $post->slug.' should have tags.');
            $this->assertNull($post->featured_image, 'No unreviewed editorial image should be assigned.');
        }

        $this->assertSame(6, Service::query()->count());
        $this->assertSame(24, ServiceFeature::query()->count());
        foreach (Service::query()->get() as $service) {
            $this->assertSame('draft', $service->status);
            $this->assertStringContainsString('Frequently asked questions', $service->content);
            $this->assertNull($service->featured_image);
        }

        foreach (NeighborhoodGuide::query()->get() as $guide) {
            $this->assertGreaterThan(400, str_word_count(strip_tags($guide->content)), $guide->slug.' should contain a locality-specific, practical guide.');
            $this->assertGreaterThan(3, preg_match_all('/<h2\\b/i', $guide->content));
            $this->assertNull($guide->hero_image, 'No unverified locality photograph should be assigned.');
        }

        foreach (FaqItem::query()->get() as $faq) {
            $this->assertGreaterThan(50, str_word_count(strip_tags($faq->answer)));
        }

        $this->assertSame(6, Page::query()->count());
        $this->assertSame(10, Tag::query()->count());
        $this->assertSame(6, Media::query()->count());
        $this->assertSame(13, Category::query()->count());
        $this->assertSame(4, Menu::query()->count());
        $this->assertSame(27, MenuItem::query()->count());
        $this->assertSame(0, PropertyImage::query()->count());
        $this->assertSame(0, ServiceImage::query()->count());
        $this->assertSame(0, Enquiry::query()->count());
        $this->assertSame(0, NewsletterSubscriber::query()->count());
        $this->assertSame(0, Comment::query()->count());
        $this->assertSame(0, Banner::query()->count());

        foreach (Media::query()->get() as $media) {
            $this->assertNotEmpty($media->alt_text);
            $this->assertTrue(Storage::disk('public')->exists($media->path), 'Seeded media must exist on the public disk.');
        }
        $this->assertSame(6, Media::query()->distinct('path')->count('path'));

        $testimonialSection = HomepageSection::query()->where('section_key', 'testimonials.home-3')->firstOrFail();
        $this->assertNull(data_get($testimonialSection->config, 'avatar_group_image'));
        $this->assertSame([], Setting::read('footer_gallery_images', []));
        $this->assertSame('', Setting::read('logo'));
        $this->assertSame('', Setting::read('favicon'));
        $this->assertSame('', Setting::read('default_og_image'));
        $this->assertSame('Buraaqh Properties', Setting::read('site_title'));
        $this->assertSame('Bareilly', Setting::read('service_area_city'));
        $this->assertSame('', Setting::read('site_phone'));
        $this->assertSame('', Setting::read('site_email'));
        $this->assertSame('', Setting::read('site_address'));

        $counts = [
            'homepages' => Homepage::query()->count(),
            'posts' => Post::query()->count(),
            'services' => Service::query()->count(),
            'pages' => Page::query()->count(),
            'faqs' => FaqItem::query()->count(),
            'gallery' => GalleryItem::query()->count(),
            'neighborhood_guides' => NeighborhoodGuide::query()->count(),
            'categories' => Category::query()->count(),
            'tags' => Tag::query()->count(),
            'media' => Media::query()->count(),
            'media_attachments' => MediaAttachment::query()->count(),
            'menus' => Menu::query()->count(),
            'menu_items' => MenuItem::query()->count(),
            'service_features' => ServiceFeature::query()->count(),
        ];

        $this->seed();

        $this->assertSame($counts, [
            'homepages' => Homepage::query()->count(),
            'posts' => Post::query()->count(),
            'services' => Service::query()->count(),
            'pages' => Page::query()->count(),
            'faqs' => FaqItem::query()->count(),
            'gallery' => GalleryItem::query()->count(),
            'neighborhood_guides' => NeighborhoodGuide::query()->count(),
            'categories' => Category::query()->count(),
            'tags' => Tag::query()->count(),
            'media' => Media::query()->count(),
            'media_attachments' => MediaAttachment::query()->count(),
            'menus' => Menu::query()->count(),
            'menu_items' => MenuItem::query()->count(),
            'service_features' => ServiceFeature::query()->count(),
        ]);
        $this->assertSame(0, Property::published()->count());

        $this->get('/')
            ->assertOk()
            ->assertSee('Buraaqh Properties')
            ->assertDontSee('offices worldwide')
            ->assertDontSee('5,600 sq ft')
            ->assertDontSee('PILLAR')
            ->assertDontSee('logo-white.svg');
    }


    public function test_legacy_demo_records_are_retired_without_deleting_or_rewriting_owner_data(): void
    {
        Storage::fake('public');

        $property = Property::query()->create([
            'title' => 'Legacy sample property',
            'slug' => 'the-oakwood-residences',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $service = Service::query()->create([
            'title' => 'Owner-reviewed valuation service',
            'slug' => 'property-valuation',
            'content' => '<p>Owner-reviewed service wording.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $serviceImage = ServiceImage::query()->create([
            'service_id' => $service->id,
            'path' => 'services/inside/seed-property-valuation.jpg',
            'alt_text' => 'Template photo',
            'sort_order' => 1,
        ]);
        $media = Media::query()->create([
            'disk' => 'public',
            'path' => $serviceImage->path,
            'original_name' => 'template-photo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 0,
            'alt_text' => 'Template photo',
        ]);
        $gallery = GalleryItem::query()->create([
            'title' => 'Legacy template gallery image',
            'slug' => 'bareilly-property-gallery-1',
            'image' => 'gallery/buraaqh-reference-1.jpg',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $banner = Banner::query()->create([
            'title' => 'Discover a place that feels like home',
            'placement' => 'home',
            'is_active' => true,
        ]);
        $legacyPage = Page::query()->create([
            'title' => 'Legacy buy page',
            'slug' => 'buy-with-pillar',
            'content' => '<p>Owner-edited legacy page copy.</p>',
            'status' => 'published',
        ]);
        $primaryMenu = Menu::query()->create(['name' => 'Legacy primary', 'location' => 'primary']);
        $legacyBuyMenuItem = MenuItem::query()->create([
            'menu_id' => $primaryMenu->id,
            'label' => 'Buy with Pillar',
            'url' => '/pages/buy-with-pillar',
            'link_type' => 'custom',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $legacySellMenuItem = MenuItem::query()->create([
            'menu_id' => $primaryMenu->id,
            'label' => 'Sell with Pillar',
            'url' => '/sell-with-pillar',
            'link_type' => 'custom',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        $legacyPost = Post::query()->create([
            'title' => 'Legacy moving article',
            'slug' => 'planning-a-delhi-ncr-move',
            'content' => '<p>Owner-edited legacy article copy.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $shortExcerpt = 'A short legacy article created by the previous content seeder.';
        $shortLegacyPost = Post::query()->create([
            'title' => 'Legacy short article',
            'slug' => 'first-time-homebuyer-checklist',
            'excerpt' => $shortExcerpt,
            'content' => '<p>'.$shortExcerpt.'</p><p>Property decisions are easier when the information is clear and the next step is practical. Start by understanding your priorities, compare the available options and take time to review the details that matter to your household or investment plan.</p><p>Property conditions, documents and transaction requirements vary. Treat this article as general information only, verify current records and seek independent professional advice before making a decision.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->seed(BuraaqhContentSeeder::class);

        $this->assertSame('draft', $property->fresh()->status);
        $this->assertSame('draft', $service->fresh()->status);
        $this->assertSame('Owner-reviewed valuation service', $service->fresh()->title);
        $this->assertSame('<p>Owner-reviewed service wording.</p>', $service->fresh()->content);
        $this->assertDatabaseHas('service_images', ['id' => $serviceImage->id, 'path' => $serviceImage->path]);
        $this->assertDatabaseHas('media', ['id' => $media->id, 'path' => $media->path]);
        $this->assertSame('draft', $gallery->fresh()->status);
        $this->assertFalse((bool) $banner->fresh()->is_active);
        $this->assertSame('draft', $legacyPage->fresh()->status);
        $this->assertSame('<p>Owner-edited legacy page copy.</p>', $legacyPage->fresh()->content);
        $this->assertSame('Buy with Buraaqh Properties', $legacyBuyMenuItem->fresh()->label);
        $this->assertSame('/pages/buy-with-buraaqh', $legacyBuyMenuItem->fresh()->url);
        $this->assertSame('Sell with Buraaqh Properties', $legacySellMenuItem->fresh()->label);
        $this->assertSame('/pages/sell-with-buraaqh', $legacySellMenuItem->fresh()->url);
        $this->assertSame('published', $legacyPost->fresh()->status);
        $this->assertSame('<p>Owner-edited legacy article copy.</p>', $legacyPost->fresh()->content);
        $this->assertSame('draft', $shortLegacyPost->fresh()->status);
        $this->assertStringContainsString($shortExcerpt, $shortLegacyPost->fresh()->content);
        $this->assertDatabaseHas('pages', ['slug' => 'buy-with-buraaqh', 'status' => 'published']);
        $this->assertDatabaseHas('posts', ['slug' => 'complete-guide-buying-residential-property-bareilly', 'status' => 'published']);
    }

    public function test_homepage_blog_cards_do_not_reuse_unverified_template_photography(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Article image pending')
            ->assertDontSee('theme-assets/img/blog/blog_1_1.jpg');
    }

    public function test_optional_admin_bootstrap_assigns_seeded_content_to_the_administrator(): void
    {
        config([
            'cms.admin.name' => 'Buraaqh Site Administrator',
            'cms.admin.email' => 'admin@example.test',
            'cms.admin.password' => 'test-only-password',
        ]);

        $this->seed();

        $admin = User::query()->where('email', 'admin@example.test')->firstOrFail();

        $this->assertTrue($admin->roles()->where('slug', 'administrator')->exists());
        $this->assertSame($admin->id, Page::query()->where('slug', 'about')->value('author_id'));
        $this->assertSame($admin->id, Post::query()->where('slug', 'complete-guide-buying-residential-property-bareilly')->value('author_id'));
    }

    public function test_known_legacy_settings_are_replaced_without_overwriting_custom_values(): void
    {
        $legacySettings = [
            'site_title' => ['Pillar Real Estate', 'general', 'string'],
            'business_name' => ['Pillar Realty', 'business', 'string'],
            'site_email' => ['hello@pillarrealty.example', 'contact', 'string'],
            'site_phone' => ['+91 121 456 7800', 'contact', 'string'],
            'whatsapp_number' => ['919876543210', 'contact', 'string'],
            'site_address' => ['Pillar Realty, Meerut', 'contact', 'string'],
            'footer_map_embed_url' => ['https://maps.google.com/?q=Meerut', 'footer', 'string'],
            'social_facebook' => ['https://www.facebook.com/', 'social', 'string'],
            'footer_copyright' => ['Pillar Real Estate', 'footer', 'string'],
            'footer_featured_heading' => ['Featured Houses', 'footer', 'string'],
            'footer_location_heading' => ['Pillar Location', 'footer', 'string'],
            'site_tagline' => ['Pillar Real Estate in Meerut', 'general', 'string'],
            'footer_gallery_images' => [json_encode(['piller-demo/demo.jpg']), 'footer', 'json'],
            'logo' => ['theme-assets/img/logo-white.svg', 'branding', 'image'],
            'favicon' => ['theme-assets/img/favicons/favicon-32x32.png', 'branding', 'image'],
            'default_og_image' => ['piller-demo/og-image.jpg', 'seo', 'image'],
            'custom_notice' => ['Administrator-approved wording', 'custom', 'string'],
        ];

        foreach ($legacySettings as $key => [$value, $group, $type]) {
            Setting::query()->create(compact('key', 'group', 'value', 'type'));
        }

        $this->seed();

        $this->assertSame('Buraaqh Properties', Setting::read('site_title'));
        $this->assertSame('Buraaqh Properties', Setting::read('business_name'));
        $this->assertSame('', Setting::read('site_email'));
        $this->assertSame('', Setting::read('site_phone'));
        $this->assertSame('', Setting::read('whatsapp_number'));
        $this->assertSame('', Setting::read('site_address'));
        $this->assertSame('', Setting::read('footer_map_embed_url'));
        $this->assertSame('', Setting::read('social_facebook'));
        $this->assertSame('Buraaqh Properties', Setting::read('footer_copyright'));
        $this->assertSame('Explore Property Types', Setting::read('footer_featured_heading'));
        $this->assertSame('Our Service Area', Setting::read('footer_location_heading'));
        $this->assertSame('Clear information for better property decisions in Bareilly.', Setting::read('site_tagline'));
        $this->assertSame([], Setting::read('footer_gallery_images', []));
        $this->assertSame('Administrator-approved wording', Setting::read('custom_notice'));
    }

    public function test_seeder_preserves_owner_managed_content_when_rerun(): void
    {
        Storage::fake('public');
        $this->seed();

        $inventory = fn (): array => [
            'faqs' => FaqItem::query()->count(),
            'faq_questions' => FaqItem::query()->distinct('question')->count('question'),
            'posts' => Post::query()->count(),
            'post_slugs' => Post::query()->distinct('slug')->count('slug'),
            'categories' => Category::query()->count(),
            'category_slugs' => Category::query()->distinct('slug')->count('slug'),
            'services' => Service::query()->count(),
            'service_slugs' => Service::query()->distinct('slug')->count('slug'),
            'menus' => Menu::query()->count(),
            'menu_locations' => Menu::query()->distinct('location')->count('location'),
            'menu_items' => MenuItem::query()->count(),
            'menu_item_keys' => MenuItem::query()->get(['menu_id', 'label'])
                ->map(fn (MenuItem $item): string => $item->menu_id.'|'.$item->label)
                ->unique()
                ->count(),
            'media' => Media::query()->count(),
            'media_attachments' => MediaAttachment::query()->count(),
            'media_attachment_keys' => MediaAttachment::query()->get(['mediable_type', 'mediable_id', 'field_key'])
                ->map(fn (MediaAttachment $attachment): string => $attachment->mediable_type.'|'.$attachment->mediable_id.'|'.$attachment->field_key)
                ->unique()
                ->count(),
        ];
        $faq = FaqItem::query()->firstOrFail();
        DB::table('faq_items')->where('id', $faq->id)->update([
            'answer' => 'Owner-approved FAQ wording written directly through the database.',
        ]);
        $guide = NeighborhoodGuide::query()->firstOrFail();
        $guide->update(['content' => '<p>Owner-approved neighborhood guide copy.</p>']);

        $page = Page::query()->create([
            'title' => 'Owner-managed page',
            'slug' => 'owner-managed-page',
            'content' => '<p>Pillar is a proper noun in this owner-managed content and must not be rewritten.</p>',
            'template' => 'default',
            'status' => 'draft',
        ]);

        Setting::query()->where('key', 'site_title')->update(['value' => 'Owner-approved brand']);
        Setting::query()->where('key', 'site_tagline')->update(['value' => '']);
        $hero = HomepageSection::query()->where('section_key', 'hero.home-9')->firstOrFail();
        $customMediaPath = 'owner/approved-hero.jpg';
        Storage::disk('public')->put($customMediaPath, 'owner-selected-image');
        $customMedia = Media::query()->create([
            'disk' => 'public',
            'path' => $customMediaPath,
            'original_name' => 'approved-hero.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 20,
            'alt_text' => 'Administrator-selected hero image',
        ]);
        $heroConfig = $hero->config;
        $heroConfig['heading_prefix'] = 'Administrator headline';
        $heroConfig['foreground_image'] = $customMedia->id;
        $hero->forceFill(['config' => $heroConfig, 'is_enabled' => false])->save();
        $hero->mediaAttachments()->where('field_key', 'foreground_image')->update(['media_id' => $customMedia->id]);

        $contactItem = MenuItem::query()->where('label', 'Contact')->firstOrFail();
        $contactItem->update(['url' => '/administrator-contact']);
        $service = Service::query()->where('slug', 'property-search-shortlisting')->firstOrFail();
        $removedFeature = $service->features()->firstOrFail();
        $removedFeature->delete();
        $remainingFeatureCount = ServiceFeature::query()->where('service_id', $service->id)->count();
        $initialInventory = $inventory();

        $this->seed();

        $this->assertSame(
            '<p>Pillar is a proper noun in this owner-managed content and must not be rewritten.</p>',
            $page->fresh()->content
        );
        $this->assertSame('draft', $page->fresh()->status);
        $this->assertSame('Owner-approved FAQ wording written directly through the database.', $faq->fresh()->answer);
        $this->assertSame('<p>Owner-approved neighborhood guide copy.</p>', $guide->fresh()->content);
        $this->assertSame('Owner-approved brand', Setting::query()->where('key', 'site_title')->value('value'));
        $this->assertSame('', Setting::query()->where('key', 'site_tagline')->value('value'));
        $this->assertSame('Administrator headline', data_get($hero->fresh()->config, 'heading_prefix'));
        $this->assertSame($customMedia->id, data_get($hero->fresh()->config, 'foreground_image'));
        $this->assertFalse((bool) $hero->fresh()->is_enabled);
        $this->assertSame($customMedia->id, $hero->fresh()->mediaAttachments()->where('field_key', 'foreground_image')->value('media_id'));
        $this->assertSame('/administrator-contact', $contactItem->fresh()->url);
        $this->assertSame($remainingFeatureCount, ServiceFeature::query()->where('service_id', $service->id)->count());
        $this->assertSame($initialInventory, $inventory(), 'A second complete seed must not duplicate content, navigation or media relations.');
        $this->assertSame(6, MediaAttachment::query()->count());
        $this->assertSame(
            6,
            MediaAttachment::query()->whereHasMorph('mediable', [HomepageSection::class])->count(),
            'All six homepage media attachments must remain connected to their original section records.'
        );
        $this->assertDatabaseHas('media_attachments', [
            'mediable_type' => HomepageSection::class,
            'mediable_id' => $hero->id,
            'field_key' => 'foreground_image',
            'media_id' => $customMedia->id,
        ]);
    }
}
