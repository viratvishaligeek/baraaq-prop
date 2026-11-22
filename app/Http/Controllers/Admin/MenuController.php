<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Service;
use App\Rules\SafeLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    public function index()
    {
        return view('admin.menus.index', [
            'menus' => Menu::withCount('allItems')->orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.menus.form', $this->formData(new Menu()));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $menu = DB::transaction(function () use ($data) {
            $menu = Menu::create(['name' => $data['name'], 'location' => $data['location']]);
            $this->syncItems($menu, $data['items'] ?? []);
            return $menu;
        });

        Cache::forget('menu:' . $menu->location);

        return redirect()->route('admin.menus.edit', $menu)->with('success', 'Menu created.');
    }

    public function edit(Menu $menu)
    {
        $items = $menu->allItems()->orderBy('sort_order')->get();
        $indexes = $items->pluck('id')->flip();

        $items->each(function ($item) use ($indexes) {
            $item->parent_index = $item->parent_id !== null ? ($indexes[$item->parent_id] ?? null) : null;
        });

        return view('admin.menus.form', $this->formData($menu, $items));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validated($request, $menu);
        $oldLocation = $menu->location;

        DB::transaction(function () use ($menu, $data) {
            $menu->update(['name' => $data['name'], 'location' => $data['location']]);
            $menu->allItems()->delete();
            $this->syncItems($menu, $data['items'] ?? []);
        });

        Cache::forget('menu:' . $oldLocation);
        Cache::forget('menu:' . $menu->location);

        return back()->with('success', 'Menu updated.');
    }

    public function destroy(Menu $menu)
    {
        $location = $menu->location;
        $menu->delete();
        Cache::forget('menu:' . $location);

        return back()->with('success', 'Menu deleted.');
    }

    private function formData(Menu $menu, $items = null): array
    {
        return [
            'menu' => $menu,
            'menuItems' => $items ?? collect(),
            'pages' => Page::where('status', 'published')->orderBy('title')->get(['id', 'title']),
            'blogCategories' => Category::where('type', 'post')->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'propertyCategories' => Category::where('type', 'property')->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'services' => Service::published()->orderBy('title')->get(['id', 'title', 'slug']),
        ];
    }

    private function validated(Request $request, ?Menu $menu = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('menus', 'location')->ignore($menu?->id)],
            'items' => ['nullable', 'array', 'max:50'],
            'items.*.label' => ['required', 'string', 'max:120'],
            'items.*.link_type' => ['nullable', Rule::in(['custom', 'page', 'blog_category', 'property_category', 'service'])],
            'items.*.link_id' => ['nullable', 'integer'],
            'items.*.url' => ['nullable', 'string', 'max:2048', new SafeLink()],
            'items.*.target' => ['required', Rule::in(['_self', '_blank'])],
            'items.*.parent_index' => ['nullable', 'integer', 'min:0', 'max:49'],
            'items.*.is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function syncItems(Menu $menu, array $items): void
    {
        $created = [];
        $depths = [];

        foreach (array_values($items) as $index => $item) {
            $parent = $item['parent_index'] ?? null;

            if ($parent !== null && (! isset($created[(int) $parent]) || (int) $parent >= $index)) {
                throw ValidationException::withMessages([
                    'items.' . $index . '.parent_index' => 'Choose a parent item positioned above this item.',
                ]);
            }

            $depth = $parent !== null ? $depths[(int) $parent] + 1 : 0;
            if ($depth > 4) {
                throw ValidationException::withMessages([
                    'items.' . $index . '.parent_index' => 'Menus support up to five levels of nesting.',
                ]);
            }

            $linkType = $item['link_type'] ?? 'custom';
            $linkId = $item['link_id'] ?? null;

            if ($linkType === 'page' && (! $linkId || ! Page::whereKey($linkId)->where('status', 'published')->exists())) {
                throw ValidationException::withMessages(['items.' . $index . '.link_id' => 'Choose a published page.']);
            }

            if ($linkType === 'blog_category' && (! $linkId || ! Category::whereKey($linkId)->where('type', 'post')->where('is_active', true)->exists())) {
                throw ValidationException::withMessages(['items.' . $index . '.link_id' => 'Choose a blog category.']);
            }

            if ($linkType === 'property_category' && (! $linkId || ! Category::whereKey($linkId)->where('type', 'property')->where('is_active', true)->exists())) {
                throw ValidationException::withMessages(['items.' . $index . '.link_id' => 'Choose a property category.']);
            }

            if ($linkType === 'service' && (! $linkId || ! Service::published()->whereKey($linkId)->exists())) {
                throw ValidationException::withMessages(['items.' . $index . '.link_id' => 'Choose a published service.']);
            }

            $created[$index] = $menu->allItems()->create([
                'label' => trim($item['label']),
                'url' => trim($item['url'] ?? '') ?: null,
                'link_type' => $linkType,
                'link_id' => $linkId,
                'target' => $item['target'] ?? '_self',
                'parent_id' => $parent !== null ? $created[(int) $parent]->id : null,
                'sort_order' => $index,
                'is_active' => isset($item['is_active']),
            ]);
            $depths[$index] = $depth;
        }
    }
}
