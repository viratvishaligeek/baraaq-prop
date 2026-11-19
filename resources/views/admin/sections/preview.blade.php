@extends('frontend.layouts.app')
@section('title', $definition['label'] . ' preview')
@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div><span class="badge bg-secondary">Individual design preview</span><h1 class="h4 mt-2 mb-1">{{ $definition['label'] }}</h1><p class="text-muted mb-0">{{ $definition['type'] }} · {{ $definition['source_design'] }}</p></div>
            <button type="button" class="btn btn-outline-secondary" onclick="window.close()">Close preview</button>
        </div>
    </div>
    @include('frontend.partials.page-sections', ['page' => $page])
@endsection
