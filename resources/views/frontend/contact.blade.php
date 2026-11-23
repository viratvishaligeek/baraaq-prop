@extends('frontend.layouts.theme')

@section('title', ($page?->meta_title ?: 'Contact Us · '.($siteSettings['site_title'] ?? config('app.name'))))
@section('meta_description', $page?->meta_description ?: 'Contact Buraaqh Properties in Bareilly, Uttar Pradesh, using the website enquiry form.')
@section('canonical_url', $page?->canonical_url ?: route('contact'))

@section('content')
    <section class="space">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9">
                    <div class="title-area text-center">
                        <p class="sub-title">Get in touch</p>
                        <h1 class="sec-title">How can we help you?</h1>
                        <p>Send our team a message and we will get back to you as soon as possible.</p>
                    </div>

                    @if ($page?->content)
                        <div class="mb-4">{!! \App\Support\HtmlSanitizer::sanitize($page->content) !!}</div>
                    @endif

                    <div class="contact-form p-4 p-lg-5">
                        @if (session('enquiry_success'))
                            <div class="alert alert-success" role="status">{{ session('enquiry_success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <p class="mb-1 fw-semibold">Please review the following:</p>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('enquiries.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-name">Your name</label>
                                    <input class="form-control" id="contact-name" name="name" value="{{ old('name') }}" maxlength="150" autocomplete="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-subject">Subject</label>
                                    <input class="form-control" id="contact-subject" name="subject" value="{{ old('subject') }}" maxlength="120">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-email">Email address</label>
                                    <input class="form-control" id="contact-email" type="email" name="email" value="{{ old('email') }}" maxlength="190" autocomplete="email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="contact-phone">Phone number</label>
                                    <input class="form-control" id="contact-phone" name="phone" value="{{ old('phone') }}" maxlength="30" autocomplete="tel">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="contact-message">Message</label>
                                    <textarea class="form-control" id="contact-message" name="message" rows="5" maxlength="5000">{{ old('message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <p class="form-text mb-2">Please provide either an email address or a phone number so we can reply.</p>
                                    <button class="th-btn" type="submit">Send enquiry</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="row g-3 mt-3 text-center">
                        @if (!empty($siteSettings['site_phone']))
                            <div class="col-md-6"><a href="tel:{{ preg_replace('/\D+/', '', $siteSettings['site_phone']) }}">{{ $siteSettings['site_phone'] }}</a></div>
                        @endif
                        @if (!empty($siteSettings['site_email']))
                            <div class="col-md-6"><a href="mailto:{{ $siteSettings['site_email'] }}">{{ $siteSettings['site_email'] }}</a></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
