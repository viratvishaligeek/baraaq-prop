@extends('frontend.layouts.theme')
@section('title', ($property->meta_title ?: $property->title).' · '.($siteSettings['site_title'] ?? config('app.name')))
@section('meta_description',$property->meta_description?:($property->description?\Illuminate\Support\Str::limit(strip_tags($property->description),155):\App\Models\Setting::read('meta_description',''))) 
@section('canonical_url',$property->canonical_url ?: route('properties.show',$property)) @section('og_image',$property->og_image ? asset('storage/'.$property->og_image) : ($property->featured_image ? Storage::disk('public')->url($property->featured_image) : (!empty($siteSettings['default_og_image']) ? asset('storage/'.$siteSettings['default_og_image']) : asset('theme-assets/img/hero/hero-10-1.jpg')))) @section('structured_data')
<script type="application/ld+json">@json([
    '@context' => 'https://schema.org',
    '@type' => 'RealEstateListing',
    'name' => $property->title,
    'description' => $property->meta_description ?: strip_tags($property->description ?? ''),
    'url' => route('properties.show', $property),
    'image' => $property->featured_image ? Storage::disk('public')->url($property->featured_image) : null,
    'offers' => $property->price ? ['@type' => 'Offer', 'price' => (float) $property->price, 'priceCurrency' => 'INR'] : null,
    'address' => ['@type' => 'PostalAddress', 'streetAddress' => $property->address, 'addressLocality' => $property->city, 'addressRegion' => $property->state, 'postalCode' => $property->postal_code],
])</script>
@endsection
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcumb-bg.jpg') }}"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ $property->title }}</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('properties.index') }}">Properties</a></li><li>{{ $property->title }}</li></ul></div></div></div>
<section class="space"><div class="container"><div class="row gy-4"><div class="col-lg-8"><div class="property-details-thumb mb-4"><img class="w-100 rounded" src="{{ $property->featured_image?Storage::disk('public')->url($property->featured_image):asset('theme-assets/img/property-details/property-details_1.jpg') }}" alt="{{ $property->title }}"></div><h2 class="sec-title">{{ $property->title }}</h2><p class="text-muted">{{ $property->address }} {{ $property->city }} {{ $property->state }} {{ $property->postal_code }}</p><h3>{{ $property->price?number_format((float)$property->price,2):$property->price_label }}</h3><ul class="property-featured my-4"><li>Bedrooms: {{ $property->bedrooms??'—' }}</li><li>Bathrooms: {{ $property->bathrooms??'—' }}</li><li>Area: {{ $property->area?number_format((float)$property->area).' '.$property->area_unit:'—' }}</li><li>Type: {{ $property->property_type??'—' }}</li></ul><div class="property-description">{!! nl2br(e($property->description)) !!}</div>@if($property->images->isNotEmpty())<div class="row g-3 mt-4">@foreach($property->images as $image)<div class="col-md-4"><img class="img-fluid rounded" src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $image->alt_text }}"></div>@endforeach</div>@endif</div><div class="col-lg-4"><div class="contact-form p-4">
    <h3 class="h4 mb-3">Enquire about this property</h3>
    @php
        $ctaMode = $property->cta_mode ?: 'both';
        $whatsappNumber = preg_replace('/\\D+/', '', $property->whatsapp_number ?: \\App\\Models\\Setting::read('whatsapp_number', ''));
        $whatsappMessage = $property->whatsapp_message ?: 'I am interested in '.$property->title.' - '.route('properties.show', $property);
    @endphp
    @if(in_array($ctaMode, ['contact_form', 'both'], true) || ($ctaMode === 'whatsapp' && !$whatsappNumber))
        <button type="button" class="th-btn w-100" data-bs-toggle="modal" data-bs-target="#propertyEnquiryModal">Request property details</button>
    @endif
    @if(session('enquiry_success'))
        <div class="alert alert-success mt-3">{{ session('enquiry_success') }}</div>
    @endif
    <div class="modal fade" id="propertyEnquiryModal" tabindex="-1" aria-labelledby="propertyEnquiryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="propertyEnquiryModalLabel">Enquire about {{ $property->title }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <form method="POST" action="{{ route('enquiries.store') }}">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <div class="form-group mb-3"><label class="form-label" for="enquiry-name">Your name</label><input id="enquiry-name" class="form-control" name="name" value="{{ old('name') }}" required maxlength="150"></div>
                    <div class="form-group mb-3"><label class="form-label" for="enquiry-email">Email</label><input id="enquiry-email" class="form-control" name="email" type="email" value="{{ old('email') }}" maxlength="190"></div>
                    <div class="form-group mb-3"><label class="form-label" for="enquiry-phone">Phone</label><input id="enquiry-phone" class="form-control" name="phone" value="{{ old('phone') }}" maxlength="30"></div>
                    <div class="form-group mb-3"><label class="form-label" for="enquiry-message">Message</label><textarea id="enquiry-message" class="form-control" name="message" rows="4" maxlength="5000">{{ old('message') }}</textarea></div>
                    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                    <button class="th-btn w-100" type="submit">Send enquiry</button>
                </form>
            </div>
        </div></div>
    </div>
    @if(in_array($ctaMode, ['whatsapp', 'both'], true) && $whatsappNumber)
        <a class="th-btn w-100 mt-2" target="_blank" rel="noopener noreferrer" href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}">Enquire on WhatsApp</a>
    @endif
</div></div></div></div></section>
@endsection