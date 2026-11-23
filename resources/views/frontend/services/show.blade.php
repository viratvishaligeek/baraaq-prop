@extends('frontend.layouts.theme')
@section('title', $service->meta_title ?: $service->title)
@section('meta_description', $service->meta_description ?: $service->excerpt)
@section('canonical_url', $service->canonical_url ?: route('services.show',$service))
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcrumb-bg.jpg') }}"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ $service->title }}</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li>{{ $service->title }}</li></ul></div></div></div><section class="space space-extra-bottom"><div class="container"><div class="row"><div class="col-xxl-8 col-lg-8"><div class="page-single mb-2"><div class="page-img">
@if($service->featured_image)
    <img src="{{ Storage::disk('public')->url($service->featured_image) }}" alt="{{ $service->title }}">
@else
    <div class="service-image-placeholder bg-smoke d-flex align-items-center justify-content-center"><p class="text-muted p-5 mb-0">A service image will be published here when approved.</p></div>
@endif
<ul class="tag-wrap"><li><span>Service</span></li></ul>
</div><div class="page-content"><h2 class="sec-title">{{ $service->title }}</h2><p>{{ $service->excerpt }}</p><div class="mb-30">{!! \App\Support\HtmlSanitizer::sanitize($service->content ?? "") !!}</div><h2 class="sec-title mb-3">Highlights</h2><div class="checklist mb-50"><ul>@forelse($service->features as $feature)<li><i class="fa-regular fa-circle-check"></i> <strong>{{ $feature->title }}</strong> @if($feature->description) — {{ $feature->description }} @endif</li>@empty<li>Service features will be published soon.</li>@endforelse</ul></div><div class="row gy-4 mb-80"><div class="col-md-12"><h2 class="sec-title mb-2">From Amazing Gallery</h2></div>@forelse($service->images as $image)<div class="col-xl-{{ in_array(($loop->index % 6), [0, 3, 4], true) ? 7 : 5 }}"><div class="property-gallery-card"><div class="property-gallery-card-img"><img class="w-100" src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $image->alt_text ?: $service->title }}"></div><a class="icon-btn popup-image" href="{{ Storage::disk('public')->url($image->path) }}"><i class="fa-solid fa-eye"></i></a></div></div>@empty<div class="col-12 text-muted">No service detail images have been published.</div>@endforelse</div><div class="row mb-1">
    <div class="col-12">
        <div class="th-comments-wrap mb-0 mt-0">
            <h2 class="blog-inner-title h4">Comments ({{ $service->comments->count() }})</h2>
            <ul class="comment-list">
                @forelse($service->comments as $comment)
                    <li class="th-comment-item">
                        <div class="th-post-comment">
                            <div class="comment-avater"><span aria-hidden="true">{{ mb_strtoupper(mb_substr($comment->name, 0, 1)) }}</span></div>
                            <div class="comment-content">
                                <h3 class="name">{{ $comment->name }}</h3>
                                <span class="commented-on">{{ $comment->created_at?->format('d M Y') }}</span>
                                <p class="text">{{ $comment->body }}</p>
                                <div class="reply_and_edit"><a href="#service-comment-form" class="reply-btn">Reply</a></div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-muted">No approved comments yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
<div class="th-comment-form mb-0 blog-comment-wrap" id="service-comment-form">
    <div class="form-title mb-25"><h3 class="blog-inner-title h4">Leave a Reply</h3><p class="form-text">Your comment will appear after moderation.</p></div>
    @if(session('service_comment_submitted'))<div class="alert alert-success">{{ session('service_comment_submitted') }}</div>@endif
    <form method="POST" action="{{ route('services.comments.store', $service) }}">@csrf
        <div class="row">
            <div class="col-md-6 form-group style-border"><i class="fa-regular fa-user"></i><input name="name" type="text" value="{{ old('name') }}" placeholder="Your name*" class="form-control" required maxlength="120"></div>
            <div class="col-md-6 form-group style-border"><i class="fa-regular fa-envelope"></i><input name="email" type="email" value="{{ old('email') }}" placeholder="Your email*" class="form-control" required maxlength="190"></div>
            <div class="col-12 form-group style-border"><textarea name="body" rows="4" placeholder="Comment*" class="form-control" required minlength="5" maxlength="5000">{{ old('body') }}</textarea></div>
            <div class="col-12 form-group"><button type="submit" class="th-btn radius">Submit comment</button></div>
        </div>
    </form>
</div>
<div class="row mt-3"></div></div></div></div><div class="col-xxl-4 col-lg-4"><aside class="sidebar-area"><div class="widget widget_search"><h3 class="widget_title">search</h3><form class="search-form" method="GET" action="{{ route('services.index') }}"><input type="search" name="q" value="{{ request('q') }}" placeholder="Enter Keyword"> <button type="submit"><i class="far fa-search"></i></button></form></div><div class="widget"><h3 class="widget_title">Featured Listings</h3><div class="recent-post-wrap featured-listing">
@forelse($relatedProperties as $relatedProperty)
    <div class="recent-post">
        <div class="media-img"><a href="{{ route('properties.show', $relatedProperty) }}">@if($relatedProperty->featured_image)<img src="{{ Storage::disk('public')->url($relatedProperty->featured_image) }}" alt="{{ $relatedProperty->title }}">@endif</a></div>
        <div class="media-body">
            <h4 class="post-title"><a class="text-inherit" href="{{ route('properties.show', $relatedProperty) }}">{{ $relatedProperty->title }}</a></h4>
            <div class="property-features-wrap"><div class="property-features-item"><div class="thumb"><img src="{{ asset('theme-assets/img/icon/bed.svg') }}" alt="Bedrooms"></div><h5 class="feature-title">Bed {{ $relatedProperty->bedrooms ?? '—' }}</h5></div><div class="divider"></div><div class="property-features-item"><div class="thumb"><img src="{{ asset('theme-assets/img/icon/bath.svg') }}" alt="Bathrooms"></div><h5 class="feature-title">Bath {{ $relatedProperty->bathrooms ?? '—' }}</h5></div><div class="divider"></div><div class="property-features-item"><div class="thumb"><img src="{{ asset('theme-assets/img/icon/sqft.svg') }}" alt="Area"></div><h5 class="feature-title">{{ $relatedProperty->area ? number_format((float) $relatedProperty->area) . ' ' . ($relatedProperty->area_unit ?? 'sqft') : '— sqft' }}</h5></div></div>
            <div class="recent-post-meta">{{ $relatedProperty->price ? number_format((float) $relatedProperty->price, 2) : ($relatedProperty->price_label ?? 'Price on request') }}</div>
        </div>
    </div>
@empty
    <p class="text-muted">No property listings are currently published.</p>
@endforelse
</div></div><div class="widget bg-smoke"><h3 class="widget_title">Contact Us</h3><p>Have a question about this service?</p><a href="{{ route('contact') }}" class="th-btn radius">Contact our team</a></div><div class="widget widget_banner" data-bg-src="{{ asset('theme-assets/img/bg/widget_banner.jpg') }}"><div class="widget-banner"><h2 class="title">We can help you to find real estate agency</h2><a href="{{ route('contact') }}" class="th-btn radius">Contact with Agent</a></div></div></aside></div></div></div></section>
@endsection
