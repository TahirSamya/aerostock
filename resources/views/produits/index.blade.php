@extends('layouts.app')
@section('title', 'Pièces & équipements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Pièces &amp; équipements</h2>
        <p class="text-muted mb-0">{{ $produits->total() }} article(s) référencé(s), triés par catégorie</p>
    </div>
    <button class="btn btn-sf-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-lg me-1"></i> Ajouter un article
    </button>
    <a href="{{ route('produits.export.csv') }}" class="btn btn-sf-outline">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exporter (Excel)
    </a>
</div>

<form method="GET" class="mb-3">
    <input class="form-control" style="max-width:320px" name="search" value="{{ request('search') }}"
           placeholder="Rechercher par nom ou référence...">
</form>

<div class="sf-panel">
    <table class="table sf-table mb-0">
        <thead>
            <tr>
                <th>Article</th><th>Référence</th><th>Catégorie</th><th>Emplacement</th>
                <th>Criticité</th><th>Stock</th><th>Prix vente</th><th></th>
            </tr>
        </thead>
        <tbody>
            @php $categoriePrecedente = null; @endphp
            @forelse($produits as $p)
                @if($categoriePrecedente !== $p->category_id)
                    @php $categoriePrecedente = $p->category_id; @endphp
                    <tr class="sf-group-row">
                        <td colspan="8">
                            <span class="sf-badge sf-badge-sky" style="font-family:monospace">{{ $p->category->code }}</span>
                            <span class="fw-semibold ms-1">{{ $p->category->nom }}</span>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="fw-semibold">{{ $p->nom }}</td>
                    <td style="font-family:monospace">{{ $p->reference }}</td>
                    <td>{{ $p->category->nom }}</td>
                    <td>{{ $p->emplacement ?? '—' }}</td>
                    <td>
                        <span class="sf-badge {{ $p->criticite === 'critique' ? 'sf-crit-critique' : 'sf-crit-normal' }}"
                              title="{{ $p->criticite === 'critique' ? 'Rupture = impact direct sur une opération importante' : 'Consommable courant, rupture non bloquante' }}">
                            {{ ucfirst($p->criticite) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $ratio = $p->tauxRemplissage();
                            $urgence = $p->niveauUrgence();
                            $color = match($urgence) {
                                'rupture', 'critique' => '#B5442E',
                                'bas' => '#C87F0A',
                                default => '#1F6F5C',
                            };
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="sf-gauge-track" title="{{ $p->quantite }} / {{ $p->capaciteReference() }} (capacité cible)">
                                <div class="sf-gauge-fill" style="width:{{ max($ratio*100,4) }}%;background:{{ $color }}"></div>
                            </div>
                            <span style="font-family:monospace;font-size:12px">{{ $p->quantite }}/{{ $p->capaciteReference() }}</span>
                        </div>
                    </td>
                    <td>{{ number_format($p->prix_vente, 2) }} MAD</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-sf-outline me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $p->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('produits.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet article ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>

                <!-- Modal édition -->
                <div class="modal fade" id="editModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('produits.update', $p) }}">
                                @csrf @method('PUT')
                                <div class="modal-header"><h5 class="modal-title">Modifier l'article</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label small fw-semibold">Nom</label>
                                        <input class="form-control" name="nom" value="{{ $p->nom }}" required></div>
                                    <div class="mb-3"><label class="form-label small fw-semibold">Référence</label>
                                        <input class="form-control" value="{{ $p->reference }}" readonly disabled>
                                        <div class="form-text">La référence est définitive, fixée à la création de l'article.</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Catégorie</label>
                                            <select class="form-select" name="category_id" required>
                                                @foreach($categories as $c)
                                                    <option value="{{ $c->id }}" @selected($c->id == $p->category_id)>{{ $c->nom }}</option>
                                                @endforeach
                                            </select></div>
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Fournisseur</label>
                                            <select class="form-select" name="fournisseur_id">
                                                <option value="">Aucun</option>
                                                @foreach($fournisseurs as $f)
                                                    <option value="{{ $f->id }}" @selected($f->id == $p->fournisseur_id)>{{ $f->nom }}</option>
                                                @endforeach
                                            </select></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Emplacement</label>
                                            <input class="form-control" name="emplacement" value="{{ $p->emplacement }}" placeholder="Magasin général, Local technique..."></div>
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Criticité</label>
                                            <select class="form-select" name="criticite">
                                                <option value="normal" @selected($p->criticite=='normal')>Normal</option>
                                                <option value="critique" @selected($p->criticite=='critique')>Critique</option>
                                            </select></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Seuil alerte</label>
                                            <input type="number" min="0" class="form-control" name="seuil_alerte" value="{{ $p->seuil_alerte }}" required></div>
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Stock max (capacité)</label>
                                            <input type="number" min="0" class="form-control" name="quantite_max" value="{{ $p->quantite_max }}"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Prix d'achat</label>
                                            <input type="number" step="0.01" min="0" class="form-control" name="prix_achat" value="{{ $p->prix_achat }}" required></div>
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Prix de vente</label>
                                            <input type="number" step="0.01" min="0" class="form-control" name="prix_vente" value="{{ $p->prix_vente }}" required></div>
                                    </div>
                                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>La quantité se modifie uniquement via "Mouvements".</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sf-outline" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-sf-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Aucun article trouvé</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $produits->links() }}</div>

<!-- Modal création -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('produits.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Nouvel article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nom</label>
                        <input class="form-control" name="nom" required></div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Catégorie</label>
                            <select class="form-select" name="category_id" id="createCategorySelect" required onchange="majReferenceAuto()">
                                <option value="">Choisir...</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" data-next-ref="{{ $nextReferences[$c->id] }}">{{ $c->nom }}</option>
                                @endforeach
                            </select></div>
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Référence</label>
                            <input class="form-control" id="createReferenceDisplay" readonly placeholder="Choisir une catégorie d'abord" style="font-family:monospace">
                            <div class="form-text">Générée automatiquement selon la catégorie (dernière référence + 1).</div>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Fournisseur</label>
                        <select class="form-select" name="fournisseur_id">
                            <option value="">Aucun</option>
                            @foreach($fournisseurs as $f)<option value="{{ $f->id }}">{{ $f->nom }}</option>@endforeach
                        </select></div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Emplacement</label>
                            <input class="form-control" name="emplacement" placeholder="Magasin général, Local technique..."></div>
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Criticité</label>
                            <select class="form-select" name="criticite">
                                <option value="normal">Normal</option>
                                <option value="critique">Critique</option>
                            </select></div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Quantité initiale</label>
                            <input type="number" min="0" class="form-control" name="quantite" value="0" required></div>
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Seuil alerte</label>
                            <input type="number" min="0" class="form-control" name="seuil_alerte" value="5" required></div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Stock max (capacité)</label>
                            <input type="number" min="0" class="form-control" name="quantite_max" placeholder="ex: 20">
                            <div class="form-text">Niveau considéré comme "plein" (100%) sur la jauge de stock.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Prix d'achat</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="prix_achat" value="0" required></div>
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Prix de vente</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="prix_vente" value="0" required></div>
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

<script>
function majReferenceAuto() {
    const select = document.getElementById('createCategorySelect');
    const display = document.getElementById('createReferenceDisplay');
    const option = select.options[select.selectedIndex];
    display.value = option ? (option.getAttribute('data-next-ref') || '') : '';
}
</script>
@endsection
