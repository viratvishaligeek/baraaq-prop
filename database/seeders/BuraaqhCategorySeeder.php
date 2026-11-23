<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BuraaqhCategorySeeder extends Seeder
{
    public function run(): void
    {
        $propertyCategories = [
            ['Apartment', 'apartment', 'Apartments for buyers comparing layout, building management, shared facilities and the costs of ownership.', 'property-item-icon-2-4.svg'],
            ['Independent House', 'independent-house', 'Independent homes for buyers assessing plot boundaries, construction, access, utilities and ongoing maintenance.', 'property-item-icon-2-2.svg'],
            ['Villa', 'villa', 'Villa-style homes where land ownership, built-up area, approvals and maintenance responsibilities deserve careful review.', 'property-item-icon-2-3.svg'],
            ['Builder Floor', 'builder-floor', 'Low-rise floor residences that should be assessed for access, common areas, parking arrangements and ownership documents.', 'property-item-icon-2-1.svg'],
            ['Residential Plot', 'residential-plot', 'Residential land options requiring careful checks of title, land use, access, dimensions and applicable approvals.', 'property-item-icon-2-2.svg'],
            ['Commercial', 'commercial', 'Commercial spaces for buyers and occupiers comparing permitted use, access, visibility, services and operating costs.', 'property-item-icon-2-1.svg'],
            ['Penthouse', 'penthouse', 'Top-floor residences where buyers should review terrace rights, waterproofing, building rules and shared-area responsibilities.', 'property-item-icon-2-3.svg'],
            ['Farmhouse', 'farmhouse', 'Out-of-city and larger-plot properties requiring additional checks of land classification, access, utilities and permitted use.', 'property-item-icon-2-2.svg'],
        ];

        foreach ($propertyCategories as $index => [$name, $slug, $description, $icon]) {
            $iconPath = '/theme-assets/img/icon/'.$icon;
            $category = $this->findOrCreate($slug, [
                'name' => $name,
                'description' => $description,
                'type' => 'property',
                'icon_path' => File::isFile(public_path(ltrim($iconPath, '/'))) ? $iconPath : null,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);

            if (! $category) {
                continue;
            }

            $missingDefaults = [];
            if (blank($category->description)) {
                $missingDefaults['description'] = $description;
            }
            if (blank($category->icon_path) && File::isFile(public_path(ltrim($iconPath, '/')))) {
                $missingDefaults['icon_path'] = $iconPath;
            }

            if ($missingDefaults !== []) {
                $category->forceFill($missingDefaults)->save();
            }
        }

        $postCategories = [
            ['Buying Guides', 'buying-guides', 'Practical checklists and explainers for people preparing to purchase property.'],
            ['Selling Guides', 'selling-guides', 'Preparation, documentation and communication guidance for property owners considering a sale.'],
            ['Market Insights', 'market-insights', 'Context for evaluating property options, local evidence and market information without unsupported forecasts.'],
            ['Property Tips', 'property-tips', 'Useful guidance on property inspections, ownership responsibilities and everyday decisions.'],
            ['Home Design', 'home-design', 'Ideas for evaluating layouts, storage, daylight, maintenance and the way a home is used.'],
        ];

        foreach ($postCategories as $index => [$name, $slug, $description]) {
            $category = $this->findOrCreate($slug, [
                'name' => $name,
                'description' => $description,
                'type' => 'post',
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);

            if ($category && blank($category->description)) {
                $category->forceFill(['description' => $description])->save();
            }
        }
    }

    private function findOrCreate(string $slug, array $attributes): ?Category
    {
        $existing = Category::withTrashed()->where('slug', $slug)->first();

        // A soft-deleted category is an administrator decision. Do not restore
        // it or attempt a duplicate insert against its unique slug.
        if ($existing?->trashed()) {
            return null;
        }

        return $existing ?? Category::query()->firstOrCreate(['slug' => $slug], $attributes);
    }
}
