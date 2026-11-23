<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function blog(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'category' => ['nullable', 'string', 'max:120'], 'tag' => ['nullable', 'string', 'max:120']]);
        $posts = Post::published()->with(['author:id,name', 'categories', 'tags'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('excerpt', 'like', '%'.$term.'%')))
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('categories', fn ($categories) => $categories->where('slug', $request->string('category')));
            })
            ->when($request->filled('tag'), function ($query) use ($request) {
                $query->whereHas('tags', fn ($tags) => $tags->where('slug', $request->string('tag')));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('frontend.blog.index', [
            'posts' => $posts,
            'searchTerm' => $filters['q'] ?? '',
            'activeCategory' => $request->query('category'),
            'activeTag' => $request->query('tag'),
            'categories' => Category::where('type', 'post')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function post(Post $post)
    {
        abort_unless($post->status === 'published' && $post->published_at && $post->published_at->lte(now()), 404);

        $post->load(['author:id,name', 'categories', 'tags', 'comments' => fn ($query) => $query->where('status', 'approved')->latest()]);
        $relatedPosts = Post::published()
            ->whereKeyNot($post->id)
            ->when($post->categories->isNotEmpty(), fn ($query) => $query->whereHas('categories', fn ($categories) => $categories->whereIn('categories.id', $post->categories->pluck('id'))))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('frontend.blog.show', compact('post', 'relatedPosts'));
    }

    public function storeComment(Request $request, Post $post)
    {
        abort_unless($post->status === 'published' && $post->published_at && $post->published_at->lte(now()), 404);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:190'], 'body' => ['required', 'string', 'min:5', 'max:5000']]);
        $post->comments()->create($data + ['status' => 'pending']);
        return back()->with('comment_submitted', 'Your comment has been received and is awaiting moderation.');
    }

    public function services(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $services = Service::published()
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('excerpt', 'like', '%'.$term.'%')))
            ->latest('published_at')->paginate(12)->withQueryString();
        return view('frontend.services.index', compact('services'));
    }

    public function service(Service $service)
    {
        abort_unless($service->status === 'published' && $service->published_at && $service->published_at->lte(now()), 404);
        $service->load(['features', 'images']);
        $relatedServices = Service::published()
            ->whereKeyNot($service->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('frontend.services.show', compact('service', 'relatedServices'));
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

    public function contact()
    {
        $page = Page::query()
            ->where('slug', 'contact')
            ->where('status', 'published')
            ->first();

        return view('frontend.contact', compact('page'));
    }

    public function about()
    {
        $page = Page::query()
            ->where('slug', 'about')
            ->where('status', 'published')
            ->firstOrFail();

        return view('frontend.about', [
            'page' => $page,
            'canonicalUrl' => $page->canonical_url ?: route('about'),
            'members' => TeamMember::query()->where('status', 'published')->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'testimonials' => Testimonial::query()->where('status', 'published')->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'services' => Service::published()->orderBy('published_at')->limit(8)->get(),
            'properties' => Property::published()->with('category')->latest('published_at')->limit(8)->get(),
            'propertyCount' => Property::published()->count(),
            'serviceCount' => Service::published()->count(),
            'teamCount' => TeamMember::query()->where('status', 'published')->where('is_active', true)->count(),
            'testimonialCount' => Testimonial::query()->where('status', 'published')->where('is_active', true)->count(),
        ]);
    }

    public function page(Page $page)
    {
        abort_unless($page->status === 'published', 404);

        return view('frontend.pages.show', [
            'page' => $page,
            'canonicalUrl' => $page->canonical_url ?: route('pages.show', $page),
        ]);
    }
}
