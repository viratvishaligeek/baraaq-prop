<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class PropertyTaxonomyController extends Controller
{
    private function authorizeView(): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isAdministrator() || $user->hasPermission('properties.manage') || $user->hasPermission('properties.view')), 403);
    }

    public function types()
    {
        $this->authorizeView();
        $types = Property::query()
            ->select('property_type', DB::raw('COUNT(*) as properties_count'))
            ->whereNotNull('property_type')
            ->where('property_type', '!=', '')
            ->groupBy('property_type')
            ->orderBy('property_type')
            ->get();

        return view('admin.properties.taxonomies', ['taxonomy' => 'types', 'items' => $types]);
    }

    public function amenities()
    {
        $this->authorizeView();
        $items = Property::query()->whereNotNull('features')->get(['id', 'features'])
            ->flatMap(fn (Property $property) => collect($property->features ?? [])->map(fn ($feature) => trim((string) $feature)))
            ->filter()
            ->countBy()
            ->sortKeys()
            ->map(fn ($count, $name) => (object) ['name' => $name, 'properties_count' => $count])
            ->values();

        return view('admin.properties.taxonomies', ['taxonomy' => 'amenities', 'items' => $items]);
    }
}
