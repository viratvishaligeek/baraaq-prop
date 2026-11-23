@extends('frontend.layouts.theme')
@section('title','Neighborhood Guides in Bareilly | '.config('app.name'))
@section('meta_description','Explore neighborhood guides for Bareilly, Uttar Pradesh, with local area insights to support informed property decisions.')
@section('canonical_url',route('neighborhood-guides.index'))
@section('content')
<div class="breadcumb-wrapper"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">Neighborhood Guides</h1><p>Explore localities across Bareilly, Uttar Pradesh.</p></div></div></div>
<section class="space"><div class="container"><div class="row gy-4">@forelse($guides as $guide)<div class="col-md-6 col-xl-4"><article class="blog-card">@if($guide->hero_image)<img class="w-100" loading="lazy" src="{{ Storage::disk('public')->url($guide->hero_image) }}" alt="{{ $guide->title }}">@endif<div class="blog-content"><span class="text-muted">{{ $guide->locality }}, {{ $guide->city }}</span><h2 class="h4"><a href="{{ route('neighborhood-guides.show',$guide) }}">{{ $guide->title }}</a></h2><p>{{ $guide->excerpt }}</p><a href="{{ route('neighborhood-guides.show',$guide) }}">Explore area</a></div></article></div>@empty<div class="col-12 text-muted">Neighborhood guides will be published as local research is completed.</div>@endforelse</div>{{ $guides->links() }}</div></section>
@endsection