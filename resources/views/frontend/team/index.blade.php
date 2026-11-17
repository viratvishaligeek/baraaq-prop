@extends('frontend.layouts.theme')
@section('title','Our Team · '.($siteSettings['site_title'] ?? config('app.name')))
@section('content')
<div class="breadcumb-wrapper"><div class="container"><div class="breadcumb-content"><h1 class="breadcumb-title">Our Team</h1></div></div></div>
<section class="space"><div class="container"><div class="row gy-4">
@forelse($members as $member)
<div class="col-md-6 col-xl-4"><article class="team-card text-center">@if($member->photo)<img class="w-100 rounded" src="{{ Storage::disk('public')->url($member->photo) }}" alt="{{ $member->name }}">@endif<div class="team-card-content p-3"><h3>{{ $member->name }}</h3><p class="text-muted">{{ $member->designation }}</p><p>{{ $member->bio }}</p>@if($member->email)<a href="mailto:{{ $member->email }}">{{ $member->email }}</a>@endif</div></article></div>
@empty<div class="col-12 text-center text-muted">Team profiles will be published here.</div>@endforelse
</div>{{ $members->links() }}</div></section>
@endsection
