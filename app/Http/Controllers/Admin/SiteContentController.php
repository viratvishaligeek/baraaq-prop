<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\NeighborhoodGuide;
use App\Support\MediaReferenceChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SiteContentController extends Controller
{
    private array $models = [
        'gallery' => GalleryItem::class,
        'faqs' => FaqItem::class,
        'neighborhood-guides' => NeighborhoodGuide::class,
    ];

    private function model(string $type): string
    {
        abort_unless(isset($this->models[$type]), 404);

        $user = request()->user();
        $action = match (true) {
            request()->routeIs('admin.site-content.create', 'admin.site-content.store') => 'create',
            request()->routeIs('admin.site-content.edit', 'admin.site-content.update') => 'edit',
            request()->routeIs('admin.site-content.destroy') => 'delete',
            default => 'view',
        };

        abort_unless(
            $user->isAdministrator()
                || $user->hasPermission($type.'.manage')
                || $user->hasPermission($type.'.'.$action),
            403
        );

        return $this->models[$type];
    }

    public function index(string $type)
    {
        $model = $this->model($type);
        $filters = request()->validate([
            'q' => ['nullable', 'string', 'max:160'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $items = $model::query()
            ->when($filters['q'] ?? null, function ($query, $term) use ($type): void {
                $columns = $type === 'faqs' ? ['question', 'answer', 'category'] : ['title', 'slug'];
                $query->where(function ($search) use ($columns, $term): void {
                    foreach ($columns as $index => $column) {
                        $search->{$index === 0 ? 'where' : 'orWhere'}($column, 'like', '%'.trim($term).'%');
                    }
                });
            })
            ->when(isset($filters['status']) && $type !== 'faqs', fn ($query) => $query->where('status', $filters['status']))
            ->orderBy($type === 'faqs' ? 'sort_order' : 'updated_at', $type === 'faqs' ? 'asc' : 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.site-content.index', compact('type', 'items'));
    }

    public function create(string $type)
    {
        $model = $this->model($type);

        return view('admin.site-content.form', ['type' => $type, 'item' => new $model()]);
    }

    public function store(Request $request, string $type)
    {
        $model = $this->model($type);
        $data = $this->validated($request, $type);
        $this->authorizePublish($type, $data);

        $uploadedPath = null;
        try {
            if ($type !== 'faqs' && $request->hasFile('image')) {
                $uploadedPath = $request->file('image')->store($type, 'public');
                if (! $uploadedPath) throw new \RuntimeException('Image storage failed.');
                $data[$type === 'gallery' ? 'image' : 'hero_image'] = $uploadedPath;
            }

            unset($data['image'], $data['remove_image']);

            if ($type !== 'faqs') {
                $data['slug'] = $this->uniqueSlug($model, $data['slug'] ?? $data['title']);
                if ($data['status'] === 'published') $data['published_at'] = now();
            } else {
                $data['is_active'] = $request->boolean('is_active');
            }

            DB::transaction(function () use ($model, $data, $type, $request, $uploadedPath): void {
                $item = $model::create($data);
                if ($uploadedPath) {
                    $file = $request->file('image');
                    $this->registerMedia($request, $uploadedPath, $file, $data['alt_text'] ?? $data['title']);
                }
            });
        } catch (\Throwable $exception) {
            if ($uploadedPath) Storage::disk('public')->delete($uploadedPath);
            throw $exception;
        }

        return redirect()->route('admin.site-content.index', $type)->with('success', 'Content created.');
    }

    public function edit(string $type, int $id)
    {
        $model = $this->model($type);

        return view('admin.site-content.form', [
            'type' => $type,
            'item' => $model::findOrFail($id),
        ]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $model = $this->model($type);
        $item = $model::findOrFail($id);
        $data = $this->validated($request, $type, $id);
        $this->authorizePublish($type, $data, $item);

        $imageField = $type === 'gallery' ? 'image' : 'hero_image';
        $oldPath = $type === 'faqs' ? null : $item->$imageField;
        $newPath = null;

        try {
            if ($type !== 'faqs' && $request->hasFile('image')) {
                $newPath = $request->file('image')->store($type, 'public');
                if (! $newPath) throw new \RuntimeException('Image storage failed.');
                $data[$imageField] = $newPath;
            } elseif ($type !== 'faqs' && $request->boolean('remove_image')) {
                $data[$imageField] = null;
            }

            unset($data['image'], $data['remove_image']);

            if ($type !== 'faqs' && ($data['status'] ?? null) === 'published' && ! $item->published_at) {
                $data['published_at'] = now();
            }
            if ($type === 'faqs') $data['is_active'] = $request->boolean('is_active');

            DB::transaction(function () use ($item, $data, $type, $request, $newPath): void {
                $item->update($data);
                if ($newPath) {
                    $file = $request->file('image');
                    $this->registerMedia($request, $newPath, $file, $data['alt_text'] ?? $data['title']);
                }
            });
        } catch (\Throwable $exception) {
            if ($newPath) Storage::disk('public')->delete($newPath);
            throw $exception;
        }

        if ($oldPath && $oldPath !== $item->$imageField
            && ! Media::where('path', $oldPath)->exists()
            && ! app(MediaReferenceChecker::class)->isPathReferenced($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('admin.site-content.index', $type)->with('success', 'Content updated.');
    }

    public function destroy(string $type, int $id)
    {
        $model = $this->model($type);
        $item = $model::findOrFail($id);
        $path = $type === 'gallery' ? $item->image : ($type === 'neighborhood-guides' ? $item->hero_image : null);
        $item->delete();

        if ($path && ! Media::where('path', $path)->exists() && ! app(MediaReferenceChecker::class)->isPathReferenced($path)) {
            Storage::disk('public')->delete($path);
        }

        return back()->with('success', 'Content deleted.');
    }

    private function validated(Request $request, string $type, ?int $id = null): array
    {
        if ($type === 'faqs') {
            return $request->validate([
                'question' => ['required', 'string', 'max:255'],
                'answer' => ['required', 'string', 'max:12000'],
                'category' => ['nullable', 'string', 'max:120'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
            ]);
        }

        $table = $type === 'gallery' ? 'gallery_items' : 'neighborhood_guides';
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($table)->ignore($id)],
            'image' => [$type === 'gallery' && ! $id ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'remove_image' => ['sometimes', 'boolean'],
            'status' => ['required', 'in:draft,published,archived'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'og_image' => ['nullable', 'string', 'max:2048'],
        ];

        if ($type === 'gallery') {
            return $request->validate($rules + [
                'description' => ['nullable', 'string', 'max:3000'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ]);
        }

        return $request->validate($rules + [
            'locality' => ['required', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'focus_keyword' => ['nullable', 'string', 'max:160'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uniqueSlug(string $model, string $value): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $suffix = 2;

        while ($model::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private function authorizePublish(string $type, array $data, $item = null): void
    {
        if ($type !== 'faqs' && ($data['status'] ?? null) === 'published' && $item?->status !== 'published') {
            $user = request()->user();
            abort_unless($user->isAdministrator() || $user->hasPermission($type.'.manage') || $user->hasPermission($type.'.publish'), 403);
        }
    }

    private function registerMedia(Request $request, string $path, $file, string $alt): void
    {
        Media::create([
            'uploaded_by' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt_text' => $alt,
        ]);
    }
}