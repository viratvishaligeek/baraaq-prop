<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
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
            'homepage' => $homepage->load('sections'),
            'availableSections' => config('cms-sections.sections', []),
        ]);
    }

    public function duplicate(Homepage $homepage)
    {
        $draft = DB::transaction(function () use ($homepage) {
            $copy = $homepage->replicate();
            $copy->name = $homepage->name.' (Draft)';
            $copy->slug = Str::slug($homepage->slug.'-draft-'.now()->format('YmdHis').'-'.Str::lower(Str::random(5)));
            $copy->status = 'draft';
            $copy->is_default = false;
            $copy->published_at = null;
            $copy->created_by = request()->user()->id;
            $copy->updated_by = request()->user()->id;
            $copy->save();

            foreach ($homepage->sections as $section) {
                $sectionCopy = $section->replicate();
                $sectionCopy->homepage_id = $copy->id;
                $sectionCopy->instance_key = (string) Str::uuid();
                $sectionCopy->save();
            }

            return $copy;
        });

        return redirect()
            ->route('admin.homepages.builder', $draft)
            ->with('success', 'Draft copy created. The published homepage has not changed.');
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
