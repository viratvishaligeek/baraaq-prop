@extends('frontend.layouts.theme')

@section('title', $homepage->meta_title ?: config('app.name'))
@section('meta_description', $homepage->meta_description ?: \App\Models\Setting::read('meta_description', ''))

@section('content')
    @foreach($homepage->sections as $section)
        @continue(! $section->is_enabled)

        @php
            $definition = config('cms-sections.sections.'.$section->section_key);
            $renderConfig = array_merge($section->reusableSection?->config ?? [], $section->config ?? []);
            $sectionData = app(\\App\\Services\\Homepage\\SectionDataResolver::class)->resolve($section, $renderConfig);
        @endphp

        @if($definition && view()->exists($definition['view']))
            @include($definition['view'], [
                'section' => $section,
                'sectionConfig' => $renderConfig,
                ...$sectionData,
            ])
        @endif
    @endforeach
@endsection
