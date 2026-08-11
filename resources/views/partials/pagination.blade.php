{{--
    Pagination réutilisable, en français, avec récapitulatif "Affichage de X à Y sur Z résultats".
    Usage : @include('partials.pagination', ['paginator' => $produits])
--}}
@if ($paginator->hasPages())
    <div class="sf-pagination">
        <div class="sf-pagination-summary">
            Affichage de <strong>{{ $paginator->firstItem() }}</strong> à <strong>{{ $paginator->lastItem() }}</strong>
            sur <strong>{{ $paginator->total() }}</strong> résultat{{ $paginator->total() > 1 ? 's' : '' }}
        </div>

        <ul class="sf-pagination-nav">
            {{-- Page précédente --}}
            @if ($paginator->onFirstPage())
                <li class="sf-page-item disabled" aria-disabled="true">
                    <span class="sf-page-link"><i class="bi bi-chevron-left"></i></span>
                </li>
            @else
                <li class="sf-page-item">
                    <a class="sf-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Numéros de page --}}
            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="sf-page-item active" aria-current="page">
                        <span class="sf-page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="sf-page-item">
                        <a class="sf-page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Page suivante --}}
            @if ($paginator->hasMorePages())
                <li class="sf-page-item">
                    <a class="sf-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Page suivante">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="sf-page-item disabled" aria-disabled="true">
                    <span class="sf-page-link"><i class="bi bi-chevron-right"></i></span>
                </li>
            @endif
        </ul>
    </div>
@elseif ($paginator->total() > 0)
    {{-- Une seule page : on affiche quand même le récapitulatif, sans les flèches --}}
    <div class="sf-pagination">
        <div class="sf-pagination-summary">
            Affichage de <strong>{{ $paginator->firstItem() }}</strong> à <strong>{{ $paginator->lastItem() }}</strong>
            sur <strong>{{ $paginator->total() }}</strong> résultat{{ $paginator->total() > 1 ? 's' : '' }}
        </div>
    </div>
@endif
