<?php

namespace Database\Seeders;

use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\User;
use App\Support\ThemeMediaImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BuraaqhHomepageSeeder extends Seeder
{
    public function run(): void
    {
        $homepage = Homepage::query()->where('slug', 'home-9')->first();

        if (! $homepage) {
            throw new \RuntimeException('Home 9 is missing. Run HomepageTemplateSeeder before BuraaqhHomepageSeeder.');
        }

        $authorId = User::query()->orderBy('id')->value('id');
        $definitions = config('cms-sections.sections', []);
        $enabled = [
            'hero.home-9' => true,
            'about.home-9' => true,
            'statistics.home-9' => false,
            'services.home-9' => true,
            'properties.index' => true,
            'featured-gallery.home-9' => false,
            'home-details.home-9' => false,
            'team.home-2' => false,
            'testimonials.home-3' => false,
            'blog.home-2' => true,
        ];

        $overrides = [
            'hero.home-9' => [
                'eyebrow' => 'Real Estate in Bareilly',
                'heading_prefix' => 'Explore',
                'heading_highlight' => 'Property Options',
                'heading_suffix' => 'with Clarity',
                'description' => 'Explore residential and commercial property guidance in Bareilly, Uttar Pradesh. Share your requirements and verify every property detail before making a decision.',
                'primary_button_label' => 'Explore Properties',
                'primary_button_url' => '/properties',
                'secondary_button_label' => 'Send an Enquiry',
                'secondary_button_url' => '/contact',
            ],
            'about.home-9' => [
                'eyebrow' => 'About Buraaqh Properties',
                'heading' => 'A clearer starting point for property decisions in Bareilly',
                'mission_title' => 'Our Mission',
                'mission_text' => 'Help people organise their property requirements, ask informed questions and approach every decision with clear information and careful verification.',
                'vision_title' => 'Our Vision',
                'vision_text' => 'Make residential and commercial property information easier to understand, compare and discuss for people exploring options in Bareilly.',
            ],
            'services.home-9' => [
                'eyebrow' => 'Property Guidance',
                'heading' => 'Understand the support you may need',
                'description' => 'Service outlines are being reviewed by the business. Each offering will be published only after its availability, scope, responsible personnel and terms have been confirmed.',
                'empty_state_text' => 'Service information will appear here after the available offerings have been confirmed and approved for publication.',
            ],
            'properties.index' => [
                'eyebrow' => 'Property Search',
                'heading' => 'Residential and Commercial Property',
                'description' => 'Browse verified property information when it is available. No sample inventory or unconfirmed prices are published as genuine listings.',
                'empty_state_text' => 'There are no verified property listings published at the moment. Share your requirements and ask us to confirm current availability.',
            ],
            'testimonials.home-3' => [
                'trust_brand' => 'Client feedback',
                'trust_score_prefix' => 'Feedback from',
                'reviews_suffix' => 'verified reviews',
                'eyebrow' => 'Client Feedback',
                'heading' => 'Experiences will be shared here after approval',
                'avatar_group_image' => '',
            ],
            'blog.home-2' => [
                'eyebrow' => 'Property Knowledge',
                'heading' => 'Guides for better property decisions',
                'description' => 'Read practical guidance on buying, documentation, locality checks and residential and commercial property decisions.',
                'empty_state_text' => 'New property guides will appear here when they are published.',
            ],
        ];

        $imageMap = [
            'hero.home-9' => [
                'background_image' => [
                    'path' => 'theme-assets/img/hero/hero-10-2.jpg',
                    'alt' => 'Illustrative modern building exterior used as a decorative homepage background',
                ],
                'foreground_image' => [
                    'path' => 'theme-assets/img/hero/hero-9-right.png',
                    'alt' => 'Illustrative detached home exterior; not a Buraaqh property listing',
                ],
            ],
            'about.home-9' => [
                'image_primary' => [
                    'path' => 'theme-assets/img/why/why-choose-left-1.jpg',
                    'alt' => 'Illustrative contemporary residential architecture',
                ],
                'image_secondary' => [
                    'path' => 'theme-assets/img/hero/hero-8-left.jpg',
                    'alt' => 'Illustrative modern residential homes',
                ],
                'image_tertiary' => [
                    'path' => 'theme-assets/img/contact/contact-img.jpg',
                    'alt' => 'Illustrative residential living space',
                ],
            ],
            'home-details.home-9' => [
                'image' => [
                    'path' => 'theme-assets/img/hero/hero-10-3.jpg',
                    'alt' => 'Illustrative modern home exterior; not a verified property listing',
                ],
            ],
        ];

        foreach (array_keys($enabled) as $sortOrder => $sectionKey) {
            $definition = $definitions[$sectionKey] ?? null;

            if (! is_array($definition)) {
                continue;
            }

            $config = array_merge($definition['defaults'] ?? [], $overrides[$sectionKey] ?? []);
            $imageFields = [];

            foreach (($definition['fields'] ?? []) as $field => $fieldDefinition) {
                if (($fieldDefinition['type'] ?? null) !== 'image') {
                    continue;
                }

                $imageFields[] = $field;

                if (isset($imageMap[$sectionKey][$field])) {
                    $image = $imageMap[$sectionKey][$field];
                    $media = app(ThemeMediaImporter::class)->import($image['path'], $image['alt'], $authorId);
                    $config[$field] = $media?->id;
                    $config[$field.'_alt'] = $image['alt'];
                    continue;
                }

                $value = $config[$field] ?? null;
                if (is_string($value) && str_starts_with($value, '/theme-assets/')) {
                    $relativePath = ltrim($value, '/');
                    if (! File::isFile(public_path($relativePath))) {
                        $config[$field] = null;
                    }
                }
            }

            $section = HomepageSection::query()->firstOrCreate(
                ['homepage_id' => $homepage->id, 'section_key' => $sectionKey],
                [
                    'label' => $definition['label'] ?? $sectionKey,
                    'section_type' => $definition['type'] ?? 'content',
                    'view' => $definition['view'] ?? null,
                    'config' => $config,
                    'data_source' => [],
                    'sort_order' => $sortOrder + 1,
                    'is_enabled' => $enabled[$sectionKey],
                ]
            );

            // Attach defaults only when the field has no relationship yet. An
            // administrator's selected image must never be replaced by a rerun.
            $persistedConfig = is_array($section->config) ? $section->config : [];

            foreach ($imageFields as $field) {
                $mediaId = $persistedConfig[$field] ?? null;

                if ((! is_string($mediaId) && ! is_int($mediaId)) || ! ctype_digit((string) $mediaId)) {
                    continue;
                }

                if (! Media::query()->whereKey((int) $mediaId)->exists()) {
                    continue;
                }

                $section->mediaAttachments()->firstOrCreate(
                    ['field_key' => $field],
                    ['media_id' => (int) $mediaId]
                );
            }
        }
    }
}
