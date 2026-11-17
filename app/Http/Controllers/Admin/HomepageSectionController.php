<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomepageSectionController extends Controller
{
    public function store(Request $request, Homepage $homepage)
    {
        abort_unless($homepage->status === 'draft', 409, 'Create a draft copy before editing a published homepage.');

        $data = $request->validate([
            'section_key' => ['required', 'string', 'max:100'],
        ]);

        $definition = config('cms-sections.sections.'.$data['section_key']);
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

        $definition = config('cms-sections.sections.'.$section->section_key);
        abort_unless($definition, 422, 'The section is not registered.');

        $rules = ['config' => ['nullable', 'array']];

        foreach ($definition['fields'] ?? [] as $field => $schema) {
            $fieldRules = ['sometimes', 'nullable'];

            switch ($schema['type'] ?? 'text') {
                case 'textarea':
                case 'text':
                case 'url':
                case 'image':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:'.($schema['max'] ?? 255);
                    break;

                case 'color':
                    $fieldRules[] = 'regex:/^#[0-9A-Fa-f]{6}$/';
                    break;

                case 'number':
                    $fieldRules[] = 'integer';
                    $fieldRules[] = 'min:'.($schema['min'] ?? 0);
                    $fieldRules[] = 'max:'.($schema['max'] ?? 9999);
                    break;

                case 'checkbox':
                    $fieldRules[] = 'boolean';
                    break;

                case 'select':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'in:'.implode(',', array_keys($schema['options'] ?? []));
                    break;
            }

            $rules['config.'.$field] = $fieldRules;
        }

        $data = $request->validate($rules);
        $allowedFields = array_keys($definition['fields'] ?? []);
        $submittedConfig = array_intersect_key(
            $data['config'] ?? [],
            array_flip($allowedFields)
        );
        DB::transaction(function () use ($submittedConfig, $definition, $section) {
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

            $section->update(['config' => $config]);
        });

        return back()->with('success', 'Section settings saved.');
    }

    public function toggle(Homepage $homepage, HomepageSection $section)
    {
        abort_unless($homepage->status === 'draft', 409);
        abort_unless($section->homepage_id === $homepage->id, 404);

        $section->update(['is_enabled' => ! $section->is_enabled]);

        return back()->with('success', 'Section visibility updated.');
    }

    public function destroy(Homepage $homepage, HomepageSection $section)
    {
        abort_unless($homepage->status === 'draft', 409);
        abort_unless($section->homepage_id === $homepage->id, 404);

        $section->delete();

        return back()->with('success', 'Section removed from the draft homepage.');
    }
}
