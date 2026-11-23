@if (isset($primaryMenu) && $primaryMenu && $primaryMenu->items->isNotEmpty())
    @include('frontend.partials.menu-items', ['items' => $primaryMenu->items])
@endif
