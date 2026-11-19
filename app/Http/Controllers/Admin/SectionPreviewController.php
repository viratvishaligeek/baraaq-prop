<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Page;
use App\Services\Widgets\CmsWidgetRegistry;

class SectionPreviewController extends Controller
{
    public function __construct(private readonly CmsWidgetRegistry $registry)
    {
    }

    public function show(string $sectionKey)
    {
        $user = request()->user();
        abort_unless($user && (
            $user->isAdministrator()
            || $user->hasPermission('settings.manage')
            || $user->hasPermission('properties.manage')
            || $user->hasPermission('posts.manage')
            || $user->hasPermission('pages.manage')
            || $user->hasPermission('widgets.manage')
            || $user->hasPermission('services.manage')
        ), 403);

        $definition = $this->registry->find($sectionKey);
        abort_unless($definition, 404);

        $section = new HomepageSection([
            'id' => 'preview-' . substr(sha1($sectionKey), 0, 10),
            'instance_key' => 'preview-' . $sectionKey,
            'section_key' => $sectionKey,
            'section_type' => $definition['type'],
            'source_design' => $definition['source_design'],
            'sort_order' => 0,
            'is_enabled' => true,
            'config' => $definition['defaults'] ?? [],
            'data_source' => [],
        ]);
        $section->setRelation('mediaAttachments', collect());

        $page = new Page(['title' => $definition['label'] . ' preview']);
        $page->setRelation('sections', collect([$section]));

        return view('admin.sections.preview', [
            'page' => $page,
            'definition' => $definition,
        ]);
    }
}
