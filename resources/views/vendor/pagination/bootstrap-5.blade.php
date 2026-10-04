@if ($paginator->hasPages())
    <nav class="modern-pagination-nav" role="navigation" aria-label="{{ __('pagination.pagination_navigation') ?? 'Pagination' }}">
        <div class="pagination-info-text">
            <span>{{ __('pagination.showing') ?? 'عرض' }}</span>
            <strong>{{ $paginator->firstItem() }}</strong>
            <span>{{ __('pagination.to') ?? 'إلى' }}</span>
            <strong>{{ $paginator->lastItem() }}</strong>
            <span>{{ __('pagination.of') ?? 'من أصل' }}</span>
            <strong>{{ $paginator->total() }}</strong>
            <span>{{ __('pagination.results') ?? 'نتيجة' }}</span>
        </div>

        <ul class="pagination-pills-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-pill disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span aria-hidden="true">&laquo; {{ __('السابق') }}</span>
                </li>
            @else
                <li class="page-pill">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        &laquo; {{ __('السابق') }}
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-pill disabled" aria-disabled="true">
                        <span>{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-pill active" aria-current="page">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-pill">
                                <a href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-pill">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        {{ __('التالي') }} &raquo;
                    </a>
                </li>
            @else
                <li class="page-pill disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span aria-hidden="true">{{ __('التالي') }} &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif

<style>
    .modern-pagination-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        padding: 12px 16px;
        background: transparent;
        font-family: inherit;
    }
    .pagination-info-text {
        font-size: 0.84rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .pagination-info-text strong {
        color: #0f172a;
        font-weight: 700;
        font-family: monospace;
    }
    .pagination-pills-list {
        display: flex;
        align-items: center;
        gap: 6px;
        list-style: none;
        margin: 0;
        padding: 0;
        flex-wrap: wrap;
    }
    .page-pill {
        display: inline-flex;
    }
    .page-pill a,
    .page-pill span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }
    .page-pill a:hover {
        background: #f1f5f9;
        color: #1e3a8a;
        border-color: #94a3b8;
    }
    .page-pill.active span {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #ffffff;
        border-color: #1e3a8a;
        box-shadow: 0 2px 6px rgba(30, 58, 138, 0.25);
    }
    .page-pill.disabled span {
        background: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    @media (max-width: 640px) {
        .modern-pagination-nav {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .pagination-pills-list {
            justify-content: center;
        }
    }
</style>
