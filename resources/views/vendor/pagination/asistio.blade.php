@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="pagination-nav">
    <ul class="pagination">

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="pagination-item pagination-disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                <span class="pagination-link">&laquo; Previous</span>
            </li>
        @else
            <li class="pagination-item">
                <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="pagination-item pagination-dots" aria-disabled="true"><span class="pagination-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="pagination-item pagination-active" aria-current="page">
                            <span class="pagination-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="pagination-item">
                            <a class="pagination-link" href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="pagination-item">
                <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next &raquo;</a>
            </li>
        @else
            <li class="pagination-item pagination-disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                <span class="pagination-link">Next &raquo;</span>
            </li>
        @endif

    </ul>
</nav>
@endif