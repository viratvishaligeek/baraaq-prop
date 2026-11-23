<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\PropertyFaq;
use App\Models\PropertyImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoPropertySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_demo_seed_creates_complete_bareilly_property_inventory_and_valid_relationships(): void
    {
        Storage::fake('public');
        config(['app.env' => 'local', 'cms.demo_properties' => null]);

        $this->seed();

        $properties = Property::published()
            ->with(['category', 'images', 'accommodations', 'faqs'])
            ->get();

        $this->assertCount(12, $properties);
        $this->assertSame(4, $properties->where('is_featured', true)->count());
        $this->assertSame(['lease', 'rent', 'sale'], $properties->pluck('listing_type')->unique()->sort()->values()->all());

        foreach ($properties as $property) {
            $this->assertNotNull($property->category, $property->slug.' must reference a category.');
            $this->assertSame('property', $property->category->type);
            $this->assertSame('Bareilly', $property->city);
            $this->assertSame('Uttar Pradesh', $property->state);
            $this->assertSame('India', $property->country);
            $this->assertNotEmpty($property->description);
            $this->assertNotEmpty($property->meta_title);
            $this->assertNotEmpty($property->meta_description);
            $this->assertNotEmpty($property->features);
            $this->assertStringContainsString('fictional demonstration listing', strtolower(strip_tags($property->description)));
            $this->assertCount(2, $property->images, $property->slug.' should have two gallery images.');
            $this->assertCount(3, $property->accommodations, $property->slug.' should have three detail rows.');
            $this->assertCount(2, $property->faqs, $property->slug.' should have two property FAQs.');

            foreach ($property->images as $image) {
                $this->assertSame($property->id, $image->property_id);
                $this->assertStringContainsString('not an actual listing photograph', $image->alt_text);
                $this->assertTrue(Storage::disk('public')->exists($image->path));
            }

            foreach ($property->accommodations as $accommodation) {
                $this->assertSame($property->id, $accommodation->property_id);
                $this->assertNotEmpty($accommodation->name);
                $this->assertNotEmpty($accommodation->description);
            }

            foreach ($property->faqs as $faq) {
                $this->assertSame($property->id, $faq->property_id);
                $this->assertNotEmpty($faq->answer);
            }
        }

        $this->assertSame(24, PropertyImage::query()->count());
        $this->assertSame(36, PropertyAccommodation::query()->count());
        $this->assertSame(24, PropertyFaq::query()->count());\n        $this->assertSame(6, GalleryItem::published()->count());
        $this->assertSame([], DB::select('PRAGMA foreign_key_check'));

        $property = $properties->firstOrFail();
        $this->get(route('properties.index'))
            ->assertOk()
            ->assertSee($property->title);

        $this->get(route('gallery.index'))\n            ->assertOk()\n            ->assertSee('Illustrative Apartment Architecture — Demo Image');\n\n        $this->get(route('properties.show', $property))
            ->assertOk()
            ->assertSee($property->title)
            ->assertSee('About This Property')
            ->assertSee('fictional demonstration listing')
            ->assertSee('not an actual listing photograph');
    }

    public function test_demo_property_seed_is_disabled_in_production_without_explicit_opt_in(): void
    {
        Storage::fake('public');
        config(['app.env' => 'production', 'cms.demo_properties' => null]);

        $this->seed();

        $this->assertDatabaseCount('properties', 0);
        $this->assertDatabaseCount('property_images', 0);
        $this->assertDatabaseCount('property_accommodations', 0);
        $this->assertDatabaseCount('property_faqs', 0);
    }

    public function test_demo_property_seed_is_repeatable_and_preserves_existing_owner_content(): void
    {
        Storage::fake('public');
        config(['app.env' => 'local', 'cms.demo_properties' => null]);

        $this->seed();

        $property = Property::query()->where('slug', 'demo-civil-lines-three-bedroom-apartment')->firstOrFail();
        $property->update(['title' => 'Administrator-edited property title']);
        $property->accommodations()->firstOrFail()->update(['description' => 'Administrator-edited room details.']);

        $counts = [
            'properties' => Property::query()->count(),
            'images' => PropertyImage::query()->count(),
            'accommodations' => PropertyAccommodation::query()->count(),
            'faqs' => PropertyFaq::query()->count(),\n            'gallery' => GalleryItem::query()->count(),
        ];

        $this->seed();

        $this->assertSame('Administrator-edited property title', $property->fresh()->title);
        $this->assertSame('Administrator-edited room details.', $property->accommodations()->firstOrFail()->description);
        $this->assertSame($counts, [
            'properties' => Property::query()->count(),
            'images' => PropertyImage::query()->count(),
            'accommodations' => PropertyAccommodation::query()->count(),
            'faqs' => PropertyFaq::query()->count(),
        ]);
    }
}
