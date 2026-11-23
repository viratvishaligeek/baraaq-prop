<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\PropertyFaq;
use App\Models\PropertyImage;
use App\Support\ThemeMediaImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BuraaqhDemoPropertySeeder extends Seeder
{
    /**
     * These are deliberately fictional preview listings. They are enabled by
     * default only for local/testing environments; production requires the
     * explicit CMS_DEMO_PROPERTIES=true opt-in.
     */
    private const IMAGE_PATHS = [
        'theme-assets/img/hero/hero-10-2.jpg',
        'theme-assets/img/hero/hero-9-right.png',
        'theme-assets/img/why/why-choose-left-1.jpg',
        'theme-assets/img/hero/hero-8-left.jpg',
        'theme-assets/img/contact/contact-img.jpg',
        'theme-assets/img/hero/hero-10-3.jpg',
    ];

    public function run(): void
    {
        $override = config('cms.demo_properties');
        $enabled = $override === null
            ? app()->environment(['local', 'testing'])
            : filter_var($override, FILTER_VALIDATE_BOOLEAN);

        if (! $enabled) {
            $this->command?->info('Demo property fixtures skipped (set CMS_DEMO_PROPERTIES=true to opt in).');

            return;
        }

        $categories = Category::query()
            ->where('type', 'property')
            ->whereIn('slug', [
                'apartment', 'independent-house', 'villa', 'builder-floor',
                'residential-plot', 'commercial', 'penthouse', 'farmhouse',
            ])
            ->get()
            ->keyBy('slug');

        $listings = $this->listings();

        foreach ($listings as $index => $listing) {
            $category = $categories->get($listing['category']);

            if (! $category) {
                $this->command?->warn('Skipped demo property because its category is unavailable: '.$listing['slug']);

                continue;
            }

            $property = Property::withTrashed()->where('slug', $listing['slug'])->first();

            // Never revive a soft-deleted record or overwrite an existing
            // administrator-owned property with fixture content.
            if ($property?->trashed()) {
                continue;
            }

            if (! $property) {
                $property = Property::query()->create([
                    'category_id' => $category->id,
                    'created_by' => null,
                    'title' => $listing['title'],
                    'slug' => $listing['slug'],
                    'reference' => $listing['reference'],
                    'property_type' => $listing['type'],
                    'listing_type' => $listing['listing_type'],
                    'price' => $listing['price'],
                    'price_label' => null,
                    'bedrooms' => $listing['bedrooms'],
                    'bathrooms' => $listing['bathrooms'],
                    'area' => $listing['area'],
                    'area_unit' => $listing['area_unit'],
                    'address' => $listing['address'],
                    'city' => 'Bareilly',
                    'state' => 'Uttar Pradesh',
                    'country' => 'India',
                    'postal_code' => $listing['postal_code'],
                    'latitude' => $listing['latitude'],
                    'longitude' => $listing['longitude'],
                    'description' => $this->description($listing),
                    'meta_title' => $listing['meta_title'],
                    'meta_description' => $listing['meta_description'],
                    'canonical_url' => null,
                    'og_image' => null,
                    'featured_image' => null,
                    'status' => 'published',
                    'is_featured' => $listing['featured'],
                    'cta_mode' => 'both',
                    'whatsapp_number' => null,
                    'whatsapp_message' => null,
                    'published_at' => now()->subDays($index + 1),
                    'features' => $listing['features'],
                    'availability_status' => 'available',
                    'focus_keyword' => $listing['focus_keyword'],
                ]);
            }

            $this->seedImages($property, $listing, $index);
            $this->seedAccommodations($property, $listing);
            $this->seedFaqs($property, $listing);
        }
    }

    private function seedImages(Property $property, array $listing, int $index): void
    {
        $importer = app(ThemeMediaImporter::class);
        $paths = [
            self::IMAGE_PATHS[$index % count(self::IMAGE_PATHS)],
            self::IMAGE_PATHS[($index + 2) % count(self::IMAGE_PATHS)],
        ];

        foreach ($paths as $position => $sourcePath) {
            $media = $importer->import(
                $sourcePath,
                'Illustrative demo property image for '.$listing['title'].'; not an actual listing photograph'
            );

            if (! $media || ! Storage::disk('public')->exists($media->path)) {
                continue;
            }

            PropertyImage::query()->firstOrCreate(
                ['property_id' => $property->id, 'path' => $media->path],
                [
                    'alt_text' => 'Illustrative demo image for '.$listing['title'].'; not an actual listing photograph',
                    'sort_order' => $position + 1,
                ]
            );
        }
    }

    private function seedAccommodations(Property $property, array $listing): void
    {
        foreach ($listing['accommodations'] as $index => $accommodation) {
            PropertyAccommodation::query()->firstOrCreate(
                ['property_id' => $property->id, 'name' => $accommodation['name']],
                [
                    'category' => $accommodation['category'],
                    'area' => $accommodation['area'],
                    'area_unit' => $listing['area_unit'] === 'acre' ? 'sqft' : $listing['area_unit'],
                    'description' => $accommodation['description'],
                    'is_included' => true,
                    'attributes' => $accommodation['attributes'],
                    'sort_order' => $index + 1,
                ]
            );
        }
    }

    private function seedFaqs(Property $property, array $listing): void
    {
        $questions = [
            [
                'question' => 'Is this a verified property available for purchase or rent?',
                'answer' => 'No. This is a fictional demonstration listing used to preview the website. Its address, price, availability and specifications are illustrative and must not be relied upon.',
            ],
            [
                'question' => 'What should a buyer or tenant verify before proceeding?',
                'answer' => 'Independently verify ownership or authority to let, title and approvals where applicable, exact measurements, permitted use, outstanding dues, physical condition and all commercial terms before making a commitment.',
            ],
        ];

        foreach ($questions as $index => $faq) {
            PropertyFaq::query()->firstOrCreate(
                ['property_id' => $property->id, 'question' => $faq['question']],
                ['answer' => $faq['answer'], 'sort_order' => $index + 1]
            );
        }
    }

    private function description(array $listing): string
    {
        $paragraphs = [
            '<p>'.$listing['description'].'</p>',
            '<p><strong>Location note:</strong> The locality and map coordinates are approximate Bareilly-area examples, not a surveyed property address or exact site pin.</p>',
            '<p><strong>Demo content notice:</strong> This fictional listing exists only for local website preview and testing. The price, availability, address, ownership, specifications and imagery are illustrative, not a real offer or verified inventory.</p>',
        ];

        return implode('', $paragraphs);
    }

    /**
     * Fictional Bareilly examples span residential, commercial, sale, rent and lease.
     * Coordinates identify approximate neighbourhood centres, never property plots.
     */
    private function listings(): array
    {
        return [
            [
                'slug' => 'demo-civil-lines-three-bedroom-apartment',
                'reference' => 'DEMO-BLY-001',
                'title' => 'Contemporary 3-Bedroom Apartment near Civil Lines',
                'category' => 'apartment', 'type' => 'Apartment', 'listing_type' => 'sale',
                'price' => 8450000, 'bedrooms' => 3, 'bathrooms' => 3, 'area' => 1540, 'area_unit' => 'sqft',
                'address' => 'Illustrative residential address near Civil Lines',
                'postal_code' => '243001', 'latitude' => 28.3672, 'longitude' => 79.4304,
                'featured' => true, 'focus_keyword' => '3 bedroom apartment in Civil Lines Bareilly',
                'meta_title' => '3-Bedroom Apartment near Civil Lines, Bareilly',
                'meta_description' => 'Illustrative 3-bedroom apartment listing near Civil Lines, Bareilly, with sample specifications for website preview.',
                'description' => 'A sample family apartment layout with a separate living and dining zone, three bedrooms, attached bathrooms and a covered balcony. The example highlights the information a buyer should compare, including usable area, parking allocation, building maintenance and ownership documents.',
                'features' => ['Covered parking', 'Lift access', 'Balcony', 'Power backup', 'Security desk'],
                'accommodations' => [
                    ['name' => 'Living and dining area', 'category' => 'Living', 'area' => 310, 'description' => 'Combined family living and dining space in the illustrative floor plan.', 'attributes' => ['flooring' => 'Vitrified tile', 'layout' => 'Open']],
                    ['name' => 'Primary bedroom', 'category' => 'Bedroom', 'area' => 185, 'description' => 'Sample primary bedroom with an attached bathroom.', 'attributes' => ['bathroom' => 'Attached', 'wardrobe' => 'Built-in provision']],
                    ['name' => 'Covered balcony', 'category' => 'Outdoor', 'area' => 65, 'description' => 'Illustrative balcony adjoining the living area.', 'attributes' => ['orientation' => 'Not specified']],
                ],
            ],
            [
                'slug' => 'demo-rajendra-nagar-two-bedroom-rental',
                'reference' => 'DEMO-BLY-002',
                'title' => 'Ready-to-View 2-Bedroom Rental Apartment in Rajendra Nagar',
                'category' => 'apartment', 'type' => 'Apartment', 'listing_type' => 'rent',
                'price' => 22000, 'bedrooms' => 2, 'bathrooms' => 2, 'area' => 1120, 'area_unit' => 'sqft',
                'address' => 'Illustrative apartment address near Rajendra Nagar',
                'postal_code' => '243122', 'latitude' => 28.3811, 'longitude' => 79.4278,
                'featured' => false, 'focus_keyword' => '2 bedroom apartment for rent Rajendra Nagar Bareilly',
                'meta_title' => '2-Bedroom Rental Apartment, Rajendra Nagar',
                'meta_description' => 'Sample 2-bedroom rental apartment in Rajendra Nagar, Bareilly, with illustrative rent and property details.',
                'description' => 'A practical two-bedroom rental example for a small household, with a defined living area, modular-kitchen provision and space for one vehicle. A real tenant should confirm the rent, deposit, maintenance split, furnishing inventory and tenancy terms in writing.',
                'features' => ['Modular kitchen provision', 'Balcony', 'Two-wheeler parking', 'Water storage'],
                'accommodations' => [
                    ['name' => 'Living room', 'category' => 'Living', 'area' => 210, 'description' => 'Sample living room with space for a compact dining table.', 'attributes' => ['layout' => 'Rectangular']],
                    ['name' => 'Kitchen', 'category' => 'Kitchen', 'area' => 95, 'description' => 'Illustrative kitchen with modular storage provision.', 'attributes' => ['counter' => 'Granite-style sample']],
                    ['name' => 'Balcony', 'category' => 'Outdoor', 'area' => 42, 'description' => 'Small utility balcony in the sample layout.', 'attributes' => ['use' => 'Utility']],
                ],
            ],
            [
                'slug' => 'demo-pilibhit-bypass-four-bedroom-villa',
                'reference' => 'DEMO-BLY-003',
                'title' => 'Spacious 4-Bedroom Villa near Pilibhit Bypass',
                'category' => 'villa', 'type' => 'Villa', 'listing_type' => 'sale',
                'price' => 16800000, 'bedrooms' => 4, 'bathrooms' => 4, 'area' => 2860, 'area_unit' => 'sqft',
                'address' => 'Illustrative villa address near Pilibhit Bypass',
                'postal_code' => '243006', 'latitude' => 28.3940, 'longitude' => 79.4540,
                'featured' => true, 'focus_keyword' => 'villa for sale near Pilibhit Bypass Bareilly',
                'meta_title' => '4-Bedroom Villa near Pilibhit Bypass, Bareilly',
                'meta_description' => 'Illustrative four-bedroom villa example near Pilibhit Bypass with sample room sizes and amenities.',
                'description' => 'A larger independent villa concept with four bedrooms, a family lounge, private outdoor space and provision for multiple vehicles. The example is intended to show detailed listing content; buyers must verify plot boundaries, sanctioned plans, construction records and the exact rights attached to outdoor areas.',
                'features' => ['Private garden area', 'Family lounge', 'Three-car driveway', 'Terrace', 'Store room'],
                'accommodations' => [
                    ['name' => 'Family lounge', 'category' => 'Living', 'area' => 360, 'description' => 'Separate family lounge in the illustrative villa plan.', 'attributes' => ['floor' => 'Ground']],
                    ['name' => 'Primary suite', 'category' => 'Bedroom', 'area' => 260, 'description' => 'Sample primary suite with dressing niche and attached bath.', 'attributes' => ['bathroom' => 'Attached']],
                    ['name' => 'Private garden', 'category' => 'Outdoor', 'area' => 420, 'description' => 'Illustrative private garden area; boundaries and rights are not verified.', 'attributes' => ['access' => 'Private in sample plan']],
                ],
            ],
            [
                'slug' => 'demo-dd-puram-independent-family-house',
                'reference' => 'DEMO-BLY-004',
                'title' => 'Independent 4-Bedroom Family House in DD Puram',
                'category' => 'independent-house', 'type' => 'Independent House', 'listing_type' => 'sale',
                'price' => 11200000, 'bedrooms' => 4, 'bathrooms' => 3, 'area' => 2250, 'area_unit' => 'sqft',
                'address' => 'Illustrative independent-house address near DD Puram',
                'postal_code' => '243122', 'latitude' => 28.3825, 'longitude' => 79.4385,
                'featured' => false, 'focus_keyword' => 'independent house for sale DD Puram Bareilly',
                'meta_title' => 'Independent Family House in DD Puram, Bareilly',
                'meta_description' => 'Sample independent four-bedroom house listing in DD Puram, Bareilly, for local website preview.',
                'description' => 'A sample multi-level family home with a ground-floor living and dining arrangement, four bedrooms and a small front setback. The example prompts a buyer to check the title chain, sanctioned construction, floor-wise area, shared access and any outstanding utility dues.',
                'features' => ['Independent entrance', 'Front setback', 'Terrace access', 'Car parking', 'Store room'],
                'accommodations' => [
                    ['name' => 'Ground-floor living room', 'category' => 'Living', 'area' => 285, 'description' => 'Main family gathering space in the sample layout.', 'attributes' => ['floor' => 'Ground']],
                    ['name' => 'Dining area', 'category' => 'Dining', 'area' => 145, 'description' => 'Dedicated dining space connected to the kitchen.', 'attributes' => ['floor' => 'Ground']],
                    ['name' => 'Front setback', 'category' => 'Outdoor', 'area' => 120, 'description' => 'Illustrative front open area; actual setback must be measured.', 'attributes' => ['use' => 'Open space']],
                ],
            ],
            [
                'slug' => 'demo-civil-lines-commercial-office-suite',
                'reference' => 'DEMO-BLY-005',
                'title' => 'Street-Facing Commercial Office Suite near Civil Lines',
                'category' => 'commercial', 'type' => 'Commercial Office', 'listing_type' => 'lease',
                'price' => 48000, 'bedrooms' => null, 'bathrooms' => 1, 'area' => 920, 'area_unit' => 'sqft',
                'address' => 'Illustrative commercial address near Civil Lines',
                'postal_code' => '243001', 'latitude' => 28.3658, 'longitude' => 79.4322,
                'featured' => true, 'focus_keyword' => 'commercial office space for lease Civil Lines Bareilly',
                'meta_title' => 'Commercial Office Suite near Civil Lines, Bareilly',
                'meta_description' => 'Illustrative office lease listing near Civil Lines with sample floor area and business-use considerations.',
                'description' => 'A sample commercial office layout with an open work area, a private cabin and a reception point. A prospective occupier must independently confirm the building use permission, signage rules, access hours, power load, common-area charges and the final lease terms.',
                'features' => ['Street-facing frontage', 'Reception area', 'Private cabin', 'Washroom access', 'Power backup provision'],
                'accommodations' => [
                    ['name' => 'Open office floor', 'category' => 'Workspace', 'area' => 560, 'description' => 'Open-plan work area in the sample commercial layout.', 'attributes' => ['use' => 'Office']],
                    ['name' => 'Private cabin', 'category' => 'Workspace', 'area' => 135, 'description' => 'Enclosed cabin for meetings or focused work.', 'attributes' => ['use' => 'Meeting']],
                    ['name' => 'Reception zone', 'category' => 'Workspace', 'area' => 95, 'description' => 'Sample reception and visitor waiting point.', 'attributes' => ['use' => 'Reception']],
                ],
            ],
            [
                'slug' => 'demo-mahanagar-residential-plot',
                'reference' => 'DEMO-BLY-006',
                'title' => 'Residential Plot Example in Mahanagar',
                'category' => 'residential-plot', 'type' => 'Residential Plot', 'listing_type' => 'sale',
                'price' => 4650000, 'bedrooms' => null, 'bathrooms' => null, 'area' => 1800, 'area_unit' => 'sqft',
                'address' => 'Illustrative residential plot location in Mahanagar',
                'postal_code' => '243122', 'latitude' => 28.3900, 'longitude' => 79.4410,
                'featured' => false, 'focus_keyword' => 'residential plot for sale Mahanagar Bareilly',
                'meta_title' => 'Residential Plot Example in Mahanagar, Bareilly',
                'meta_description' => 'Sample residential land listing in Mahanagar with illustrative dimensions and due-diligence notes.',
                'description' => 'A sample residential land parcel represented with an illustrative area and frontage. No boundary, title, land-use classification, road width or development permission is verified; these must be established from current official records and a site survey before any transaction.',
                'features' => ['Illustrative rectangular plot', 'Road access to be verified', 'Utility connections to be verified', 'Residential use subject to approval'],
                'accommodations' => [
                    ['name' => 'Illustrative plot area', 'category' => 'Land', 'area' => 1800, 'description' => 'Sample plot area only; not a surveyed measurement.', 'attributes' => ['dimensions' => '30 ft × 60 ft illustrative']],
                    ['name' => 'Frontage', 'category' => 'Land', 'area' => null, 'description' => 'Road frontage and legal access must be verified at site.', 'attributes' => ['verification' => 'Required']],
                    ['name' => 'Utility availability', 'category' => 'Services', 'area' => null, 'description' => 'Water, sewerage and electricity availability are not confirmed.', 'attributes' => ['verification' => 'Required']],
                ],
            ],
            [
                'slug' => 'demo-izatnagar-three-bedroom-builder-floor',
                'reference' => 'DEMO-BLY-007',
                'title' => '3-Bedroom Builder Floor for Rent in Izatnagar',
                'category' => 'builder-floor', 'type' => 'Builder Floor', 'listing_type' => 'rent',
                'price' => 18500, 'bedrooms' => 3, 'bathrooms' => 2, 'area' => 1380, 'area_unit' => 'sqft',
                'address' => 'Illustrative builder-floor address in Izatnagar',
                'postal_code' => '243122', 'latitude' => 28.3790, 'longitude' => 79.4140,
                'featured' => false, 'focus_keyword' => 'builder floor for rent Izatnagar Bareilly',
                'meta_title' => '3-Bedroom Builder Floor for Rent, Izatnagar',
                'meta_description' => 'Illustrative three-bedroom builder-floor rental in Izatnagar, Bareilly, with sample facilities.',
                'description' => 'A sample low-rise floor residence with three bedrooms, a shared stair lobby and a separate kitchen. Before renting, occupants should confirm which areas are private or shared, water arrangements, parking allocation, maintenance responsibilities and the landlord’s authority to let.',
                'features' => ['Low-rise building', 'Separate kitchen', 'Balcony', 'Shared stair access', 'Two-wheeler parking'],
                'accommodations' => [
                    ['name' => 'Living and dining', 'category' => 'Living', 'area' => 245, 'description' => 'Combined living and dining zone in the sample floor plan.', 'attributes' => ['floor' => 'Upper floor']],
                    ['name' => 'Kitchen', 'category' => 'Kitchen', 'area' => 105, 'description' => 'Separate kitchen with storage provision.', 'attributes' => ['layout' => 'Closed']],
                    ['name' => 'Shared stair lobby', 'category' => 'Common area', 'area' => null, 'description' => 'Common access area; use and maintenance rules should be confirmed.', 'attributes' => ['ownership' => 'Shared']],
                ],
            ],
            [
                'slug' => 'demo-civil-lines-luxury-penthouse',
                'reference' => 'DEMO-BLY-008',
                'title' => 'Luxury 4-Bedroom Penthouse near Civil Lines',
                'category' => 'penthouse', 'type' => 'Penthouse', 'listing_type' => 'sale',
                'price' => 22400000, 'bedrooms' => 4, 'bathrooms' => 4, 'area' => 3120, 'area_unit' => 'sqft',
                'address' => 'Illustrative penthouse address near Civil Lines',
                'postal_code' => '243001', 'latitude' => 28.3690, 'longitude' => 79.4340,
                'featured' => true, 'focus_keyword' => 'luxury penthouse for sale Civil Lines Bareilly',
                'meta_title' => 'Luxury Penthouse near Civil Lines, Bareilly',
                'meta_description' => 'Sample luxury penthouse listing with terrace, four bedrooms and illustrative area details.',
                'description' => 'A premium sample residence with four bedrooms, a large entertaining space and a private-use terrace concept. Terrace title or exclusive-use rights, waterproofing, lift access, fire safety, society rules and recurring maintenance charges require independent confirmation.',
                'features' => ['Private-use terrace concept', 'Lift access', 'Covered parking', 'Large living area', 'Power backup provision'],
                'accommodations' => [
                    ['name' => 'Entertaining lounge', 'category' => 'Living', 'area' => 440, 'description' => 'Large sample lounge for family gatherings.', 'attributes' => ['layout' => 'Open plan']],
                    ['name' => 'Terrace', 'category' => 'Outdoor', 'area' => 520, 'description' => 'Illustrative terrace area; legal access rights are not verified.', 'attributes' => ['access' => 'Subject to documents']],
                    ['name' => 'Primary suite', 'category' => 'Bedroom', 'area' => 280, 'description' => 'Sample primary suite with dressing and bath zones.', 'attributes' => ['bathroom' => 'Attached']],
                ],
            ],
            [
                'slug' => 'demo-bareilly-outskirts-farmhouse-lease',
                'reference' => 'DEMO-BLY-009',
                'title' => 'Open-Setting Farmhouse Property for Lease near Bareilly',
                'category' => 'farmhouse', 'type' => 'Farmhouse', 'listing_type' => 'lease',
                'price' => 95000, 'bedrooms' => 3, 'bathrooms' => 3, 'area' => 1.25, 'area_unit' => 'acre',
                'address' => 'Illustrative outskirts location near Bareilly; exact site not specified',
                'postal_code' => '243006', 'latitude' => 28.4100, 'longitude' => 79.4800,
                'featured' => false, 'focus_keyword' => 'farmhouse property lease near Bareilly',
                'meta_title' => 'Farmhouse Property Lease near Bareilly',
                'meta_description' => 'Illustrative farmhouse lease example near Bareilly; land classification and permitted use require verification.',
                'description' => 'A fictional larger-plot farmhouse example for demonstrating land and building details. The area is illustrative and does not establish agricultural or residential classification, ownership, access, water rights, construction permission or lawful commercial use.',
                'features' => ['Larger open plot concept', 'Three-bedroom dwelling', 'Outdoor sitting area', 'On-site parking concept'],
                'accommodations' => [
                    ['name' => 'Main dwelling', 'category' => 'Building', 'area' => 2100, 'description' => 'Sample built-up dwelling area; not a measured building survey.', 'attributes' => ['bedrooms' => 3]],
                    ['name' => 'Open land parcel', 'category' => 'Land', 'area' => 1.25, 'description' => 'Illustrative land extent in acres; title and classification are unverified.', 'attributes' => ['area_unit' => 'acre']],
                    ['name' => 'Outdoor sitting area', 'category' => 'Outdoor', 'area' => 240, 'description' => 'Sample outdoor seating zone.', 'attributes' => ['use' => 'Recreation']],
                ],
            ],
            [
                'slug' => 'demo-mahanagar-two-bedroom-apartment-sale',
                'reference' => 'DEMO-BLY-010',
                'title' => 'Well-Planned 2-Bedroom Apartment in Mahanagar',
                'category' => 'apartment', 'type' => 'Apartment', 'listing_type' => 'sale',
                'price' => 5900000, 'bedrooms' => 2, 'bathrooms' => 2, 'area' => 1195, 'area_unit' => 'sqft',
                'address' => 'Illustrative apartment address in Mahanagar',
                'postal_code' => '243122', 'latitude' => 28.3880, 'longitude' => 79.4440,
                'featured' => false, 'focus_keyword' => '2 bedroom apartment for sale Mahanagar Bareilly',
                'meta_title' => '2-Bedroom Apartment in Mahanagar, Bareilly',
                'meta_description' => 'Sample two-bedroom apartment for sale in Mahanagar with illustrative price and specifications.',
                'description' => 'A compact apartment example designed around a bright living space, two bedrooms and a practical kitchen. The sample listing demonstrates how buyers can compare carpet area with super built-up area, monthly maintenance, parking rights and the condition of common facilities.',
                'features' => ['Lift access', 'Balcony', 'Community water storage', 'Two-wheeler parking'],
                'accommodations' => [
                    ['name' => 'Living room', 'category' => 'Living', 'area' => 205, 'description' => 'Sample living room with balcony access.', 'attributes' => ['layout' => 'Open']],
                    ['name' => 'Kitchen', 'category' => 'Kitchen', 'area' => 90, 'description' => 'Compact kitchen with counter and storage provision.', 'attributes' => ['layout' => 'Parallel']],
                    ['name' => 'Second bedroom', 'category' => 'Bedroom', 'area' => 125, 'description' => 'Flexible second bedroom for family or work use.', 'attributes' => ['use' => 'Flexible']],
                ],
            ],
            [
                'slug' => 'demo-pilibhit-bypass-commercial-retail-unit',
                'reference' => 'DEMO-BLY-011',
                'title' => 'Ground-Floor Retail Unit near Pilibhit Bypass',
                'category' => 'commercial', 'type' => 'Commercial Retail', 'listing_type' => 'sale',
                'price' => 7350000, 'bedrooms' => null, 'bathrooms' => 1, 'area' => 680, 'area_unit' => 'sqft',
                'address' => 'Illustrative retail-unit address near Pilibhit Bypass',
                'postal_code' => '243006', 'latitude' => 28.3970, 'longitude' => 79.4580,
                'featured' => false, 'focus_keyword' => 'commercial retail unit for sale Pilibhit Bypass Bareilly',
                'meta_title' => 'Ground-Floor Retail Unit, Pilibhit Bypass',
                'meta_description' => 'Illustrative commercial retail unit near Pilibhit Bypass, Bareilly, with sample frontage and area.',
                'description' => 'A sample ground-floor retail space with a display frontage, open sales floor and rear utility zone. Any real buyer or operator must check sanctioned commercial use, frontage and access rights, loading restrictions, signage permissions, association rules and applicable taxes.',
                'features' => ['Ground-floor access', 'Display frontage concept', 'Rear utility zone', 'Customer entry'],
                'accommodations' => [
                    ['name' => 'Retail sales floor', 'category' => 'Workspace', 'area' => 480, 'description' => 'Open customer-facing retail floor in the sample plan.', 'attributes' => ['use' => 'Retail']],
                    ['name' => 'Rear utility area', 'category' => 'Services', 'area' => 85, 'description' => 'Small rear utility and storage area.', 'attributes' => ['use' => 'Storage']],
                    ['name' => 'Display frontage', 'category' => 'Frontage', 'area' => null, 'description' => 'Shopfront dimensions and signage rights require verification.', 'attributes' => ['verification' => 'Required']],
                ],
            ],
            [
                'slug' => 'demo-bareilly-cantt-independent-house-rental',
                'reference' => 'DEMO-BLY-012',
                'title' => 'Independent 3-Bedroom House for Rent near Bareilly Cantt',
                'category' => 'independent-house', 'type' => 'Independent House', 'listing_type' => 'rent',
                'price' => 26000, 'bedrooms' => 3, 'bathrooms' => 2, 'area' => 1680, 'area_unit' => 'sqft',
                'address' => 'Illustrative residential address near Bareilly Cantt; jurisdiction to be checked',
                'postal_code' => '243001', 'latitude' => 28.3450, 'longitude' => 79.4200,
                'featured' => false, 'focus_keyword' => 'independent house for rent Bareilly Cantt',
                'meta_title' => '3-Bedroom Independent House for Rent, Bareilly',
                'meta_description' => 'Sample independent house rental near Bareilly Cantt with illustrative rent, area and room details.',
                'description' => 'A single-family rental example with three bedrooms, a separate kitchen, a small courtyard and independent entry. The exact jurisdiction, landlord authority, access conditions, utility arrangements, security deposit and tenancy restrictions must be checked for any real property in this broad area.',
                'features' => ['Independent entry', 'Small courtyard', 'Separate kitchen', 'Two-wheeler parking'],
                'accommodations' => [
                    ['name' => 'Living room', 'category' => 'Living', 'area' => 230, 'description' => 'Main family living space in the sample plan.', 'attributes' => ['floor' => 'Ground']],
                    ['name' => 'Courtyard', 'category' => 'Outdoor', 'area' => 110, 'description' => 'Illustrative open courtyard; dimensions are not surveyed.', 'attributes' => ['use' => 'Outdoor']],
                    ['name' => 'Kitchen', 'category' => 'Kitchen', 'area' => 100, 'description' => 'Separate kitchen with utility access.', 'attributes' => ['layout' => 'Closed']],
                ],
            ],
        ];
    }
}
