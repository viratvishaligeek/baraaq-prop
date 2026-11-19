<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use App\Models\WidgetArea;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WidgetController extends Controller
{
    private const TYPES = ['text', 'image', 'html', 'recent_posts', 'categories', 'featured_properties', 'contact', 'social', 'cta', 'custom'];

    public function index()
    {
        return view('admin.widgets.index', [
            'widgets' => Widget::with('areas')->latest()->paginate(20),
            'areas' => WidgetArea::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.widgets.form', [
            'widget' => new Widget(['status' => 'published', 'type' => 'text']),
            'areas' => WidgetArea::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        foreach (['body', 'html'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = HtmlSanitizer::sanitize($data[$field]);
            }
        }
        $areaIds = $data['area_ids'] ?? [];
        unset($data['area_ids']);
        $data['config'] = $this->config($data);
        unset($data['title'], $data['body'], $data['url'], $data['image_url'], $data['button_text'], $data['phone'], $data['email'], $data['address'], $data['social_links'], $data['html']);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $widget = DB::transaction(function () use ($data, $areaIds) {
            $widget = Widget::create($data);
            $this->syncAreas($widget, $areaIds);
            return $widget;
        });

        return redirect()->route('admin.widgets.edit', $widget)->with('success', 'Widget created.');
    }

    public function edit(Widget $widget)
    {
        return view('admin.widgets.form', [
            'widget' => $widget,
            'areas' => WidgetArea::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Widget $widget)
    {
        $data = $this->validated($request);
        $areaIds = $data['area_ids'] ?? [];
        unset($data['area_ids']);
        $data['config'] = $this->config($data);
        unset($data['title'], $data['body'], $data['url'], $data['image_url'], $data['button_text'], $data['phone'], $data['email'], $data['address'], $data['social_links'], $data['html']);
        $data['updated_by'] = $request->user()->id;

        DB::transaction(function () use ($widget, $data, $areaIds) {
            $widget->update($data);
            $this->syncAreas($widget, $areaIds);
        });

        return back()->with('success', 'Widget updated.');
    }

    public function destroy(Widget $widget)
    {
        $widget->delete();
        return back()->with('success', 'Widget moved to trash.');
    }

    public function reorder(Request $request, WidgetArea $area)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['required', 'integer', Rule::exists('widget_area_items', 'id')->where('widget_area_id', $area->id)],
        ]);
        DB::transaction(function () use ($data, $area) {
            foreach ($data['items'] as $order => $id) {
                $area->items()->whereKey($id)->update(['sort_order' => $order]);
            }
        });
        return response()->json(['success' => true]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(self::TYPES)],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'area_ids' => ['nullable', 'array'],
            'area_ids.*' => ['integer', 'distinct', 'exists:widget_areas,id'],
            'title' => ['nullable', 'string', 'max:180'],
            'body' => ['nullable', 'string', 'max:20000'],
            'html' => ['nullable', 'string', 'max:30000'],
            'url' => ['nullable', 'url', 'max:2048'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['nullable', 'string', 'max:1000'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.label' => ['nullable', 'string', 'max:40'],
            'social_links.*.url' => ['nullable', 'url', 'max:2048'],
        ]);

        if (! empty($data['image_url'])) {
            if (ctype_digit((string) $data['image_url'])) {
                $request->validate([
                    'image_url' => [
                        Rule::exists('media', 'id')->where('mime_type', 'like', 'image/%'),
                    ],
                ]);
            } else {
                $request->validate([
                    'image_url' => ['regex:~^(https?://|/)~i'],
                ]);
            }
        }

        return $data;
    }

    private function config(array $data): array
    {
        $fields = ['title', 'body', 'html', 'url', 'image_url', 'button_text', 'phone', 'email', 'address', 'social_links'];
        return array_intersect_key($data, array_flip($fields));
    }

    private function syncAreas(Widget $widget, array $areaIds): void
    {
        $sync = [];
        foreach (array_values(array_unique($areaIds)) as $order => $areaId) {
            $sync[$areaId] = ['sort_order' => $order, 'is_active' => true];
        }
        $widget->areas()->sync($sync);
    }
}
