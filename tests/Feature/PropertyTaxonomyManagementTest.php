<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyTaxonomyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_types_and_amenities_are_discoverable_and_filterable(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());
        Property::create([
            'title' => 'Garden villa',
            'slug' => 'garden-villa-taxonomy',
            'property_type' => 'Villa',
            'features' => ['Swimming pool', 'Private garden'],
            'status' => 'draft',
        ]);

        $this->actingAs($user)->get(route('admin.properties.types'))
            ->assertOk()
            ->assertSee('Villa');

        $this->actingAs($user)->get(route('admin.properties.amenities'))
            ->assertOk()
            ->assertSee('Swimming pool')
            ->assertSee('Private garden');

        $this->actingAs($user)->get(route('admin.properties.index', ['property_type' => 'Villa']))
            ->assertOk()
            ->assertSee('Garden villa');

        $this->actingAs($user)->get(route('admin.properties.index', ['feature' => 'Swimming pool']))
            ->assertOk()
            ->assertSee('Garden villa');
    }
}
