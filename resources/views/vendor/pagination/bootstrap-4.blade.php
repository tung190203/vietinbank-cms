@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
{{--            @if ($paginator->onFirstPage())--}}
{{--                <li class="page-item disabled me-auto">--}}
{{--                    <a class="page-link text-primary font-size-base" href="#!">--}}
{{--                        <i class="fal fa-arrow-left"></i>--}}
{{--                        <span class="d-none d-md-inline ms-2">Trang trước</span>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @else--}}
{{--                <li class="page-item me-auto">--}}
{{--                    <a class="page-link text-primary font-size-base" href="{{ $paginator->previousPageUrl() }}">--}}
{{--                        <i class="fal fa-arrow-left"></i>--}}
{{--                        <span class="d-none d-md-inline ms-2">Trang trước</span>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @endif--}}

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
{{--            @if ($paginator->hasMorePages())--}}
{{--                <li class="page-item ms-auto">--}}
{{--                    <a class="page-link text-primary font-size-base" href="{{ $paginator->nextPageUrl() }}">--}}
{{--                        <span class="d-none d-md-inline me-2">Trang sau</span>--}}
{{--                        <i class="fal fa-arrow-right"></i>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @else--}}
{{--                <li class="page-item disabled ms-auto">--}}
{{--                    <a class="page-link text-primary font-size-base" href="#!">--}}
{{--                        <span class="d-none d-md-inline me-2">Trang sau</span>--}}
{{--                        <i class="fal fa-arrow-right"></i>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @endif--}}
        </ul>
    </nav>
@endif
