@extends('frontend.layouts.theme')
@section('title', \App\Models\Setting::read('properties_meta_title', 'Properties in Bareilly | '.\App\Models\Setting::read('site_title', config('app.name'))))
@section('meta_description', \App\Models\Setting::read('properties_meta_description', 'Browse published property listings and enquire about verified availability in Bareilly, Uttar Pradesh.'))
@section('canonical_url', route('properties.index'))
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcumb-bg.jpg') }}">
<div class="container">
<div class="breadcumb-content">
<h1 class="breadcumb-title">Properties</h1>
<ul class="breadcumb-menu">
<li>
<a href="{{ route('home') }}">Home</a>
</li>
<li>Properties</li>
</ul>
</div>
</div>
</div>
<section class="th-blog-wrapper space-top space-extra-bottom">
<div class="container">
<form method="GET" action="{{ route('properties.index') }}" class="property-search-form mb-5">
<div class="row g-3">
<div class="col-lg-3 col-md-6">
<input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search properties">
</div>
<div class="col-lg-2 col-md-6">
<input class="form-control" name="city" value="{{ request('city') }}" placeholder="City">
</div>
<div class="col-lg-2 col-md-6">
<select class="form-select" name="listing_type">
<option value="">Buy or rent</option>
<option value="sale" @selected(request('listing_type')==='sale')>For sale</option>
<option value="rent" @selected(request('listing_type')==='rent')>For rent</option>
<option value="lease" @selected(request('listing_type')==='lease')>For lease</option>
</select>
</div>
<div class="col-lg-2 col-md-6">
<input class="form-control" name="property_type" value="{{ request('property_type') }}" placeholder="Type">
</div>
<div class="col-lg-1 col-md-6">
<input class="form-control" type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min">
</div>
<div class="col-lg-1 col-md-6">
<input class="form-control" type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max">
</div>
<div class="col-lg-1 col-md-12">
<button class="th-btn w-100">Search</button>
</div>
</div>
</form>
<div class="row gy-4">@forelse($properties as $property)<div class="col-xl-4 col-lg-6 col-md-6">
<div class="popular-list-1 grid-style">
<div class="thumb-wrapper">
@if ($property->featured_image)
<a class="popular-popup-image" href="{{ Storage::disk('public')->url($property->featured_image) }}">
<img src="{{ Storage::disk('public')->url($property->featured_image) }}" alt="{{ $property->title }}">
</a>
@else
<div class="bg-light d-flex align-items-center justify-content-center text-muted" style="min-height: 180px">Image not provided</div>
@endif
<div class="popular-badge">
<img src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}" alt="icon">
<p>{{ $property->listing_type==='rent'?'For Rent':'For Sale' }}</p>
</div>
</div>
<div class="property-content">
<div class="media-body">
<h3 class="box-title">
<a href="{{ route('properties.show',$property) }}">{{ $property->title }}</a>
</h3>
<div class="box-text">
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/popular-location.svg') }}" alt="icon">
</div>{{ $property->address }} {{ $property->city }}</div>
</div>
<ul class="property-featured">
<li>
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/bed.svg') }}" alt="icon">
</div>Bed {{ $property->bedrooms??'—' }}</li>
<li>
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/bath.svg') }}" alt="icon">
</div>Bath {{ $property->bathrooms??'—' }}</li>
<li>
<div class="icon">
<img src="{{ asset('theme-assets/img/icon/sqft.svg') }}" alt="icon">
</div>{{ $property->area?number_format((float)$property->area).' '.$property->area_unit:'—' }}</li>
</ul>
<div class="property-bottom">
<h6 class="box-title">{{ $property->price?number_format((float)$property->price,2):$property->price_label }}</h6>
<a class="th-btn sm style3 pill" href="{{ route('properties.show',$property) }}">View More</a>
</div>
</div>
</div>
</div>@empty<div class="col-12 text-center py-5">No matching properties found.</div>@endforelse</div>
<div class="mt-5">{{ $properties->links() }}</div>
</div>
</section>
@endsection
