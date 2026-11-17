@php
    $animateCards = $sectionConfig['animate_cards'] ?? true;
    $posts = $posts->take(max(1, min(3, (int) ($sectionConfig['items_limit'] ?? 3))))->values();
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<section class="blog-sec-3 {{ $sectionConfig['vertical_spacing'] ?? 'space' }} overflow-hidden space-extra-bottom {{ $visibility }}" id="blog-sec-{{ $section->id }}" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
    <div class="container">
        <div class="row justify-content-lg-between justify-content-center align-items-end">
            <div class="col-lg-6">
                <div class="title-area text-left text-lg-start">
                    <p class="sub-title text-anime-style-2"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                    <h2 class="sec-title text-anime-style-3">{{ $sectionConfig['heading'] ?? '' }}</h2>
                </div>
            </div>
            @if(!empty($sectionConfig['button_label']))
                <div class="col-lg-auto d-none d-lg-block"><div class="sec-btn fadeinup wow" data-wow-delay="0.5s"><a href="{{ $sectionConfig['button_url'] ?? route('blog.index') }}" class="th-btn pill style3">{{ $sectionConfig['button_label'] }} <i class="fas fa-arrow-up-right"></i></a></div></div>
            @endif
        </div>
        <div class="row gy-40 gx-30">
            @if($posts->isNotEmpty())
                @php($firstPost = $posts->first())
                <div class="col-lg-6">
                    <div class="blog-card style-3{{ $animateCards ? ' fadeinup wow' : '' }}" data-wow-delay="0.3s">
                        <div class="blog-img">
                            <a href="{{ route('blog.show', $firstPost) }}">
                                @if($firstPost->featured_image)<img src="{{ Storage::disk('public')->url($firstPost->featured_image) }}" alt="{{ $firstPost->title }}">@endif
                            </a>
                        </div>
                        <div class="blog-content pe-xl-5">
                            <div class="blog-meta">
                                @if(($sectionConfig['show_date'] ?? true) && $firstPost->published_at)<a href="{{ route('blog.show', $firstPost) }}">{{ $firstPost->published_at->format('F d, Y') }}</a>@endif
                            </div>
                            <h3 class="box-title"><a href="{{ route('blog.show', $firstPost) }}">{{ $firstPost->title }}</a></h3>
                            <p class="box-text">{{ $firstPost->excerpt }}</p>
                            <div class="blog-bottom">
                                <div class="blog-auth">
                                    @if($sectionConfig['show_author'] ?? true)
                                        <div class="media-body"><p class="desig">Written by</p><h5 class="name"><a href="{{ route('blog.show', $firstPost) }}">{{ $firstPost->author?->name ?? config('app.name') }}</a></h5></div>
                                    @endif
                                </div>
                                <a href="{{ route('blog.show', $firstPost) }}" class="th-btn radius outline">{{ $sectionConfig['card_button_label'] ?? 'Read More' }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="blog-3-right-wrap">
                        @foreach($posts->skip(1) as $post)
                            <div class="blog-card style-3 grid-style{{ $animateCards ? ' fadeinright wow' : '' }}" data-wow-delay="{{ $loop->first ? '0.5s' : '0.7s' }}">
                                <div class="blog-img">
                                    <a href="{{ route('blog.show', $post) }}">@if($post->featured_image)<img src="{{ Storage::disk('public')->url($post->featured_image) }}" alt="{{ $post->title }}">@endif</a>
                                </div>
                                <div class="blog-content">
                                    <div class="blog-meta">@if(($sectionConfig['show_date'] ?? true) && $post->published_at)<a href="{{ route('blog.show', $post) }}">{{ $post->published_at->format('F d, Y') }}</a>@endif</div>
                                    <h3 class="box-title"><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h3>
                                    <p class="box-text">{{ $post->excerpt }}</p>
                                    <div class="blog-bottom">
                                        <div class="blog-auth">@if($sectionConfig['show_author'] ?? true)<div class="media-body"><p class="desig">Written by</p><h5 class="name"><a href="{{ route('blog.show', $post) }}">{{ $post->author?->name ?? config('app.name') }}</a></h5></div>@endif</div>
                                        <a href="{{ route('blog.show', $post) }}" class="th-btn radius outline">{{ $sectionConfig['card_button_label'] ?? 'Read More' }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="col-12"><p class="text-center">Published real estate articles will appear here.</p></div>
            @endif
        </div>
    </div>
</section>
