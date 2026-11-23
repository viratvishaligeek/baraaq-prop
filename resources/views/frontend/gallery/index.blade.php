@extends('frontend.layouts.theme')
@section('title',\App\Models\Setting::read('gallery_meta_title','Property Gallery | '.config('app.name')))
@section('meta_description',\App\Models\Setting::read('gallery_meta_description','Explore property photography and real estate highlights in Bareilly, Uttar Pradesh.'))
@section('canonical_url',route('gallery.index'))
@section('og_image', $items->first()?->image ? Storage::disk('public')->url($items->first()->image) : '')
@section('structured_data')
    @php
        $galleryImages = $items->getCollection()->filter(fn ($item) => $item->image)->map(fn ($item) => [
            '@type' => 'ImageObject',
            'contentUrl' => Storage::disk('public')->url($item->image),
            'caption' => $item->title,
        ])->values();
    @endphp
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'ImageGallery', 'name' => 'Buraaqh Properties Gallery', 'url' => route('gallery.index'), 'image' => $galleryImages], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endsection
@section('content')
<div class="breadcumb-wrapper"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ \\App\\Models\\Setting::read('gallery_page_heading', 'Property Gallery') }}</h1><p>{{ \\App\\Models\\Setting::read('gallery_page_intro', 'Explore homes, spaces and real estate highlights around Bareilly.') }}</p></div></div></div>
<section class="space"><div class="container"><form method="GET" class="row g-2 mb-4"><div class="col-md-5"><input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search gallery"></div><div class="col-auto"><button class="th-btn">Search</button><a class="btn btn-link" href="{{ route('gallery.index') }}">Reset</a></div></form><div class="row gy-4">@forelse($items as $item)<div class="col-sm-6 col-lg-4"><article class="blog-card">@if($item->image)<img class="w-100" loading="lazy" src="{{ Storage::disk('public')->url($item->image) }}" alt="{{ $item->alt_text?:$item->title }}">@endif<div class="blog-content"><h2 class="h4">{{ $item->title }}</h2>@if($item->description)<p>{{ $item->description }}</p>@endif</div></article></div>@empty<div class="col-12 text-center text-muted">No gallery photographs have been published yet.</div>@endforelse</div>{{ $items->links() }}</div></section>
@endsection