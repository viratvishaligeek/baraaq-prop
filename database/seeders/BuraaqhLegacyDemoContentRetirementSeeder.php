<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Comment;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class BuraaqhLegacyDemoContentRetirementSeeder extends Seeder
{
    public function run(): void
    {
        // These exact identifiers belong to the former template/demo content.
        // Retire their public visibility without deleting records, files or copy.
        Property::query()->whereIn('slug', [
            'the-oakwood-residences',
            'cedar-grove-villa',
            'the-aranya-apartments',
            'palm-court-independent-home',
            'the-skyline-penthouse',
            'maple-leaf-builder-floor',
            'the-courtyard-house',
            'riverside-commercial-suites',
            'meadow-view-farmhouse',
            'the-meridian-apartments',
            'willow-creek-residence',
            'orchid-business-plaza',
            'silver-oak-residential-plot',
            'lakeview-family-apartment',
            'the-heritage-bungalow',
            'studio-one-business-loft',
        ])->update([
            'status' => 'draft',
            'is_featured' => false,
            'published_at' => null,
        ]);

        Service::query()->whereIn('slug', [
            'property-valuation',
            'legal-assistance',
            'mortgage-services',
            'insurance-solutions',
            'home-inspection',
            'escrow-transaction-support',
            'property-management',
            'real-estate-consulting',
        ])->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        GalleryItem::query()->whereIn('slug', [
            'bareilly-property-gallery-1',
            'bareilly-property-gallery-2',
            'bareilly-property-gallery-3',
            'bareilly-property-gallery-4',
        ])->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        Banner::query()->whereIn('title', [
            'Discover a place that feels like home',
            'Make your next move with confidence',
            'Find the right space for your business',
        ])->update(['is_active' => false]);

        TeamMember::query()->where('email', 'like', '%@pillarrealty.example')
            ->update(['status' => 'draft', 'is_active' => false]);

        Page::query()->whereIn('slug', ['buy-with-pillar', 'sell-with-pillar'])
            ->update(['status' => 'draft']);

        Post::query()->where('slug', 'planning-a-delhi-ncr-move')
            ->update(['status' => 'draft', 'published_at' => null]);

        Testimonial::query()->whereIn('name', [
            'Julia Era',
            'Carlos Bauch',
            'Martha Collins',
            'Daniel Foster',
            'Sophia Bennett',
            'Oliver Hayes',
            'Aisha Khan',
            'Noah Williams',
        ])->update(['status' => 'draft', 'is_active' => false]);

        Comment::query()->whereIn('email', [
            'nisha@example.test',
            'rahul@example.test',
            'priya@example.test',
            'arjun@example.test',
            'kavya@example.test',
            'manav@example.test',
            'tara@example.test',
            'neil@example.test',
            'ritika@example.test',
            'aditya@example.test',
        ])->update(['status' => 'pending']);

        // Enquiries, newsletter subscribers, service images and media files are
        // intentionally untouched. They may contain operational or shared data.
    }
}
