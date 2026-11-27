@extends('frontend.layouts.theme')
@section('title', $guide->meta_title ?: $guide->title)
@section('meta_description', $guide->meta_description ?: $guide->excerpt)
@section('canonical_url', $guide->canonical_url ?: route('neighborhood-guides.show', $guide))
@section('og_image', $guide->og_image ? Storage::disk('public')->url($guide->og_image) : ($guide->hero_image ?
    Storage::disk('public')->url($guide->hero_image) : ''))
@section('structured_data')
    <script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'Place','name'=>$guide->title,'description'=>$guide->meta_description?:$guide->excerpt,'address'=>['@type'=>'PostalAddress','addressLocality'=>$guide->locality,'addressRegion'=>$guide->state,'addressCountry'=>'IN'],'url'=>route('neighborhood-guides.show',$guide)],JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!}</script>
@endsection
@section('content')
    <div class="breadcumb-wrapper">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $guide->title }}</h1>
                <p>{{ $guide->locality }}, {{ $guide->city }}, {{ $guide->state }}</p>
            </div>
        </div>
    </div>
    <section class="space">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    @if ($guide->hero_image)
                        <img class="w-100 rounded mb-4" src="{{ Storage::disk('public')->url($guide->hero_image) }}"
                            alt="{{ $guide->alt_text ?: $guide->title }}">
                        @endif @if ($guide->excerpt)
                            <p class="lead">{{ $guide->excerpt }}</p>
                        @endif
                        <div>{!! \App\Support\HtmlSanitizer::sanitize($guide->content) !!}</div>
                </div>
            </div>
        </div>
    </section>
@endsection
