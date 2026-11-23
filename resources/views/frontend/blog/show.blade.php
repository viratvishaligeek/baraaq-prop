@extends('frontend.layouts.theme')
@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: $post->excerpt)
@section('canonical_url', $post->canonical_url ?: route('blog.show',$post))
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/blog/breadcrumb-bg.jpg') }}"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ $post->title }}</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li>Blog Details</li></ul></div></div></div><section class="th-blog-wrapper blog-details space-top space-extra-bottom"><div class="container"><div class="row"><div class="col-xxl-8 col-lg-7"><div class="th-blog blog-single"><div class="blog-img">@if($post->featured_image)<img src="{{ Storage::disk('public')->url($post->featured_image) }}" alt="{{ $post->title }}">@else<div class="blog-image-placeholder bg-smoke d-flex align-items-center justify-content-center"><span class="text-muted p-5">Article image pending</span></div>@endif</div><div class="blog-content"><div class="blog-meta">
<a href="{{ route('blog.index') }}"><i class="fa-light fa-user"></i> {{ $post->author?->name ?? 'Buraaqh Properties' }}</a>
@if($post->published_at)<a href="{{ route('blog.index') }}"><i class="fa-regular fa-calendar"></i> {{ $post->published_at->format('d/m/Y') }}</a>@endif
@foreach($post->categories as $category)<a href="{{ route('blog.index', ['category' => $category->slug]) }}"><i class="fa-regular fa-tag"></i> {{ $category->name }}</a>@endforeach
</div>
<h2 class="blog-title">{{ $post->title }}</h2>
<div class="cms-post-content">{!! \App\Support\HtmlSanitizer::sanitize($post->content ?? '') !!}</div>
<div class="share-links clearfix">
    <div class="row justify-content-between">
        <div class="col-sm-auto">
            <span class="share-links-title">Tags:</span>
            <div class="tagcloud">
                @forelse($post->tags as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}">{{ $tag->name }}</a>
                @empty
                    <span class="text-muted">No tags</span>
                @endforelse
            </div>
        </div>
        <div class="col-sm-auto text-xl-end">
            <span class="share-links-title">Share this article:</span>
            <div class="th-social">
                @php($facebook = \App\Models\Setting::read('social_facebook', ''))
                @php($instagram = \App\Models\Setting::read('social_instagram', ''))
                @php($linkedin = \App\Models\Setting::read('social_linkedin', ''))
                @if($facebook)<a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>@endif
                @if($linkedin)<a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>@endif
                @if($instagram)<a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>@endif
            </div>
        </div>
    </div>
</div>
</div></div><div class="th-comments-wrap"><h2 class="blog-inner-title h4">Comments ({{ $post->comments->count() }})</h2><ul class="comment-list">@forelse($post->comments as $comment)<li class="th-comment-item"><div class="th-post-comment"><div class="comment-content"><h3 class="name">{{ $comment->name }}</h3><span class="commented-on">{{ $comment->created_at?->format('d M Y') }}</span><p class="text">{{ $comment->body }}</p></div></div></li>@empty<li class="text-muted">No approved comments yet.</li>@endforelse</ul></div><div class="th-comment-form blog-comment-wrap"><div class="form-title mb-25"><h3 class="blog-inner-title h4">Leave a Reply</h3><p class="form-text">Your comment will appear after moderation.</p></div>@if(session('comment_submitted'))<div class="alert alert-success">{{ session('comment_submitted') }}</div>@endif<form method="POST" action="{{ route('blog.comments.store', $post) }}">@csrf<div class="row"><div class="col-md-6 form-group style-border"><i class="fa-regular fa-user"></i><input id="comment-name" name="name" type="text" value="{{ old('name') }}" placeholder="Your name*" class="form-control" required maxlength="120"></div><div class="col-md-6 form-group style-border"><i class="fa-regular fa-envelope"></i><input id="comment-email" name="email" type="email" value="{{ old('email') }}" placeholder="Your email*" class="form-control" required maxlength="190"></div><div class="col-12 form-group style-border"><textarea name="body" id="comment-body" rows="4" placeholder="Comment*" class="form-control" required>{{ old('body') }}</textarea></div><div class="col-12 form-group"><button type="submit" class="th-btn radius">Submit comment</button></div></div></form></div></div><div class="col-xxl-4 col-lg-5"><aside class="sidebar-area"><div class="widget widget_search"><h3 class="widget_title">search</h3><form class="search-form" method="GET" action="{{ route('blog.index') }}"><input type="search" name="q" value="{{ request('q') }}" placeholder="Enter Keyword"> <button type="submit"><i class="far fa-search"></i></button></form></div><div class="widget"><h3 class="widget_title">Related Articles</h3><div class="recent-post-wrap">@forelse($relatedPosts as $relatedPost)<div class="recent-post"><div class="media-img"><a href="{{ route('blog.show',$relatedPost) }}">@if($relatedPost->featured_image)<img src="{{ Storage::disk('public')->url($relatedPost->featured_image) }}" alt="{{ $relatedPost->title }}">@endif</a></div><div class="media-body"><h4 class="post-title"><a class="text-inherit" href="{{ route('blog.show',$relatedPost) }}">{{ $relatedPost->title }}</a></h4><span class="recent-post-meta">{{ $relatedPost->published_at?->format('d M Y') }}</span></div></div>@empty<p class="text-muted">No related articles are available.</p>@endforelse</div></div><div class="widget bg-smoke"><h3 class="widget_title">Contact Us</h3><p>Want to discuss a property question raised in this article?</p><a href="{{ route('contact') }}" class="th-btn radius">Contact our team</a></div><div class="widget widget_banner" data-bg-src="{{ asset('theme-assets/img/bg/widget_banner.jpg') }}"><div class="widget-banner"><h2 class="title">We can help you to find real estate agency</h2><a href="{{ route('contact') }}" class="th-btn radius">Contact with Agent</a></div></div></aside></div></div></div></section>
@if ($relatedPosts->isNotEmpty())<section class="space-top"><div class="container"><h2 class="sec-title mb-4">Related articles</h2><div class="row gy-4">@foreach($relatedPosts as $relatedPost)<div class="col-md-6 col-lg-4"><article class="blog-card"><a href="{{ route('blog.show',$relatedPost) }}">@if($relatedPost->featured_image)<img class="w-100" src="{{ Storage::disk('public')->url($relatedPost->featured_image) }}" alt="{{ $relatedPost->title }}">@endif</a><div class="blog-content"><h3><a href="{{ route('blog.show',$relatedPost) }}">{{ $relatedPost->title }}</a></h3><p>{{ $relatedPost->excerpt }}</p></div></article></div>@endforeach</div></div></section>@endif
@endsection
