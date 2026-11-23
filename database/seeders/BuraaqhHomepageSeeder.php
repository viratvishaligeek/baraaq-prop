<?php

namespace Database\Seeders;

use App\Models\Homepage;
use App\Models\HomepageSection;
use Illuminate\Database\Seeder;
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

        foreach ($sections as $position => $key => $definition) {
            $schema = $definitions[$key] ?? null;

            if (! $schema || ! view()->exists($schema['view'] ?? '')) {
                throw new RuntimeException('Home 9 section is not renderable: '.$key);
            }

            $config = array_merge($schema['defaults'] ?? [], $definition['config'] ?? []);
            HomepageSection::query()->firstOrCreate(
                ['homepage_id' => $homepage->id, 'section_key' => $key],
                [
                    'section_type' => $schema['type'],
                    'sort_order' => $position + 1,
                    'is_enabled' => $definition['enabled'],
                    'config' => $config,
                    'data_source' => $this->dataSourceFor($schema['type']),
                ]
            );
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
