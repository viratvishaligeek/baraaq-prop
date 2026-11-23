@extends('frontend.layouts.theme')
@section('title',\App\Models\Setting::read('faq_meta_title','Frequently Asked Questions | '.config('app.name')))
@section('meta_description',\App\Models\Setting::read('faq_meta_description','Answers to common property buying, selling and real estate questions in Bareilly, Uttar Pradesh.'))
@section('canonical_url',route('faq.index'))
@section('structured_data')
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$faqs->map(fn($faq)=>['@type'=>'Question','name'=>$faq->question,'acceptedAnswer'=>['@type'=>'Answer','text'=>strip_tags($faq->answer)]])->values()],JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!}</script>
@endsection
@section('content')
<div class="breadcumb-wrapper"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">Frequently Asked Questions</h1><p>Helpful answers about property decisions in Bareilly.</p></div></div></div>
<section class="space"><div class="container"><div class="accordion" id="faq-list">@forelse($faqs as $faq)<div class="accordion-item"><h2 class="accordion-header" id="faq-heading-{{ $faq->id }}"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}" aria-expanded="false" aria-controls="faq-{{ $faq->id }}">{{ $faq->question }}</button></h2><div id="faq-{{ $faq->id }}" class="accordion-collapse collapse" aria-labelledby="faq-heading-{{ $faq->id }}" data-bs-parent="#faq-list"><div class="accordion-body">{!! \App\Support\HtmlSanitizer::sanitize($faq->answer) !!}</div></div></div>@empty<p class="text-muted">FAQs will be published here soon.</p>@endforelse</div></div></section>
@endsection