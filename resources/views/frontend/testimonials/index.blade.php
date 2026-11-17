@extends('frontend.layouts.theme')
@section('title','Testimonials · '.($siteSettings['site_title'] ?? config('app.name')))
@section('content')
<div class="breadcumb-wrapper"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">Client testimonials</h1></div></div></div>
<section class="space"><div class="container"><div class="row gy-4">
@forelse($testimonials as $testimonial)
<div class="col-md-6"><article class="content-card h-100"><div class="d-flex align-items-center gap-3 mb-3">@if($testimonial->photo)<img src="{{ Storage::disk('public')->url($testimonial->photo) }}" alt="{{ $testimonial->name }}" class="rounded-circle" width="64" height="64">@endif<div><h3 class="h5 mb-1">{{ $testimonial->name }}</h3><div class="text-muted small">{{ $testimonial->designation }}@if($testimonial->company), {{ $testimonial->company }}@endif</div></div></div>@if($testimonial->rating)<div class="text-warning mb-2" aria-label="Rating {{ $testimonial->rating }} out of 5">{{ str_repeat('★',$testimonial->rating) }}</div>@endif<blockquote class="mb-0">{{ $testimonial->quote }}</blockquote></article></div>
@empty<div class="col-12 text-center text-muted">No testimonials have been published yet.</div>@endforelse
</div>{{ $testimonials->links() }}</div></section>
@endsection
