@php
    $imageUrl = $item['image'] && (str_starts_with($item['image'], 'http://') || str_starts_with($item['image'], 'https://') || str_starts_with($item['image'], '/'))
        ? $item['image']
        : asset($item['image']);
@endphp
<div class="thumb {{ $thumbClass ?? '' }}" @if(!empty($delay)) data-wow-delay="{{ $delay }}" @endif>
    <img src="{{ $imageUrl }}" alt="{{ $item['title'] }}">
    <div class="plus-icon">
<i class="fa-solid fa-plus">
</i>
</div>
    <div class="hover-content">
        <h4 class="box-title">
<a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
</h4>
        <p class="box-text">{{ $item['text'] }}</p>
        @if($item['button'])
            <a class="th-btn bg-theme pill" href="{{ $item['url'] }}">{{ $item['button'] }}</a>
        @endif
    </div>
</div>
