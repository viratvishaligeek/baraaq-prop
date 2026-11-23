@extends('frontend.layouts.theme')

@section('title', 'Page not found · '.($siteSettings['site_title'] ?? config('app.name')))
@section('meta_description', 'The page you requested could not be found.')

@section('content')
    <section class="space">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <p class="sub-title">404 · Page not found</p>
                    <h1 class="sec-title">We couldn’t find that page.</h1>
                    <p class="mb-4">The link may be outdated, or the page may have been moved or unpublished.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a class="th-btn" href="{{ route('home') }}">Back to homepage</a>
                        <a class="th-btn style-border" href="{{ route('properties.index') }}">Browse properties</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
