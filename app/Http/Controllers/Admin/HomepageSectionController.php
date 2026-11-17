<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Services\Widgets\CmsWidgetRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class HomepageSectionController extends Controller
{
    public function store(Request $request, Homepage $homepage)
    {
        abort_unless($homepage->status === 'draft', 409, 'Create a draft copy before editing a published homepage.');

        $data = $request->validate([
            'section_key' => ['required', 'string', 'max:100'],
        ]);

        $sectionDefinitions = config('cms-sections.sections', []);
        $definition = $sectionDefinitions[$data['section_key']] ?? null;
        abort_unless($definition, 422, 'The selected section is not registered.');

        $homepage->sections()->create([
            'instance_key' => (string) Str::uuid(),
            'section_key' => $definition['key'],
            'section_type' => $definition['type'],
            'source_design' => $definition['source_design'],
            'sort_order' => ((int) $homepage->sections()->max('sort_order')) + 1,
            'is_enabled' => true,
            'config' => $definition['defaults'] ?? [],
        ]);

        return redirect()->route('admin.homepages.builder', $homepage)
            ->with('success', 'Section added to the draft homepage.');
    }

    public function duplicate(Homepage $homepage, HomepageSection $section)
    {
        abort_unless($homepage->status === 'draft', 409);
        abort_unless($section->homepage_id === $homepage->id, 404);

        DB::transaction(function () use ($homepage, $section) {
            $homepage->sections()
                ->where('sort_order', '>', $section->sort_order)
                ->lockForUpdate()
                ->increment('sort_order');

            $copy = $section->replicate();
            $copy->instance_key = (string) Str::uuid();
            $copy->sort_order = $section->sort_order + 1;
            $copy->save();

            foreach ($section->mediaAttachments as $attachment) {
                $copy->mediaAttachments()->create([
                    'field_key' => $attachment->field_key,
                    'media_id' => $attachment->media_id,
                ]);
            }
        });

        return back()->with('success', 'Section duplicated in the draft homepage.');
    }

    public function reorder(Request $request, Homepage $homepage)
    {
        abort_unless($homepage->status === 'draft', 409);

        $data = $request->validate([
            'sections' => ['required', 'array', 'max:100'],
            'sections.*' => ['required', 'integer', 'distinct'],
        ]);

        $sections = $homepage->sections()
            ->whereIn('id', $data['sections'])
            ->get()
            ->keyBy('id');

        abort_unless($sections->count() === count($data['sections']), 422, 'The section order contains an invalid item.');

        DB::transaction(function () use ($data, $sections) {
            foreach ($data['sections'] as $position => $id) {
                $sections[$id]->update(['sort_order' => $position + 1]);
            }
        });

        return response()->json(['saved' => true]);
    }

    public function update(Request $request, Homepage $homepage, HomepageSection $section)
    {
        abort_unless($homepage->status === 'draft', 409);
        abort_unless($section->homepage_id === $homepage->id, 404);

        $sectionDefinitions = config('cms-sections.sections', []);
        $definition = $sectionDefinitions[$section->section_key] ?? null;
        abort_unless($definition, 422, 'The section is not registered.');

        $rules = ['config' => ['nullable', 'array']];

        foreach ($definition['fields'] ?? [] as $field => $schema) {
            $fieldRules = ['sometimes', 'nullable'];

            switch ($schema['type'] ?? 'text') {
                case 'textarea':
                case 'richtext':
                case 'text':
                case 'url':
                case 'image':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:' . ($schema['max'] ?? 255);
                    if (($schema['type'] ?? '') === 'url') {
                        $fieldRules[] = 'regex:~^(https?://|/|#|mailto:|tel:)~i';
                    }
                    break;

                case 'color':
                    $fieldRules[] = 'regex:/^#[0-9A-Fa-f]{6}$/';
                    break;

                case 'number':
                    $fieldRules[] = 'integer';
                    $fieldRules[] = 'min:' . ($schema['min'] ?? 0);
                    $fieldRules[] = 'max:' . ($schema['max'] ?? 9999);
                    break;

                case 'checkbox':
                    $fieldRules[] = 'boolean';
                    break;

                case 'select':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'in:' . implode(',', array_keys($schema['options'] ?? []));
                    break;
            }

            $rules['config.' . $field] = $fieldRules;
        }

        $data = $request->validate($rules);
        $queryable = app(CmsWidgetRegistry::class)->queryableTypes()[$section->section_type] ?? null;
        $source = $section->data_source ?? [];

        if ($queryable) {
            $sourceInput = $request->input('data_source', $source);
            if (isset($sourceInput['selected_ids']) && is_string($sourceInput['selected_ids'])) {
                $sourceInput['selected_ids'] = array_values(array_filter(array_map(
                    'intval',
                    preg_split('/[,\\s]+/', $sourceInput['selected_ids']) ?: []
                )));
            }
            $request->merge(['data_source' => $sourceInput]);

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

            foreach ($queryable['filters'] ?? [] as $filter => $schema) {
                $filterRules = ['nullable'];
                switch ($schema['type'] ?? 'text') {
                    case 'select':
                        $filterRules[] = 'string';
                        $filterRules[] = Rule::in(array_map('strval', array_keys($schema['options'] ?? [])));
                        break;
                    case 'integer':
                        $filterRules[] = 'integer';
                        $filterRules[] = 'min:' . ($schema['min'] ?? 0);
                        if (isset($schema['max'])) $filterRules[] = 'max:' . $schema['max'];
                        if ($filter === 'category_id') {
                            $filterRules[] = Rule::exists('categories', 'id')
                                ->where('type', $schema['category_type'] ?? 'property');
                        }
                        break;
                    case 'number':
                        $filterRules[] = 'numeric';
                        $filterRules[] = 'min:' . ($schema['min'] ?? 0);
                        if (isset($schema['max'])) $filterRules[] = 'max:' . $schema['max'];
                        break;
                    default:
                        $filterRules[] = 'string';
                        $filterRules[] = 'max:' . ($schema['max'] ?? 120);
                        break;
                }
                $queryRules['data_source.filters.' . $filter] = $filterRules;
            }

            $source = $request->validate($queryRules)['data_source'] ?? [];
            $filters = $source['filters'] ?? [];
            if (isset($filters['min_price'], $filters['max_price'])
                && $filters['min_price'] !== '' && $filters['max_price'] !== ''
                && (float) $filters['min_price'] > (float) $filters['max_price']) {
                throw ValidationException::withMessages([
                    'data_source.filters.max_price' => 'Maximum price must be greater than or equal to minimum price.',
                ]);
            }
        }

        $allowedFields = array_keys($definition['fields'] ?? []);
        $submittedConfig = array_intersect_key(
            $data['config'] ?? [],
            array_flip($allowedFields)
        );
        DB::transaction(function () use ($submittedConfig, $definition, $section, $source) {
            foreach ($submittedConfig as $field => $value) {
                if (($definition['fields'][$field]['type'] ?? null) !== 'image') {
                    continue;
                }

                if ($value === null || $value === '') {
                    $section->mediaAttachments()->where('field_key', $field)->delete();
                    continue;
                }

                if (ctype_digit((string) $value)) {
                    abort_unless(Media::whereKey((int) $value)->where('mime_type', 'like', 'image/%')->exists(), 422, 'Select a valid image from the Media Library.');
                    $section->mediaAttachments()->updateOrCreate(
                        ['field_key' => $field],
                        ['media_id' => (int) $value]
                    );
                } else {
                    // Keep old URL-based configurations working during the migration.
                    $section->mediaAttachments()->where('field_key', $field)->delete();
                }
            }

            $config = array_merge($section->config ?? [], $submittedConfig);

            $section->update(['config' => $config, 'data_source' => $source]);
        });

        return back()->with('success', 'Section settings saved.');
    }

    public function toggle(Homepage $homepage, HomepageSection $section)
    {
        abort_unless($homepage->status === 'draft', 409);
        abort_unless($section->homepage_id === $homepage->id, 404);

        $section->update(['is_enabled' => !$section->is_enabled]);

        return back()->with('success', 'Section visibility updated.');
    }

    public function destroy(Homepage $homepage, HomepageSection $section)
    {
        abort_unless($homepage->status === 'draft', 409);
        abort_unless($section->homepage_id === $homepage->id, 404);

        DB::transaction(function () use ($section) {
            $section->mediaAttachments()->delete();
            $section->delete();
        });

        return back()->with('success', 'Section removed from the draft homepage.');
    }
}
