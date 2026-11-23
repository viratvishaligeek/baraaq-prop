@extends('frontend.layouts.theme')
@section('title', ($property->meta_title ?: $property->title).' · '.($siteSettings['site_title'] ?? config('app.name')))
@section('meta_description',$property->meta_description?:($property->description?\Illuminate\Support\Str::limit(strip_tags($property->description),155):\App\Models\Setting::read('meta_description','')))
@php
    $propertyOgImage = $property->og_image
        ? asset('storage/' . $property->og_image)
        : ($property->featured_image
            ? Storage::disk('public')->url($property->featured_image)
            : (!empty($siteSettings['default_og_image'])
                ? asset('storage/' . $siteSettings['default_og_image'])
                : asset('theme-assets/img/hero/hero-10-1.jpg')));
@endphp
@section('canonical_url', $property->canonical_url ?: route('properties.show', $property))
@section('og_image', $propertyOgImage)
@section('structured_data')
@php
    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [[
            '@type' => 'RealEstateListing',
            'name' => $property->title,
            'description' => $property->meta_description ?: strip_tags($property->description ?? ''),
            'url' => route('properties.show', $property),
            'image' => $property->featured_image ? Storage::disk('public')->url($property->featured_image) : null,
            'offers' => $property->price ? ['@type' => 'Offer', 'price' => (float) $property->price, 'priceCurrency' => 'INR'] : null,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $property->address,
                'addressLocality' => $property->city,
                'addressRegion' => $property->state,
                'postalCode' => $property->postal_code,
                'addressCountry' => $property->country ?: 'IN',
            ],
        ]],
    ];

    if ($property->faqs->isNotEmpty()) {
        $structuredData['@graph'][] = [
            '@type' => 'FAQPage',
            'mainEntity' => $property->faqs->map(function ($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq->question,
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($faq->answer)],
                ];
            })->all(),
        ];
    }
@endphp
<script type="application/ld+json">@json($structuredData)</script>
@endsection
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcumb-bg.jpg') }}">
<div class="container">
<div class="breadcumb-content">
<h1 class="breadcumb-title">{{ $property->title }}</h1>
<ul class="breadcumb-menu">
<li>
<a href="{{ route('home') }}">Home</a>
</li>
<li>
<a href="{{ route('properties.index') }}">Properties</a>
</li>
<li>{{ $property->title }}</li>
</ul>
</div>
</div>
</div>
<section class="space">
<div class="container">
<div class="row gy-4">
<div class="col-lg-8">
<div class="property-details-thumb mb-4">
@if ($property->featured_image)
                            <img class="w-100 rounded" src="{{ Storage::disk('public')->url($property->featured_image) }}" alt="{{ $property->title }}">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="min-height: 280px">Property images will be added soon</div>
                        @endif
</div>
<h2 class="sec-title">{{ $property->title }}</h2>
<p class="text-muted">{{ $property->address }} {{ $property->city }} {{ $property->state }} {{ $property->postal_code }}</p>
<h3>{{ $property->price?number_format((float)$property->price,2):$property->price_label }}</h3>
<ul class="property-featured my-4">
<li>Bedrooms: {{ $property->bedrooms??'—' }}</li>
<li>Bathrooms: {{ $property->bathrooms??'—' }}</li>
<li>Area: {{ $property->area?number_format((float)$property->area).' '.$property->area_unit:'—' }}</li>
<li>Type: {{ $property->property_type??'—' }}</li>
</ul>
<div class="property-description">{!! \App\Support\HtmlSanitizer::sanitize($property->description) !!}</div>
@if(!empty($property->features))
    <div class="d-flex flex-wrap gap-2 my-4" aria-label="Property amenities">@foreach($property->features as $feature)<span class="badge text-bg-light">{{ $feature }}</span>@endforeach</div>
@endif
@if($property->accommodations->isNotEmpty())
    <section class="mt-5">
        <h3 class="sec-title">Accommodation & spaces</h3>
        <div class="row g-3">
            @foreach($property->accommodations as $space)
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex justify-content-between gap-2">
                            <strong>{{ $space->name }}</strong>
                            @if($space->area)<span class="text-muted small">{{ number_format((float) $space->area, 2) }} {{ $space->area_unit }}</span>@endif
                        </div>
                        @if($space->imageMedia)<img class="img-fluid rounded mt-2 mb-2" style="max-height:180px;object-fit:cover" src="{{ $space->imageMedia->url }}" alt="{{ $space->imageMedia->alt_text ?: $space->name }}">@endif
                        @if($space->category)<div class="small text-muted">{{ $space->category }}</div>@endif
                        @if($space->description)<p class="mb-2 mt-2">{{ $space->description }}</p>@endif
                        @if(!empty($space->attributes))<dl class="row small mt-2 mb-2">@foreach($space->attributes as $attribute)@if(!empty($attribute['key']))<dt class="col-5">{{ $attribute['key'] }}</dt><dd class="col-7">{{ $attribute['value'] ?? '' }}</dd>@endif @endforeach</dl>@endif
                        <span class="badge {{ $space->is_included ? 'text-bg-success' : 'text-bg-light' }}">{{ $space->is_included ? 'Included' : 'Not included' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
@if($property->faqs->isNotEmpty())
    <section class="mt-5">
        <h3 class="sec-title">Frequently asked questions</h3>
        <div class="d-grid gap-2">
            @foreach($property->faqs as $faq)
                <details class="border rounded p-3">
                    <summary class="fw-semibold">{{ $faq->question }}</summary>
                    <div class="mt-2">{{ $faq->answer }}</div>
                </details>
            @endforeach
        </div>
    </section>
@endif
@if($property->images->isNotEmpty())<div class="row g-3 mt-4">@foreach($property->images as $image)<div class="col-md-4">
<a class="popular-popup-image d-block" href="{{ Storage::disk('public')->url($image->path) }}"><img class="img-fluid rounded" loading="lazy" src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $image->alt_text ?: $property->title }}"></a>
</div>@endforeach</div>@endif</div>
<div class="col-lg-4">
<div class="contact-form p-4">
    <h3 class="h4 mb-3">Enquire about this property</h3>
    @php
        $ctaMode = $property->cta_mode ?: 'both';
        $whatsappNumber = preg_replace('/\\D+/', '', $property->whatsapp_number ?: \App\Models\Setting::read('whatsapp_number', ''));
        $whatsappMessage = $property->whatsapp_message ?: 'I am interested in '.$property->title.' - '.route('properties.show', $property);
    @endphp
    @if(in_array($ctaMode, ['contact_form', 'both'], true) || ($ctaMode === 'whatsapp' && !$whatsappNumber))
        <button type="button" class="th-btn w-100" data-bs-toggle="modal" data-bs-target="#propertyEnquiryModal">Request property details</button>
    @endif
    @if(session('enquiry_success'))
        <div class="alert alert-success mt-3">{{ session('enquiry_success') }}</div>
    @endif
    <div class="modal fade" id="propertyEnquiryModal" tabindex="-1" aria-labelledby="propertyEnquiryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
            <div class="modal-header">
<h5 class="modal-title" id="propertyEnquiryModalLabel">Enquire about {{ $property->title }}</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
</button>
</div>
            <div class="modal-body">
                <form method="POST" action="{{ route('enquiries.store') }}">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <div class="form-group mb-3">
<label class="form-label" for="enquiry-name">Your name</label>
<input id="enquiry-name" class="form-control" name="name" value="{{ old('name') }}" required maxlength="150">
</div>
                    <div class="form-group mb-3">
<label class="form-label" for="enquiry-email">Email</label>
<input id="enquiry-email" class="form-control" name="email" type="email" value="{{ old('email') }}" maxlength="190">
</div>
                    <div class="form-group mb-3">
<label class="form-label" for="enquiry-phone">Phone</label>
<input id="enquiry-phone" class="form-control" name="phone" value="{{ old('phone') }}" maxlength="30">
</div>
                    <div class="form-group mb-3">
<label class="form-label" for="enquiry-message">Message</label>
<textarea id="enquiry-message" class="form-control" name="message" rows="4" maxlength="5000">{{ old('message') }}</textarea>
</div>
                    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                    <button class="th-btn w-100" type="submit">Send enquiry</button>
                </form>
            </div>
        </div>
</div>
    </div>
    @if(in_array($ctaMode, ['whatsapp', 'both'], true) && $whatsappNumber)
        <a class="th-btn w-100 mt-2" target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}">Enquire on WhatsApp</a>
    @endif
</div>
</div>
</div>
</div>
</section>
@if ($related->isNotEmpty())
    <section class="space-top">
        <div class="container">
            <h2 class="sec-title mb-4">Related properties</h2>
            <div class="row gy-4">
                @foreach ($related as $relatedProperty)
                    <div class="col-md-6 col-lg-4">
                        <article class="blog-card h-100">
                            @if ($relatedProperty->featured_image)
                                <a href="{{ route('properties.show', $relatedProperty) }}"><img class="w-100" loading="lazy" src="{{ Storage::disk('public')->url($relatedProperty->featured_image) }}" alt="{{ $relatedProperty->title }}"></a>
                            @endif
                            <div class="blog-content">
                                <h3 class="h5"><a href="{{ route('properties.show', $relatedProperty) }}">{{ $relatedProperty->title }}</a></h3>
                                <p>{{ $relatedProperty->address }}{{ $relatedProperty->city ? ', '.$relatedProperty->city : '' }}</p>
                                @if ($relatedProperty->price_label || $relatedProperty->price)
                                    <p class="fw-semibold">{{ $relatedProperty->price_label ?: number_format((float) $relatedProperty->price, 2) }}</p>
                                @endif
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('propertyEnquiryModal');
            if (modalElement && window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        });
    </script>
@endif
@endsection
