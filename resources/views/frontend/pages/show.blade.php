@extends('frontend.layouts.theme') @section('title',$page->meta_title?:$page->title) @section('meta_description',$page->meta_description?:\App\Models\Setting::read('meta_description','')) @section('canonical_url',$page->canonical_url ?: route('pages.show',$page)) @section('og_image',$page->og_image ? asset('storage/'.$page->og_image) : asset('storage/'.($siteSettings['default_og_image'] ?? ''))) @section('structured_data')
<script type="application/ld+json">@json([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $page->meta_title ?: $page->title,
    'description' => $page->meta_description ?: ($siteSettings['meta_description'] ?? ''),
    'url' => route('pages.show', $page),
])</script>
@endsection
@section('content')<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcumb-bg.jpg') }}"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ $page->title }}</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li>{{ $page->title }}</li></ul></div></div></div><section class="space"><div class="container"><div class="row justify-content-center"><div class="col-lg-9">{!! nl2br(e($page->content)) !!}</div></div></div></section>@endsection