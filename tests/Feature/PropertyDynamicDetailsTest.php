<?php

namespace Tests\Feature;

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
}
