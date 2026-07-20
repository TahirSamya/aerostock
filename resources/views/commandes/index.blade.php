@extends('layouts.app')
@section('title', 'Commandes fournisseurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Commandes fournisseurs</h2>
        <p class="text-muted mb-0">Suivi des achats, de la commande à la réception</p>
    </div>
    <button class="btn btn-sf-primary" data-bs-toggle="modal" data-bs-target="#commandeModal">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle commande
    </button>
</div>

<div class="sf-panel">
    <table class="table sf-table mb-0">
        <thead>
            <tr><th>Date</th><th>Article</th><th>Fournisseur</th><th>Commandé</th><th>Reçu</th><th>Statut</th><th>Coût estimé</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($commandes as $c)
                <tr>
                    <td style="font-family:monospace">{{ $c->date_commande }}</td>
                    <td>{{ $c->produit->nom }}</td>
                    <td>{{ $c->fournisseur->nom }}</td>
                    <td>{{ $c->quantite_commandee }}</td>
                    <td>{{ $c->quantite_recue }}</td>
                    <td>
                        @if($c->statut === 'en_attente')
                            <span class="sf-badge sf-badge-amber">En attente</span>
                        @elseif($c->statut === 'partiellement_recue')
                            <span class="sf-badge sf-badge-sky">Partiellement reçue</span>
                        @elseif($c->statut === 'recue')
                            <span class="sf-badge sf-badge-teal">Reçue</span>
                        @else
                            <span class="sf-badge sf-badge-rust">Annulée</span>
                        @endif
                    </td>
                    <td>{{ number_format($c->quantite_commandee * $c->prix_unitaire, 2) }} MAD</td>
                    <td class="text-end">
                        @if(in_array($c->statut, ['en_attente', 'partiellement_recue']))
                            <button class="btn btn-sm btn-sf-outline me-1" data-bs-toggle="modal" data-bs-target="#receptionModal{{ $c->id }}">
                                <i class="bi bi-box-arrow-in-down"></i> Réceptionner
                            </button>
                            @if(auth()->user()->isAdmin())
                                <form action="{{ route('commandes.annuler', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Annuler cette commande ?')">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>

                @if(in_array($c->statut, ['en_attente', 'partiellement_recue']))
                <div class="modal fade" id="receptionModal{{ $c->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('commandes.receptionner', $c) }}">
                                @csrf
                                <div class="modal-header"><h5 class="modal-title">Réceptionner la commande</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <p>Article : <strong>{{ $c->produit->nom }}</strong></p>
                                    <p class="text-muted small">Restant à recevoir : {{ $c->quantiteRestante() }} / {{ $c->quantite_commandee }}</p>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Quantité reçue maintenant</label>
                                        <input type="number" min="1" max="{{ $c->quantiteRestante() }}" class="form-control" name="quantite_recue" value="{{ $c->quantiteRestante() }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-sf-primary">Confirmer la réception</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Aucune commande</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $commandes->links() }}</div>

<div class="modal fade" id="commandeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('commandes.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Nouvelle commande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fournisseur</label>
                        <select class="form-select" name="fournisseur_id" required>
                            @foreach($fournisseurs as $f)<option value="{{ $f->id }}">{{ $f->nom }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Article</label>
                        <select class="form-select" name="produit_id" required>
                            @foreach($produits as $p)<option value="{{ $p->id }}">{{ $p->nom }} (stock actuel : {{ $p->quantite }})</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-semibold">Quantité commandée</label>
                            <input type="number" min="1" class="form-control" name="quantite_commandee" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-semibold">Prix unitaire (MAD)</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="prix_unitaire" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Notes (optionnel)</label>
                        <input class="form-control" name="notes" placeholder="ex: livraison prévue sous 15 jours">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sf-primary">Créer la commande</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
