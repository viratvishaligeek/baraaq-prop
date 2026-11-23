@extends('frontend.layouts.theme')

@section('title', $service->meta_title ?: $service->title)
@section('meta_description', $service->meta_description ?: \App\Models\Setting::read('meta_description', ''))
@section('canonical_url', $service->canonical_url ?: route('services.show', $service))
@section('og_image', $service->og_image ? asset('storage/'.$service->og_image) : ($service->featured_image ? Storage::disk('public')->url($service->featured_image) : (!empty($siteSettings['default_og_image']) ? asset('storage/'.$siteSettings['default_og_image']) : asset('theme-assets/img/hero/hero-10-1.jpg'))))

@section('structured_data')
    @php
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->title,
            'description' => $service->meta_description ?: $service->excerpt,
            'url' => route('services.show', $service),
            'image' => $service->featured_image ? Storage::disk('public')->url($service->featured_image) : null,
            'provider' => [
                '@type' => 'Organization',
                'name' => $siteSettings['business_name'] ?? $siteSettings['site_title'] ?? config('app.name'),
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endsection

@section('content')
    <div class="breadcumb-wrapper">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $service->title }}</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('services.index') }}">Services</a></li>
                    <li>{{ $service->title }}</li>
                </ul>
            </div>
        </div>
    </div>

    @if ($isPreview ?? false)<div class="container py-3"><span class="badge bg-warning text-dark">Preview — not publicly published</span></div>@endif
    <section class="space"><div class="container"><div class="row justify-content-center"><div class="col-lg-9">
        @if ($service->featured_image)<img class="w-100 rounded mb-4" src="{{ Storage::disk('public')->url($service->featured_image) }}" alt="{{ $service->title }}">@endif
        @if ($service->excerpt)<p class="lead">{{ $service->excerpt }}</p>@endif
        <div>{!! \App\Support\HtmlSanitizer::sanitize($service->content) !!}</div>
        @if ($service->features->isNotEmpty())
            <section class="space-top"><h2>What this service includes</h2><div class="row gy-3">
                @foreach ($service->features as $feature)
                    <div class="col-md-6"><div class="border rounded p-3 h-100"><h3 class="h5">{{ $feature->title }}</h3>@if($feature->description)<p class="mb-0">{{ $feature->description }}</p>@endif</div></div>
                @endforeach
            </div></section>
        @endif
        @if ($service->images->isNotEmpty())
            <section class="space-top"><h2>Inside this service</h2><div class="row gy-3">
                @foreach ($service->images as $image)
                    <div class="col-6 col-lg-4"><img class="w-100 rounded" loading="lazy" src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $image->alt_text ?: $service->title }}"></div>
                @endforeach
            </div></section>
        @endif
    </div></div></div></section>

@endsection
