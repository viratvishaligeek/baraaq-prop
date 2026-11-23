@extends('frontend.layouts.theme')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: ($post->excerpt ?: \App\Models\Setting::read('meta_description', '')))
@section('canonical_url', $post->canonical_url ?: route('blog.show', $post))
@section('og_image', $post->og_image ? asset('storage/'.$post->og_image) : ($post->featured_image ? Storage::disk('public')->url($post->featured_image) : (!empty($siteSettings['default_og_image']) ? asset('storage/'.$siteSettings['default_og_image']) : asset('theme-assets/img/hero/hero-10-1.jpg'))))

@section('structured_data')
    @php
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->meta_description ?: $post->excerpt,
            'datePublished' => $post->published_at?->toAtomString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'mainEntityOfPage' => route('blog.show', $post),
            'image' => $post->featured_image ? Storage::disk('public')->url($post->featured_image) : null,
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endsection

@section('content')
    @if (($isPreview ?? false) && $post->status === 'draft')<div class="container py-3"><span class="badge bg-warning text-dark">Draft preview — not publicly published</span></div>@endif
    <div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcumb-bg.jpg') }}">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $post->title }}</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li>{{ $post->title }}</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="space">
        <div class="container">
            <article class="row justify-content-center">
                <div class="col-lg-9">
                    @if ($post->featured_image)
                        <img class="w-100 rounded mb-4" src="{{ Storage::disk('public')->url($post->featured_image) }}" alt="{{ $post->title }}">
                    @endif
                    <h2>{{ $post->title }}</h2>
                    <div class="text-muted mb-4">{{ $post->published_at?->format('d M Y') }}</div>
                    @if ($post->tags->isNotEmpty())<div class="d-flex flex-wrap gap-2 mb-4">@foreach ($post->tags as $tag)<a class="badge bg-light text-dark text-decoration-none" href="{{ route('blog.index', ['tag' => $tag->slug]) }}">{{ $tag->name }}</a>@endforeach</div>@endif
                    <div>{!! \App\Support\HtmlSanitizer::sanitize($post->content) !!}</div>
                </div>
            </article>
        </div>
    </section>

    <section class="space-top">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <h3>Comments</h3>
                    @if (session('comment_submitted'))
                        <div class="alert alert-success">{{ session('comment_submitted') }}</div>
                    @endif

                    @forelse ($post->comments as $comment)
                        <div class="border-bottom py-3">
                            <strong>{{ $comment->name }}</strong>
                            <p class="mb-0">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <p class="text-muted">No approved comments yet.</p>
                    @endforelse

                    <h4 class="mt-4">Leave a comment</h4>
                    <form method="POST" action="{{ route('blog.comments.store', $post) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="{{ old('name') }}" required maxlength="120">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Comment</label>
                                <textarea class="form-control" name="body" rows="4" required>{{ old('body') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary">Submit comment</button>
                                <small class="d-block text-muted mt-2">Comments appear after moderation.</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
