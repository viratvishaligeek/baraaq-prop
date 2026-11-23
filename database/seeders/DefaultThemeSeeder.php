<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DefaultThemeSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_title' => ['Buraaqh Properties', 'general', 'string'],
            'site_tagline' => ['Clear information for better property decisions in Bareilly.', 'general', 'string'],
            'business_name' => ['Buraaqh Properties', 'business', 'string'],
            'site_email' => ['', 'contact', 'string'],
            'site_phone' => ['', 'contact', 'string'],
            'whatsapp_number' => ['', 'contact', 'string'],
            'site_address' => ['', 'contact', 'string'],
            'logo' => ['', 'branding', 'string'],
            'favicon' => ['', 'branding', 'string'],
            'header_menu_location' => ['primary', 'navigation', 'string'],
            'header_cta_label' => ['Send an Enquiry', 'navigation', 'string'],
            'header_cta_url' => ['/contact', 'navigation', 'string'],
            'footer_menu_location' => ['footer', 'navigation', 'string'],
            'footer_quick_links_menu_location' => ['footer-quick-links', 'navigation', 'string'],
            'footer_support_menu_location' => ['footer-support', 'navigation', 'string'],
            'footer_about_heading' => ['About Buraaqh Properties', 'footer', 'string'],
            'footer_services_heading' => ['Property Guidance', 'footer', 'string'],
            'footer_featured_heading' => ['Explore Property Types', 'footer', 'string'],
            'footer_quick_links_heading' => ['Quick Links', 'footer', 'string'],
            'footer_support_heading' => ['Helpful Information', 'footer', 'string'],
            'footer_location_heading' => ['Our Service Area', 'footer', 'string'],
            'footer_social_heading' => ['Connect With Us', 'footer', 'string'],
            'sticky_header' => ['1', 'appearance', 'boolean'],
            'primary_color' => ['#068FFF', 'appearance', 'string'],
            'secondary_color' => ['#044DBC', 'appearance', 'string'],
            'footer_description' => [
                'Buraaqh Properties is a Bareilly-focused real estate information and enquiry platform. Explore residential and commercial property topics, review practical buying guidance and share your requirements. Specific property details and service availability should always be confirmed directly before you make a decision.',
                'footer',
                'string',
            ],
            'footer_copyright' => ['Buraaqh Properties', 'footer', 'string'],
            'footer_rights_text' => ['All Rights Reserved.', 'footer', 'string'],
            'footer_map_embed_url' => ['', 'footer', 'string'],
            'footer_gallery_handle' => ['', 'footer', 'string'],
            'footer_gallery_title' => ['Property Gallery', 'footer', 'string'],
            'footer_gallery_images' => ['[]', 'footer', 'json'],
            'social_facebook' => ['', 'social', 'string'],
            'social_instagram' => ['', 'social', 'string'],
            'social_linkedin' => ['', 'social', 'string'],
            'social_youtube' => ['', 'social', 'string'],
            'social_x' => ['', 'social', 'string'],
            'meta_title' => ['Buraaqh Properties | Real Estate in Bareilly, Uttar Pradesh', 'seo', 'string'],
            'meta_description' => [
                'Explore residential and commercial property guidance in Bareilly, Uttar Pradesh. Share your requirements with Buraaqh Properties and verify every property detail before proceeding.',
                'seo',
                'string',
            ],
            'meta_keywords' => [
                'Buraaqh Properties Bareilly, real estate in Bareilly, property dealers in Bareilly, residential properties in Bareilly, commercial properties in Bareilly, property consultation Bareilly',
                'seo',
                'string',
            ],
            'meta_robots' => ['index,follow', 'seo', 'string'],
            'default_og_image' => ['', 'seo', 'string'],
            'gallery_meta_title' => ['Property Gallery | Buraaqh Properties Bareilly', 'seo', 'string'],
            'gallery_page_heading' => ['Property Gallery', 'content', 'string'],
            'gallery_page_intro' => ['Approved property and business photographs will be added here after they have been reviewed.', 'content', 'string'],
            'gallery_meta_description' => ['View approved property and business photographs shared by Buraaqh Properties in Bareilly.', 'seo', 'string'],
            'faq_meta_title' => ['Property Buying FAQs in Bareilly | Buraaqh Properties', 'seo', 'string'],
            'faq_page_heading' => ['Frequently Asked Questions', 'content', 'string'],
            'faq_page_intro' => ['Practical answers to common questions about property searches, documentation and due diligence.', 'content', 'string'],
            'faq_meta_description' => ['Read practical property buying, documentation and due-diligence FAQs for Bareilly and Uttar Pradesh.', 'seo', 'string'],
            'neighborhood_guides_meta_title' => ['Bareilly Neighborhood Guides | Buraaqh Properties', 'seo', 'string'],
            'neighborhood_guides_page_heading' => ['Explore Bareilly Neighborhoods', 'content', 'string'],
            'neighborhood_guides_page_intro' => ['Use these locality guides as checklists for comparing specific streets, properties and everyday requirements in Bareilly.', 'content', 'string'],
            'neighborhood_guides_meta_description' => ['Explore practical locality-selection and property due-diligence guides for Bareilly, Uttar Pradesh.', 'seo', 'string'],
            'service_area_city' => ['Bareilly', 'general', 'string'],
            'head_scripts' => ['', 'integrations', 'string'],
            'body_scripts' => ['', 'integrations', 'string'],
        ];

        foreach ($settings as $key => [$value, $group, $type]) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                ['group' => $group, 'value' => $value, 'type' => $type]
            );
        }
    }
}
