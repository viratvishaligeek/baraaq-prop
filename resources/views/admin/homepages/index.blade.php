@extends('admin.layouts.app')

@section('title', 'Homepage management')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="page-title mb-1">Homepage management</h1>
        <p class="text-muted mb-0">Choose which of the ten original Piller homepage designs is published on the site.</p>
    </div>
</div>

<div class="row g-3">
    @foreach($homepages as $homepage)
        <div class="col-xl-6">
            <div class="content-card h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h2 class="section-title mb-1">{{ $homepage->name }}</h2>
                        <p class="text-muted small mb-2">Source design: <code>{{ $homepage->design_key }}</code></p>
                    </div>
                    @if($homepage->is_default)
                        <span class="badge bg-success">Live homepage</span>
                    @else
                        <span class="badge bg-light text-dark">{{ ucfirst($homepage->status) }}</span>
                    @endif
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @if($homepage->design_key === 'index')
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">Preview</a>
                    @else
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('theme.page', ['template' => $homepage->design_key]) }}" target="_blank" rel="noopener noreferrer">Preview</a>
                    @endif
                    @unless($homepage->is_default)
                        <form method="POST" action="{{ route('admin.homepages.default', $homepage) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-primary" type="submit">Set as homepage</button>
                        </form>
                    @endunless
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
