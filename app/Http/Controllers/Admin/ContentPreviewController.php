<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;

class ContentPreviewController extends Controller
{
    private function authorizePreview(string $type): void
    {
        $user = request()->user();

        abort_unless(
            $user && ($user->isAdministrator()
                || $user->hasPermission($type.'.manage')
                || $user->hasPermission($type.'.view')
                || $user->hasPermission($type.'.edit')),
            403
        );
    }

    public function show(string $type, int $id)
    {
        abort_unless(in_array($type, ['posts', 'pages', 'services'], true), 404);
        $this->authorizePreview($type);

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

        return view('frontend.blog.show', ['post' => $post, 'isPreview' => true]);
    }

    private function page(int $id)
    {
        $page = Page::findOrFail($id);

        return view('frontend.pages.show', ['page' => $page, 'isHomepage' => false, 'isPreview' => true]);
    }

    private function service(int $id)
    {
        $service = Service::findOrFail($id);

        return view('frontend.services.show', ['service' => $service, 'isPreview' => true]);
    }
}
