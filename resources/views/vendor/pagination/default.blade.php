@if ($paginator->hasPages())
    <nav style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; font-size: 0.875rem; color: var(--text-muted);">
        <div>
            Menampilkan <strong style="color: #fff;">{{ $paginator->firstItem() }}</strong>
            hingga <strong style="color: #fff;">{{ $paginator->lastItem() }}</strong>
            dari <strong style="color: #fff;">{{ $paginator->total() }}</strong> data
        </div>

        <div style="display: flex; gap: 6px; align-items: center;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 8px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 8px; color: rgba(255,255,255,0.25); cursor: not-allowed;">
                    &laquo; Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-muted); transition: all 0.2s ease; text-decoration: none;" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--text-muted)'">
                    &laquo; Sebelumnya
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="padding: 8px 10px; color: var(--text-muted);">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="padding: 8px 14px; background: var(--primary); color: #fff; border-radius: 8px; font-weight: 700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="padding: 8px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-muted); transition: all 0.2s ease; text-decoration: none;" onmouseover="this.style.background='rgba(5, 150, 105, 0.15)'; this.style.color='#34d399'; this.style.borderColor='rgba(16, 185, 129, 0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-color)'">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-muted); transition: all 0.2s ease; text-decoration: none;" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--text-muted)'">
                    Selanjutnya &raquo;
                </a>
            @else
                <span style="padding: 8px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 8px; color: rgba(255,255,255,0.25); cursor: not-allowed;">
                    Selanjutnya &raquo;
                </span>
            @endif
        </div>
    </nav>
@endif
