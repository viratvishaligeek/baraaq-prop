<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Widgets\CmsWidgetRegistry;

class SectionLibraryController extends Controller
{
    public function __construct(private readonly CmsWidgetRegistry $registry) {}

    public function index()
    {
        $user = request()->user();
        abort_unless($user && (
            $user->isAdministrator()
            || $user->hasPermission('settings.manage')
            || $user->hasPermission('widgets.manage')
            || $user->hasPermission('pages.manage')
            || $user->hasPermission('services.manage')
        ), 403);

        return view('admin.sections.index', [
            'groups' => collect($this->registry->all())->groupBy('type'),
        ]);
    }
}
