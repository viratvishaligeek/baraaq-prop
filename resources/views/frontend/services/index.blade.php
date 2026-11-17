@extends('frontend.layouts.theme')
@section('title', 'Services · '.\\App\\Models\\Setting::read('site_title', config('app.name')))
@section('content')
<div class="breadcumb-wrapper"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">Our Services</h1></div></div></div>
<section class="space"><div class="container"><div class="row gy-4">
@forelse($services as $service)
<div class="col-md-6 col-xl-4"><article class="blog-card">@if($service->featured_image)<img class="w-100" src="{{ Storage::disk('public')->url($service->featured_image) }}" alt="{{ $service->title }}">@endif<div class="blog-content"><h3><a href="{{ route('services.show',$service) }}">{{ $service->title }}</a></h3><p>{{ $service->excerpt }}</p><a href="{{ route('services.show',$service) }}">Learn more</a></div></article></div>
@empty<div class="col-12 text-center text-muted">No services are currently published.</div>@endforelse
</div>{{ $services->links() }}</div></section>
@endsection
