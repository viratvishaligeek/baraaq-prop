@php $pageBuilderData = app(\App\Services\Pages\PageBuilderService::class)->frontendData(); @endphp
@foreach ($page->sections as $section)
    @continue(! $section->is_enabled)
    @php
        $definition = config('cms-sections.sections', [])[$section->section_key] ?? null;
        $renderConfig = array_merge($definition['defaults'] ?? [], $section->data_source ?? [], $section->config ?? []);
        $renderConfig = app(\App\Services\Homepage\SectionMediaResolver::class)->forRendering($section, $renderConfig);
        $sectionData = app(\App\Services\Homepage\SectionDataResolver::class)->resolve($section, $renderConfig);
    @endphp
    @if ($definition && view()->exists($definition['view']))
        @include($definition['view'], ['section' => $section, 'sectionConfig' => $renderConfig, ...$pageBuilderData, ...$sectionData])
    @endif
@endforeach
