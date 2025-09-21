@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="d-flex justify-content-center mt-5">
        <div class="d-flex d-sm-none">
            {{-- Mobile Previous / Next --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-outline-secondary disabled" aria-disabled="true">
                    « Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline-primary" rel="prev">
                    « Trước
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline-primary ml-3" rel="next">
                    Sau »
                </a>
            @else
                <span class="btn btn-outline-secondary disabled ml-3" aria-disabled="true">
                    Sau »
                </span>
            @endif
        </div>

        <div class="d-none d-sm-flex align-items-center justify-content-between w-100">
            <div>
                <p class="text-muted mb-0">
                    Hiển thị
                    <span class="fw-bold">{{ $paginator->firstItem() }}</span>
                    đến
                    <span class="fw-bold">{{ $paginator->lastItem() }}</span>
                    trong tổng số
                    <span class="fw-bold">{{ $paginator->total() }}</span>
                    kết quả
                </p>
            </div>

            <div>
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">‹</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="page-item disabled">
                                <span class="page-link">{{ $element }}</span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="page-item active">
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
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">›</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif