<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\ReusableSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReusableSectionController extends Controller
{
    public function storeFromSection(Request $request, Homepage $homepage, HomepageSection $section)
    {
        abort_unless($homepage->status === 'draft', 409);
        abort_unless($section->homepage_id === $homepage->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $sectionDefinitions = config('cms-sections.sections', []);
        $definition = $sectionDefinitions[$section->section_key] ?? null;
        abort_unless($definition, 422, 'The section is not registered.');

        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $suffix = 2;

        while (ReusableSection::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        $sharedConfig = array_merge(
            $section->reusableSection?->config ?? [],
            $section->config ?? []
        );

        DB::transaction(function () use ($request, $section, $definition, $sharedConfig, $data, $slug) {
            $reusableSection = ReusableSection::create([
                'name' => $data['name'],
                'slug' => $slug,
                'section_type' => $section->section_type,
                'section_key' => $section->section_key,
                'source_design' => $section->source_design,
                'config' => $sharedConfig,
                'status' => 'published',
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            foreach ($definition['fields'] ?? [] as $field => $schema) {
                $mediaId = $sharedConfig[$field] ?? null;

                if (($schema['type'] ?? null) !== 'image' || (! is_string($mediaId) && ! is_int($mediaId)) || ! ctype_digit((string) $mediaId)) {
                    continue;
                }

                abort_unless(Media::whereKey((int) $mediaId)->where('mime_type', 'like', 'image/%')->exists(), 422, 'Select a valid image from the Media Library.');

                $reusableSection->mediaAttachments()->updateOrCreate(
                    ['field_key' => $field],
                    ['media_id' => (int) $mediaId]
                );
            }
        });

        return back()->with('success', 'Section saved to the reusable section library.');
    }

    public function attach(Homepage $homepage, ReusableSection $reusableSection)
    {
        abort_unless($homepage->status === 'draft', 409);
        abort_unless($reusableSection->status === 'published', 404);

        $sectionDefinitions = config('cms-sections.sections', []);
        $definition = $sectionDefinitions[$reusableSection->section_key] ?? null;
        abort_unless($definition, 422, 'This reusable section is no longer registered.');

        $homepage->sections()->create([
            'instance_key' => (string) Str::uuid(),
            'reusable_section_id' => $reusableSection->id,
            'section_key' => $reusableSection->section_key,
            'section_type' => $reusableSection->section_type,
            'source_design' => $reusableSection->source_design,
            'sort_order' => ((int) $homepage->sections()->max('sort_order')) + 1,
            'is_enabled' => true,
            'config' => [],
        ]);

        return back()->with('success', 'Reusable section added. Its shared settings remain linked to the library item.');
    }
}
