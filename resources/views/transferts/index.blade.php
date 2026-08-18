@extends('layouts.app')
@section('title', 'Transferts')

@section('content')

<div class="sf-page-header">
    <div>
        <h2 class="fw-bold mb-1">Transferts entre emplacements</h2>
        <p class="text-muted mb-0">
            Déplacer du stock d'un emplacement à un autre
            (magasin, bureau, local technique...)
        </p>
    </div>

    <button class="btn btn-sf-primary"
            data-bs-toggle="modal"
            data-bs-target="#transfertModal">
        <i class="bi bi-arrow-left-right me-1"></i>
        Nouveau transfert
    </button>
</div>

<div class="row mb-4">

    <div class="col-md-4">
        <div class="sf-panel p-3">
            <div class="text-muted small">
                Nombre total de transferts
            </div>
            <div class="fs-2 fw-bold">
                {{ $totalTransferts }}
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sf-panel p-3">
            <div class="text-muted small">
                Quantité totale transférée
            </div>
            <div class="fs-2 fw-bold">
                {{ $totalQuantite }}
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="sf-panel p-3">
            <div class="text-muted small">
                Produits concernés
            </div>
            <div class="fs-2 fw-bold">
                {{ $totalProduits }}
            </div>
        </div>
    </div>

</div>

<form method="GET" class="d-flex gap-2 mb-3">

    <input
        class="form-control"
        name="search"
        value="{{ request('search') }}"
        placeholder="Rechercher un article ou une référence..."
        style="max-width:420px">
<button type="submit" class="btn btn-primary">
    <i class="bi bi-search"></i>
</button>
    @if(request('search'))

        <a href="{{ route('transferts.index') }}"
           class="btn btn-outline-secondary">
            Réinitialiser
        </a>

    @endif

</form>

<div class="mb-2 text-muted">
    {{ $totalTransferts }} transfert(s) enregistré(s)
</div>

<div class="sf-panel">

    <table class="table sf-table mb-0">

        <thead>
        <tr>
            <th>Date</th>
            <th>Référence</th>
            <th>Article</th>
            <th>De</th>
            <th>Vers</th>
            <th>Quantité</th>
            <th>Agent</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>

        @forelse($transferts as $t)

            <tr>

                <td style="font-family:monospace">
                    {{ \Carbon\Carbon::parse($t->date_transfert)->format('d/m/Y') }}
                </td>

                <td>
                    <span class="badge bg-primary">
                        {{ $t->produit->reference ?? '-' }}
                    </span>
                </td>

                <td class="fw-semibold">
                    {{ $t->produit->nom }}
                </td>

                <td class="text-muted">
                    {{ $t->emplacement_source ?? '—' }}
                </td>

                <td class="fw-semibold">
                    {{ $t->emplacement_destination }}
                </td>

                <td>
                    <span class="badge bg-warning text-dark">
                        {{ $t->quantite }}
                    </span>
                </td>

                <td>
                    {{ $t->user->name ?? '—' }}
                </td>

                <td>
                    @if(($t->statut ?? 'effectue') === 'annule')
                        <span class="badge bg-danger">
                            Annulé
                        </span>
                    @else
                        <span class="badge bg-success">
                            Effectué
                        </span>
                    @endif
                </td>

                <td>

                    @if(($t->statut ?? 'effectue') !== 'annule')

                        <form method="POST"
                              action="{{ route('transferts.annuler', $t) }}"
                              onsubmit="return confirm('Annuler ce transfert ?')">

                            @csrf
                            @method('PUT')

                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger">
                                Annuler
                            </button>

                        </form>

                    @endif

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="9">
                    <div class="sf-empty text-center py-4">
                        <i class="bi bi-signpost-split"></i>
                        <span>Aucun transfert enregistré</span>
                    </div>
                </td>
            </tr>

        @endforelse

        </tbody>

    </table>

</div>

<div class="mt-3">
    {{ $transferts->links() }}
</div>

<div class="modal fade" id="transfertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST"
                  action="{{ route('transferts.store') }}">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        Nouveau transfert
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Article
                        </label>

                        <select class="form-select"
                                name="produit_id"
                                required>

                            @foreach($produits as $p)

                                <option value="{{ $p->id }}">
                                    {{ $p->reference }}
                                    - {{ $p->nom }}
                                    (Stock : {{ $p->quantite }})
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Nouvel emplacement
                        </label>

                        <input type="text"
                               class="form-control"
                               name="emplacement_destination"
                               required
                               placeholder="Ex : Local technique">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Quantité à transférer
                        </label>

                        <input type="number"
                               min="1"
                               class="form-control"
                               name="quantite"
                               required>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Annuler
                    </button>

                    <button type="submit"
                            class="btn btn-sf-primary">
                        Enregistrer
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection