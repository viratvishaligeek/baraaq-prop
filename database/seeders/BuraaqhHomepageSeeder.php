<?php

namespace Database\Seeders;

use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Support\ThemeMediaImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use RuntimeException;

class BuraaqhHomepageSeeder extends Seeder
{
    public function run(): void
    {
        $homepage = Homepage::query()->where('slug', 'home-9')->first();

        if (! $homepage) {
            throw new RuntimeException('Home 9 must be created by HomepageTemplateSeeder before BuraaqhHomepageSeeder.');
        }

        $definitions = config('cms-sections.sections', []);
        $sections = [
            'hero.home-9' => [
                'enabled' => true,
                'config' => [
                    'eyebrow' => 'Real Estate in Bareilly',
                    'heading_prefix' => 'Explore',
                    'heading_highlight' => 'Property Options',
                    'heading_suffix' => 'with Clarity',
                    'description' => 'Explore residential and commercial property information in Bareilly, Uttar Pradesh. Share your requirements and verify every property detail before proceeding.',
                    'primary_button_label' => 'Explore Properties',
                    'primary_button_url' => '/properties',
                    'secondary_button_label' => 'Send an Enquiry',
                    'secondary_button_url' => '/contact',
                ],
            ],
            'about.home-9' => [
                'enabled' => true,
                'config' => [
                    'eyebrow' => 'About Buraaqh Properties',
                    'heading' => 'A clearer starting point for property decisions in Bareilly',
                    'button_label' => 'About Us',
                    'button_url' => '/about',
                    'mission_title' => 'Our Mission',
                    'mission_text' => 'To make property information easier to understand through clear enquiries, practical questions and careful verification.',
                    'vision_title' => 'Our Vision',
                    'vision_text' => 'To support a more informed property-search experience for people exploring residential and commercial real estate in Bareilly.',
                ],
            ],
            'statistics.home-9' => [
                'enabled' => false,
                'config' => [],
            ],
            'services.home-9' => [
                'enabled' => true,
                'config' => [
                    'eyebrow' => 'Property Guidance',
                    'heading' => 'Services and support',
                    'description' => 'Service information will be published after the available offerings and scope have been confirmed.',
                    'empty_state_text' => 'Service information will appear here once the available offerings have been confirmed and published.',
                ],
            ],
            'properties.index' => [
                'enabled' => true,
                'config' => [
                    'eyebrow' => 'Property Search',
                    'heading' => 'Residential and Commercial Property',
                    'description' => 'Explore property information and share the details of what you are looking for.',
                    'empty_state_text' => 'No verified property listings are currently published. Contact Buraaqh Properties to share your requirements.',
                ],
            ],
            'featured-gallery.home-9' => [
                'enabled' => false,
                'config' => [
                    'eyebrow' => 'Property Gallery',
                    'heading' => 'Approved Property Photography',
                    'video_url' => '',
                ],
            ],
            'home-details.home-9' => [
                'enabled' => false,
                'config' => [
                    'eyebrow' => 'Bareilly Property Information',
                    'heading' => 'Explore property options in Bareilly',
                    'location' => 'Bareilly, Uttar Pradesh',
                    'total_area' => '',
                    'living_space' => '',
                    'bedrooms' => '',
                    'bathrooms' => '',
                    'floors' => '',
                    'button_label' => 'Contact Us',
                    'button_url' => '/contact',
                ],
            ],
            'team.home-2' => [
                'enabled' => false,
                'config' => [
                    'eyebrow' => 'Our Team',
                    'heading' => 'Meet the Team',
                    'empty_state_text' => 'Team profiles will be published after the business confirms the names, roles and biographies.',
                ],
            ],
            'testimonials.home-3' => [
                'enabled' => false,
                'config' => [
                    'eyebrow' => 'Client Feedback',
                    'heading' => 'Testimonials',
                    'trust_brand' => '',
                    'trust_score_prefix' => '',
                    'reviews_suffix' => '',
                    'avatar_group_image' => '',
                ],
            ],
            'blog.home-2' => [
                'enabled' => true,
                'config' => [
                    'eyebrow' => 'Property Journal',
                    'heading' => 'Practical Real Estate Guides',
                    'description' => 'Read useful guidance about property searches, documentation, locality assessment and ownership decisions.',
                    'items_limit' => 6,
                    'button_label' => 'Explore All Articles',
                    'button_url' => '/blog',
                    'empty_state_text' => 'Property guidance articles will appear here as they are published.',
                ],
            ],
        ];

        // These bundled theme photographs were visually inspected and dimension-checked.
        // They are illustrative design assets, not photographs of Buraaqh listings.
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

        $importer = app(ThemeMediaImporter::class);
        $position = 0;

        foreach ($sections as $key => $definition) {
            $schema = $definitions[$key] ?? null;

            if (! $schema || ! view()->exists($schema['view'] ?? '')) {
                throw new RuntimeException('Home 9 section is not renderable: '.$key);
            }

            $existingSection = HomepageSection::query()
                ->where('homepage_id', $homepage->id)
                ->where('section_key', $key)
                ->first();
            $config = array_merge($schema['defaults'] ?? [], $definition['config'] ?? []);
            $imageFields = [];

            foreach (($schema['fields'] ?? []) as $field => $fieldDefinition) {
                if (($fieldDefinition['type'] ?? null) !== 'image') {
                    continue;
                }

                $imageFields[] = $field;

                if (isset($imageMap[$key][$field])) {
                    $image = $imageMap[$key][$field];
                    $existingValue = data_get($existingSection?->config, $field);
                    $defaultValue = $schema['defaults'][$field] ?? null;

                    // Import only for a new section or an untouched theme default.
                    // Administrator-selected media and custom paths remain untouched.
                    $existingAttachmentId = $existingSection && $existingValue === $defaultValue
                        ? $existingSection->mediaAttachments()->where('field_key', $field)->value('media_id')
                        : null;

                    if ($existingAttachmentId && Media::query()->whereKey($existingAttachmentId)->exists()) {
                        $config[$field] = (int) $existingAttachmentId;
                    } elseif (! $existingSection || $existingValue === $defaultValue) {
                        $media = $importer->import($image['path'], $image['alt']);
                        $config[$field] = $media?->id;
                        $config[$field.'_alt'] = $image['alt'];
                    } else {
                        $config[$field] = $existingValue;
                    }
                    continue;
                }

                $value = $config[$field] ?? null;
                if (is_string($value) && str_starts_with($value, '/theme-assets/')) {
                    if (! File::isFile(public_path(ltrim($value, '/')))) {
                        $config[$field] = null;
                    }
                }
            }

            $section = $existingSection ?? HomepageSection::query()->firstOrCreate(
                ['homepage_id' => $homepage->id, 'section_key' => $key],
                [
                    'section_type' => $schema['type'],
                    'sort_order' => $position + 1,
                    'is_enabled' => $definition['enabled'],
                    'config' => $config,
                    'data_source' => $this->dataSourceFor($schema['type']),
                ]
            );

            $persistedConfig = is_array($section->config) ? $section->config : [];
            $configChanged = false;

            foreach ($imageFields as $field) {
                $currentValue = $persistedConfig[$field] ?? null;
                $defaultValue = $schema['defaults'][$field] ?? null;

                // If the administrator selected media through the attachment
                // relation while the config still contains the old theme path,
                // keep that selected media and align the config to it.
                $existingAttachmentId = $section->mediaAttachments()
                    ->where('field_key', $field)
                    ->value('media_id');

                if (
                    is_string($currentValue)
                    && $currentValue === $defaultValue
                    && $existingAttachmentId
                    && Media::query()->whereKey($existingAttachmentId)->exists()
                ) {
                    $persistedConfig[$field] = (int) $existingAttachmentId;
                    $configChanged = true;
                    $currentValue = (int) $existingAttachmentId;
                } elseif (
                    isset($imageMap[$key][$field])
                    && is_string($currentValue)
                    && $currentValue === $defaultValue
                ) {
                    $image = $imageMap[$key][$field];
                    $media = $importer->import($image['path'], $image['alt']);

                    $persistedConfig[$field] = $media?->id;
                    $persistedConfig[$field.'_alt'] = $image['alt'];
                    $configChanged = true;
                    $currentValue = $media?->id;
                } elseif (
                    is_string($currentValue)
                    && str_starts_with($currentValue, '/theme-assets/')
                    && ! File::isFile(public_path(ltrim($currentValue, '/')))
                    && $currentValue === $defaultValue
                ) {
                    $persistedConfig[$field] = null;
                    $configChanged = true;
                    $currentValue = null;
                }

                if (
                    (is_string($currentValue) || is_int($currentValue))
                    && ctype_digit((string) $currentValue)
                    && Media::query()->whereKey((int) $currentValue)->exists()
                ) {
                    $section->mediaAttachments()->firstOrCreate(
                        ['field_key' => $field],
                        ['media_id' => (int) $currentValue]
                    );
                }
            }

            if ($configChanged) {
                $section->forceFill(['config' => $persistedConfig])->save();
            }

            $position++;
        }
    }

    private function dataSourceFor(string $type): array
    {
        return match ($type) {
            'service-list' => ['limit' => 8, 'sort_by' => 'published_at', 'sort_direction' => 'desc'],
            'property-list', 'property-slider-grid' => ['limit' => 8, 'sort_by' => 'published_at', 'sort_direction' => 'desc', 'filters' => []],
            'featured-gallery-slider' => ['limit' => 3, 'sort_by' => 'published_at', 'sort_direction' => 'desc', 'filters' => []],
            'team-slider' => ['limit' => 6, 'sort_by' => 'sort_order', 'sort_direction' => 'asc'],
            'testimonial-slider' => ['limit' => 4, 'sort_by' => 'sort_order', 'sort_direction' => 'asc'],
            'blog-slider' => ['limit' => 6, 'sort_by' => 'published_at', 'sort_direction' => 'desc'],
            default => [],
        };
    }
}
