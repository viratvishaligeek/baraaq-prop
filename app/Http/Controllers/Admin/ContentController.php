<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Rules\SafeLink;
use App\Support\HtmlSanitizer;
use App\Support\MediaReferenceChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class ContentController extends Controller
{
    private array $types = [
        'posts' => Post::class,
        'pages' => Page::class,
        'categories' => Category::class,
        'banners' => Banner::class,
        'team' => TeamMember::class,
        'services' => Service::class,
        'testimonials' => Testimonial::class,
    ];

    private function model(string $type): string
    {
        abort_unless(isset($this->types[$type]), 404);

        $user = request()->user();
        $managePermission = $type.'.manage';
        $action = match (true) {
            request()->routeIs('admin.content.create', 'admin.content.store') => 'create',
            request()->routeIs('admin.content.edit', 'admin.content.update') => 'edit',
            request()->routeIs('admin.content.destroy') => 'delete',
            default => 'view',
        };

        abort_unless(
            $user->isAdministrator()
                || $user->hasPermission($managePermission)
                || $user->hasPermission($type.'.'.$action),
            403
        );

        return $this->types[$type];
    }

    private function authorizePublish(string $type, string $status, ?string $currentStatus = null): void
    {
        if ($status === 'published' && $currentStatus !== 'published') {
            $user = request()->user();
            abort_unless(
                $user->isAdministrator()
                    || $user->hasPermission($type.'.manage')
                    || $user->hasPermission($type.'.publish'),
                403
            );
        }
    }

    private function uniqueSlug(string $model, string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $suffix = 2;

        while ($model::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    public function index(string $type)
    {
        $model = $this->model($type);
        $filters = request()->validate([
            'q' => ['nullable', 'string', 'max:160'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'category_type' => ['nullable', Rule::in(['property', 'post'])],
        ]);

        $searchColumns = match ($type) {
            'posts', 'services' => ['title', 'slug', 'excerpt'],
            'pages' => ['title', 'slug'],
            'categories' => ['name', 'slug', 'description'],
            'banners' => ['title', 'subtitle', 'placement'],
            'team' => ['name', 'designation', 'email'],
            'testimonials' => ['name', 'designation', 'company', 'quote'],
            default => [],
        };

        $items = $model::query()
            ->when(
                ! empty($filters['q']),
                function ($query) use ($filters, $searchColumns): void {
                    $term = '%'.trim($filters['q']).'%';
                    $query->where(function ($search) use ($searchColumns, $term): void {
                        foreach ($searchColumns as $index => $column) {
                            $method = $index === 0 ? 'where' : 'orWhere';
                            $search->{$method}($column, 'like', $term);
                        }
                    });
                }
            )
            ->when(
                ! empty($filters['status']) && in_array($type, ['posts', 'pages', 'services', 'team', 'testimonials'], true),
                fn ($query) => $query->where('status', $filters['status'])
            )
            ->when(
                $type === 'categories' && ! empty($filters['category_type']),
                fn ($query) => $query->where('type', $filters['category_type'])
            )
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.content.index', compact('type', 'items'));
    }

    public function create(string $type)
    {
        $model = $this->model($type);

        return view('admin.content.form', [
            'type' => $type,
            'item' => new $model(),
            'categories' => $type === 'posts' ? Category::where('type', 'post')->orderBy('name')->get() : collect(),
            'tags' => $type === 'posts' ? Tag::orderBy('name')->get() : collect(),
            'selectedTagIds' => [],
            'parentCategories' => $type === 'categories' ? Category::orderBy('name')->get() : collect(),
            'mediaItems' => Media::where('mime_type', 'like', 'image/%')->latest()->limit(100)->get(['id', 'path', 'original_name']),
            'serviceFeatures' => collect(),
            'serviceImages' => collect(),
        ]);
    }

    public function store(Request $request, string $type)
    {
        $model = $this->model($type);
        $data = $this->validated($request, $type);

        if (isset($data['content']) && in_array($type, ['posts', 'pages', 'services'], true)) {
            $data['content'] = HtmlSanitizer::sanitize($data['content']);
        }

        $this->authorizePublish($type, $data['status'] ?? 'draft');
        $serviceFeatures = $type === 'services' ? $this->decodeServiceFeatures($data['features_json'] ?? null) : [];
        $insideImageMediaIds = $type === 'services' ? ($data['inside_image_media_ids'] ?? []) : [];
        unset($data['features_json'], $data['inside_images'], $data['inside_image_media_ids']);
        $insideImageFiles = $type === 'services' ? array_filter((array) $request->file('inside_images', [])) : [];
        $this->applyMediaSelections($data);
        $categoryIds = $data['category_ids'] ?? [];
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['category_ids'], $data['tag_ids']);

        if (isset($data['title']) && in_array($type, ['posts', 'pages', 'services'], true)) {
            $data['slug'] = $this->uniqueSlug($model, $data['slug'] ?? $data['title']);
        }

        if (isset($data['name']) && $type === 'categories') {
            $data['slug'] = $this->uniqueSlug($model, $data['slug'] ?? $data['name']);
        }

        if (in_array($type, ['posts', 'pages'], true)) {
            $data['author_id'] = $request->user()->id;
        }

        $this->decodeAndValidateSocialLinks($data, $type);
        $uploadedPaths = [];
        $uploadedMedia = [];

        try {
            foreach (['image', 'featured_image', 'photo', 'icon_path', 'og_image'] as $field) {
                if (! $request->hasFile($field)) {
                    continue;
                }

                $file = $request->file($field);
                $path = $file->store($type, 'public');
                if (! $path) {
                    throw new RuntimeException('The uploaded '.$field.' file could not be stored.');
                }

                $uploadedPaths[] = $path;
                $data[$field] = $path;
                $uploadedMedia[] = $this->mediaAttributes($request, $file, $path, $field, $data);
            }

            $insideImageRows = [];
            foreach ($insideImageFiles as $file) {
                $path = $file->store('services/inside', 'public');
                if (! $path) throw new RuntimeException('A service inside image could not be stored.');
                $uploadedPaths[] = $path;
                $insideImageRows[] = ['path' => $path, 'alt_text' => $data['title']];
                $uploadedMedia[] = $this->mediaAttributes($request, $file, $path, 'service_inside_image', $data);
            }
            foreach ($insideImageMediaIds as $mediaId) {
                $media = Media::query()->whereKey($mediaId)->where('mime_type', 'like', 'image/%')->firstOrFail();
                $insideImageRows[] = ['path' => $media->path, 'alt_text' => $media->alt_text ?: $data['title']];
            }

            if (in_array($type, ['posts', 'services'], true) && ($data['status'] ?? null) === 'published') {
                $data['published_at'] = now();
            }

            if (in_array($type, ['banners', 'team', 'categories', 'testimonials'], true)) {
                $data['is_active'] = $request->boolean('is_active');
            }

            DB::transaction(function () use ($model, $data, $type, $categoryIds, $tagIds, $uploadedMedia, $serviceFeatures, $insideImageRows): void {
                $created = $model::create($data);

                foreach ($uploadedMedia as $attributes) {
                    Media::create($attributes);
                }

                if ($type === 'services') {
                    foreach ($serviceFeatures as $index => $feature) {
                        if (! empty($feature['title'])) $created->features()->create(['title' => $feature['title'], 'description' => $feature['description'] ?? null, 'icon' => $feature['icon'] ?? null, 'sort_order' => $index + 1]);
                    }
                    foreach ($insideImageRows as $index => $image) $created->images()->create($image + ['sort_order' => $index + 1]);
                }

                if ($type === 'posts') {
                    $created->categories()->sync($categoryIds);
                    $created->tags()->sync($tagIds);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

        return redirect()->route('admin.content.index', $type)
            ->with('success', ucfirst($type).' item created.');
    }

    public function edit(string $type, int $id)
    {
        $model = $this->model($type);
        $item = $model::findOrFail($id);

        return view('admin.content.form', [
            'type' => $type,
            'item' => $item,
            'categories' => $type === 'posts' ? Category::where('type', 'post')->orderBy('name')->get() : collect(),
            'tags' => $type === 'posts' ? Tag::orderBy('name')->get() : collect(),
            'selectedTagIds' => $type === 'posts' ? $item->tags()->pluck('tags.id')->all() : [],
            'parentCategories' => $type === 'categories'
                ? Category::where('type', $item->type)->where('id', '!=', $item->id)->orderBy('name')->get()
                : collect(),
            'mediaItems' => Media::where('mime_type', 'like', 'image/%')->latest()->limit(100)->get(['id', 'path', 'original_name']),
            'serviceFeatures' => $type === 'services' ? $item->features()->get() : collect(),
            'serviceImages' => $type === 'services' ? $item->images()->get() : collect(),
        ]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $model = $this->model($type);
        $item = $model::findOrFail($id);
        $data = $this->validated($request, $type, $item->id);

        if (isset($data['content']) && in_array($type, ['posts', 'pages', 'services'], true)) {
            $data['content'] = HtmlSanitizer::sanitize($data['content']);
        }

        $this->authorizePublish($type, $data['status'] ?? $item->status, $item->status);
        $serviceFeatures = $type === 'services' ? $this->decodeServiceFeatures($data['features_json'] ?? null) : [];
        $insideImageMediaIds = $type === 'services' ? ($data['inside_image_media_ids'] ?? []) : [];
        unset($data['features_json'], $data['inside_images'], $data['inside_image_media_ids']);
        $insideImageFiles = $type === 'services' ? array_filter((array) $request->file('inside_images', [])) : [];
        $removeInsideImageIds = $type === 'services' ? ($data['remove_inside_images'] ?? []) : [];
        unset($data['remove_inside_images']);
        $this->applyMediaSelections($data);
        foreach (['image' => 'remove_image', 'featured_image' => 'remove_featured_image', 'photo' => 'remove_photo', 'icon_path' => 'remove_icon_path', 'og_image' => 'remove_og_image'] as $field => $removeInput) {
            $mediaInput = match ($field) { 'image' => 'image_media_id', 'featured_image' => 'featured_image_media_id', 'photo' => 'photo_media_id', 'icon_path' => 'icon_media_id', 'og_image' => 'og_image_media_id' };
            if ($request->boolean($removeInput) && ! $request->filled($mediaInput) && ! $request->hasFile($field)) $data[$field] = null;
            unset($data[$removeInput]);
        }
        $categoryIds = $data['category_ids'] ?? [];
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['category_ids'], $data['tag_ids']);

        if (isset($data['title']) && in_array($type, ['posts', 'pages', 'services'], true)) {
            $data['slug'] = $this->uniqueSlug($model, $data['slug'] ?? $data['title'], $item->id);
        }

        if (isset($data['name']) && $type === 'categories') {
            $data['slug'] = $this->uniqueSlug($model, $data['slug'] ?? $data['name'], $item->id);
        }

        $this->decodeAndValidateSocialLinks($data, $type);
        $oldFiles = [];
        $uploadedPaths = [];
        $uploadedMedia = [];

        foreach ([
            'image_media_id' => 'image',
            'featured_image_media_id' => 'featured_image',
            'photo_media_id' => 'photo',
            'icon_media_id' => 'icon_path',
            'og_image_media_id' => 'og_image',
        ] as $input => $field) {
            $removeInput = match ($field) { 'image' => 'remove_image', 'featured_image' => 'remove_featured_image', 'photo' => 'remove_photo', 'icon_path' => 'remove_icon_path', 'og_image' => 'remove_og_image' };
            if (($request->filled($input) || $request->boolean($removeInput)) && $item->$field && ! Media::where('path', $item->$field)->exists()) $oldFiles[] = $item->$field;
        }

        try {
            foreach (['image', 'featured_image', 'photo', 'icon_path', 'og_image'] as $field) {
                if (! $request->hasFile($field)) {
                    continue;
                }

                if ($item->$field && ! Media::where('path', $item->$field)->exists()) {
                    $oldFiles[] = $item->$field;
                }

                $file = $request->file($field);
                $path = $file->store($type, 'public');
                if (! $path) {
                    throw new RuntimeException('The uploaded '.$field.' file could not be stored.');
                }

                $uploadedPaths[] = $path;
                $data[$field] = $path;
                $uploadedMedia[] = $this->mediaAttributes($request, $file, $path, $field, $data);
            }

            $insideImageRows = [];
            foreach ($insideImageFiles as $file) {
                $path = $file->store('services/inside', 'public');
                if (! $path) throw new RuntimeException('A service inside image could not be stored.');
                $uploadedPaths[] = $path;
                $insideImageRows[] = ['path' => $path, 'alt_text' => $data['title'] ?? $item->title];
                $uploadedMedia[] = $this->mediaAttributes($request, $file, $path, 'service_inside_image', $data + ['title' => $item->title]);
            }
            $existingInsidePaths = $item->images()->pluck('path')->all();
            foreach ($insideImageMediaIds as $mediaId) {
                $media = Media::query()->whereKey($mediaId)->where('mime_type', 'like', 'image/%')->firstOrFail();
                if (! in_array($media->path, $existingInsidePaths, true)) $insideImageRows[] = ['path' => $media->path, 'alt_text' => $media->alt_text ?: ($data['title'] ?? $item->title)];
            }

            if (in_array($type, ['posts', 'services'], true)
                && ($data['status'] ?? null) === 'published'
                && ! $item->published_at) {
                $data['published_at'] = now();
            }

            if (in_array($type, ['banners', 'team', 'categories', 'testimonials'], true)) {
                $data['is_active'] = $request->boolean('is_active');
            }

            DB::transaction(function () use ($item, $data, $type, $categoryIds, $tagIds, $uploadedMedia, $serviceFeatures, $insideImageRows, $removeInsideImageIds): void {
                $item->update($data);

                foreach ($uploadedMedia as $attributes) {
                    Media::create($attributes);
                }

                if ($type === 'services') {
                    $item->features()->delete();
                    foreach ($serviceFeatures as $index => $feature) {
                        if (! empty($feature['title'])) $item->features()->create(['title' => $feature['title'], 'description' => $feature['description'] ?? null, 'icon' => $feature['icon'] ?? null, 'sort_order' => $index + 1]);
                    }
                    if ($removeInsideImageIds) $item->images()->whereIn('id', $removeInsideImageIds)->delete();
                    foreach ($insideImageRows as $index => $image) $item->images()->create($image + ['sort_order' => $item->images()->count() + $index + 1]);
                }

                if ($type === 'posts') {
                    $item->categories()->sync($categoryIds);
                    $item->tags()->sync($tagIds);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            throw $exception;
        }

        foreach (array_unique($oldFiles) as $oldPath) {
            if (! Media::where('path', $oldPath)->exists()
                && ! app(MediaReferenceChecker::class)->isPathReferenced($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        return redirect()->route('admin.content.index', $type)
            ->with('success', ucfirst($type).' item updated.');
    }

    public function destroy(string $type, int $id)
    {
        $model = $this->model($type);
        $model::findOrFail($id)->delete();

        return back()->with('success', 'Item moved to trash.');
    }

    private function decodeServiceFeatures(?string $json): array
    {
        if ($json === null || trim($json) === '') return [];
        $features = json_decode($json, true);
        if (! is_array($features) || ! array_is_list($features)) {
            throw ValidationException::withMessages(['features_json' => 'Service features must be a JSON array.']);
        }

        foreach ($features as $feature) {
            if (! is_array($feature)
                || ! is_string($feature['title'] ?? null)
                || trim($feature['title']) === ''
                || mb_strlen($feature['title']) > 180
                || (isset($feature['description']) && ! is_string($feature['description']))
                || (isset($feature['icon']) && ! is_string($feature['icon']))) {
                throw ValidationException::withMessages(['features_json' => 'Each service feature needs a title and may include a description and icon name.']);
            }
        }

        return $features;
    }

    private function validated(Request $request, string $type, ?int $id = null): array
    {
        $rules = match ($type) {
            'posts' => [
                'title' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', Rule::unique('posts')->ignore($id)],
                'excerpt' => ['nullable', 'string'],
                'content' => ['nullable', 'string'],
                'category_ids' => ['nullable', 'array'],
                'category_ids.*' => ['integer', Rule::exists('categories', 'id')->where('type', 'post')],
                'tag_ids' => ['nullable', 'array'],
                'tag_ids.*' => ['integer', 'distinct', Rule::exists('tags', 'id')],
                'featured_image' => ['nullable', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
                'status' => ['required', 'in:draft,published,archived'],
                'meta_title' => ['nullable', 'string', 'max:255'],
                'meta_description' => ['nullable', 'string', 'max:1000'],
                'focus_keyword' => ['nullable', 'string', 'max:160'],
                'og_image' => ['nullable', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
            ],
            'pages' => [
                'title' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', Rule::unique('pages')->ignore($id)],
                'content' => ['nullable', 'string'],
                'status' => ['required', 'in:draft,published,archived'],
                'meta_title' => ['nullable', 'string', 'max:255'],
                'meta_description' => ['nullable', 'string', 'max:1000'],
                'focus_keyword' => ['nullable', 'string', 'max:160'],
                'og_image' => ['nullable', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
            ],
            'categories' => [
                'name' => ['required', 'string', 'max:150'],
                'slug' => ['nullable', 'string', 'max:180', Rule::unique('categories')->ignore($id)],
                'description' => ['nullable', 'string'],
                'icon_path' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
                'parent_id' => ['nullable', Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', $request->input('type'))->when($id, fn ($query) => $query->where('id', '!=', $id)))],
                'type' => ['required', 'in:property,post'],
            ],
            'banners' => [
                'title' => ['required', 'string', 'max:255'],
                'subtitle' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'image' => ['nullable', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
                'button_text' => ['nullable', 'string', 'max:100'],
                'button_url' => ['nullable', 'string', 'max:2048', new SafeLink()],
                'placement' => ['required', 'string', 'max:60'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'starts_at' => ['nullable', 'date'],
                'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'team' => [
                'name' => ['required', 'string', 'max:150'],
                'designation' => ['nullable', 'string', 'max:150'],
                'bio' => ['nullable', 'string'],
                'photo' => ['nullable', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
                'email' => ['nullable', 'email'],
                'phone' => ['nullable', 'string', 'max:30'],
                'social_links' => ['nullable', 'json'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'status' => ['required', 'in:draft,published'],
                'is_active' => ['sometimes', 'boolean'],
            ],
            'services' => [
                'title' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', Rule::unique('services')->ignore($id)],
                'excerpt' => ['nullable', 'string', 'max:500'],
                'content' => ['nullable', 'string'],
                'featured_image' => ['nullable', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
                'status' => ['required', 'in:draft,published,archived'],
                'meta_title' => ['nullable', 'string', 'max:255'],
                'meta_description' => ['nullable', 'string', 'max:1000'],
                'focus_keyword' => ['nullable', 'string', 'max:160'],
                'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'features_json' => ['nullable', 'json', 'max:12000'],
                'inside_images' => ['nullable', 'array', 'max:20'],
                'inside_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'inside_image_media_ids' => ['nullable', 'array', 'max:20'],
                'inside_image_media_ids.*' => ['integer', 'distinct', Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%'))],
                'remove_inside_images' => ['nullable', 'array'],
                'remove_inside_images.*' => ['integer', Rule::exists('service_images', 'id')->where('service_id', $id)],
            ],
            'testimonials' => [
                'name' => ['required', 'string', 'max:150'],
                'designation' => ['nullable', 'string', 'max:150'],
                'company' => ['nullable', 'string', 'max:150'],
                'quote' => ['required', 'string'],
                'photo' => ['nullable', 'mimes:svg,jpg,jpeg,png,webp', 'max:5120'],
                'rating' => ['nullable', 'integer', 'between:1,5'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'status' => ['required', 'in:draft,published'],
                'is_active' => ['sometimes', 'boolean'],
            ],
        };

        foreach (['remove_image', 'remove_featured_image', 'remove_photo', 'remove_icon_path', 'remove_og_image'] as $field) $rules[$field] = ['sometimes', 'boolean'];

        foreach ([
            'image_media_id',
            'featured_image_media_id',
            'photo_media_id',
            'icon_media_id',
            'og_image_media_id',
        ] as $field) {
            $rules[$field] = [
                'nullable',
                'integer',
                Rule::exists('media', 'id')->where(fn ($query) => $query->where('mime_type', 'like', 'image/%')),
            ];
        }

        return $request->validate($rules);
    }

    private function applyMediaSelections(array &$data): void
    {
        foreach ([
            'image_media_id' => 'image',
            'featured_image_media_id' => 'featured_image',
            'photo_media_id' => 'photo',
            'icon_media_id' => 'icon_path',
            'og_image_media_id' => 'og_image',
        ] as $input => $field) {
            if (! empty($data[$input])) {
                $media = Media::query()
                    ->whereKey((int) $data[$input])
                    ->where('mime_type', 'like', 'image/%')
                    ->firstOrFail();
                $data[$field] = $media->path;
            }

            unset($data[$input]);
        }
    }

    private function mediaAttributes(Request $request, $file, string $path, string $field, array $data): array
    {
        return [
            'uploaded_by' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt_text' => (string) ($data['title'] ?? $data['name'] ?? str_replace('_', ' ', $field)),
        ];
    }

    private function decodeAndValidateSocialLinks(array &$data, string $type): void
    {
        if ($type !== 'team' || ! isset($data['social_links'])) {
            return;
        }

        $links = json_decode($data['social_links'], true);
        if (! is_array($links)) {
            throw ValidationException::withMessages(['social_links' => 'Enter social links as a valid JSON object.']);
        }

        foreach ($links as $platform => $url) {
            if ($url === null || $url === '') {
                unset($links[$platform]);
                continue;
            }

            $parts = is_string($url) ? parse_url($url) : false;
            if (! SafeLink::isValid($url)
                || ! is_array($parts)
                || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
                || empty($parts['host'])) {
                throw ValidationException::withMessages([
                    'social_links' => 'Social profile links must use a valid HTTP or HTTPS URL.',
                ]);
            }
        }

        $data['social_links'] = $links;
    }
}
