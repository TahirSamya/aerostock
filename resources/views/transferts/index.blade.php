@extends('layouts.app')
@section('title', 'Transferts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Transferts entre emplacements</h2>
        <p class="text-muted mb-0">Déplacer du stock d'un emplacement à un autre (magasin, bureau, local technique...)</p>
    </div>
    <button class="btn btn-sf-primary" data-bs-toggle="modal" data-bs-target="#transfertModal">
        <i class="bi bi-arrow-left-right me-1"></i> Nouveau transfert
    </button>
</div>

<div class="sf-panel">
    <table class="table sf-table mb-0">
        <thead><tr><th>Date</th><th>Article</th><th>De</th><th>Vers</th><th>Quantité</th><th>Agent</th></tr></thead>
        <tbody>
            @forelse($transferts as $t)
                <tr>
                    <td style="font-family:monospace">{{ $t->date_transfert }}</td>
                    <td>{{ $t->produit->nom }}</td>
                    <td class="text-muted">{{ $t->emplacement_source ?? '—' }}</td>
                    <td class="fw-semibold">{{ $t->emplacement_destination }}</td>
                    <td>{{ $t->quantite }}</td>
                    <td>{{ $t->user->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Aucun transfert enregistré</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $transferts->links() }}</div>

<div class="modal fade" id="transfertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('transferts.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Nouveau transfert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Article</label>
                        <select class="form-select" name="produit_id" required id="transfertProduitSelect">
                            @foreach($produits as $p)
                                <option value="{{ $p->id }}" data-emplacement="{{ $p->emplacement }}" data-quantite="{{ $p->quantite }}">
                                    {{ $p->nom }} (actuellement : {{ $p->emplacement ?? 'non défini' }}, stock : {{ $p->quantite }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nouvel emplacement</label>
                        <input class="form-control" name="emplacement_destination" required placeholder="ex: Magasin général - Étagère 3">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Quantité à transférer</label>
                        <input type="number" min="1" class="form-control" name="quantite" required>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Si tu transfères la totalité du stock, l'emplacement du produit est mis à jour automatiquement.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sf-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
