<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
