@extends('frontend.layouts.theme')
@section('title', \App\Models\Setting::read('faq_meta_title','FAQ | '.config('app.name')))
@section('meta_description', \App\Models\Setting::read('faq_meta_description','Answers to common real estate questions.'))
@section('canonical_url', route('faq.index'))
@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/bg/breadcrumb-bg.jpg') }}"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">{{ \App\Models\Setting::read('faq_page_heading', 'FAQS') }}</h1><ul class="breadcumb-menu"><li><a href="{{ route('home') }}">Home</a></li><li>{{ \App\Models\Setting::read('faq_page_heading', 'Frequently Asked Questions') }}</li></ul></div></div></div><div class="th-blog-wrapper blog-details space"><div class="container"><div class="row justify-content-between"><div class="col-xxl-4 col-lg-5"><aside class="sidebar-area"><div class="title-area text-left pe-xxl-5"><p class="sub-title text-anime-style-1"><span class="double-line"></span> FAQ</p><h2 class="sec-title text-anime-style-2">{{ \\App\\Models\\Setting::read('faq_section_heading', 'What would you like to know about pillar?') }}</h2><p class="sec-text text-anime-style-3">{{ \App\Models\Setting::read('faq_page_intro', 'Find answers to common property questions.') }}</p><a href="{{ route('contact') }}" class="th-btn bg-theme pill">Contact Our Team</a></div><div class="widget bg-smoke faq"><div class="th-widget-contact"><div class="info-box"><div class="info-box_icon"><i class="fas fa-phone"></i></div><p class="info-box_text">@if(!empty($siteSettings['site_phone']))<a href="tel:{{ preg_replace('/\D+/', '', $siteSettings['site_phone']) }}" class="info-box_link">{{ $siteSettings['site_phone'] }}</a>@endif</p></div><div class="info-box"><div class="info-box_icon"><i class="fas fa-envelope"></i></div><p class="info-box_text">@if(!empty($siteSettings['site_email']))<a href="mailto:{{ $siteSettings['site_email'] }}" class="info-box_link">{{ $siteSettings['site_email'] }}</a>@endif</p></div><div class="info-box"><div class="info-box_icon"><i class="fas fa-location-dot"></i></div><p class="info-box_text">{{ $siteSettings['site_address'] ?? '' }}</p></div></div></div><div class="widget bg-smoke">
    <h3 class="widget_title">Contact Us</h3>
    <div class="contact-form-widget">
        <form action="{{ route('enquiries.store') }}" method="POST" class="newsletter-form">
            @csrf
            <input type="hidden" name="subject" value="FAQ enquiry">
            <div class="form-group"><input class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="Your Name" required maxlength="150"></div>
            <div class="form-group"><input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Your Email"></div>
            <div class="form-group"><input class="form-control" type="text" name="phone" value="{{ old('phone') }}" placeholder="Your Phone"></div>
            <div class="form-group mb-4"><textarea name="message" id="faq-message" cols="30" rows="3" class="form-control" placeholder="Your Message">{{ old('message') }}</textarea></div>
            <div class="form-btn pt-2"><button type="submit" class="th-btn radius">Send Us</button></div>
        </form>
    </div>
</div></aside></div><div class="col-xxl-7 col-lg-7"><div class="th-faq-wrapper"><div class="accordion-1 accordion" id="faqAccordion">@forelse ($faqs as $faq)
<div class="accordion-card {{ $loop->first ? 'active' : '' }} wow fadeinup" data-wow-delay=".1s"><div class="accordion-header" id="collapse-item-{{ $faq->id }}"><button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $faq->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $faq->id }}"><span class="serial-numb">{{ $loop->iteration }}. </span>{{ $faq->question }}</button></div><div id="collapse-{{ $faq->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="collapse-item-{{ $faq->id }}" data-bs-parent="#faqAccordion"><div class="accordion-body"><p class="faq-text">{!! \App\Support\HtmlSanitizer::sanitize($faq->answer) !!}</p></div></div></div>
@empty
<p class="text-muted">FAQs will be published here soon.</p>
@endforelse</div></div></div></div></div></div>
<div class="container mt-4">{{ $faqs->withQueryString()->links('vendor.pagination.buraaqh') }}</div>
@endsection
