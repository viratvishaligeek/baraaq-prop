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
        $counts = [];
        foreach (DB::table('properties')->whereNotNull('features')->select('features')->cursor() as $row) {
            $features = is_array($row->features) ? $row->features : json_decode((string) $row->features, true);
            foreach (is_array($features) ? $features : [] as $feature) {
                $name = trim((string) $feature);
                if ($name !== '') $counts[$name] = ($counts[$name] ?? 0) + 1;
            }
        }
        ksort($counts);
        $items = collect($counts)->map(fn ($count, $name) => (object) ['name' => $name, 'properties_count' => $count])->values();

        return view('admin.properties.taxonomies', ['taxonomy' => 'amenities', 'items' => $items]);
    }
}
