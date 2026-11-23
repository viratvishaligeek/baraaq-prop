@extends('frontend.layouts.theme')
@section('title', 'Page not found · '.config('app.name'))
@section('meta_description', 'The page you requested could not be found.')
@section('meta_robots', 'noindex, nofollow')
@section('canonical_url', url()->current())
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcrumb-bg.jpg') }}"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">Error Page</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li>Error Page</li></ul></div></div></div><section class="bg-smoke space" data-bg-src="{{ asset('theme-assets/img/shape/error-shape-bg.png') }}"><div class="shape-mockup d-none d-xxl-block" data-bottom="0%" data-right="0%"><img src="{{ asset('theme-assets/img/error/error-bottom.png') }}" alt="img"></div><div class="error-bg-img" data-mask-src="{{ asset('theme-assets/img/error/error-bg-shape.png') }}"><img src="{{ asset('theme-assets/img/error/error-bg.png') }}" alt="img"></div><div class="container"><div class="row justify-content-center"><div class="col-lg-6"><div class="error-content"><h2 class="error-title">Error 404</h2><p class="error-text">Oops! The page you’re looking for doesn’t exist</p><a href="{{ route('home') }}" class="th-btn radius">Back To Home</a></div></div></div></div></section>
@endsection
