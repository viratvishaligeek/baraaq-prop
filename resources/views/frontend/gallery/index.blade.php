@extends('frontend.layouts.theme')
@section('title', \App\Models\Setting::read('gallery_meta_title','Gallery | '.config('app.name')))
@section('meta_description', \App\Models\Setting::read('gallery_meta_description','Explore our property gallery.'))
@section('canonical_url', route('gallery.index'))
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/blog/breadcrumb-bg.jpg') }}"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ \App\Models\Setting::read('gallery_page_heading', 'Amazing Gallery') }}</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li>Gallery</li></ul></div></div></div><section class="gallery-inner-sec space"><div class="container"><div class="row justify-content-center"><div class="col-xl-7"><div class="title-area text-center mb-2"><p class="sub-title text-anime-style-2"><span class="double-line"></span> Gallery</p><h2 class="sec-title mb-4 text-anime-style-3">{{ \App\Models\Setting::read('gallery_section_heading', 'Stunning Pillar Gallery') }}</h2><p class="sec-text">{{ \App\Models\Setting::read('gallery_page_intro', 'Explore approved property and business photographs.') }}</p><div class="filter-menu style-2 indicator-active filter-menu-active mt-2 justify-content-center fadeinup wow" data-wow-duration="1.5s" data-wow-delay="0.5s"><button data-filter="*" class="th-btn tab-btn {{ request('category') ? '' : 'active' }}" type="button">View All</button> <button data-filter=".cat1" class="th-btn tab-btn {{ request('category') === 'apartment' ? 'active' : '' }}" type="button">Apartment</button> <button data-filter=".cat2" class="th-btn tab-btn {{ request('category') === 'commercial' ? 'active' : '' }}" type="button">Commercial</button> <button data-filter=".cat3" class="th-btn tab-btn {{ request('category') === 'land-or-plot' ? 'active' : '' }}" type="button">Land or Plot</button> <button data-filter=".cat4" class="th-btn tab-btn {{ request('category') === 'farm' ? 'active' : '' }}" type="button">Farm</button></div></div></div></div><div class="row gy-4 justify-content-center filter-active">@forelse ($items as $item)
@php($filterClass = match(\Illuminate\Support\Str::slug($item->category ?? '')) { 'apartment' => 'cat1', 'commercial' => 'cat2', 'land-or-plot' => 'cat3', 'farm' => 'cat4', default => '' })
<div class="col-lg-4 col-xl-4 col-md-6 col-xxl-auto filter-item {{ $filterClass }}"><div class="gallery-card2"><div class="gallery-img">@if($item->image)<img src="{{ Storage::disk('public')->url($item->image) }}" alt="{{ $item->alt_text ?: $item->title }}"> <a class="popular-popup-image" href="{{ Storage::disk('public')->url($item->image) }}"><i class="fa-solid fa-eye"></i></a>@else<div class="gallery-image-placeholder bg-smoke d-flex align-items-center justify-content-center"><span class="text-muted">Image pending</span></div>@endif</div></div></div>
@empty
<div class="col-12 text-center text-muted">No gallery photographs have been published.</div>
@endforelse</div></div></section>
<div class="container mt-4">{{ $items->withQueryString()->links('vendor.pagination.buraaqh') }}</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categories = { '*': null, '.cat1': 'apartment', '.cat2': 'commercial', '.cat3': 'land-or-plot', '.cat4': 'farm' };
    document.querySelectorAll('.gallery-inner-sec .filter-menu [data-filter]').forEach(function (button) {
        button.addEventListener('click', function () {
            const url = new URL(window.location.href);
            const category = categories[button.dataset.filter];
            if (category) url.searchParams.set('category', category);
            else url.searchParams.delete('category');
            url.searchParams.delete('page');
            window.location.assign(url.toString());
        });
    });
});
</script>
@endpush
@endsection
