<div class="gallery-img">
    @if(!empty($item['image']))<img src="{{ $item['image'] }}" alt="gallery image">@endif
    <div class="gallery-content">
        <div class="gallery-content-wrapper">
            <div class="left-content">
                <p class="box-text">{{ $item['count'] }}</p>
                <h2 class="box-title">
<a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
</h2>
            </div>
            <div class="link">
<a href="{{ $item['url'] }}">
<i class="far fa-arrow-right">
</i>
</a>
</div>
        </div>
    </div>
</div>
