<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'listing_type' => ['nullable', 'in:sale,rent,lease'],
            'status' => ['nullable', 'in:sale,rent'],
            'property_type' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:180'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'max_bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'min_area' => ['nullable', 'numeric', 'min:0'],
            'mini_area' => ['nullable', 'numeric', 'min:0'],
            'max_area' => ['nullable', 'numeric', 'min:0'],
        ]);

        $query = Property::published()->with(['category:id,name', 'images']);

        if (! empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(function ($builder) use ($term) {
                $builder->where('title', 'like', '%' . $term . '%')
                    ->orWhere('reference', 'like', '%' . $term . '%')
                    ->orWhere('address', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');
            });
        }

        $city = $filters['city'] ?? $filters['location'] ?? null;
        if ($city !== null && $city !== '') {
            $query->where('city', 'like', '%' . trim($city) . '%');
        }

        foreach (['state', 'postal_code', 'property_type'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        $listingType = $filters['listing_type'] ?? $filters['status'] ?? null;
        if ($listingType) {
            $query->where('listing_type', $listingType);
        }

        $categoryValue = $filters['category'] ?? $filters['category_id'] ?? null;
        if ($categoryValue) {
            $category = Category::where('type', 'property')
                ->where(function ($builder) use ($categoryValue) {
                    $builder->where('slug', $categoryValue);
                    if (is_numeric($categoryValue)) {
                        $builder->orWhereKey((int) $categoryValue);
                    }
                })->first();

            if ($category) {
                $query->where('category_id', $category->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        foreach (['min_price' => '>=', 'max_price' => '<=', 'min_bedrooms' => '>=', 'max_bedrooms' => '<='] as $field => $operator) {
            if (isset($filters[$field]) && $filters[$field] !== '') {
                $column = str_contains($field, 'bedrooms') ? 'bedrooms' : 'price';
                $query->where($column, $operator, $filters[$field]);
            }
        }

        $minArea = $filters['min_area'] ?? $filters['mini_area'] ?? null;
        if ($minArea !== null && $minArea !== '') {
            $query->where('area', '>=', $minArea);
        }
        if (isset($filters['max_area']) && $filters['max_area'] !== '') {
            $query->where('area', '<=', $filters['max_area']);
        }

        return view('frontend.properties.index', [
            'properties' => $query->latest('published_at')->paginate(12)->withQueryString(),
            'categories' => Category::query()
                ->where('type', 'property')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Property $property)
    {
        abort_unless(
            $property->status === 'published'
                && $property->published_at
                && $property->published_at->lte(now()),
            404
        );

        $property->load(['category', 'images', 'accommodations.imageMedia', 'faqs']);
        $relatedQuery = Property::published()->whereKeyNot($property->id);
        if (filled($property->city)) {
            $relatedQuery->where('city', $property->city);
        } elseif ($property->category_id) {
            $relatedQuery->where('category_id', $property->category_id);
        }
        $related = $relatedQuery->latest('published_at')->limit(3)->get();

        return view('frontend.properties.show', compact('property', 'related'));
    }
}
