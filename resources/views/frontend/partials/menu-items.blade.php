@foreach ($items as $item)
    @if ($item->is_active)
        @php
            $menuPath = parse_url($item->resolved_url, PHP_URL_PATH) ?: '';
            $isCurrent = $menuPath !== '' && rtrim($menuPath, '/') === rtrim(request()->getPathInfo(), '/');
            $menuClasses = [];
            if ($item->children->isNotEmpty()) {
                $menuClasses[] = 'menu-item-has-children';
            }
            if ($isCurrent) {
                $menuClasses[] = 'active';
            }
        @endphp
        <li class="{{ implode(' ', $menuClasses) }}">
            <a href="{{ $item->resolved_url }}" target="{{ $item->target }}"
                @if ($item->target === '_blank') rel="noopener noreferrer" @endif
                @if ($isCurrent) aria-current="page" @endif>{{ $item->label }}</a>
            @if ($item->children->isNotEmpty())
                <ul class="sub-menu">
                    @include('frontend.partials.menu-items', ['items' => $item->children])
                </ul>
            @endif
        </li>
    @endif
@endforeach
