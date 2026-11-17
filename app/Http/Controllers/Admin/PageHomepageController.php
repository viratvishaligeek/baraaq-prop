<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PageHomepageController extends Controller
{
    public function store(Page $page)
    {
        abort_unless(
            auth()->user()->isAdministrator() || auth()->user()->hasPermission('pages.manage'),
            403
        );

        abort_unless($page->status === 'published', 422, 'Only a published page can be selected as the homepage.');

        DB::transaction(function () use ($page): void {
            Homepage::query()->lockForUpdate()->update(['is_default' => false]);

            Page::query()
                ->whereNotNull('homepage_key')
                ->lockForUpdate()
                ->update(['homepage_key' => null]);

            $page->update(['homepage_key' => 'primary']);
        });

        Cache::forget('homepage:published-default');
        Cache::forget('homepage:published-default:id');

        return redirect()
            ->route('admin.content.index', 'pages')
            ->with('success', 'The selected page is now the website homepage.');
    }
}
