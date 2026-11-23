@extends('frontend.layouts.theme')
@section('title', \App\Models\Setting::read('blog_meta_title', 'Journal · '.($siteSettings['site_title'] ?? config('app.name'))))
@section('meta_description', \App\Models\Setting::read('blog_meta_description', 'Property guidance and practical real estate articles for Bareilly, Uttar Pradesh.'))
@section('canonical_url', route('blog.index'))
@section('content')<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcumb-bg.jpg') }}">
<div class="container">
<div class="breadcumb-content">
<h1 class="breadcumb-title">Latest News</h1>
<ul class="breadcumb-menu">
<li>
<a href="{{ route('home') }}">Home</a>
</li>
<li>Blog</li>
</ul>
</div>
</div>
</div>
<section class="space">
<div class="container">
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-5"><label class="visually-hidden" for="blog-search">Search articles</label><input id="blog-search" class="form-control" type="search" name="q" value="{{ $searchTerm ?? '' }}" placeholder="Search articles"></div>
    <div class="col-auto"><button class="th-btn" type="submit">Search</button><a class="btn btn-link" href="{{ route('blog.index') }}">Reset</a></div>
</form>
<div class="d-flex flex-wrap gap-2 mb-4">
<a class="btn btn-sm {{ empty($activeCategory) ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('blog.index') }}">All articles</a>@foreach($categories as $category)<a class="btn btn-sm {{ $activeCategory === $category->slug ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('blog.index',['category'=>$category->slug]) }}">{{ $category->name }}</a>@endforeach</div>
<div class="row gy-4">@forelse($posts as $post)<div class="col-md-6 col-xl-4">
<article class="blog-card">
<a href="{{ route('blog.show',$post) }}">
<img class="w-100" src="{{ $post->featured_image?Storage::disk('public')->url($post->featured_image):asset('theme-assets/img/blog/blog_1_1.jpg') }}" alt="{{ $post->title }}">
</a>
<div class="blog-content">
<h3>
<a href="{{ route('blog.show',$post) }}">{{ $post->title }}</a>
</h3>
<p>{{ $post->excerpt }}</p>
<a href="{{ route('blog.show',$post) }}">Read more</a>
</div>
</article>
</div>@empty<div class="col-12 text-center">No articles published yet.</div>@endforelse</div>{{ $posts->links() }}</div>
</section>@endsection
