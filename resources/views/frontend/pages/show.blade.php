@extends('frontend.layouts.theme')
@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description ?: \App\Models\Setting::read('meta_description', ''))
@section('canonical_url', $page->canonical_url ?: (($isHomepage ?? false) ? route('home') : route('pages.show', $page)))
@section('og_image', $page->og_image ? asset('storage/' . $page->og_image) : (!empty($siteSettings['default_og_image']) ? asset('storage/' . $siteSettings['default_og_image']) : asset('theme-assets/img/hero/hero-10-1.jpg')))
@section('structured_data')
    <script type="application/ld+json">@json([
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $page->meta_title ?: $page->title,
        'description' => $page->meta_description ?: ($siteSettings['meta_description'] ?? ''),
        'url' => ($isHomepage ?? false) ? route('home') : route('pages.show', $page),
    ])</script>
@endsection
@section('content')
    <div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcumb-bg.jpg') }}">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $page->title }}</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>{{ $page->title }}</li>
                </ul>
            </div>
        </div>
    </div>
    @if ($page->sections->isNotEmpty())
        @include('frontend.partials.page-sections', ['page' => $page])
    @elseif ($page->content)
        <section class="space">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-9">{!! nl2br(e($page->content)) !!}</div>
                </div>
            </div>
        </section>
    @endif
    @if (($isBuilderPreview ?? false) && $page->status === 'draft')
        <div class="container py-3"><span class="badge bg-warning text-dark">Draft preview — not publicly published</span></div>
    @endif
@endsection
