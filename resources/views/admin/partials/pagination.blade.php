@if ($paginator->hasPages())
    <nav class="pager" aria-label="Halaman">
        <span class="pager-info">Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }}</span>

        <div class="pager-actions">
            @if ($paginator->onFirstPage())
                <span class="btn btn-ghost is-disabled" aria-disabled="true">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-ghost">Sebelumnya</a>
            @endif

            <span class="pager-now">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-ghost">Berikutnya</a>
            @else
                <span class="btn btn-ghost is-disabled" aria-disabled="true">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
