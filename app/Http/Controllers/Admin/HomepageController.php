<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Homepage;
use App\Models\Post;
use App\Models\Property;
use App\Models\ReusableSection;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomepageController extends Controller
{
    public function index()
    {
        return view('admin.homepages.index', [
            'homepages' => Homepage::orderByDesc('is_default')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function builder(Homepage $homepage)
    {
        abort_unless($homepage->status === 'draft', 409, 'Create a draft copy before editing a published homepage.');

        return view('admin.homepages.builder', [
            'homepage' => $homepage->load('sections.mediaAttachments.media', 'sections.reusableSection.mediaAttachments.media'),
            'reusableSections' => ReusableSection::query()->where('status', 'published')->orderBy('name')->get(),
            'availableSections' => config('cms-sections.sections', []),
        ]);
    }

    public function duplicate(Homepage $homepage)
    {
        $draft = DB::transaction(function () use ($homepage) {
            $copy = $homepage->replicate();
            $copy->name = $homepage->name . ' (Draft)';
            $copy->slug = Str::slug($homepage->slug . '-draft-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(5)));
            $copy->status = 'draft';
            $copy->is_default = false;
            $copy->published_at = null;
            $copy->created_by = request()->user()->id;
            $copy->updated_by = request()->user()->id;
            $copy->save();

            if ($homepage->sections->isEmpty()) {
                $sectionKeys = ['hero.' . $homepage->design_key];

                if ($homepage->design_key === 'home-2') {
                    $sectionKeys[] = 'search.home-2';
                    $sectionKeys[] = 'categories.home-2';
                    $sectionKeys = array_merge($sectionKeys, [
                        'about.home-2',
                        'why.home-2',
                        'properties.home-2',
                        'neighborhoods.home-2',
                        'property-values.home-2',
                        'team.home-2',
                        'download.home-2',
                        'marquee.home-2',
                        'featured-properties.home-2',
                        'testimonials.home-2',
                        'faq.home-2',
                        'blog.home-2',
                    ]);
                }

                if ($homepage->design_key === 'home-3') {
                    $sectionKeys = array_merge($sectionKeys, [
                        'categories.home-3',
                        'properties.home-3',
                        'why.home-3',
                        'team.home-3',
                        'gallery.home-3',
                        'download.home-3',
                        'featured-gallery.home-3',
                        'testimonials.home-3',
                        'faq.home-3',
                        'enquiry.home-3',
                        'marquee.home-3',
                        'blog.home-3',
                        'brands.home-3',
                    ]);
                }

                if ($homepage->design_key === 'home-7') {
                    $sectionKeys[] = 'categories.home-7';
                }

                if ($homepage->design_key === 'index') {
                    $sectionKeys = array_merge($sectionKeys, [
                        'properties.index',
                        'team.index',
                        'blog.index',
                    ]);
                }

                foreach ($sectionKeys as $sectionKey) {
                    $definition = config('cms-sections.sections.' . $sectionKey);

                    if (!$definition) {
                        continue;
                    }

                    $copy->sections()->create([
                        'instance_key' => (string) Str::uuid(),
                        'section_key' => $definition['key'],
                        'section_type' => $definition['type'],
                        'source_design' => $definition['source_design'],
                        'sort_order' => $copy->sections()->count() + 1,
                        'is_enabled' => true,
                        'config' => $definition['defaults'] ?? [],
                    ]);
                }
            } else {
                foreach ($homepage->sections as $section) {
                    $sectionCopy = $section->replicate();
                    $sectionCopy->homepage_id = $copy->id;
                    $sectionCopy->instance_key = (string) Str::uuid();
                    $sectionCopy->save();

                    foreach ($section->mediaAttachments as $attachment) {
                        $sectionCopy->mediaAttachments()->create([
                            'field_key' => $attachment->field_key,
                            'media_id' => $attachment->media_id,
                        ]);
                    }
                }
            }

            return $copy;
        });

        return redirect()
            ->route('admin.homepages.builder', $draft)
            ->with('success', 'Draft copy created. The published homepage has not changed.');
    }

    public function preview(Homepage $homepage)
    {
        abort_unless($homepage->status === 'draft', 404);

        return view('frontend.home-builder', [
            'homepage' => $homepage->load(['sections.reusableSection.mediaAttachments.media', 'sections.mediaAttachments.media']),
            'properties' => Property::published()
                ->with('category:id,name')
                ->latest('published_at')
                ->limit(6)
                ->get(),
            'featuredProperties' => Property::published()
                ->where('is_featured', true)
                ->with('category:id,name')
                ->latest('published_at')
                ->limit(4)
                ->get(),
            'banners' => Banner::visible()
                ->where('placement', 'home')
                ->orderBy('sort_order')
                ->get(),
            'posts' => Post::published()
                ->with('author:id,name')
                ->latest('published_at')
                ->limit(3)
                ->get(),
            'teamMembers' => TeamMember::where('is_active', true)
                ->orderBy('sort_order')
                ->limit(4)
                ->get(),
            'isHomepagePreview' => true,
        ]);
    }

    public function publish(Homepage $homepage)
    {
        abort_unless($homepage->status === 'draft', 409, 'Only a draft homepage can be published.');

        $homepage->update([
            'status' => 'published',
            'published_at' => now(),
            'updated_by' => request()->user()->id,
        ]);

        Cache::forget('homepage:published-default');

        return redirect()
            ->route('admin.homepages.index')
            ->with('success', 'Homepage draft published. Select it as the default to make it live.');
    }

    public function setDefault(Homepage $homepage)
    {
        abort_unless($homepage->status === 'published', 422, 'Only a published homepage can be selected.');
        abort_unless($homepage->is_composition_complete, 422, 'This homepage cannot become the live default until every source section has been extracted, configured and verified.');

        DB::transaction(function () use ($homepage) {
            Homepage::query()->lockForUpdate()->update(['is_default' => false]);

            $homepage->update([
                'is_default' => true,
                'updated_by' => request()->user()->id,
            ]);
        });

        Cache::forget('homepage:published-default');

        return redirect()
            ->route('admin.homepages.index')
            ->with('success', 'Default homepage updated.');
    }
}
