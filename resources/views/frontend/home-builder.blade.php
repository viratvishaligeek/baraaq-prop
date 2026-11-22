@extends('frontend.layouts.theme')

@section('title', $homepage->meta_title ?: config('app.name'))
@section('meta_description', $homepage->meta_description ?: \App\Models\Setting::read('meta_description', ''))

@section('content')
    @php $sectionDefinitions = config('cms-sections.sections', []); @endphp
    @foreach($homepage->sections as $section)
        @continue(! $section->is_enabled)

        @php
            $definition = $sectionDefinitions[$section->section_key] ?? null;
            $renderConfig = array_merge($section->data_source ?? [], $section->config ?? []);
            $renderConfig = app(\App\Services\Homepage\SectionMediaResolver::class)->forRendering($section, $renderConfig);
            $sectionData = app(\App\Services\Homepage\SectionDataResolver::class)->resolve($section, $renderConfig);
        @endphp

        @if($definition && view()->exists($definition['view']))
            @php
                $widgetDomId = 'cms-widget-'.$section->id;
                $widgetStyles = [];
                if (!empty($renderConfig['widget_background_color'])) $widgetStyles[] = 'background-color: '.$renderConfig['widget_background_color'];
                if (!empty($renderConfig['widget_background_image'])) $widgetStyles[] = 'background-image: url('.$renderConfig['widget_background_image'].'); background-size: cover; background-position: center';
                if (isset($renderConfig['widget_padding_top'])) $widgetStyles[] = 'padding-top: '.(int)$renderConfig['widget_padding_top'].'px';
                if (isset($renderConfig['widget_padding_bottom'])) $widgetStyles[] = 'padding-bottom: '.(int)$renderConfig['widget_padding_bottom'].'px';
            @endphp
            <div id="{{ $widgetDomId }}" class="cms-widget-instance" @if(count($widgetStyles)) style="{{ implode(';', $widgetStyles) }}" @endif>
                @include($definition['view'], [
                    'section' => $section,
                    'sectionConfig' => $renderConfig,
                    ...$sectionData,
                ])
                @if(!empty($renderConfig['widget_heading_color']) || !empty($renderConfig['widget_text_color']) || !empty($renderConfig['widget_heading_font_size']) || !empty($renderConfig['widget_body_font_size']))
                    <style>
                        #{{ $widgetDomId }} h1, #{{ $widgetDomId }} h2, #{{ $widgetDomId }} h3, #{{ $widgetDomId }} h4, #{{ $widgetDomId }} h5, #{{ $widgetDomId }} h6, #{{ $widgetDomId }} .sec-title, #{{ $widgetDomId }} .box-title { @if(!empty($renderConfig['widget_heading_color'])) color: {{ $renderConfig['widget_heading_color'] }} !important; @endif @if(!empty($renderConfig['widget_heading_font_size'])) font-size: {{ (int)$renderConfig['widget_heading_font_size'] }}px !important; @endif }
                        #{{ $widgetDomId }} p, #{{ $widgetDomId }} .box-text, #{{ $widgetDomId }} .sec-text { @if(!empty($renderConfig['widget_text_color'])) color: {{ $renderConfig['widget_text_color'] }} !important; @endif @if(!empty($renderConfig['widget_body_font_size'])) font-size: {{ (int)$renderConfig['widget_body_font_size'] }}px !important; @endif }
                    </style>
                @endif
            </div>
        @endif
    @endforeach
@endsection
