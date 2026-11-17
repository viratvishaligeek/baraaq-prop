@extends('frontend.layouts.theme')
@section('title', $service->meta_title ?: $service->title)
@section('meta_description', $service->meta_description ?: \App\Models\Setting::read('meta_description', ''))
@section('canonical_url',$service->canonical_url ?: route('services.show',$service)) @section('og_image',$service->og_image ? asset('storage/'.$service->og_image) : ($service->featured_image ? Storage::disk('public')->url($service->featured_image) : (!empty($siteSettings['default_og_image']) ? asset('storage/'.$siteSettings['default_og_image']) : asset('theme-assets/img/hero/hero-10-1.jpg')))) @section('structured_data')
<script type="application/ld+json">@json([
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => $service->title,
    'description' => $service->meta_description ?: $service->excerpt,
    'url' => route('services.show', $service),
    'image' => $service->featured_image ? Storage::disk('public')->url($service->featured_image) : null,
    'provider' => ['@type' => 'Organization', 'name' => $siteSettings['business_name'] ?? $siteSettings['site_title'] ?? config('app.name')],
])</script>
@endsection
@section('content')
<div class="breadcumb-wrapper"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ $service->title }}</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('services.index') }}">Services</a></li><li>{{ $service->title }}</li></ul></div></div></div>
<section class="space"><div class="container"><div class="row justify-content-center"><div class="col-lg-9">@if($service->featured_image)<img class="w-100 rounded mb-4" src="{{ Storage::disk('public')->url($service->featured_image) }}" alt="{{ $service->title }}">@endif@if($service->excerpt)<p class="lead">{{ $service->excerpt }}</p>@endif<div>{!! \App\Support\HtmlSanitizer::sanitize($service->content) !!}</div></div></div></div></section>
@endsection
