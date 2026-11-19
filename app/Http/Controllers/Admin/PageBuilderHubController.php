<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Service;

class PageBuilderHubController extends Controller
{
    public function index()
    {
        $user = request()->user();
        abort_unless($user && (
            $user->isAdministrator()
            || $user->hasPermission('pages.view')
            || $user->hasPermission('services.view')
            || $user->hasPermission('pages.manage')
            || $user->hasPermission('services.manage')
        ), 403);

        return view('admin.page-builder.index', [
            'pages' => Page::query()->latest('updated_at')->paginate(15, ['*'], 'pages_page'),
            'services' => Service::query()->latest('updated_at')->paginate(15, ['*'], 'services_page'),
        ]);
    }
}
