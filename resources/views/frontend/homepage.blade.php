@extends('frontend.layouts.theme')

@section('title', $homepage->meta_title ?: config('app.name'))
@section('meta_description', $homepage->meta_description ?: \App\Models\Setting::read('meta_description', ''))

@section('content')
    @php $sectionDefinitions = config('cms-sections.sections', []); @endphp
    @foreach ($homepage->sections as $section)
        @continue(! $section->is_enabled)

        @php
            $definition = $sectionDefinitions[$section->section_key] ?? null;
            $renderConfig = array_merge($definition['defaults'] ?? [], $section->data_source ?? [], $section->config ?? []);
            foreach ($renderConfig as $configKey => $configValue) {
                if (str_ends_with($configKey, '_url')) {
                    $renderConfig[$configKey] = \\App\\Rules\\SafeLink::sanitize($configValue);
                }
            }
            $renderConfig = app(\App\Services\Homepage\SectionMediaResolver::class)->forRendering($section, $renderConfig);
            $sectionData = app(\App\Services\Homepage\SectionDataResolver::class)->resolve($section, $renderConfig);
        @endphp

        @if ($definition && view()->exists($definition['view']))
            @include($definition['view'], [
                'section' => $section,
                'sectionConfig' => $renderConfig,
                ...$sectionData,
            ])
        @endif
    @endforeach
@endsection
