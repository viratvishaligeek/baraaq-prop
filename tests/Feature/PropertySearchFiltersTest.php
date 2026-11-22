<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Property;
use App\Http\Controllers\Frontend\PropertyController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class PropertySearchFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_search_applies_category_location_listing_price_bedroom_and_area_filters(): void
    {
        $category = Category::create([
            'name' => 'Apartments',
            'slug' => 'apartments',
            'type' => 'property',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $matched = $this->createProperty('Meerut apartment', [
            'category_id' => $category->id,
            'city' => 'Meerut',
            'listing_type' => 'sale',
            'price' => 5000000,
            'bedrooms' => 3,
            'area' => 1500,
        ]);

        $this->createProperty('Meerut rental', [
            'category_id' => $category->id,
            'city' => 'Meerut',
            'listing_type' => 'rent',
            'price' => 25000,
            'bedrooms' => 2,
            'area' => 900,
        ]);

        $response = app(PropertyController::class)->index(Request::create('/properties', 'GET', [
            'q' => 'apartment',
            'city' => 'Meerut',
            'listing_type' => 'sale',
            'category' => (string) $category->id,
            'min_price' => 4000000,
            'max_price' => 6000000,
            'min_bedrooms' => 2,
            'max_bedrooms' => 4,
            'min_area' => 1000,
            'max_area' => 2000,
        ]));

        $results = $response->getData()['properties']->getCollection();

        $this->assertCount(1, $results);
        $this->assertSame($matched->id, $results->first()->id);
    }

    public function test_property_category_navigation_accepts_a_category_slug(): void
    {
        $category = Category::create([
            'name' => 'Villas',
            'slug' => 'villas',
            'type' => 'property',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $matching = $this->createProperty('Published villa', ['category_id' => $category->id]);
        $this->createProperty('Uncategorized listing');

        $response = app(PropertyController::class)->index(Request::create('/properties', 'GET', [
            'category' => 'villas',
        ]));

        $results = $response->getData()['properties']->getCollection();

        $this->assertCount(1, $results);
        $this->assertSame($matching->id, $results->first()->id);
    }

    private function createProperty(string $title, array $overrides = []): Property
    {
        return Property::create(array_merge([
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'property_type' => 'Apartment',
            'listing_type' => 'sale',
            'city' => 'Meerut',
            'state' => 'Uttar Pradesh',
            'price' => 5000000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area' => 1500,
            'status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }
}
