<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function blog(Request $request)
    {
        $posts = Post::published()->with(['author:id,name', 'categories'])
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('categories', fn ($categories) => $categories->where('slug', $request->string('category')));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('frontend.blog.index', [
            'posts' => $posts,
            'activeCategory' => $request->query('category'),
            'categories' => Category::where('type', 'post')->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function post(Post $post)
    {
        abort_unless($post->status === 'published' && $post->published_at && $post->published_at->lte(now()), 404);

        return view('frontend.blog.show', compact('post'));
    }

    public function page(Page $page)
    {
        abort_unless($page->status === 'published', 404);

        return view('frontend.pages.show', compact('page'));
    }
}
