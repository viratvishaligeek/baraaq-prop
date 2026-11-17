@if (isset($primaryMenu) && $primaryMenu && $primaryMenu->items->isNotEmpty())
    @include('frontend.partials.menu-items', ['items' => $primaryMenu->items])
@else
    <li><a href="{{ route('home') }}">Home</a></li>
    <li><a href="{{ route('properties.index') }}">Properties</a></li>
    <li><a href="{{ route('blog.index') }}">Blog</a></li>
    <li><a href="{{ route('theme.page', ['template' => 'contact']) }}">Contact</a></li>
@endif
