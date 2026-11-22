<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Models\Category;
use App\Models\Media;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Support\HtmlSanitizer;
use App\Support\MediaReferenceChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class PropertyController extends Controller
{
    private function authorizeAction(string $action): void
    {
        $user = request()->user();
        abort_unless(
            $user && ($user->isAdministrator()
                || $user->hasPermission('properties.manage')
                || $user->hasPermission('properties.'.$action)),
            403
        );
    }

    private function slug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $suffix = 2;

        while (Property::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    public function index()
    {
        $this->authorizeAction('view');

        return view('admin.properties.index', [
            'properties' => Property::with('category:id,name')
                ->when(
                    in_array(request('status'), ['draft', 'published', 'archived'], true),
                    fn ($query) => $query->where('status', request('status'))
                )
                ->when(request()->filled('property_type'), fn ($query) => $query->where('property_type', request('property_type')))
                ->when(request()->filled('feature'), fn ($query) => $query->whereJsonContains('features', request('feature')))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        $this->authorizeAction('create');

        return view('admin.properties.form', [
            'property' => new Property(),
            'categories' => Category::where('type', 'property')->where('is_active', true)->orderBy('name')->get(),
            'mediaItems' => Media::query()->where('mime_type', 'like', 'image/%')->latest('id')->limit(200)->get(['id', 'path', 'original_name', 'alt_text']),
        ]);
    }

    private function storeMediaUpload(
        Request $request,
        $file,
        string $directory,
        string $altText,
        array &$uploadedPaths,
        array $variants = []
    ): Media {
        $path = $file->store($directory, 'public');
        if (! $path) {
            throw new RuntimeException('An uploaded property image could not be stored.');
        }

        $uploadedPaths[] = $path;
        $attributes = [
            'uploaded_by' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $altText,
        ];

        foreach (['small', 'medium', 'large'] as $size) {
            $variant = $variants[$size] ?? null;
            if (! $variant) {
                continue;
            }

            $variantPath = $variant->store('media/variants/'.$size, 'public');
            if (! $variantPath) {
                throw new RuntimeException('A responsive property image variant could not be stored.');
            }

            $uploadedPaths[] = $variantPath;
            $attributes[$size.'_path'] = $variantPath;
        }

        return Media::create($attributes);
    }

    private function resolveAccommodationImages(array $accommodations, Request $request, array &$uploadedPaths): array
    {
        foreach ($accommodations as $index => &$space) {
            $image = $request->file('accommodations.'.$index.'.image');

            if ($image) {
                $variants = [];
                foreach (['small', 'medium', 'large'] as $size) {
                    $variants[$size] = $request->file('accommodation_variants.'.$index.'.'.$size);
                }

                $media = $this->storeMediaUpload(
                    $request,
                    $image,
                    'properties/accommodations',
                    (string) ($space['name'] ?? $image->getClientOriginalName()),
                    $uploadedPaths,
                    $variants
                );
                $space['image_media_id'] = $media->id;
            }

            unset($space['image']);
            $space['sort_order'] = $index;
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
        $accommodationInput = array_map(
            fn ($space) => array_merge($space, ['is_included' => (bool) ($space['is_included'] ?? false)]),
            $data['accommodations'] ?? []
        );
        $faqs = array_values($data['faqs'] ?? []);
        foreach ($faqs as $sortOrder => &$faq) {
            $faq['sort_order'] = $sortOrder;
        }
        unset($faq);

        unset(
            $data['gallery'],
            $data['gallery_media_ids'],
            $data['accommodations'],
            $data['accommodation_variants'],
            $data['faqs']
        );

        $data['slug'] = $this->slug($data['slug'] ?? $data['title']);
        $data['created_by'] = $request->user()->id;
        $data['is_featured'] = $request->boolean('is_featured');
        $uploadedPaths = [];

        try {
            $property = DB::transaction(function () use ($data, $request, $galleryMediaIds, $accommodationInput, $faqs, &$uploadedPaths): Property {
                if ($request->hasFile('featured_image')) {
                    $media = $this->storeMediaUpload(
                        $request,
                        $request->file('featured_image'),
                        'properties',
                        (string) $data['title'],
                        $uploadedPaths
                    );
                    $data['featured_image'] = $media->path;
                }

                if ($request->hasFile('og_image')) {
                    $media = $this->storeMediaUpload(
                        $request,
                        $request->file('og_image'),
                        'properties/seo',
                        (string) $data['title'].' social image',
                        $uploadedPaths
                    );
                    $data['og_image'] = $media->path;
                }

                if (($data['status'] ?? null) === 'published') {
                    $data['published_at'] = now();
                }

                $created = Property::create($data);

                foreach ($request->file('gallery', []) as $index => $file) {
                    $media = $this->storeMediaUpload(
                        $request,
                        $file,
                        'properties/gallery',
                        (string) $created->title,
                        $uploadedPaths
                    );
                    $created->images()->create(['path' => $media->path, 'sort_order' => $index]);
                }

                foreach ($galleryMediaIds as $mediaId) {
                    $media = Media::findOrFail((int) $mediaId);
                    if (! $created->images()->where('path', $media->path)->exists()) {
                        $created->images()->create(['path' => $media->path, 'sort_order' => (int) $created->images()->max('sort_order') + 1]);
                    }
                }

                $accommodations = $this->resolveAccommodationImages($accommodationInput, $request, $uploadedPaths);
                $created->accommodations()->createMany($accommodations);
                $created->faqs()->createMany($faqs);

                return $created;
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

        return redirect()->route('admin.properties.index')->with('success', 'Property created.');
    }

    public function edit(Property $property)
    {
        $this->authorizeAction('edit');

        return view('admin.properties.form', [
            'property' => $property->load(['images', 'accommodations', 'faqs']),
            'categories' => Category::where('type', 'property')->where('is_active', true)->orderBy('name')->get(),
            'mediaItems' => Media::query()->where('mime_type', 'like', 'image/%')->latest('id')->limit(200)->get(['id', 'path', 'original_name', 'alt_text']),
        ]);
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
        $accommodationInput = array_map(
            fn ($space) => array_merge($space, ['is_included' => (bool) ($space['is_included'] ?? false)]),
            $data['accommodations'] ?? []
        );
        $faqs = array_values($data['faqs'] ?? []);
        foreach ($faqs as $sortOrder => &$faq) {
            $faq['sort_order'] = $sortOrder;
        }
        unset($faq);

        unset(
            $data['gallery'],
            $data['gallery_media_ids'],
            $data['accommodations'],
            $data['accommodation_variants'],
            $data['faqs']
        );

        $data['slug'] = $this->slug($data['slug'] ?? $data['title'], $property->id);
        $data['is_featured'] = $request->boolean('is_featured');
        $oldFeaturedImage = $property->featured_image;
        $oldOgImage = $property->og_image;
        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($property, $data, $request, $galleryMediaIds, $accommodationInput, $faqs, &$uploadedPaths): void {
                if ($request->hasFile('featured_image')) {
                    $media = $this->storeMediaUpload(
                        $request,
                        $request->file('featured_image'),
                        'properties',
                        (string) $data['title'],
                        $uploadedPaths
                    );
                    $data['featured_image'] = $media->path;
                }

                if ($request->hasFile('og_image')) {
                    $media = $this->storeMediaUpload(
                        $request,
                        $request->file('og_image'),
                        'properties/seo',
                        (string) $data['title'].' social image',
                        $uploadedPaths
                    );
                    $data['og_image'] = $media->path;
                }

                if (($data['status'] ?? null) === 'published' && ! $property->published_at) {
                    $data['published_at'] = now();
                }

                $property->update($data);

                foreach ($request->file('gallery', []) as $file) {
                    $media = $this->storeMediaUpload(
                        $request,
                        $file,
                        'properties/gallery',
                        (string) $property->title,
                        $uploadedPaths
                    );
                    $property->images()->create(['path' => $media->path, 'sort_order' => (int) $property->images()->max('sort_order') + 1]);
                }

                foreach ($galleryMediaIds as $mediaId) {
                    $media = Media::findOrFail((int) $mediaId);
                    if (! $property->images()->where('path', $media->path)->exists()) {
                        $property->images()->create(['path' => $media->path, 'sort_order' => (int) $property->images()->max('sort_order') + 1]);
                    }
                }

                $property->accommodations()->delete();
                $property->faqs()->delete();

                $accommodations = $this->resolveAccommodationImages($accommodationInput, $request, $uploadedPaths);
                $property->accommodations()->createMany($accommodations);
                $property->faqs()->createMany($faqs);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

        $this->deletePropertyImageIfUnused($oldFeaturedImage);
        $this->deletePropertyImageIfUnused($oldOgImage);

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
        $path = $image->path;

        DB::transaction(function () use ($property, $image): void {
            if ($property->featured_image === $image->path) {
                $property->update(['featured_image' => null]);
            }
            $image->delete();
        });

        $this->deletePropertyImageIfUnused($path);

        return back()->with('success', 'Gallery image deleted.');
    }

    public function reorderImages(Request $request, Property $property)
    {
        $this->authorizeAction('edit');
        $data = $request->validate([
            'gallery_order' => ['required', 'array', 'max:100'],
            'gallery_order.*' => ['required', 'integer', 'distinct', Rule::exists('property_images', 'id')->where('property_id', $property->id)],
        ]);

        DB::transaction(function () use ($data, $property): void {
            foreach ($data['gallery_order'] as $order => $id) {
                $property->images()->whereKey($id)->update(['sort_order' => $order]);
            }
        });

        return back()->with('success', 'Gallery order saved.');
    }

    public function setFeaturedImage(Property $property, PropertyImage $image)
    {
        $this->authorizeAction('edit');
        abort_unless($image->property_id === $property->id, 404);
        $old = $property->featured_image;
        $property->update(['featured_image' => $image->path]);
        $this->deletePropertyImageIfUnused($old);

        return back()->with('success', 'Featured image updated.');
    }

    private function deletePropertyImageIfUnused(?string $path): void
    {
        if (! $path || Media::where('path', $path)->exists()) {
            return;
        }

        if (! app(MediaReferenceChecker::class)->isPathReferenced($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
