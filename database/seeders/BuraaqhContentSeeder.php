<?php

namespace Database\Seeders;

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
use App\Models\Service;
use App\Models\Setting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\HomepageSection;
use App\Models\GalleryItem;
use App\Models\FaqItem;
use App\Models\NeighborhoodGuide;
use App\Models\ServiceFeature;
use App\Models\ServiceImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class BuraaqhContentSeeder extends Seeder
{
    private ?int $authorId = null;

    /** @var array<string, Media|null> */
    private array $mediaCache = [];

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->retireLegacyPillarDemoRecords();
            $this->replaceLegacyBrandContent();
            $this->authorId = User::query()->orderBy('id')->value('id');

            $this->seedSettings();
            [$propertyCategories, $postCategories] = $this->seedCategories();
            $properties = $this->seedProperties($propertyCategories);
            $posts = $this->seedPosts($postCategories);
            $this->seedServices();
            // Team and testimonials require real, approved client/staff information.
            $this->seedPages();
            $this->seedLocalContent();
            $this->seedServiceDetails();
            $this->seedBanners();
            // Do not seed fabricated leads, comments, or subscribers.
            $this->seedMenus($propertyCategories, $postCategories);
            $this->seedHomeNine();
        });

        Cache::forget('homepage:published-default');
        Cache::forget('homepage:published-default:id');
        Cache::forget('settings:all');
    }

    private function retireLegacyPillarDemoRecords(): void
    {
        $legacyPropertySlugs = [
            'the-oakwood-residences', 'cedar-grove-villa', 'the-aranya-apartments',
            'palm-court-independent-home', 'the-skyline-penthouse', 'maple-leaf-builder-floor',
            'the-courtyard-house', 'riverside-commercial-suites', 'meadow-view-farmhouse',
            'the-meridian-apartments', 'willow-creek-residence', 'orchid-business-plaza',
            'silver-oak-residential-plot', 'lakeview-family-apartment', 'the-heritage-bungalow',
            'studio-one-business-loft',
        ];
        Property::query()->whereIn('slug', $legacyPropertySlugs)->update([
            'status' => 'draft',
            'is_featured' => false,
            'published_at' => null,
            'whatsapp_number' => null,
        ]);

        Banner::query()->whereIn('title', [
            'Discover a place that feels like home',
            'Make your next move with confidence',
            'Find the right space for your business',
        ])->update(['is_active' => false]);

        TeamMember::query()->where('email', 'like', '%@pillarrealty.example')->delete();
        // Keep the legacy demo pages and post in draft for owner review. Fresh
        // Buraaqh equivalents are seeded separately under their own slugs.
        Page::query()->whereIn('slug', ['buy-with-pillar', 'sell-with-pillar'])->update(['status' => 'draft']);
        Post::query()->where('slug', 'planning-a-delhi-ncr-move')->update(['status' => 'draft', 'published_at' => null]);

        Testimonial::query()->whereIn('name', [
            'Julia Era', 'Carlos Bauch', 'Martha Collins', 'Daniel Foster',
            'Sophia Bennett', 'Oliver Hayes', 'Aisha Khan', 'Noah Williams',
        ])->update(['status' => 'draft', 'is_active' => false]);

        Comment::query()->whereIn('email', [
            'nisha@example.test', 'rahul@example.test', 'priya@example.test',
            'arjun@example.test', 'kavya@example.test', 'manav@example.test',
            'tara@example.test', 'neil@example.test', 'ritika@example.test', 'aditya@example.test',
        ])->update(['status' => 'pending']);

        Enquiry::query()->whereIn('email', [
            'aarav.sharma@example.test', 'meera.kapoor@example.test', 'rohan.verma@example.test',
            'ananya.singh@example.test', 'kabir.malhotra@example.test', 'ishita.jain@example.test',
            'dev.mehta@example.test', 'sara.khan@example.test',
        ])->update(['status' => 'closed', 'admin_notes' => 'Legacy synthetic demo enquiry — not a customer lead.']);

        NewsletterSubscriber::query()->whereIn('email', [
            'riya.shah@example.test', 'aditya.malhotra@example.test', 'neha.verma@example.test',
            'ishaan.kapoor@example.test', 'simran.kaur@example.test', 'arjun.mehta@example.test',
            'tanvi.joshi@example.test', 'rohit.singh@example.test', 'anika.bose@example.test',
            'vivaan.gupta@example.test', 'sana.ali@example.test', 'karan.bhatia@example.test',
        ])->delete();
    }

    private function replaceLegacyBrandContent(): void
    {
        // Update only the known legacy navigation destinations. Do not rewrite
        // arbitrary labels, URLs or owner-authored page/post/service content.
        MenuItem::query()->whereIn('url', ['/pages/buy-with-pillar', '/buy-with-pillar'])
            ->update(['label' => 'Buy with Buraaqh Properties', 'url' => '/pages/buy-with-buraaqh']);
        MenuItem::query()->whereIn('url', ['/pages/sell-with-pillar', '/sell-with-pillar'])
            ->update(['label' => 'Sell with Buraaqh Properties', 'url' => '/pages/sell-with-buraaqh']);
    }

    private function seedSettings(): void
    {
        $site = trim((string) env('SITE_NAME', 'Buraaqh Properties')) ?: 'Buraaqh Properties';
        if (stripos($site, 'Pillar') !== false) $site = 'Buraaqh Properties';
        $defaultOgMedia = $this->themeMedia('theme-assets/img/hero/hero-9-right.png', 'Buraaqh Properties social sharing image');
        // Do not present stock template photography as real property or office imagery.
        $footerGalleryImages = [];

        $settings = [
            'site_title' => [$site, 'general', 'string'],
            'site_tagline' => ['Thoughtful real estate. Better living.', 'general', 'string'],
            'business_name' => [$site, 'business', 'string'],
            'site_email' => ['', 'contact', 'string'],
            'site_phone' => ['', 'contact', 'string'],
            'whatsapp_number' => ['', 'contact', 'string'],
            'site_address' => ['', 'contact', 'string'],
            'header_cta_label' => ['Add Listing', 'navigation', 'string'],
            'header_cta_url' => ['/contact', 'navigation', 'string'],
            'footer_description' => ['Buraaqh Properties provides a place to share property enquiries and explore general property information for Bareilly. Details for specific properties and services are published only after review.', 'footer', 'string'],
            'footer_copyright' => [$site, 'footer', 'string'],
            'footer_rights_text' => ['All Rights Reserved.', 'footer', 'string'],
            'footer_map_embed_url' => ['', 'footer', 'string'],
            'footer_gallery_handle' => ['', 'footer', 'string'],
            'footer_gallery_title' => ['Bareilly property moments', 'footer', 'string'],
            'footer_gallery_images' => [$footerGalleryImages, 'footer', 'json'],
            'social_facebook' => ['', 'social', 'string'],
            'social_instagram' => ['', 'social', 'string'],
            'social_linkedin' => ['', 'social', 'string'],
            'primary_color' => ['#068FFF', 'appearance', 'string'],
            'secondary_color' => ['#044DBC', 'appearance', 'string'],
            'meta_title' => [$site.' | Discover a better place to call home', 'seo', 'string'],
            'meta_description' => ['Explore carefully selected homes, apartments and investment properties across Bareilly and the Bareilly region with the local property specialists at Buraaqh Properties.', 'seo', 'string'],
            'meta_keywords' => ['real estate Bareilly, homes for sale, apartments, property consultants, Bareilly region', 'seo', 'string'],
            'gallery_meta_title' => ['Property Gallery in Bareilly | Buraaqh Properties', 'seo', 'string'],
            'gallery_page_heading' => ['Property Gallery', 'content', 'string'],
            'gallery_page_intro' => ['Explore homes, spaces and real estate highlights around Bareilly.', 'content', 'string'],
            'gallery_meta_description' => ['Explore property photography and real estate highlights in Bareilly, Uttar Pradesh.', 'seo', 'string'],
            'faq_meta_title' => ['Property FAQs in Bareilly | Buraaqh Properties', 'seo', 'string'],
            'faq_page_heading' => ['Frequently Asked Questions', 'content', 'string'],
            'faq_page_intro' => ['Helpful answers about property decisions in Bareilly.', 'content', 'string'],
            'faq_meta_description' => ['Answers to common property buying, selling and real estate questions in Bareilly, Uttar Pradesh.', 'seo', 'string'],
            'neighborhood_guides_meta_title' => ['Neighborhood Guides in Bareilly | Buraaqh Properties', 'seo', 'string'],
            'neighborhood_guides_page_heading' => ['Neighborhood Guides', 'content', 'string'],
            'neighborhood_guides_page_intro' => ['Explore localities across Bareilly, Uttar Pradesh.', 'content', 'string'],
            'neighborhood_guides_meta_description' => ['Explore neighborhood guides for Bareilly, Uttar Pradesh, with local area insights to support informed property decisions.', 'seo', 'string'],
            'service_area_city' => ['Bareilly', 'general', 'string'],
            'default_og_image' => [$defaultOgMedia?->path ?? 'theme-assets/img/hero/hero-9-right.png', 'seo', 'string'],
        ];

        foreach ($settings as $key => [$stored, $group, $type]) {

            $existing = Setting::query()->where('key', $key)->first();

            // Fill empty starter defaults, but never replace a value already configured by the site owner.
            $legacyMapUrl = $key === 'footer_map_embed_url' && str_contains((string) ($existing?->value ?? ''), 'Meerut');
            $legacyDemoValue = str_contains((string) ($existing?->value ?? ''), 'Pillar') || str_contains((string) ($existing?->value ?? ''), 'Meerut') || in_array($existing?->value, ['hello@pillarrealty.example', '+91 121 456 7800', '919876543210', 'https://www.facebook.com/', 'https://www.instagram.com/', 'https://www.linkedin.com/'], true);
            if (! $existing || trim((string) $existing->value) === '' || $legacyMapUrl || $legacyDemoValue) {
                Setting::write($key, $stored, $group, $type);
            }
        }
    }

    private function seedCategories(): array
    {
        $property = [];
        $propertyRows = [
            ['Apartment', 'apartment', 'Well-planned apartments in established and emerging neighbourhoods.', 'icon/property-item-icon-2-4.svg'],
            ['Independent House', 'independent-house', 'Independent homes with privacy, space and room to grow.', 'icon/property-item-icon-2-2.svg'],
            ['Villa', 'villa', 'Premium villas with considered layouts and private outdoor spaces.', 'icon/property-item-icon-2-3.svg'],
            ['Builder Floor', 'builder-floor', 'Low-rise builder floors with practical family layouts.', 'icon/property-item-icon-2-1.svg'],
            ['Residential Plot', 'residential-plot', 'Residential plots in well-connected planned communities.', 'icon/property-item-icon-2-2.svg'],
            ['Commercial', 'commercial', 'Retail, office and mixed-use spaces for growing businesses.', 'icon/property-item-icon-2-1.svg'],
            ['Penthouse', 'penthouse', 'Distinctive top-floor residences with expansive views.', 'icon/property-item-icon-2-3.svg'],
            ['Farmhouse', 'farmhouse', 'Weekend homes and farmhouse properties near the city.', 'icon/property-item-icon-2-2.svg'],
        ];

        foreach ($propertyRows as $index => [$name, $slug, $description, $icon]) {
            $category = Category::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $description, 'type' => 'property', 'sort_order' => $index + 1, 'is_active' => true]
            );
            if (! $category->description || ! $category->icon_path) {
                $category->forceFill([
                    'description' => $category->description ?: $description,
                    'icon_path' => $category->icon_path ?: '/theme-assets/img/'.$icon,
                    'is_active' => true,
                ])->save();
            }
            $property[$slug] = $category;
        }

        $post = [];
        $postRows = [
            ['Buying Guides', 'buying-guides', 'Practical advice for a confident property purchase.'],
            ['Selling Guides', 'selling-guides', 'Preparation, pricing and marketing advice for sellers.'],
            ['Market Insights', 'market-insights', 'Local property trends, pricing and neighbourhood updates.'],
            ['Property Tips', 'property-tips', 'Ideas to make home ownership easier and more rewarding.'],
            ['Home Design', 'home-design', 'Architecture, interiors and thoughtful ways to live.'],
        ];
        foreach ($postRows as $index => [$name, $slug, $description]) {
            $post[$slug] = Category::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $description, 'type' => 'post', 'sort_order' => $index + 1, 'is_active' => true]
            );
        }

        return [$property, $post];
    }

    private function seedProperties(array $categories): array
    {
        $rows = []; // Real listings are entered only after the owner verifies their details.


        $properties = [];
        foreach ($rows as $index => $row) {
            [$title, $slug, $categorySlug, $listing, $price, $beds, $baths, $area, $address, $city, $state, $postal, $lat, $lng, $image, $featured] = $row;
            $imagePath = $this->themeImagePath('img/'.$image);
            $property = Property::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categories[$categorySlug]->id,
                    'created_by' => $this->authorId,
                    'title' => $title,
                    'reference' => 'PLR-'.str_pad((string) ($index + 101), 5, '0', STR_PAD_LEFT),
                    'property_type' => $categories[$categorySlug]->name,
                    'listing_type' => $listing,
                    'price' => $price,
                    'price_label' => $listing === 'rent' ? '₹'.number_format($price).' / month' : '₹'.number_format($price),
                    'bedrooms' => $beds,
                    'bathrooms' => $baths,
                    'area' => $area,
                    'area_unit' => 'sq ft',
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'postal_code' => $postal,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'description' => '<p>'.$title.' is a thoughtfully planned property in '.$city.', selected for its location, practical layout and everyday convenience.</p><p>Enjoy well-proportioned interiors, quality fittings and easy access to schools, workplaces, shopping and essential services. Our property team can arrange a private viewing and share the complete specifications.</p>',
                    'meta_title' => $title.' | Buraaqh Properties',
                    'meta_description' => 'Explore '.$title.' in '.$city.'. View property details, location, amenities and arrange a private viewing with Buraaqh Properties.',
                    'featured_image' => $imagePath,
                    'status' => 'published',
                    'is_featured' => $featured,
                    'cta_mode' => 'both',
                    'whatsapp_number' => '',
                    'whatsapp_message' => 'Hello, I would like to know more about '.$title.'.',
                    'published_at' => now()->subDays(($index % 20) + 1),
                    'features' => ['Covered parking', 'Power backup', '24-hour security', 'Landscaped common areas', 'Nearby schools and retail'],
                ]
            );

            $properties[] = $property;
            foreach ([$image, 'popular/popular-1-'.(($index + 1) % 12 + 1).'.jpg'] as $imageIndex => $galleryImage) {
                $path = $this->themeImagePath('img/'.$galleryImage);
                if ($path) {
                    PropertyImage::query()->firstOrCreate(
                        ['property_id' => $property->id, 'path' => $path],
                        ['alt_text' => $title.' — gallery image '.($imageIndex + 1), 'sort_order' => $imageIndex + 1]
                    );
                }
            }
        }

        return $properties;
    }

    private function seedPosts(array $categories): array
    {
        $rows = [
            ['Building gains into housing stocks and how to evaluate the sector', 'building-gains-housing-stocks', 'A practical look at property-linked investments, rental fundamentals and long-term value.', 'market-insights'],
            ['What first-time homebuyers should know before booking', 'first-time-homebuyer-checklist', 'A clear checklist for planning a purchase, comparing homes and reviewing documents.', 'buying-guides'],
            ['How neighbourhood infrastructure shapes property value', 'neighbourhood-infrastructure-property-value', 'Connectivity, schools, public spaces and daily conveniences all influence the way a neighbourhood grows.', 'market-insights'],
            ['A smarter way to prepare your home for sale', 'prepare-your-home-for-sale', 'Simple presentation and maintenance improvements can help buyers understand a property at its best.', 'selling-guides'],
            ['Choosing between an apartment and an independent home', 'apartment-vs-independent-home', 'Compare privacy, maintenance, amenities and ownership costs before choosing your next home.', 'buying-guides'],
            ['Design details that make compact homes feel spacious', 'design-details-compact-homes', 'Thoughtful storage, daylight and circulation can make a smaller home work beautifully.', 'home-design'],
            ['Understanding the documents involved in a property purchase', 'property-purchase-documents-guide', 'Learn which ownership, approval and payment records to review with a qualified professional.', 'buying-guides'],
            ['Rental yield: the numbers every investor should understand', 'rental-yield-investor-guide', 'A straightforward guide to gross yield, recurring costs and realistic rental assumptions.', 'market-insights'],
            ['Five maintenance habits that protect your home', 'home-maintenance-habits', 'A seasonal routine for keeping fixtures, finishes and essential systems in good condition.', 'property-tips'],
            ['Planning a move in Bareilly and nearby areas', 'planning-a-bareilly-move', 'A practical moving plan covering commute, locality needs and a smooth handover.', 'property-tips'],
        ];

        $posts = [];
        foreach ($rows as $index => [$title, $slug, $excerpt, $categorySlug]) {
            $image = $this->themeImagePath('img/blog/blog_1_'.(($index % 12) + 1).'.jpg');
            $post = Post::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'author_id' => $this->authorId,
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'content' => '<p>'.$excerpt.'</p><p>Property decisions are easier when the information is clear and the next step is practical. Start by understanding your priorities, compare the available options and take time to review the details that matter to your household or investment plan.</p><p>Property conditions, documents and transaction requirements vary. Treat this article as general information only, verify current records and seek independent professional advice before making a decision.</p>',
                    'featured_image' => $image,
                    'status' => 'published',
                    'published_at' => now()->subDays(($index * 3) + 2),
                    'meta_title' => $title.' | Buraaqh Properties Journal',
                    'meta_description' => $excerpt,
                ]
            );
            if ($post->wasRecentlyCreated) {
                $post->categories()->syncWithoutDetaching([$categories[$categorySlug]->id]);
            }
            $posts[] = $post;
        }

        return $posts;
    }

    private function seedServices(): void
    {
        $rows = [
            ['Property Valuation', 'property-valuation', 'A clear view of your property’s market position.', 'Receive a considered valuation based on location, comparable sales, property condition and current buyer demand.', 'icon/service_card_2_1.svg'],
            ['Legal Assistance', 'legal-assistance', 'Guidance through important property documentation.', 'We help you organise the questions and documents to discuss with your appointed legal and registration professionals.', 'icon/service_card_2_2.svg'],
            ['Mortgage Services', 'mortgage-services', 'Explore financing options with greater confidence.', 'Understand the information lenders typically require and compare suitable home-finance options with our referral network.', 'icon/service_card_2_3.svg'],
            ['Insurance Solutions', 'insurance-solutions', 'Protect your home and the things that matter.', 'Explore practical home and property protection options with guidance on the cover that may suit your needs.', 'icon/service_card_2_4.svg'],
            ['Home Inspection', 'home-inspection', 'Look beyond the first impression before you buy.', 'Arrange a structured inspection to review visible condition, fittings and maintenance considerations before completion.', 'icon/service_card_2_5.svg'],
            ['Escrow and Transaction Support', 'escrow-transaction-support', 'Keep the transaction organised from offer to handover.', 'We coordinate key milestones and communication so buyers and sellers know what is needed at each stage.', 'icon/service_card_2_6.svg'],
            ['Property Management', 'property-management', 'Reliable support for owners and landlords.', 'From tenant coordination to routine property checks, our management support helps protect your time and asset.', 'icon/service_card_2_7.svg'],
            ['Real Estate Consulting', 'real-estate-consulting', 'Make your next property decision with local insight.', 'Discuss your goals with a specialist who understands local demand, neighbourhoods and the practical details of a move.', 'icon/service_card_2_8.svg'],
        ];

        foreach ($rows as $index => [$title, $slug, $excerpt, $body, $icon]) {
            Service::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'content' => '<p>'.$body.'</p><p>Contact Buraaqh Properties to discuss your requirements and the most suitable next step.</p>',
                    'featured_image' => $this->themeImagePath('img/'.$icon),
                    'status' => 'draft',
                    'published_at' => null,
                    'meta_title' => $title.' | Buraaqh Properties',
                    'meta_description' => $excerpt,
                ]
            );
        }
    }

    private function seedPages(): void
    {
        $pages = [
            ['About Us', 'about', '<p>Buraaqh Properties is built around a simple belief: good property advice starts with listening. We combine local knowledge, careful research and a personal approach to help people make confident decisions.</p><h2>Our mission</h2><p>To make every property move clearer, more informed and more rewarding.</p><h2>Our approach</h2><p>We value honest communication, thoughtful recommendations and dependable support from the first conversation through to handover.</p>'],
            ['Contact', 'contact', '<p>Share your property requirements through the website enquiry form. Please provide accurate contact details so the team can respond to your request.</p>'],
            ['Buy with Buraaqh Properties', 'buy-with-buraaqh', '<p>Start by recording your preferred location, budget and everyday requirements. Use the contact form to share your enquiry; the team can confirm whether assistance is available. Verify each property and its documents independently before making a decision.</p>'],
            ['Sell with Buraaqh Properties', 'sell-with-buraaqh', '<p>Prepare accurate property details, current photographs and relevant ownership records before marketing. Use the contact form to share your enquiry and confirm the next steps directly with the business.</p>'],
            ['Privacy Policy', 'privacy-policy', '<p>We use the information you provide to respond to property enquiries and deliver the services you request. We do not publish your contact details. Please contact our team if you would like to review or update information you have submitted.</p>'],
            ['Frequently Asked Questions', 'frequently-asked-questions', '<h2>How do I arrange a viewing?</h2><p>Contact our team with the property you are interested in and your preferred viewing time.</p><h2>Can I list my property?</h2><p>Yes. Share the property details and our team will contact you to discuss the next steps.</p>'],
        ];

        foreach ($pages as [$title, $slug, $html]) {
            Page::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'author_id' => $this->authorId,
                    'title' => $title,
                    'content' => $html,
                    'template' => 'default',
                    'status' => 'published',
                    'meta_title' => $title.' | Buraaqh Properties',
                    'meta_description' => Str::limit(strip_tags($html), 155),
                ]
            );
        }
    }

    private function seedBanners(): void
    {
        $rows = [
            ['Share your property enquiry', 'Buraaqh Properties · Bareilly', 'Use the website contact form to share your requirements and ask about current availability.', '', 'Contact Us', '/contact'],
            ['Property information', 'Clear details matter', 'Review the information available and confirm current details directly before making a decision.', '', 'Contact Us', '/contact'],
            ['Property enquiries', 'Bareilly, Uttar Pradesh', 'Share your property requirements so the business can confirm whether suitable information is available.', '', 'Send Enquiry', '/contact'],
        ];

        foreach ($rows as $index => [$title, $subtitle, $description, $image, $button, $url]) {
            Banner::query()->firstOrCreate(
                ['title' => $title, 'placement' => 'home'],
                [
                    'subtitle' => $subtitle,
                    'description' => $description,
                    'image' => null,
                    'button_text' => $button,
                    'button_url' => $url,
                    'sort_order' => $index + 1,
                    'is_active' => false,
                ]
            );
        }
    }

    private function seedMenus(array $propertyCategories, array $postCategories): void
    {
        $primary = Menu::query()->firstOrCreate(['location' => 'primary'], ['name' => 'Primary navigation']);
        $primaryItems = [
            ['Home', '/', null, 'custom', null],
            ['Properties', '/properties', null, 'custom', null],
            ['Apartments', null, 'Properties', 'property_category', $propertyCategories['apartment']->id],
            ['Villas', null, 'Properties', 'property_category', $propertyCategories['villa']->id],
            ['Independent Houses', null, 'Properties', 'property_category', $propertyCategories['independent-house']->id],
            ['Services', '/services', null, 'custom', null],
            ['Gallery', '/gallery', null, 'custom', null],
            ['Neighborhood Guide', '/neighborhood-guide', null, 'custom', null],
            ['FAQs', '/faq', null, 'custom', null],
            ['About Us', '/about', null, 'page', Page::query()->where('slug', 'about')->value('id')],
            ['Journal', '/blog', null, 'custom', null],
            ['Contact', '/contact', null, 'page', Page::query()->where('slug', 'contact')->value('id')],
        ];
        $this->insertMenuItems($primary, $primaryItems);

        $footer = Menu::query()->firstOrCreate(['location' => 'footer'], ['name' => 'Footer navigation']);
        $this->insertMenuItems($footer, [
            ['Properties', '/properties', null, 'custom', null],
            ['Buy with Buraaqh Properties', '/pages/buy-with-buraaqh', null, 'custom', null],
            ['Sell with Buraaqh Properties', '/pages/sell-with-buraaqh', null, 'custom', null],
            ['Journal', '/blog', null, 'custom', null],
            ['Gallery', '/gallery', null, 'custom', null],
            ['Neighborhood Guide', '/neighborhood-guide', null, 'custom', null],
            ['FAQs', '/faq', null, 'custom', null],
            ['Contact', '/contact', null, 'custom', null],
            ['Privacy Policy', '/privacy-policy', null, 'custom', null],
        ]);
    }

    private function insertMenuItems(Menu $menu, array $items): void
    {
        $parents = [];
        foreach ($items as [$label, $url, $parentLabel, $linkType, $linkId]) {
            $parentId = $parentLabel ? ($parents[$parentLabel] ?? null) : null;
            $item = MenuItem::query()->firstOrCreate(
                ['menu_id' => $menu->id, 'label' => $label],
                [
                    'parent_id' => $parentId,
                    'url' => $url,
                    'link_type' => $linkType,
                    'link_id' => $linkId,
                    'target' => '_self',
                    'sort_order' => count($parents) + 1,
                    'is_active' => true,
                ]
            );
            $parents[$label] = $item->id;
        }
        Menu::forgetLocationCache($menu->location);
    }



    private function seedLocalContent(): void
    {
        $faqs = [
            ['What should I check before buying a property in Bareilly?', 'Review the ownership chain, approved plans where applicable, land-use status, encumbrances, taxes and the terms of the sale agreement. Have the documents independently checked by a qualified local professional.'],
            ['How can I compare two residential properties?', 'Compare the exact location, usable area, construction condition, access, utilities, title documents, total transaction cost and resale considerations. Visit each property and verify all seller-provided information.'],
            ['What documents are commonly reviewed during a property purchase?', 'Depending on the property and transaction, buyers commonly review title documents, prior sale deeds, sanctioned plans where applicable, tax receipts, identity documents and applicable project or authority approvals. Requirements vary; obtain professional advice.'],
            ['Should I pay a booking amount before document verification?', 'Do not rely on verbal assurances. Understand the written booking terms, refund conditions, seller authority and relevant documents before making a payment. Seek independent legal advice when needed.'],
            ['What affects residential property value in Bareilly?', 'Value can depend on micro-location, road access, plot or built-up area, construction quality, legal status, available infrastructure and comparable transactions. Verify current market evidence rather than relying on general estimates.'],
            ['Can Buraaqh Properties confirm legal title or government approvals?', 'A property decision should be supported by independent document verification and relevant authority records. Do not treat marketing information as a legal opinion or an official approval.'],
        ];
        foreach ($faqs as $i => [$question, $answer]) {
            FaqItem::firstOrCreate(['question' => $question], ['answer' => $answer, 'category' => 'Property guidance', 'sort_order' => $i + 1, 'is_active' => true]);
        }

        $localities = ['Civil Lines', 'Rajendra Nagar', 'Izatnagar', 'Mahanagar', 'DD Puram', 'Pilibhit Bypass'];
        foreach ($localities as $i => $locality) {
            $title = 'Property considerations in '.$locality.', Bareilly';
            $slug = Str::slug($locality.' Bareilly property guide');
            NeighborhoodGuide::firstOrCreate(['slug' => $slug], [
                'title' => $title,
                'locality' => $locality,
                'city' => 'Bareilly',
                'state' => 'Uttar Pradesh',
                'excerpt' => 'A practical checklist for evaluating residential property options in '.$locality.'.',
                'content' => '<p>Use this guide as a starting point when assessing a property in '.$locality.'. It is not a rating of the locality or a statement that any particular facility, road, school or project is nearby.</p><h2>What to verify on a visit</h2><ul><li>Check the exact street, approach road and access at different times of day.</li><li>Confirm water, electricity, drainage and other utility arrangements for the specific property.</li><li>Verify land use, title chain, sanctioned plans and applicable approvals independently.</li><li>Compare recent transactions for genuinely comparable properties in the immediate area.</li><li>Check the written terms, total charges and any society or maintenance obligations.</li></ul><p>Local conditions can vary street by street. Confirm current details with relevant authorities and qualified local professionals before making a decision.</p>',
                'status' => 'published',
                'published_at' => now()->subDays($i),
                'meta_title' => $title.' | Buraaqh Properties',
                'meta_description' => 'Practical residential property due-diligence considerations for '.$locality.', Bareilly, Uttar Pradesh.',
                'focus_keyword' => $locality.' Bareilly property',
                'sort_order' => $i + 1,
            ]);
        }

        // The template gallery photos are illustrative assets, not verified Buraaqh property photographs.
        // Gallery entries are created only when an administrator uploads approved business imagery.
    }

    private function seedServiceDetails(): void
    {
        $features = [
            'property-valuation' => ['Comparable market review', 'Property condition considerations', 'Clear valuation discussion'],
            'legal-assistance' => ['Document checklist', 'Title and approval review coordination', 'Independent professional guidance'],
            'mortgage-services' => ['Finance requirement discussion', 'Lender document checklist', 'Offer comparison support'],
            'insurance-solutions' => ['Property risk discussion', 'Cover option overview', 'Policy question checklist'],
            'home-inspection' => ['Visible condition review', 'Fittings and maintenance checklist', 'Inspection summary coordination'],
            'escrow-transaction-support' => ['Milestone tracking', 'Document exchange coordination', 'Handover checklist'],
            'property-management' => ['Routine property coordination', 'Tenant communication support', 'Maintenance follow-up'],
            'real-estate-consulting' => ['Requirement discovery', 'Location comparison', 'Decision support'],
        ];
        foreach ($features as $slug => $titles) {
            $service = Service::query()->where('slug', $slug)->first();
            if (!$service) continue;
            foreach ($titles as $i => $title) {
                ServiceFeature::firstOrCreate(['service_id' => $service->id, 'title' => $title], ['description' => 'A clearly scoped part of the '.$service->title.' service.', 'sort_order' => $i + 1]);
            }
            // Service photography is uploaded only after the offering is confirmed.
        }
    }

    private function seedHomeNine(): void
    {
        $homepage = Homepage::query()->where('slug', 'home-9')->first();
        if (! $homepage) {
            throw new RuntimeException('Home 9 template is missing. Run HomepageTemplateSeeder before BuraaqhContentSeeder.');
        }

        $requiredKeys = [
            'hero.home-9',
            'about.home-9',
            'statistics.home-9',
            'services.home-9',
            'properties.index',
            'featured-gallery.home-9',
            'home-details.home-9',
            'team.home-2',
            'testimonials.home-3',
            'blog.home-2',
        ];
        $definitions = config('cms-sections.sections', []);
        $overrides = [
            'hero.home-9' => [
                'eyebrow' => 'Don’t Missed Out',
                'heading' => 'Discover Most Suitable Property',
                'heading_prefix' => 'Discover',
                'heading_highlight' => 'Most Suitable',
                'heading_suffix' => 'Property',
                'description' => 'Explore property information and contact Buraaqh Properties to discuss your requirements in Bareilly, Uttar Pradesh.',
                'primary_button_label' => 'Explore Properties',
                'primary_button_url' => '/properties',
                'secondary_button_label' => 'Contact Us',
                'secondary_button_url' => '/contact',
                'background_image' => '/theme-assets/img/hero/hero-9-bg.png',
                'foreground_image' => '/theme-assets/img/hero/hero-9-right.png',
            ],
            'about.home-9' => [
                'eyebrow' => 'About Us',
                'heading' => 'Create Your Story in a place where dreams and reality merge',
                'mission_title' => 'Our Mission',
                'mission_text' => 'To help people approach property decisions with clear information, careful questions and a practical next step.',
                'vision_title' => 'Our Vision',
                'vision_text' => 'To make property information easier to understand and the process more transparent for people exploring homes and commercial spaces in Bareilly.',
            ],
            'services.home-9' => [
                'eyebrow' => 'Our Services',
                'heading' => 'Property information and support',
                'description' => 'Explore the services currently published by Buraaqh Properties. Contact the team to confirm availability and scope before proceeding.',
                'items_limit' => 8,
                'columns' => '4',
            ],
            'properties.index' => [
                'eyebrow' => 'Popular Properties',
                'heading' => 'Best Properties Sale',
                'items_limit' => 8,
                'listing_filter' => 'all',
                'button_label' => 'View All Properties',
                'button_url' => '/properties',
            ],
            'featured-gallery.home-9' => [
                'eyebrow' => 'Popular Properties',
                'heading' => 'Featured Properties',
                'items_limit' => 3,
            ],
            'team.home-2' => [
                'eyebrow' => 'Team Members',
                'heading' => 'Meet the Team',
                'items_limit' => 6,
                'chat_text' => 'Share your property requirements through the contact form and confirm available services directly with the team.',
                'chat_link_label' => 'Contact us',
                'chat_link_url' => '/contact',
            ],
            'testimonials.home-3' => [
                'eyebrow' => 'Testimonials',
                'heading' => 'Client Testimonials',
                'items_limit' => 4,
                'trust_brand' => 'Client feedback',
                'view_all_label' => 'View All Reviews',
                'view_all_url' => '/testimonials',
            ],
            'blog.home-2' => [
                'eyebrow' => 'latest News',
                'heading' => 'Helpful Real Estate Articles',
                'description' => 'Ideas, insights and practical guidance for your next property decision.',
                'items_limit' => 6,
                'button_label' => 'Explore All',
                'button_url' => '/blog',
            ],
        ];

        foreach ($requiredKeys as $position => $key) {
            $definition = $definitions[$key] ?? null;
            if (! $definition || ! view()->exists($definition['view'] ?? '')) {
                throw new RuntimeException('Home 9 section is not renderable: '.$key);
            }

            $config = array_merge($definition['defaults'] ?? [], $overrides[$key] ?? []);
            $config = $this->replaceImagePathsWithMediaIds($config, $definition['fields'] ?? []);
            $source = $this->dataSourceFor($definition['type'] ?? '');

            $section = HomepageSection::query()
                ->where('homepage_id', $homepage->id)
                ->where('section_key', $key)
                ->first();

            if (! $section) {
                $section = $homepage->sections()->create([
                    'section_key' => $key,
                    'section_type' => $definition['type'],
                    'sort_order' => $position + 1,
                    'is_enabled' => true,
                    'config' => $config,
                    'data_source' => $source,
                ]);
            } else {
                // Existing administrator edits are preserved on reruns.
            }

            foreach ($config as $field => $mediaId) {
                if (is_int($mediaId) && $mediaId > 0 && ($definition['fields'][$field]['type'] ?? null) === 'image') {
                    $section->mediaAttachments()->updateOrCreate(
                        ['field_key' => $field],
                        ['media_id' => $mediaId]
                    );
                }
            }
        }

        $homepage->update([
            'name' => 'Home 9 — Buraaqh Properties',
            'status' => 'published',
            'published_at' => $homepage->published_at ?: now(),
        ]);

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

    private function replaceImagePathsWithMediaIds(array $config, array $fields): array
    {
        foreach ($fields as $field => $schema) {
            if (($schema['type'] ?? null) !== 'image' || ! is_string($config[$field] ?? null)) {
                continue;
            }

            $path = ltrim($config[$field], '/');
            if (! str_starts_with($path, 'theme-assets/')) {
                continue;
            }

            $media = $this->themeMedia($path, str_replace('-', ' ', pathinfo($path, PATHINFO_FILENAME)));
            if ($media) {
                $config[$field] = $media->id;
            }
        }

        return $config;
    }

    private function themeImagePath(string $relativePath): ?string
    {
        $media = $this->themeMedia('theme-assets/'.$relativePath, str_replace(['-', '_'], ' ', pathinfo($relativePath, PATHINFO_FILENAME)));

        return $media?->path;
    }

    private function themeMedia(string $themePath, string $altText): ?Media
    {
        if (isset($this->mediaCache[$themePath])) {
            return $this->mediaCache[$themePath];
        }

        $source = public_path($themePath);
        if (! File::exists($source) || ! is_file($source)) {
            return $this->mediaCache[$themePath] = null;
        }

        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        $target = 'buraaqh/'.sha1($themePath).'.'.$extension;
        $disk = Storage::disk('public');

        if (! $disk->exists($target)) {
            $disk->put($target, File::get($source));
        }

        $media = Media::query()->firstOrCreate(
            ['disk' => 'public', 'path' => $target],
            [
                'uploaded_by' => $this->authorId,
                'original_name' => basename($source),
                'mime_type' => File::mimeType($source) ?: 'application/octet-stream',
                'size' => File::size($source),
                'alt_text' => Str::limit($altText, 255, ''),
            ]
        );

        return $this->mediaCache[$themePath] = $media;
    }
}
