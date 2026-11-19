<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;

class ContentPreviewController extends Controller
{
    public function show(string $type, int $id)
    {
        $user = request()->user();
        abort_unless(in_array($type, ['posts', 'pages', 'services'], true), 404);
        $permission = $type . '.view';
        $managePermission = $type . '.manage';
        abort_unless($user && ($user->isAdministrator() || $user->hasPermission($permission) || $user->hasPermission($managePermission)), 403);

        return match ($type) {
            'posts' => $this->post($id),
            'pages' => $this->page($id),
            'services' => $this->service($id),
        };
    }

    private function post(int $id)
    {
        $post = Post::with(['author:id,name', 'categories', 'tags', 'comments' => fn ($query) => $query->where('status', 'approved')->latest()])
            ->findOrFail($id);

        return view('frontend.blog.show', ['post' => $post, 'isBuilderPreview' => true]);
    }

    private function page(int $id)
    {
        $page = Page::with('sections.mediaAttachments.media')->findOrFail($id);

        return view('frontend.pages.show', ['page' => $page, 'isHomepage' => false, 'isBuilderPreview' => true]);
    }

    private function service(int $id)
    {
        $service = Service::with('sections.mediaAttachments.media')->findOrFail($id);

        return view('frontend.services.show', ['service' => $service, 'isBuilderPreview' => true]);
    }
}
