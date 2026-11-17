@extends('admin.layouts.app')
@section('title', 'SEO overview')
@section('content')
    <div class="mb-4">
        <h1 class="page-title">SEO overview</h1>
        <p class="text-muted">Find content missing search and social metadata. Edit an item to complete its SEO fields.</p>
    </div>
    <div class="content-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h2 class="section-title mb-1">Global SEO defaults</h2>
                <p class="text-muted mb-0">Configure fallback metadata, robots directives and the default social sharing
                    image.</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('sitemap') }}" target="_blank" rel="noopener">XML
                    sitemap</a>
                <a class="btn btn-outline-secondary" href="{{ route('robots') }}" target="_blank"
                    rel="noopener">Robots.txt</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.settings.edit') }}">Website settings</a>
            </div>
        </div>
    </div>
    @foreach ([['Properties', $properties, 'properties'], ['Blog posts', $posts, 'posts'], ['Pages', $pages, 'pages'], ['Services', $services, 'services']] as [$label, $items, $type])
        <div class="content-card mb-4">
            <h2 class="section-title">{{ $label }}</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Content</th>
                            <th>Meta title</th>
                            <th>Description</th>
                            <th>Canonical</th>
                            <th>Social image</th>
                            <th>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>{!! $item->meta_title ? '<span class="text-success">Set</span>' : '<span class="text-danger">Missing</span>' !!}</td>
                                <td>{!! $item->meta_description
                                    ? '<span class="text-success">Set</span>'
                                    : '<span class="text-danger">Missing</span>' !!}</td>
                                <td>{!! $item->canonical_url ? '<span class="text-success">Set</span>' : '<span class="text-danger">Missing</span>' !!}</td>
                                <td>{!! $item->og_image ? '<span class="text-success">Set</span>' : '<span class="text-danger">Missing</span>' !!}</td>
                                <td class="text-end">
                                    @if ($type === 'properties')
                                        <a class="btn btn-sm btn-light"
                                            href="{{ route('admin.properties.edit', $item) }}">Edit SEO</a>
                                    @else
                                        <a class="btn btn-sm btn-light"
                                            href="{{ route('admin.content.edit', [$type, $item->id]) }}">Edit SEO</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted text-center py-3">No content needs attention in this
                                    group.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
