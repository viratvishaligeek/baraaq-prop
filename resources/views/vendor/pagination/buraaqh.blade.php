@if ($paginator->hasPages())
    <nav aria-label="Pagination">
        <div class="th-pagination text-center pt-4">
            <ul>
                @if ($paginator->onFirstPage())
                    <li><span aria-disabled="true" aria-label="Previous page"><i class="far fa-arrow-left"></i></span></li>
                @else
                    <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"><i class="far fa-arrow-left"></i></a></li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li><span aria-hidden="true">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page === $paginator->currentPage())
                                <li><a class="active" href="{{ $url }}" aria-current="page">{{ $page }}</a></li>
                            @else
                                <li><a href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li><a class="next-page" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">Next <i class="far fa-arrow-right"></i></a></li>
                @else
                    <li><span class="next-page" aria-disabled="true">Next <i class="far fa-arrow-right"></i></span></li>
                @endif
            </ul>
        </div>
    </nav>
@endif
