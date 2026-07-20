@extends('layouts.app')
@section('title', 'Mouvements de stock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Mouvements de stock</h2>
        <p class="text-muted mb-0">Historique des entrées et sorties</p>
    </div>
    <button class="btn btn-sf-primary" data-bs-toggle="modal" data-bs-target="#mvtModal">
        <i class="bi bi-plus-lg me-1"></i> Nouveau mouvement
    </button>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-sf-outline" data-bs-toggle="modal" data-bs-target="#ajustModal">
            <i class="bi bi-sliders me-1"></i> Ajustement manuel
        </button>
    @endif
    <a href="{{ route('mouvements.export.csv') }}" class="btn btn-sf-outline">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exporter (Excel)
    </a>
</div>

<form method="GET" class="d-flex gap-2 mb-3">
    <select name="type" class="form-select" style="max-width:200px" onchange="this.form.submit()">
        <option value="">Tous les types</option>
        <option value="entree" @selected(request('type')=='entree')>Entrée</option>
        <option value="sortie" @selected(request('type')=='sortie')>Sortie</option>
        <option value="ajustement" @selected(request('type')=='ajustement')>Ajustement</option>
    </select>
    <select name="produit_id" class="form-select" style="max-width:280px" onchange="this.form.submit()">
        <option value="">Tous les articles</option>
        @foreach($produits as $p)
            <option value="{{ $p->id }}" @selected(request('produit_id')==$p->id)>{{ $p->nom }}</option>
        @endforeach
    </select>
    @if(request('type') || request('produit_id'))
        <a href="{{ route('mouvements.index') }}" class="btn btn-sf-outline">Réinitialiser</a>
    @endif
</form>

<div class="sf-panel">
    <table class="table sf-table mb-0">
        <thead><tr><th>Date</th><th>Article</th><th>Type</th><th>Détail</th><th>Motif</th><th>Agent</th><th></th></tr></thead>
        <tbody>
            @forelse($mouvements as $m)
                <tr>
                    <td style="font-family:monospace">{{ $m->date_mouvement }}</td>
                    <td>
                        {{ $m->produit->nom }}
                        <div class="text-muted" style="font-family:monospace;font-size:11px">{{ $m->produit->reference }}</div>
                    </td>
                    <td>
                        @if($m->type === 'entree')
                            <span class="sf-badge sf-badge-teal">▲ Entrée</span>
                        @elseif($m->type === 'sortie')
                            <span class="sf-badge sf-badge-rust">▼ Sortie</span>
                        @else
                            <span class="sf-badge sf-badge-amber">⚙ Ajustement</span>
                        @endif
                    </td>
                    <td style="font-family:monospace">
                        @if($m->type === 'entree')
                            <span class="text-success fw-semibold">+{{ $m->quantite }}</span>
                        @elseif($m->type === 'sortie')
                            <span class="text-danger fw-semibold">-{{ $m->quantite }}</span>
                        @else
                            {{ $m->ancienne_quantite }} → {{ $m->nouvelle_quantite }}
                            <span class="text-muted">({{ $m->nouvelle_quantite >= $m->ancienne_quantite ? '+' : '' }}{{ $m->nouvelle_quantite - $m->ancienne_quantite }})</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $m->motif ?? '—' }}</td>
                    <td>{{ $m->user->name ?? '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('mouvements.export.pdf', $m) }}" target="_blank" class="btn btn-sm btn-sf-outline me-1">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('mouvements.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('Annuler ce mouvement ? Le stock sera restauré.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Annuler</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Aucun mouvement</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $mouvements->links() }}</div>

<div class="modal fade" id="mvtModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('mouvements.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Nouveau mouvement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Article</label>
                        <select class="form-select" name="produit_id" required>
                            @foreach($produits as $p)
                                <option value="{{ $p->id }}">{{ $p->nom }} (dispo : {{ $p->quantite }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold d-block">Type de mouvement</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="typeEntree" value="entree" checked>
                            <label class="btn btn-outline-success" for="typeEntree">▲ Entrée</label>
                            <input type="radio" class="btn-check" name="type" id="typeSortie" value="sortie">
                            <label class="btn btn-outline-danger" for="typeSortie">▼ Sortie</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Quantité</label>
                        <input type="number" min="1" class="form-control" name="quantite" value="1" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Motif (optionnel)</label>
                        <input class="form-control" name="motif" placeholder="ex: Distribution service comptabilité, réapprovisionnement...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sf-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="modal fade" id="ajustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('mouvements.ajuster') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Ajustement manuel de stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p class="text-muted small">
                        À utiliser après un inventaire physique, pour corriger un écart entre
                        le stock théorique et le stock réel constaté.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Article</label>
                        <select class="form-select" name="produit_id" required>
                            @foreach($produits as $p)
                                <option value="{{ $p->id }}">{{ $p->nom }} (stock actuel : {{ $p->quantite }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Quantité réelle constatée</label>
                        <input type="number" min="0" class="form-control" name="nouvelle_quantite" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Motif (obligatoire)</label>
                        <input class="form-control" name="motif" required placeholder="ex: Inventaire physique du 14/07, écart constaté">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sf-primary">Enregistrer l'ajustement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
