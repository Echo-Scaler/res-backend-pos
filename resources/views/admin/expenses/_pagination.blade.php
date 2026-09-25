@if ($paginator->hasPages())
    <nav class="modern-pagination-nav" role="navigation" aria-label="Pagination Navigation">
        <ul class="modern-pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="Previous Page">
                    <span class="page-link page-arrow" aria-hidden="true">
                        <i class="ti ti-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link page-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous Page">
                        <i class="ti ti-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link page-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next Page">
                        <i class="ti ti-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Next Page">
                    <span class="page-link page-arrow" aria-hidden="true">
                        <i class="ti ti-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
