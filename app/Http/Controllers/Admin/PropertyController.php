<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Support\HtmlSanitizer;
use App\Models\Category;
use App\Models\Media;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    private function authorizeAction(string $action): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isAdministrator() || $user->hasPermission('properties.manage') || $user->hasPermission('properties.' . $action)), 403);
    }

    private function slug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $i = 2;
        while (Property::withTrashed()->where('slug', $slug)->when($ignoreId, fn($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function index()
    {
        $this->authorizeAction('view');
        return view('admin.properties.index', ['properties' => Property::with('category:id,name')
            ->when(in_array(request('status'), ['draft', 'published', 'archived'], true), fn ($query) => $query->where('status', request('status')))
            ->when(request()->filled('property_type'), fn ($query) => $query->where('property_type', request('property_type')))
            ->when(request()->filled('feature'), fn ($query) => $query->whereJsonContains('features', request('feature')))
            ->latest()->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        $this->authorizeAction('create');
        return view('admin.properties.form', ['property' => new Property(), 'categories' => Category::where('type', 'property')->where('is_active', true)->orderBy('name')->get(), 'mediaItems' => Media::query()->where('mime_type', 'like', 'image/%')->latest('id')->limit(200)->get(['id', 'path', 'original_name', 'alt_text'])]);
    }


    private function resolveAccommodationImages(array $accommodations, StorePropertyRequest $request): array
    {
        foreach ($accommodations as $index => &$space) {
            if ($request->hasFile('accommodations.' . $index . '.image')) {
                $file = $request->file('accommodations.' . $index . '.image');
                $path = $file->store('properties/accommodations', 'public');
                $attributes = [
                    'uploaded_by' => $request->user()->id,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'alt_text' => $space['name'] ?? $file->getClientOriginalName(),
                ];
                foreach (['small', 'medium', 'large'] as $size) {
                    $variant = $request->file('accommodation_variants.' . $index . '.' . $size);
                    if ($variant) {
                        $attributes[$size . '_path'] = $variant->store('media/variants/' . $size, 'public');
                    }
                }
                $media = Media::create($attributes);
                $space['image_media_id'] = $media->id;
            }

            unset($space['image']);
        }
        unset($space);

        return array_values($accommodations);
    }

    public function store(StorePropertyRequest $request)
    {
        $this->authorizeAction('create');
        $data = $request->validated();
        $data['description'] = HtmlSanitizer::sanitize($data['description'] ?? '');
        foreach (['featured_image_media_id' => 'featured_image', 'og_image_media_id' => 'og_image'] as $mediaField => $field) {
            if (! empty($data[$mediaField]) && ! $request->hasFile($field)) {
                $data[$field] = Media::query()->whereKey((int) $data[$mediaField])->value('path');
            }
            unset($data[$mediaField]);
        }
        if (($data['status'] ?? null) === 'published') {
            $this->authorizeAction('publish');
        }
        $galleryMediaIds = $data['gallery_media_ids'] ?? [];
        $accommodations = array_map(fn ($space) => array_merge($space, ['is_included' => (bool) ($space['is_included'] ?? false)]), $data['accommodations'] ?? []);
        $accommodations = $this->resolveAccommodationImages($accommodations, $request);
        $faqs = $data['faqs'] ?? [];
        unset($data['gallery'], $data['gallery_media_ids'], $data['accommodations'], $data['accommodation_variants'], $data['faqs']);
        $data['slug'] = $this->slug($data['slug'] ?? $data['title']);
        $data['created_by'] = $request->user()->id;
        $data['is_featured'] = $request->boolean('is_featured');
        if ($request->hasFile('featured_image')) $data['featured_image'] = $request->file('featured_image')->store('properties', 'public');
        if ($request->hasFile('og_image')) $data['og_image'] = $request->file('og_image')->store('properties/seo', 'public');
        if ($data['status'] === 'published') $data['published_at'] = now();

        $property = DB::transaction(function () use ($data, $request, $galleryMediaIds, $accommodations, $faqs) {
            $created = Property::create($data);
            foreach ($request->file('gallery', []) as $file) $created->images()->create(['path' => $file->store('properties/gallery', 'public')]);
            foreach ($galleryMediaIds as $mediaId) {
                $media = Media::findOrFail((int) $mediaId);
                if (! $created->images()->where('path', $media->path)->exists()) {
                    $created->images()->create(['path' => $media->path]);
                }
            }
            $created->accommodations()->createMany($accommodations);
            $created->faqs()->createMany($faqs);
            return $created;
        });

        return redirect()->route('admin.properties.index')->with('success', 'Property created.');
    }

    public function edit(Property $property)
    {
        $this->authorizeAction('edit');
        return view('admin.properties.form', ['property' => $property->load(['images', 'accommodations', 'faqs']), 'categories' => Category::where('type', 'property')->where('is_active', true)->orderBy('name')->get(), 'mediaItems' => Media::query()->where('mime_type', 'like', 'image/%')->latest('id')->limit(200)->get(['id', 'path', 'original_name', 'alt_text'])]);
    }

    public function update(StorePropertyRequest $request, Property $property)
    {
        $this->authorizeAction('edit');
        $data = $request->validated();
        $data['description'] = HtmlSanitizer::sanitize($data['description'] ?? '');
        foreach (['featured_image_media_id' => 'featured_image', 'og_image_media_id' => 'og_image'] as $mediaField => $field) {
            if (! empty($data[$mediaField]) && ! $request->hasFile($field)) {
                $data[$field] = Media::query()->whereKey((int) $data[$mediaField])->value('path');
            }
            unset($data[$mediaField]);
        }
        if (($data['status'] ?? null) === 'published' && $property->status !== 'published') {
            $this->authorizeAction('publish');
        }
        $galleryMediaIds = $data['gallery_media_ids'] ?? [];
        $accommodations = $this->resolveAccommodationImages($data['accommodations'] ?? [], $request);
        $faqs = $data['faqs'] ?? [];
        unset($data['gallery'], $data['gallery_media_ids'], $data['accommodations'], $data['accommodation_variants'], $data['faqs']);
        $data['slug'] = $this->slug($data['slug'] ?? $data['title'], $property->id);
        $data['is_featured'] = $request->boolean('is_featured');
        $oldFeaturedImage = $property->featured_image;
        $oldOgImage = $property->og_image;
        if ($request->hasFile('featured_image')) $data['featured_image'] = $request->file('featured_image')->store('properties', 'public');
        if ($request->hasFile('og_image')) $data['og_image'] = $request->file('og_image')->store('properties/seo', 'public');
        if ($data['status'] === 'published' && !$property->published_at) $data['published_at'] = now();

        DB::transaction(function () use ($property, $data, $request, $galleryMediaIds, $accommodations, $faqs) {
            $property->update($data);
            foreach ($request->file('gallery', []) as $file) $property->images()->create(['path' => $file->store('properties/gallery', 'public')]);
            foreach ($galleryMediaIds as $mediaId) {
                $media = Media::findOrFail((int) $mediaId);
                if (! $property->images()->where('path', $media->path)->exists()) {
                    $property->images()->create(['path' => $media->path]);
                }
            }
            $property->accommodations()->delete();
            $property->faqs()->delete();
            $property->accommodations()->createMany($accommodations);
            $property->faqs()->createMany($faqs);
        });

        if ($oldFeaturedImage && $oldFeaturedImage !== ($data['featured_image'] ?? $oldFeaturedImage) && $property->images()->where('path', $oldFeaturedImage)->doesntExist() && ! Media::where('path', $oldFeaturedImage)->exists()) Storage::disk('public')->delete($oldFeaturedImage);
        if ($oldOgImage && $oldOgImage !== ($data['og_image'] ?? $oldOgImage) && ! Media::where('path', $oldOgImage)->exists()) Storage::disk('public')->delete($oldOgImage);

        return redirect()->route('admin.properties.index')->with('success', 'Property updated.');
    }

    public function destroy(Property $property)
    {
        $this->authorizeAction('delete');
        $property->delete();
        return back()->with('success', 'Property moved to trash.');
    }

    public function destroyImage(Property $property, PropertyImage $image)
    {
        $this->authorizeAction('delete');
        abort_unless($image->property_id === $property->id, 404);
        DB::transaction(function () use ($property, $image) {
            if ($property->featured_image === $image->path) $property->update(['featured_image' => null]);
            $path = $image->path;
            $image->delete();
            if (! Media::where('path', $path)->exists()) {
                Storage::disk('public')->delete($path);
            }
        });
        return back()->with('success', 'Gallery image deleted.');
    }

    public function reorderImages(Request $request, Property $property)
    {
        $this->authorizeAction('edit');
        $data = $request->validate(['gallery_order' => ['required', 'array', 'max:100'], 'gallery_order.*' => ['required', 'integer', 'distinct', Rule::exists('property_images', 'id')->where('property_id', $property->id)]]);
        DB::transaction(function () use ($data, $property) {
            foreach ($data['gallery_order'] as $order => $id) $property->images()->whereKey($id)->update(['sort_order' => $order]);
        });
        return back()->with('success', 'Gallery order saved.');
    }

    public function setFeaturedImage(Property $property, PropertyImage $image)
    {
        $this->authorizeAction('edit');
        abort_unless($image->property_id === $property->id, 404);
        $old = $property->featured_image;
        $property->update(['featured_image' => $image->path]);
        if ($old && $old !== $image->path && $property->images()->where('path', $old)->doesntExist() && ! Media::where('path', $old)->exists()) Storage::disk('public')->delete($old);
        return back()->with('success', 'Featured image updated.');
    }
}
