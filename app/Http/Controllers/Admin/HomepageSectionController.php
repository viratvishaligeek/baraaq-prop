<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use App\Models\HomepageSection;
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
        foreach ($definition['settings'] ?? [] as $field) {
            $rules['config.'.$field] = ['nullable', 'string', 'max:180'];
        }

        $data = $request->validate($rules);
        $config = array_intersect_key(
            $data['config'] ?? [],
            array_flip($definition['settings'] ?? [])
        );

        $section->update(['config' => $config]);

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
