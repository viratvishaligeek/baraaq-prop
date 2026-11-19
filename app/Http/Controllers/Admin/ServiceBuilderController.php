<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\Service;
use App\Services\Pages\PageBuilderService;
use App\Services\Widgets\CmsWidgetRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceBuilderController extends Controller
{
    public function __construct(
        private readonly PageBuilderService $builder,
        private readonly CmsWidgetRegistry $registry
    ) {
    }

    private function authorizeServices(): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isAdministrator() || $user->hasPermission('services.manage')), 403);
    }

    private function authorizeDraft(Service $service): void
    {
        abort_unless($service->status === 'draft', 409, 'Create a draft copy before editing a published service.');
    }

    public function edit(Service $service)
    {
        $this->authorizeServices();

        return view('admin.services.builder', [
            'service' => $service->load('sections.mediaAttachments.media'),
            'widgetGroups' => $this->registry->grouped(),
            'widgetDefinitions' => $this->registry->all(),
            'mediaLibrary' => Media::query()->where('mime_type', 'like', 'image/%')->latest('id')->limit(200)->get(['id', 'original_name', 'mime_type', 'disk', 'path', 'alt_text']),
            'queryableTypes' => $this->registry->queryableTypes(),
        ]);
    }

    public function preview(Service $service)
    {
        $this->authorizeServices();

        return view('frontend.services.show', [
            'service' => $service->load('sections.mediaAttachments.media'),
            'isBuilderPreview' => true,
        ]);
    }

    public function add(Request $request, Service $service)
    {
        $this->authorizeServices();
        $this->authorizeDraft($service);
        $data = $request->validate(['section_key' => ['required', 'string', 'max:120']]);
        $this->builder->add($service, $data['section_key']);

        return back()->with('success', 'Widget added to the service.');
    }

    public function update(Request $request, Service $service, HomepageSection $section)
    {
        $this->authorizeServices();
        $this->authorizeDraft($service);
        abort_unless($section->service_id === $service->id, 404);

        $requested = $request->validate(['section_key' => ['sometimes', 'string', 'max:120']]);
        $sectionKey = $requested['section_key'] ?? $section->section_key;
        $definition = $this->registry->find($sectionKey);
        abort_unless($definition && $definition['type'] === $section->section_type, 422, 'Choose a registered design from the same widget family.');

        $rules = ['config' => ['nullable', 'array'], 'data_source' => ['nullable', 'array']];
        foreach ($definition['fields'] ?? [] as $field => $schema) {
            $fieldRules = ['sometimes', 'nullable'];
            $type = $schema['type'] ?? 'text';
            if (in_array($type, ['text', 'textarea', 'richtext', 'url', 'image'], true)) {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:' . ($schema['max'] ?? 2048);
                if ($type === 'url') $fieldRules[] = 'regex:~^(https?://|/|#)~i';
            } elseif ($type === 'color') {
                $fieldRules[] = 'regex:/^#[0-9A-Fa-f]{6}$/';
            } elseif ($type === 'number') {
                $fieldRules[] = 'integer';
                $fieldRules[] = 'min:' . ($schema['min'] ?? 0);
                $fieldRules[] = 'max:' . ($schema['max'] ?? 999999);
            } elseif ($type === 'checkbox') {
                $fieldRules[] = 'boolean';
            } elseif ($type === 'select') {
                $fieldRules[] = 'string';
                $fieldRules[] = Rule::in(array_map('strval', array_keys($schema['options'] ?? [])));
            }
            $rules['config.' . $field] = $fieldRules;
        }

        $data = $request->validate($rules);
        $source = $data['data_source'] ?? [];
        if (isset($source['selected_ids']) && is_string($source['selected_ids'])) {
            $source['selected_ids'] = array_values(array_filter(array_map('intval', explode(',', $source['selected_ids']))));
        }

        $queryable = $this->registry->queryableTypes()[$section->section_type] ?? null;
        if ($queryable) {
            $queryRules = [
                'data_source.limit' => ['nullable', 'integer', 'min:1', 'max:24'],
                'data_source.sort_by' => ['nullable', Rule::in($queryable['sort'])],
                'data_source.sort_direction' => ['nullable', Rule::in(['asc', 'desc'])],
                'data_source.columns' => ['nullable', 'integer', 'min:1', 'max:6'],
                'data_source.slider' => ['nullable', 'boolean'],
                'data_source.selected_ids' => ['nullable', 'array', 'max:100'],
                'data_source.selected_ids.*' => ['integer', 'distinct', Rule::exists($queryable['table'], 'id')],
                'data_source.filters' => ['nullable', 'array'],
            ];
            $source = array_merge($source, $request->validate($queryRules)['data_source'] ?? []);
        } else {
            $source = [];
        }

        $this->builder->save($service, $section, [
            'section_key' => $sectionKey,
            'config' => $data['config'] ?? [],
            'data_source' => $source,
        ]);

        return $request->expectsJson()
            ? response()->json(['saved' => true, 'section_id' => $section->id])
            : back()->with('success', 'Service widget configuration saved.');
    }

    public function duplicate(Service $service, HomepageSection $section)
    {
        $this->authorizeServices();
        $this->authorizeDraft($service);
        $this->builder->duplicate($service, $section);

        return back()->with('success', 'Service widget duplicated.');
    }

    public function reorder(Request $request, Service $service)
    {
        $this->authorizeServices();
        $this->authorizeDraft($service);
        $data = $request->validate([
            'sections' => ['required', 'array', 'max:150'],
            'sections.*' => ['required', 'integer', 'distinct'],
        ]);
        $this->builder->reorder($service, $data['sections']);

        return response()->json(['saved' => true]);
    }

    public function toggle(Service $service, HomepageSection $section)
    {
        $this->authorizeServices();
        $this->authorizeDraft($service);
        abort_unless($section->service_id === $service->id, 404);
        $section->update(['is_enabled' => ! $section->is_enabled]);

        return back()->with('success', 'Service widget visibility updated.');
    }

    public function destroy(Service $service, HomepageSection $section)
    {
        $this->authorizeServices();
        $this->authorizeDraft($service);
        abort_unless($section->service_id === $service->id, 404);
        $section->mediaAttachments()->delete();
        $section->delete();

        return back()->with('success', 'Widget removed from the service.');
    }

    public function publish(Service $service)
    {
        $this->authorizeServices();
        $this->authorizeDraft($service);
        $service->update(['status' => 'published', 'published_at' => now()]);

        return redirect()->route('admin.content.index', 'services')->with('success', 'Service published.');
    }
}
