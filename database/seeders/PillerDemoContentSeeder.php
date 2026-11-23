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
            $this->seedAnalytics();
        });

        Cache::forget('homepage:published-default');
        Cache::forget('homepage:published-default:id');
        Cache::forget('settings:all');
    }

    private function seedSettings(): void
    {
        $site = trim((string) env('SITE_NAME', 'Buraaqh Properties')) ?: 'Buraaqh Properties';
        $footerGalleryPaths = [
            'theme-assets/img/gallery/footer-gallery-1-1.jpg',
            'theme-assets/img/gallery/footer-gallery-1-2.jpg',
            'theme-assets/img/gallery/footer-gallery-1-3.jpg',
            'theme-assets/img/gallery/footer-gallery-1-5.jpg',
            'theme-assets/img/gallery/footer-gallery-1-6.jpg',
            'theme-assets/img/gallery/footer-gallery-1-7.jpg',
            'theme-assets/img/gallery/footer-gallery-1-4.jpg',
        ];
        $defaultOgMedia = $this->themeMedia('theme-assets/img/hero/hero-9-right.png', 'Pillar social sharing image');
        $footerGalleryImages = [];
        foreach ($footerGalleryPaths as $galleryPath) {
            $galleryMedia = $this->themeMedia($galleryPath, 'Pillar Instagram gallery');
            $footerGalleryImages[] = $galleryMedia?->id ?? $galleryPath;
        }

        $settings = [
            'site_title' => [$site, 'general', 'string'],
            'site_tagline' => ['Thoughtful real estate. Better living.', 'general', 'string'],
            'business_name' => [$site, 'business', 'string'],
            'site_email' => ['', 'contact', 'string'],
            'site_phone' => ['', 'contact', 'string'],
            'whatsapp_number' => ['', 'contact', 'string'],
            'site_address' => ['Bareilly, Uttar Pradesh, India', 'contact', 'string'],
            'header_cta_label' => ['Add Listing', 'navigation', 'string'],
            'header_cta_url' => ['/contact', 'navigation', 'string'],
            'footer_description' => ['Buraaqh Properties brings local insight, considered advice and a carefully selected collection of homes together. From your first viewing to the final signature, our team makes every move feel simpler.', 'footer', 'string'],
            'footer_copyright' => [$site, 'footer', 'string'],
            'footer_rights_text' => ['All Rights Reserved.', 'footer', 'string'],
            'footer_map_embed_url' => ['https://www.google.com/maps?q=Civil+Lines,+Bareilly,+Uttar+Pradesh&output=embed', 'footer', 'string'],
            'footer_gallery_handle' => ['@buraaqhproperties on Instagram', 'footer', 'string'],
            'footer_gallery_title' => ['Bareilly property moments', 'footer', 'string'],
            'footer_gallery_images' => [$footerGalleryImages, 'footer', 'json'],
            'social_facebook' => ['https://www.facebook.com/', 'social', 'string'],
            'social_instagram' => ['https://www.instagram.com/', 'social', 'string'],
            'social_linkedin' => ['https://www.linkedin.com/', 'social', 'string'],
            'primary_color' => ['#068FFF', 'appearance', 'string'],
            'secondary_color' => ['#044DBC', 'appearance', 'string'],
            'meta_title' => [$site.' | Discover a better place to call home', 'seo', 'string'],
            'meta_description' => ['Explore carefully selected homes, apartments and investment properties across Bareilly and the Bareilly region with the local property specialists at Buraaqh Properties.', 'seo', 'string'],
            'meta_keywords' => ['real estate Bareilly, homes for sale, apartments, property consultants, Bareilly region', 'seo', 'string'],
            'default_og_image' => [$defaultOgMedia?->path ?? 'theme-assets/img/hero/hero-9-right.png', 'seo', 'string'],
        ];

        foreach ($settings as $key => [$stored, $group, $type]) {

            $existing = Setting::query()->where('key', $key)->first();

            // Fill empty starter defaults, but never replace a value already configured by the site owner.
            $legacyMapUrl = $key === 'footer_map_embed_url' && $existing?->value === 'https://www.google.com/maps?q=Bareilly';
            if (! $existing || trim((string) $existing->value) === '' || $legacyMapUrl) {
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
            ['Planning a move across the Bareilly region region', 'planning-a-delhi-ncr-move', 'A practical moving plan covering commute, neighbourhood needs and a smooth handover.', 'property-tips'],
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
                    'content' => '<p>'.$excerpt.'</p><p>Property decisions are easier when the information is clear and the next step is practical. Start by understanding your priorities, compare the available options and take time to review the details that matter to your household or investment plan.</p><p>Our local team keeps a close eye on neighbourhood development, buyer preferences and the details that shape a well-informed decision. Speak with a property specialist if you would like guidance tailored to your situation.</p>',
                    'featured_image' => $image,
                    'status' => 'published',
                    'published_at' => now()->subDays(($index * 3) + 2),
                    'meta_title' => $title.' | Buraaqh Properties Journal',
                    'meta_description' => $excerpt,
                ]
            );
            $post->categories()->syncWithoutDetaching([$categories[$categorySlug]->id]);
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
                    'status' => 'published',
                    'published_at' => now()->subDays($index + 1),
                    'meta_title' => $title.' | Buraaqh Properties',
                    'meta_description' => $excerpt,
                ]
            );
        }
    }

    private function seedTeam(): void
    {
        $rows = [
            ['Vickie Wisozk', 'Real Estate Manager', 'Vickie helps clients define their priorities and navigate each stage of a property search with calm, practical advice.', 'vickie@buraaqhproperties.example'],
            ['Renee Strosin', 'Property Developer', 'Renee works with owners and development partners to bring thoughtful residential projects to market.', 'renee@buraaqhproperties.example'],
            ['Zulia Era', 'Listing Coordinator', 'Zulia coordinates listing preparation, property information and viewing schedules for buyers and sellers.', 'zulia@buraaqhproperties.example'],
            ['Bernice Roberts', 'Leasing Consultant', 'Bernice helps tenants and landlords find suitable rental arrangements with clear communication.', 'bernice@buraaqhproperties.example'],
            ['Martha Leffler', 'Senior Property Advisor', 'Martha brings a detail-led approach to residential sales and neighbourhood research.', 'martha@buraaqhproperties.example'],
            ['Thomas Kirlin', 'Investment Consultant', 'Thomas supports investors with property comparisons, location research and rental considerations.', 'thomas@buraaqhproperties.example'],
            ['Avery Mitchell', 'Client Relationship Manager', 'Avery keeps client communication moving and makes sure every enquiry reaches the right specialist.', 'avery@buraaqhproperties.example'],
            ['Jordan Bennett', 'Commercial Property Advisor', 'Jordan advises business owners on office, retail and mixed-use property requirements.', 'jordan@buraaqhproperties.example'],
        ];

        foreach ($rows as $index => [$name, $designation, $bio, $email]) {
            TeamMember::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'designation' => $designation,
                    'bio' => $bio,
                    'photo' => $this->themeImagePath('img/team/team_2_'.(($index % 8) + 1).'.png'),
                    'email' => $email,
                    'phone' => '+91 98'.str_pad((string) (76543210 + $index * 137), 8, '0', STR_PAD_LEFT),
                    'social_links' => ['linkedin' => 'https://www.linkedin.com/', 'instagram' => 'https://www.instagram.com/'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'status' => 'published',
                ]
            );
        }
    }

    private function seedTestimonials(): void
    {
        $rows = [
            ['Julia Era', 'Property owner', 'Residential buyer', 'The team understood what mattered to our family and kept every viewing focused. The process felt organised, transparent and genuinely personal.', 5],
            ['Carlos Bauch', 'Business owner', 'Commercial client', 'We needed a practical commercial space in a specific location. Pillar helped us compare the options and move forward with confidence.', 5],
            ['Martha Collins', 'Home buyer', 'First-time buyer', 'Every question was answered clearly, from the first shortlist to the final paperwork. We always knew what the next step would be.', 5],
            ['Daniel Foster', 'Property investor', 'Investment client', 'The local market insight was valuable. The team explained the trade-offs and helped us assess the numbers without pressure.', 4],
            ['Sophia Bennett', 'Home seller', 'Residential seller', 'The listing preparation and viewing coordination were excellent. Communication was consistent throughout the sale.', 5],
            ['Oliver Hayes', 'Tenant', 'Rental client', 'We found a home that suited our commute and budget. The team made the viewing and move-in arrangements straightforward.', 5],
            ['Aisha Khan', 'Property owner', 'Landlord', 'Pillar has been responsive and thoughtful in helping us manage our rental property and tenant requirements.', 5],
            ['Noah Williams', 'Home buyer', 'Residential buyer', 'A patient team with strong neighbourhood knowledge. We appreciated the honest advice and attention to detail.', 5],
        ];

        foreach ($rows as $index => [$name, $designation, $company, $quote, $rating]) {
            Testimonial::query()->firstOrCreate(
                ['name' => $name, 'quote' => $quote],
                [
                    'designation' => $designation,
                    'company' => $company,
                    'photo' => $this->themeImagePath('img/testimonial/testi_1_'.(($index % 3) + 1).'.jpg'),
                    'rating' => $rating,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'status' => 'published',
                ]
            );
        }
    }

    private function seedPages(): void
    {
        $pages = [
            ['About Us', 'about', '<p>Buraaqh Properties is built around a simple belief: good property advice starts with listening. We combine local knowledge, careful research and a personal approach to help people make confident decisions.</p><h2>Our mission</h2><p>To make every property move clearer, more informed and more rewarding.</p><h2>Our approach</h2><p>We value honest communication, thoughtful recommendations and dependable support from the first conversation through to handover.</p>'],
            ['Contact', 'contact', '<p>Tell us what you are looking for and a member of our property team will get back to you.</p><p>Email  or call . Our office is at Bareilly, Uttar Pradesh, India.</p>'],
            ['Buy with Pillar', 'buy-with-pillar', '<p>Start with your preferred location, budget and everyday requirements. Our advisors will help you compare suitable homes, arrange viewings and understand the next steps.</p>'],
            ['Sell with Pillar', 'sell-with-pillar', '<p>We help prepare your property for market with considered pricing, accurate information, quality presentation and coordinated viewings.</p>'],
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
            ['Discover a place that feels like home', 'Thoughtful homes. Local expertise.', 'Explore a considered collection of properties across Bareilly and the Bareilly region.', 'popular/popular-1-1.jpg', 'Explore Properties', '/properties'],
            ['Make your next move with confidence', 'Advice that puts you first', 'Speak with a property specialist about buying, selling or renting.', 'popular/popular-1-4.jpg', 'Talk to our team', '/contact'],
            ['Find the right space for your business', 'Commercial property, made simpler', 'Explore office and retail opportunities in well-connected locations.', 'popular/popular-1-8.jpg', 'View Commercial', '/properties?property_type=Commercial'],
        ];

        foreach ($rows as $index => [$title, $subtitle, $description, $image, $button, $url]) {
            Banner::query()->firstOrCreate(
                ['title' => $title, 'placement' => 'home'],
                [
                    'subtitle' => $subtitle,
                    'description' => $description,
                    'image' => $this->themeImagePath('img/'.$image),
                    'button_text' => $button,
                    'button_url' => $url,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedEnquiries(array $properties): void
    {
        $rows = [
            ['Aarav Sharma', 'aarav.sharma@example.test', '+91 98765 10001', 'Interested in a family apartment', 'Could you share the floor plan and arrange a weekend viewing?', 'contacted'],
            ['Meera Kapoor', 'meera.kapoor@example.test', '+91 98765 10002', 'Villa viewing request', 'We would like to understand the possession timeline and nearby schools.', 'new'],
            ['Rohan Verma', 'rohan.verma@example.test', '+91 98765 10003', 'Commercial property enquiry', 'Please send the available office specifications and parking details.', 'qualified'],
            ['Ananya Singh', 'ananya.singh@example.test', '+91 98765 10004', 'Rental availability', 'Is the property available from the beginning of next month?', 'new'],
            ['Kabir Malhotra', 'kabir.malhotra@example.test', '+91 98765 10005', 'Property valuation', 'I am considering selling and would like to understand the current market value.', 'contacted'],
            ['Ishita Jain', 'ishita.jain@example.test', '+91 98765 10006', 'Investment consultation', 'Please arrange a call to discuss rental yield in Noida.', 'qualified'],
            ['Dev Mehta', 'dev.mehta@example.test', '+91 98765 10007', 'Independent home enquiry', 'Can we schedule a visit this Saturday afternoon?', 'new'],
            ['Sara Khan', 'sara.khan@example.test', '+91 98765 10008', 'Plot documentation', 'I would like to know more about approvals and available plot sizes.', 'closed'],
        ];

        foreach ($rows as $index => [$name, $email, $phone, $subject, $message, $status]) {
            Enquiry::query()->firstOrCreate(
                ['email' => $email, 'subject' => $subject],
                [
                    'property_id' => $properties[$index % count($properties)]->id,
                    'name' => $name,
                    'phone' => $phone,
                    'preferred_date' => now()->addDays(($index % 10) + 1)->toDateString(),
                    'preferred_time' => $index % 2 ? 'Afternoon' : '11:00 AM – 1:00 PM',
                    'message' => $message,
                    'source' => $index % 2 ? 'property' : 'website',
                    'status' => $status,
                    'admin_notes' => $status === 'new' ? null : 'Demo enquiry seeded for CMS preview.',
                    'contacted_at' => in_array($status, ['contacted', 'qualified', 'closed'], true) ? now()->subDays($index + 1) : null,
                ]
            );
        }
    }

    private function seedComments(array $posts): void
    {
        $rows = [
            ['Nisha Gupta', 'nisha@example.test', 'A very useful checklist. The document review points are especially helpful.', 'approved'],
            ['Rahul Arora', 'rahul@example.test', 'Could you publish a follow-up about home loan pre-approval?', 'approved'],
            ['Priya Sethi', 'priya@example.test', 'The neighbourhood comparison made our shortlist much easier.', 'approved'],
            ['Arjun Bansal', 'arjun@example.test', 'Would love to know more about maintenance costs for apartments.', 'pending'],
            ['Kavya Rao', 'kavya@example.test', 'Clear and practical advice. Thank you for sharing.', 'approved'],
            ['Manav Khanna', 'manav@example.test', 'Please include more information about commercial leases.', 'pending'],
            ['Tara Malhotra', 'tara@example.test', 'The selling preparation tips are easy to follow.', 'approved'],
            ['Neil Thomas', 'neil@example.test', 'A good overview for first-time buyers.', 'approved'],
            ['Ritika Jain', 'ritika@example.test', 'Can you explain how to compare two similar projects?', 'pending'],
            ['Aditya Nair', 'aditya@example.test', 'Helpful read. I have shared this with my family.', 'approved'],
        ];

        foreach ($rows as $index => [$name, $email, $body, $status]) {
            Comment::query()->firstOrCreate(
                ['post_id' => $posts[$index % count($posts)]->id, 'email' => $email, 'body' => $body],
                ['name' => $name, 'status' => $status, 'created_at' => now()->subDays($index + 1), 'updated_at' => now()->subDays($index + 1)]
            );
        }
    }

    private function seedNewsletterSubscribers(): void
    {
        foreach ([
            'riya.shah@example.test', 'aditya.malhotra@example.test', 'neha.verma@example.test',
            'ishaan.kapoor@example.test', 'simran.kaur@example.test', 'arjun.mehta@example.test',
            'tanvi.joshi@example.test', 'rohit.singh@example.test', 'anika.bose@example.test',
            'vivaan.gupta@example.test', 'sana.ali@example.test', 'karan.bhatia@example.test',
        ] as $index => $email) {
            NewsletterSubscriber::query()->firstOrCreate(
                ['email' => $email],
                ['subscribed_at' => now()->subDays(($index * 4) + 1)]
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
            ['About Us', '/about', null, 'page', Page::query()->where('slug', 'about')->value('id')],
            ['Journal', '/blog', null, 'custom', null],
            ['Contact', '/contact', null, 'page', Page::query()->where('slug', 'contact')->value('id')],
        ];
        $this->insertMenuItems($primary, $primaryItems);

        $footer = Menu::query()->firstOrCreate(['location' => 'footer'], ['name' => 'Footer navigation']);
        $this->insertMenuItems($footer, [
            ['Properties', '/properties', null, 'custom', null],
            ['Buy with Pillar', '/buy-with-pillar', null, 'custom', null],
            ['Sell with Pillar', '/sell-with-pillar', null, 'custom', null],
            ['Journal', '/blog', null, 'custom', null],
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
            FaqItem::updateOrCreate(['question' => $question], ['answer' => $answer, 'category' => 'Property guidance', 'sort_order' => $i + 1, 'is_active' => true]);
        }

        $localities = ['Civil Lines', 'Rajendra Nagar', 'Izatnagar', 'Mahanagar', 'DD Puram', 'Pilibhit Bypass'];
        foreach ($localities as $i => $locality) {
            $title = 'Property considerations in '.$locality.', Bareilly';
            $slug = Str::slug($locality.' Bareilly property guide');
            NeighborhoodGuide::updateOrCreate(['slug' => $slug], [
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
            ]);
        }

        $galleryAssets = [
            'theme-assets/img/gallery/footer-gallery-1-1.jpg',
            'theme-assets/img/gallery/footer-gallery-1-2.jpg',
            'theme-assets/img/gallery/footer-gallery-1-3.jpg',
            'theme-assets/img/gallery/footer-gallery-1-4.jpg',
        ];
        foreach ($galleryAssets as $i => $asset) {
            $source = public_path($asset);
            if (! File::exists($source)) continue;
            $path = 'gallery/buraaqh-reference-'.($i + 1).'.jpg';
            if (! Storage::disk('public')->exists($path)) Storage::disk('public')->put($path, File::get($source));
            $item = GalleryItem::updateOrCreate(['slug' => 'bareilly-property-gallery-'.($i + 1)], [
                'title' => 'Property and lifestyle image '.($i + 1),
                'description' => 'Visual inspiration from the Buraaqh Properties website gallery.',
                'image' => $path,
                'alt_text' => 'Property and real estate gallery image',
                'sort_order' => $i + 1,
                'status' => 'published',
                'published_at' => now(),
            ]);
            Media::firstOrCreate(['path' => $path], [
                'uploaded_by' => $this->authorId,
                'disk' => 'public',
                'original_name' => basename($asset),
                'mime_type' => 'image/jpeg',
                'size' => Storage::disk('public')->size($path),
                'alt_text' => $item->alt_text,
            ]);
        }
    }

    private function seedServiceDetails(): void
    {
        $features = [
            'property-valuation' => ['Comparable market review', 'Property condition considerations', 'Clear valuation discussion'],
            'legal-assistance' => ['Document checklist', 'Title and approval review coordination', 'Independent professional guidance'],
            'property-management' => ['Routine property coordination', 'Tenant communication support', 'Maintenance follow-up'],
            'buying-consultation' => ['Requirement discovery', 'Property shortlist support', 'Offer and negotiation preparation'],
            'selling-consultation' => ['Property presentation guidance', 'Enquiry coordination', 'Offer comparison support'],
        ];
        foreach ($features as $slug => $titles) {
            $service = Service::query()->where('slug', $slug)->first();
            if (!$service) continue;
            foreach ($titles as $i => $title) {
                ServiceFeature::firstOrCreate(['service_id' => $service->id, 'title' => $title], ['description' => 'A clearly scoped part of the '.$service->title.' service.', 'sort_order' => $i + 1]);
            }
        }
    }

    private function seedHomeNine(): void
    {
        $homepage = Homepage::query()->where('slug', 'home-9')->first();
        if (! $homepage) {
            throw new RuntimeException('Home 9 template is missing. Run HomepageTemplateSeeder before PillerDemoContentSeeder.');
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
                'description' => 'Explore top listings and let our expert agents guide you every step of the way to your new home.',
                'primary_button_label' => 'Explore Properties',
                'primary_button_url' => '/properties',
                'secondary_button_label' => 'Request A Visit',
                'secondary_button_url' => '/contact',
                'background_image' => '/theme-assets/img/hero/hero-9-bg.png',
                'foreground_image' => '/theme-assets/img/hero/hero-9-right.png',
            ],
            'about.home-9' => [
                'eyebrow' => 'About Us',
                'heading' => 'Create Your Story in a place where dreams and reality merge',
                'mission_title' => 'Our Mission',
                'mission_text' => 'We specialize in residential and commercial property transactions, offering end-to-end assistance—from property valuation and marketing to negotiation, documentation, and closing support.',
                'vision_title' => 'Our Vision',
                'vision_text' => 'At the heart of our agency is a commitment to honesty, availability, and tailored service. Whether you are searching for your dream home, planning to list your property or exploring investment opportunities, our advice stays personal.',
            ],
            'statistics.home-9' => [
                'stat_1_value' => '98', 'stat_1_suffix' => '+', 'stat_1_label' => 'offices worldwide',
                'stat_2_value' => '162', 'stat_2_suffix' => '+', 'stat_2_label' => 'completed projects',
                'stat_3_value' => '150', 'stat_3_suffix' => '+', 'stat_3_label' => 'properties available',
                'stat_4_value' => '2.6', 'stat_4_suffix' => 'M', 'stat_4_label' => 'total project value',
            ],
            'services.home-9' => [
                'eyebrow' => 'Our Services',
                'heading' => 'We’re committed to discovering the perfect property for you',
                'description' => 'From valuation and legal coordination to financing guidance and property management, our specialists support every step of your real estate journey.',
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
            'home-details.home-9' => [
                'eyebrow' => 'Home Details',
                'heading' => 'Luxury Real Estate Home Details at a Glance',
                'location' => 'Ganga Nagar, Bareilly, Uttar Pradesh',
                'total_area' => '5,600 sq ft',
                'living_space' => '4,200 sq ft',
                'bedrooms' => '4',
                'bathrooms' => '3',
                'floors' => '2 floors',
                'button_label' => 'Discover More',
                'button_url' => '/about',
            ],
            'team.home-2' => [
                'eyebrow' => 'Team Members',
                'heading' => 'Meet Our Pillar Agents',
                'items_limit' => 6,
                'chat_text' => 'Explore Property agents are here to help with all your buying, renting and selling goals. Find the home of your dreams with an expert you can trust.',
                'chat_link_label' => 'Let’s chat',
                'chat_link_url' => '/contact',
            ],
            'testimonials.home-3' => [
                'eyebrow' => 'Testimonials',
                'heading' => 'What Clients Say About Pillar',
                'items_limit' => 4,
                'trust_brand' => 'Trustipilot',
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
            'name' => 'Home 9 — Pillar Single Property',
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

    private function seedAnalytics(): void
    {
        for ($i = 0; $i < 48; $i++) {
            $path = $i % 4 === 0 ? '/' : (['/properties', '/services', '/blog', '/about'][$i % 4]);
            $visitor = hash('sha256', 'piller-demo-visitor-'.($i % 19));
            $visit = hash('sha256', 'piller-demo-session-'.($i % 27));
            DB::table('page_views')->updateOrInsert(
                ['visitor_hash' => $visitor, 'visit_hash' => $visit, 'path' => $path, 'created_at' => now()->startOfDay()->subHours($i * 3)->toDateTimeString()],
                ['route_name' => $path === '/' ? 'home' : null, 'content_type' => 'page', 'content_id' => null]
            );
        }

        for ($i = 0; $i < 12; $i++) {
            DB::table('activity_logs')->updateOrInsert(
                ['subject_type' => 'demo', 'subject_id' => $i + 1, 'event' => 'seeded', 'description' => 'Demo CMS activity record '.($i + 1)],
                ['causer_id' => $this->authorId, 'properties' => json_encode(['source' => 'PillerDemoContentSeeder']), 'created_at' => now()->subDays($i)->toDateTimeString()]
            );
        }
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
        $target = 'piller-demo/'.sha1($themePath).'.'.$extension;
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
