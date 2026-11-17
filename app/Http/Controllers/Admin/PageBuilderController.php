<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\Page;
use App\Services\Pages\PageBuilderService;
use App\Services\Widgets\CmsWidgetRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageBuilderController extends Controller
{
    public function __construct(
        private readonly PageBuilderService $builder,
        private readonly CmsWidgetRegistry $registry
    ) {
    }

    private function authorizePage(): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isAdministrator() || $user->hasPermission('pages.manage')), 403);
    }

    private function authorizeDraft(Page $page): void
    {
        abort_unless($page->status === 'draft', 409, 'Create a draft copy before editing a published page.');
    }

    public function edit(Page $page)
    {
        $this->authorizePage();

        return view('admin.pages.builder', [
            'page' => $page->load('sections.mediaAttachments.media'),
            'widgetGroups' => $this->registry->grouped(),
            'widgetDefinitions' => $this->registry->all(),
            'mediaLibrary' => Media::query()->where('mime_type', 'like', 'image/%')->latest('id')->limit(200)->get(['id', 'original_name', 'mime_type', 'disk', 'path', 'alt_text']),
            'queryableTypes' => $this->registry->queryableTypes(),
        ]);
    }

    public function duplicate(Page $page)
    {
        $this->authorizePage();

        $copy = DB::transaction(function () use ($page) {
            $copy = $page->replicate();
            $copy->title = $page->title . ' (Draft)';
            $base = Str::slug($page->slug . '-draft');
            $slug = $base;
            $suffix = 2;
            while (Page::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $suffix++;
            }
            $copy->slug = $slug;
            $copy->status = 'draft';
            $copy->homepage_key = null;
            $copy->save();

            foreach ($page->sections as $section) {
                $sectionCopy = $section->replicate();
                $sectionCopy->page_id = $copy->id;
                $sectionCopy->homepage_id = null;
                $sectionCopy->instance_key = (string) Str::uuid();
                $sectionCopy->save();

                foreach ($section->mediaAttachments as $attachment) {
                    $sectionCopy->mediaAttachments()->create([
                        'field_key' => $attachment->field_key,
                        'media_id' => $attachment->media_id,
                    ]);
                }
            }

            return $copy;
        });

        return redirect()->route('admin.pages.builder', $copy)->with('success', 'A separate draft copy was created. The published page remains unchanged.');
    }

    public function preview(Page $page)
    {
        $this->authorizePage();

        return view('frontend.pages.show', [
            'page' => $page->load('sections.mediaAttachments.media'),
            'isBuilderPreview' => true,
        ]);
    }

    public function add(Request $request, Page $page)
    {
        $this->authorizePage();
        $this->authorizeDraft($page);
        $data = $request->validate(['section_key' => ['required', 'string', 'max:120']]);
        $this->builder->add($page, $data['section_key']);

        return back()->with('success', 'Widget added to the page.');
    }

    public function update(Request $request, Page $page, HomepageSection $section)
    {
        $this->authorizePage();
        $this->authorizeDraft($page);
        abort_unless($section->page_id === $page->id, 404);

        $requested = $request->validate([
            'section_key' => ['sometimes', 'string', 'max:120'],
        ]);
        $sectionKey = $requested['section_key'] ?? $section->section_key;
        $allowedDesigns = collect($this->registry->all())
            ->filter(fn (array $candidate) => ($candidate['type'] ?? null) === $section->section_type)
            ->keys()
            ->all();
        abort_unless(in_array($sectionKey, $allowedDesigns, true), 422, 'Choose a registered design from the same widget family.');

        $definition = $this->registry->find($sectionKey);
        abort_unless($definition, 422, 'The widget design is no longer registered.');

        $rules = ['config' => ['nullable', 'array'], 'data_source' => ['nullable', 'array']];
        foreach ($definition['fields'] ?? [] as $field => $schema) {
            $fieldRules = ['sometimes', 'nullable'];
            $type = $schema['type'] ?? 'text';

            if (in_array($type, ['text', 'textarea', 'url', 'image'], true)) {
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
            $request->merge(['data_source' => $source]);
            $source = $request->validate([
                'data_source.limit' => ['nullable', 'integer', 'min:1', 'max:24'],
                'data_source.sort_by' => ['nullable', Rule::in($queryable['sort'])],
                'data_source.sort_direction' => ['nullable', Rule::in(['asc', 'desc'])],
                'data_source.columns' => ['nullable', 'integer', 'min:1', 'max:6'],
                'data_source.slider' => ['nullable', 'boolean'],
                'data_source.selected_ids' => ['nullable', 'array', 'max:100'],
                'data_source.selected_ids.*' => ['integer', 'distinct', Rule::exists($queryable['table'], 'id')],
            ])['data_source'] ?? [];
        } else {
            $source = [];
        }

        $this->builder->save($page, $section, [
            'section_key' => $sectionKey,
            'config' => $data['config'] ?? [],
            'data_source' => $source,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['saved' => true, 'section_id' => $section->id]);
        }

        return back()->with('success', 'Widget configuration saved.');
    }

    public function duplicateSection(Page $page, HomepageSection $section)
    {
        $this->authorizePage();
        $this->authorizeDraft($page);
        $this->builder->duplicate($page, $section);

        return back()->with('success', 'Widget duplicated.');
    }

    public function reorder(Request $request, Page $page)
    {
        $this->authorizePage();
        $this->authorizeDraft($page);
        $data = $request->validate([
            'sections' => ['required', 'array', 'max:150'],
            'sections.*' => ['required', 'integer', 'distinct'],
        ]);
        $this->builder->reorder($page, $data['sections']);

        return response()->json(['saved' => true]);
    }

    public function toggle(Page $page, HomepageSection $section)
    {
        $this->authorizePage();
        $this->authorizeDraft($page);
        abort_unless($section->page_id === $page->id, 404);
        $section->update(['is_enabled' => ! $section->is_enabled]);

        return back()->with('success', 'Widget visibility updated.');
    }

    public function destroy(Page $page, HomepageSection $section)
    {
        $this->authorizePage();
        $this->authorizeDraft($page);
        abort_unless($section->page_id === $page->id, 404);
        $section->mediaAttachments()->delete();
        $section->delete();

        return back()->with('success', 'Widget removed from the page.');
    }

    public function publish(Page $page)
    {
        $this->authorizePage();
        abort_unless($page->status === 'draft', 409, 'Only a draft page can be published.');
        $page->update(['status' => 'published']);

        return redirect()->route('admin.content.index', 'pages')->with('success', 'Page published.');
    }
}
