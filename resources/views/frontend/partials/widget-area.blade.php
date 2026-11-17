@php
    $renderedWidgets = app(\App\Services\Widgets\WidgetRenderer::class)->forLocation($location);
@endphp
@if ($renderedWidgets->isNotEmpty())
    @foreach ($renderedWidgets as $widget)
        <section class="widget {{ str_starts_with($location, 'footer-') ? 'footer-widget' : '' }} cms-widget cms-widget-{{ $widget['type'] }}" data-widget-id="{{ $widget['id'] }}">
            @if (!empty($widget['config']['title']))
                <h3 class="widget_title">{{ $widget['config']['title'] }}</h3>
            @endif
            @switch($widget['type'])
                @case('text')
                @case('custom')
                    @if (!empty($widget['config']['body']))
                        <div class="widget-content">{!! \App\Support\HtmlSanitizer::sanitize($widget['config']['body']) !!}</div>
                    @endif
                @break

                @case('html')
                    {!! \App\Support\HtmlSanitizer::sanitize($widget['config']['html'] ?? '') !!}
                @break

                @case('image')
                    @if (!empty($widget['config']['image_url']))
                        <a href="{{ $widget['config']['url'] ?? '#' }}"><img class="img-fluid"
                                src="{{ $widget['config']['image_url'] }}" alt="{{ $widget['config']['image_alt'] ?? ($widget['config']['title'] ?? '') }}"></a>
                    @endif
                @break

                @case('recent_posts')
                    <ul class="list-unstyled">
                        @foreach ($widget['data'] as $post)
                            <li class="mb-2"><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></li>
                        @endforeach
                    </ul>
                @break

                @case('categories')
                    <ul class="list-unstyled">
                        @foreach ($widget['data'] as $category)
                            <li class="mb-2"><a
                                    href="{{ route('properties.index', ['category' => $category->id]) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                @break

                @case('featured_properties')
                    <div class="row g-3">
                        @foreach ($widget['data'] as $property)
                            <div class="col-12"><a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                <div class="small text-muted">
                                    {{ $property->price_label ?: number_format((float) $property->price) }}</div>
                            </div>
                        @endforeach
                    </div>
                @break

                @case('contact')
                    <div class="widget-contact">
                        @if (!empty($widget['config']['phone']))
                            <p><a
                                    href="tel:{{ preg_replace('/\D+/', '', $widget['config']['phone']) }}">{{ $widget['config']['phone'] }}</a>
                            </p>
                            @endif @if (!empty($widget['config']['email']))
                                <p><a href="mailto:{{ $widget['config']['email'] }}">{{ $widget['config']['email'] }}</a></p>
                                @endif @if (!empty($widget['config']['address']))
                                    <p>{{ $widget['config']['address'] }}</p>
                                @endif
                    </div>
                @break

                @case('social')
                    <div class="th-social">
                        @foreach ($widget['config']['social_links'] ?? [] as $link)
                            <a href="{{ $link['url'] }}" target="_blank"
                                rel="noopener noreferrer">{{ $link['label'] ?: 'Social' }}</a>
                        @endforeach
                    </div>
                @break

                @case('cta')
                    @if (!empty($widget['config']['body']))
                        <div class="widget-content">{!! \App\Support\HtmlSanitizer::sanitize($widget['config']['body']) !!}</div>
                    @endif
                    @if (!empty($widget['config']['url']))
                        <a class="th-btn"
                            href="{{ $widget['config']['url'] }}">{{ $widget['config']['button_text'] ?: 'Learn more' }}</a>
                    @endif
                @break
            @endswitch
        </section>
    @endforeach
@endif
