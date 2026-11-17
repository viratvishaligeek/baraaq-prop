<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Property;
use App\Services\Homepage\SectionDataResolver;
use App\Http\Controllers\Frontend\PropertyController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class Home3PropertySearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_three_map_uses_only_published_properties_with_real_coordinates(): void
    {
        $homepage = Homepage::create([
            'name' => 'Home 3 map test',
            'slug' => 'home-3-map-test',
            'design_key' => 'home-3',
            'status' => 'draft',
            'is_default' => false,
        ]);

        $section = HomepageSection::create([
            'homepage_id' => $homepage->id,
            'instance_key' => (string) Str::uuid(),
            'section_key' => 'hero.home-3',
            'section_type' => 'hero',
            'source_design' => 'home-3',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => ['map_card_limit' => 4],
        ]);

        $this->createProperty('Mapped property one', [
            'latitude' => 28.6139,
            'longitude' => 77.2090,
        ]);
        $this->createProperty('Mapped property two', [
            'latitude' => 28.7041,
            'longitude' => 77.1025,
        ]);
        $this->createProperty('Property without coordinates', [
            'latitude' => null,
            'longitude' => null,
        ]);
        $this->createProperty('Unpublished mapped property', [
            'latitude' => 28.5355,
            'longitude' => 77.3910,
            'status' => 'draft',
            'published_at' => null,
        ]);

        $data = app(SectionDataResolver::class)->resolve($section, $section->config);

        $this->assertCount(2, $data['mapProperties']);
        $this->assertSame(
            ['Mapped property one', 'Mapped property two'],
            $data['mapProperties']->pluck('title')->sort()->values()->all()
        );
        $this->assertEqualsWithDelta(28.6139, (float) $data['mapBounds']['min_lat'], 0.0001);
        $this->assertEqualsWithDelta(28.7041, (float) $data['mapBounds']['max_lat'], 0.0001);
    }

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
            'category' => $category->id,
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
