@extends('frontend.layouts.theme')
@section('title', $service->meta_title ?: $service->title)
@section('meta_description', $service->meta_description ?: \\App\\Models\\Setting::read('meta_description', ''))
@section('content')
<div class="breadcumb-wrapper"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ $service->title }}</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('services.index') }}">Services</a></li><li>{{ $service->title }}</li></ul></div></div></div>
<section class="space"><div class="container"><div class="row justify-content-center"><div class="col-lg-9">@if($service->featured_image)<img class="w-100 rounded mb-4" src="{{ Storage::disk('public')->url($service->featured_image) }}" alt="{{ $service->title }}">@endif@if($service->excerpt)<p class="lead">{{ $service->excerpt }}</p>@endif<div>{!! nl2br(e($service->content)) !!}</div></div></div></div></section>
@endsection
