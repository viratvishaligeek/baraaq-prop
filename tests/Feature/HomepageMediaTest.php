<?php

namespace Tests\Feature;

use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\ReusableSection;
use App\Services\Homepage\SectionMediaResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomepageMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        config()->set('cms-sections.sections.media-test', [
            'fields' => [
                'background_image' => ['type' => 'image'],
            ],
        ]);
    }

    public function test_section_media_id_resolves_to_the_linked_media_record_url(): void
    {
        $media = $this->createMedia('homepage/hero.png');
        $section = $this->createSection([
            'background_image' => (string) $media->id,
        ]);

        $section->mediaAttachments()->create([
            'field_key' => 'background_image',
            'media_id' => $media->id,
        ]);

        $rendered = app(SectionMediaResolver::class)->forRendering($section, $section->config);

        $this->assertSame($media->url, $rendered['background_image']);
        $this->assertSame($media->alt_text, $rendered['background_image_alt']);
        $this->assertSame($media->original_name, $rendered['background_image_media_name']);
    }

    public function test_reusable_section_media_relationship_is_used_when_instance_has_no_local_override(): void
    {
        $media = $this->createMedia('homepage/shared-background.png');
        $homepage = $this->createHomepage();
        $reusable = ReusableSection::create([
            'name' => 'Shared hero background',
            'slug' => 'shared-hero-background',
            'section_type' => 'hero',
            'section_key' => 'media-test',
            'source_design' => 'home-3',
            'config' => ['background_image' => (string) $media->id],
            'status' => 'published',
        ]);
        $reusable->mediaAttachments()->create([
            'field_key' => 'background_image',
            'media_id' => $media->id,
        ]);

        $section = HomepageSection::create([
            'homepage_id' => $homepage->id,
            'reusable_section_id' => $reusable->id,
            'instance_key' => (string) Str::uuid(),
            'section_key' => 'media-test',
            'section_type' => 'hero',
            'source_design' => 'home-3',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => [],
        ]);
        $section->setRelation('reusableSection', $reusable->load('mediaAttachments.media'));

        $rendered = app(SectionMediaResolver::class)->forRendering($section, $reusable->config);

        $this->assertSame($media->url, $rendered['background_image']);
    }

    public function test_deleted_media_does_not_render_a_stale_numeric_url(): void
    {
        $media = $this->createMedia('homepage/deleted-image.png');
        $section = $this->createSection([
            'background_image' => (string) $media->id,
        ]);

        $media->delete();

        $rendered = app(SectionMediaResolver::class)->forRendering($section, $section->config);

        $this->assertNull($rendered['background_image']);
    }

    public function test_media_foreign_key_cascades_attachment_rows_on_record_deletion(): void
    {
        $media = $this->createMedia('homepage/attached-image.png');
        $section = $this->createSection([
            'background_image' => (string) $media->id,
        ]);
        $section->mediaAttachments()->create([
            'field_key' => 'background_image',
            'media_id' => $media->id,
        ]);

        $media->delete();

        $this->assertDatabaseMissing('media_attachments', [
            'mediable_type' => HomepageSection::class,
            'mediable_id' => $section->id,
            'field_key' => 'background_image',
        ]);
    }

    private function createMedia(string $path): Media
    {
        return Media::create([
            'disk' => 'public',
            'path' => $path,
            'original_name' => basename($path),
            'mime_type' => 'image/png',
            'size' => 1024,
            'alt_text' => 'Homepage test image',
        ]);
    }

    private function createHomepage(): Homepage
    {
        return Homepage::create([
            'name' => 'Media test homepage',
            'slug' => 'media-test-homepage',
            'design_key' => 'home-3',
            'status' => 'draft',
            'is_default' => false,
        ]);
    }

    private function createSection(array $config): HomepageSection
    {
        $homepage = $this->createHomepage();

        return HomepageSection::create([
            'homepage_id' => $homepage->id,
            'instance_key' => (string) Str::uuid(),
            'section_key' => 'media-test',
            'section_type' => 'hero',
            'source_design' => 'home-3',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => $config,
        ]);
    }
}
