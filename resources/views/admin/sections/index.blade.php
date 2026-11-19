@extends('admin.layouts.app')
@section('title', 'Section library')
@section('content')
    <div class="mb-4"><h1 class="page-title mb-1">Section library</h1><p class="text-muted mb-0">Browse every registered design variation, preview it independently, then add it from a homepage, page or service builder.</p></div>
    @forelse($groups as $group => $sections)
        <details class="content-card mb-3" @if($loop->first) open @endif>
            <summary class="fw-semibold d-flex justify-content-between"><span>{{ \Illuminate\Support\Str::headline($group) }}</span><span class="text-muted small">{{ $sections->count() }} designs</span></summary>
            <div class="row g-3 mt-1">
                @foreach($sections as $section)
                    <div class="col-md-6 col-xl-4"><div class="border rounded p-3 h-100 d-flex flex-column">
                        <div class="fw-semibold">{{ $section['label'] }}</div><div class="small text-muted mb-3">{{ $section['source_design'] }} · {{ $section['type'] }}</div>
                        <a class="btn btn-sm btn-outline-primary mt-auto" target="_blank" rel="noopener" href="{{ route('admin.sections.preview', $section['key']) }}">Preview design</a>
                    </div></div>
                @endforeach
            </div>
        </details>
    @empty
        <div class="content-card text-muted">No registered section designs are available.</div>
    @endforelse
@endsection
