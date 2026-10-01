@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation" style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 6px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 6px 12px; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 6px; background: rgba(255,255,255,0.02); cursor: not-allowed;">
                    &laquo;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="padding: 6px 12px; border: 1px solid var(--border-color); color: var(--text-sub); border-radius: 6px; background: var(--bg-card); text-decoration: none; transition: background 0.2s;">
                    &laquo;
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="padding: 6px 12px; color: var(--text-muted);">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="padding: 6px 12px; border: 1px solid var(--primary); background: rgba(16, 185, 129, 0.1); color: var(--primary); font-weight: bold; border-radius: 6px;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" style="padding: 6px 12px; border: 1px solid var(--border-color); color: var(--text-sub); border-radius: 6px; background: var(--bg-card); text-decoration: none; transition: background 0.2s;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="padding: 6px 12px; border: 1px solid var(--border-color); color: var(--text-sub); border-radius: 6px; background: var(--bg-card); text-decoration: none; transition: background 0.2s;">
                    &raquo;
                </a>
            @else
                <span style="padding: 6px 12px; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 6px; background: rgba(255,255,255,0.02); cursor: not-allowed;">
                    &raquo;
                </span>
            @endif
        </div>
        
        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Menampilkan {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} data
        </div>
    </nav>
@endif
