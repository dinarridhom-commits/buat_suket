@if($paginator->total() > 0)
    @php
        $window = \Illuminate\Pagination\UrlWindow::make($paginator);
        $elements = array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    @endphp
    <div class="pagination-wrap">
        <nav class="pg-bar" aria-label="Navigasi Halaman">
            <div class="pg-info">
                Menampilkan <strong>{{ $paginator->firstItem() }}</strong>&ndash;<strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> {{ $label ?? 'data' }}
            </div>
            <div class="pg-links">
                @if($paginator->onFirstPage())
                    <span class="pg-btn is-disabled" aria-disabled="true" aria-label="Halaman sebelumnya">&lsaquo;</span>
                @else
                    <a class="pg-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">&lsaquo;</a>
                @endif

                @foreach($elements as $element)
                    @if(is_string($element))
                        <span class="pg-dots">{{ $element }}</span>
                    @endif
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $paginator->currentPage())
                                <span class="pg-btn is-active" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="pg-btn" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if($paginator->hasMorePages())
                    <a class="pg-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">&rsaquo;</a>
                @else
                    <span class="pg-btn is-disabled" aria-disabled="true" aria-label="Halaman berikutnya">&rsaquo;</span>
                @endif
            </div>
        </nav>
    </div>
@endif
