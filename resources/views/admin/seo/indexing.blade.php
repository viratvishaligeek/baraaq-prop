@extends('admin.layouts.app')
@section('title', 'Sitemap and indexing')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div><h1 class="page-title mb-1">Sitemap and indexing</h1><p class="text-muted mb-0">Review crawler directives, robots policy and the generated XML sitemap.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.settings.edit') }}#seo-settings">Edit SEO configuration</a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6"><div class="content-card h-100"><div class="small text-muted">Meta robots policy</div><div class="h4 mt-2 mb-0">{{ $metaRobots }}</div></div></div>
        <div class="col-md-6"><div class="content-card h-100"><div class="small text-muted">Sitemap endpoint</div><a class="d-block mt-2" href="{{ $sitemapUrl }}" target="_blank" rel="noopener">{{ $sitemapUrl }}</a></div></div>
    </div>
    <div class="content-card mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">robots.txt directives</h2><a href="{{ $robotsUrl }}" target="_blank" rel="noopener">Open robots.txt</a></div>
        <pre class="bg-light rounded p-3 mb-0"><code>{{ $robotsRules }}</code></pre>
    </div>
    <p class="small text-muted">The public robots.txt endpoint filters unsupported directives and appends the current sitemap URL automatically.</p>
@endsection
