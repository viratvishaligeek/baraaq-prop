<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyDynamicDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_can_store_unlimited_accommodation_entries_and_faqs(): void
    {
        $property = Property::create([
            'title' => 'Flexible home',
            'slug' => 'flexible-home',
            'status' => 'draft',
        ]);

        $spaces = [];
        for ($i = 1; $i <= 25; $i++) {
            $spaces[] = [
                'name' => 'Custom space '.$i,
                'category' => 'Custom',
                'area' => $i * 10,
                'area_unit' => 'sqft',
                'is_included' => true,
                'attributes' => ['finish' => 'premium'],
                'sort_order' => $i,
            ];
        }

        $property->accommodations()->createMany($spaces);
        $property->faqs()->createMany([
            ['question' => 'Is parking included?', 'answer' => 'Yes.', 'sort_order' => 1],
            ['question' => 'Can I visit?', 'answer' => 'Contact the agent.', 'sort_order' => 2],
        ]);

        $this->assertCount(25, $property->fresh()->accommodations);
        $this->assertCount(2, $property->fresh()->faqs);
        $this->assertSame('premium', $property->fresh()->accommodations->first()->attributes['finish']);
    }

    public function test_property_dynamic_details_are_cascaded_when_property_is_force_deleted(): void
    {
        $property = Property::create([
            'title' => 'Delete test',
            'slug' => 'delete-test-property',
            'status' => 'draft',
        ]);
        $property->accommodations()->create(['name' => 'Bedroom']);
        $property->faqs()->create(['question' => 'Question?', 'answer' => 'Answer.']);

        $property->forceDelete();

        $this->assertDatabaseCount('property_accommodations', 0);
        $this->assertDatabaseCount('property_faqs', 0);
    }
    public function test_public_property_page_renders_accommodation_and_faq_structured_data(): void
    {
        $property = Property::create([
            'title' => 'Family home',
            'slug' => 'family-home',
            'status' => 'published',
            'published_at' => now(),
            'description' => '<p>A welcoming family property.</p>',
        ]);
        $media = Media::create(['disk' => 'public', 'path' => 'properties/accommodations/master-bedroom.webp', 'original_name' => 'master-bedroom.webp', 'mime_type' => 'image/webp', 'size' => 2048]);
        $property->accommodations()->create([
            'image_media_id' => $media->id,
            'name' => 'Master bedroom',
            'category' => 'Bedroom',
            'area' => 240,
            'area_unit' => 'sqft',
            'is_included' => true,
        ]);
        $property->faqs()->create([
            'question' => 'Is parking included?',
            'answer' => 'Yes, one parking space is included.',
        ]);

        $this->get(route('properties.show', $property))
            ->assertOk()
            ->assertSee('Master bedroom')
            ->assertSee($media->url, false)
            ->assertSee('Is parking included?')
            ->assertSee('FAQPage')
            ->assertSee('RealEstateListing');
    }

}
