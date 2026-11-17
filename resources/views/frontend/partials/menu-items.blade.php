@foreach($items as $item)
@if($item->is_active)
<li class="{{ $item->children->isNotEmpty() ? 'menu-item-has-children' : '' }}"><a href="{{ $item->resolved_url }}" target="{{ $item->target }}" @if($item->target==='_blank') rel="noopener noreferrer" @endif>{{ $item->label }}</a>
@if($item->children->isNotEmpty())<ul class="sub-menu">@include('frontend.partials.menu-items',['items'=>$item->children])</ul>@endif
</li>
@endif
@endforeach