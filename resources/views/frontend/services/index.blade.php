@extends('frontend.layouts.theme')
@section('title', \App\Models\Setting::read('services_meta_title','Services | '.config('app.name')))
@section('meta_description', \App\Models\Setting::read('services_meta_description','Explore our real estate services.'))
@section('canonical_url', route('services.index'))
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcrumb-bg.jpg') }}"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">Our Services</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li>Our Services</li></ul></div></div></div><form method="GET" action="{{ route('services.index') }}" class="container mb-4"><div class="row g-2"><div class="col-md-5"><input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search services"></div><div class="col-auto"><button class="th-btn">Search</button></div></div></form><section class="overflow-hidden space space-extra-bottom" id="service-sec"><div class="container"><div class="row gy-40 justify-content-center">@forelse ($services as $service)
<div class="col-xl-4 col-md-12 fadeinup wow"><div class="service-card"><div class="box-thumb"><a href="{{ route('services.show', $service) }}"><img src="{{ $service->featured_image ? Storage::disk('public')->url($service->featured_image) : asset('theme-assets/img/service/service_inner_1.jpg') }}" alt="thumb"></a></div><div class="box-content"><div class="icon-wrap"><div class="icon"><img src="{{ asset('theme-assets/img/icon/why-icon-1-1.svg') }}" alt="img"></div><div class="number"><h3>01</h3></div></div><h3 class="box-title"><a href="{{ route('services.show', $service) }}">{{ $service->title }}</a></h3><p class="box-text">{{ $service->excerpt }}</p></div></div></div>
@empty
<div class="col-12 text-center text-muted">No services are currently published.</div>
@endforelse</div></section>
<div class="container mt-4">{{ $services->withQueryString()->links('vendor.pagination.buraaqh') }}</div>
@endsection
