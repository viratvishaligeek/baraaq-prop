@extends('frontend.layouts.theme')
@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description ?: \App\Models\Setting::read('meta_description', ''))
@section('canonical_url', route('pages.show', $page))
@section('og_image', $page->og_image ? asset('storage/' . $page->og_image) : (!empty($siteSettings['default_og_image']) ? asset('storage/' . $siteSettings['default_og_image']) : asset('theme-assets/img/hero/hero-10-1.jpg')))
@section('structured_data')
    @php
        $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $page->meta_title ?: $page->title,
        'description' => $page->meta_description ?: ($siteSettings['meta_description'] ?? ''),
        'url' => ($isHomepage ?? false) ? route('home') : route('pages.show', $page),
    
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
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
    @if ($isPreview ?? false)<div class="container py-3"><span class="badge bg-warning text-dark">Preview — not publicly published</span></div>@endif
    @if ($page->content)
        <section class="space"><div class="container"><div class="row justify-content-center"><div class="col-lg-9">{!! \App\Support\HtmlSanitizer::sanitize($page->content) !!}</div></div></div></section>
    @endif

@endsection
