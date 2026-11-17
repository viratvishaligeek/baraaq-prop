<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
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
            'categories' => Category::where('type', 'post')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function post(Post $post)
    {
        abort_unless($post->status === 'published' && $post->published_at && $post->published_at->lte(now()), 404);

        return view('frontend.blog.show', ['post' => $post->load(['categories', 'comments' => fn ($query) => $query->where('status', 'approved')->latest()])]);
    }

    public function storeComment(Request $request, Post $post)
    {
        abort_unless($post->status === 'published' && $post->published_at && $post->published_at->lte(now()), 404);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:190'], 'body' => ['required', 'string', 'min:5', 'max:5000']]);
        $post->comments()->create($data + ['status' => 'pending']);
        return back()->with('comment_submitted', 'Your comment has been received and is awaiting moderation.');
    }

    public function services()
    {
        $services = Service::published()->latest('published_at')->paginate(12);
        return view('frontend.services.index', compact('services'));
    }

    public function service(Service $service)
    {
        abort_unless($service->status === 'published' && $service->published_at && $service->published_at->lte(now()), 404);
        return view('frontend.services.show', compact('service'));
    }

    public function team()
    {
        $members = TeamMember::where('status', 'published')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->paginate(12);
        return view('frontend.team.index', compact('members'));
    }

    public function testimonials()
    {
        $testimonials = Testimonial::where('status', 'published')->where('is_active', true)->orderBy('sort_order')->latest()->paginate(12);
        return view('frontend.testimonials.index', compact('testimonials'));
    }

    public function page(Page $page)
    {
        abort_unless($page->status === 'published', 404);

        return view('frontend.pages.show', compact('page'));
    }
}
