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
    <a href="{{ route('produits.export.pdf') }}" target="_blank" class="btn btn-sf-outline">
        <i class="bi bi-file-earmark-pdf me-1"></i> Exporter (PDF)
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
                        <button class="btn btn-sm btn-sf-outline me-1" data-bs-toggle="modal" data-bs-target="#emplacementsModal{{ $p->id }}" title="Répartition par emplacement">
                            <i class="bi bi-geo-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-sf-outline me-1" data-bs-toggle="modal" data-bs-target="#historiqueModal{{ $p->id }}" title="Historique du prix d'achat">
                            <i class="bi bi-clock-history"></i>
                        </button>
                        <button class="btn btn-sm btn-sf-outline me-1" data-bs-toggle="modal" data-bs-target="#qrModal{{ $p->id }}" title="Code QR de la référence">
                            <i class="bi bi-qr-code"></i>
                        </button>
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

                <!-- Modal répartition par emplacement -->
                <div class="modal fade" id="emplacementsModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header"><h5 class="modal-title">Répartition — {{ $p->nom }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <p class="text-muted small">Stock total de l'article : <strong>{{ $p->quantite }}</strong>. Répartissez-le entre plusieurs lieux physiques ci-dessous.</p>
                                @if($p->emplacements->isNotEmpty())
                                    <table class="table sf-table sf-table-compact mb-3">
                                        <thead><tr><th>Emplacement</th><th>Quantité</th><th></th></tr></thead>
                                        <tbody>
                                            @foreach($p->emplacements as $e)
                                                <tr>
                                                    <td>{{ $e->emplacement }}</td>
                                                    <td style="font-family:monospace">{{ $e->quantite }}</td>
                                                    <td class="text-end">
                                                        <form action="{{ route('produits.emplacements.destroy', [$p, $e]) }}" method="POST" onsubmit="return confirm('Retirer cet emplacement ?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                                <div class="d-flex justify-content-between small mb-2">
                                    <span class="text-muted">Réparti : {{ $p->quantiteAffectee() }} / {{ $p->quantite }}</span>
                                    @if($p->quantiteNonAffectee() > 0)
                                        <span class="sf-badge sf-badge-amber">{{ $p->quantiteNonAffectee() }} non affecté(s)</span>
                                    @endif
                                </div>
                                <form action="{{ route('produits.emplacements.store', $p) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input class="form-control form-control-sm" name="emplacement" placeholder="ex: Local technique" required>
                                    <input type="number" min="0" class="form-control form-control-sm" style="max-width:90px" name="quantite" placeholder="Qté" required>
                                    <button class="btn btn-sm btn-sf-primary text-nowrap">Ajouter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal historique du prix d'achat -->
                <div class="modal fade" id="historiqueModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header"><h5 class="modal-title">Historique du prix — {{ $p->nom }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <table class="table sf-table sf-table-compact mb-0">
                                    <thead><tr><th>Date</th><th>Prix d'achat</th><th>Variation</th></tr></thead>
                                    <tbody>
                                        @foreach($p->prixAchatHistorique as $i => $h)
                                            <tr>
                                                <td style="font-family:monospace">{{ \Carbon\Carbon::parse($h->date_changement)->format('d/m/Y') }}</td>
                                                <td>{{ number_format($h->prix_achat, 2) }} MAD</td>
                                                <td>
                                                    @if(isset($p->prixAchatHistorique[$i + 1]))
                                                        @php $delta = $h->prix_achat - $p->prixAchatHistorique[$i + 1]->prix_achat; @endphp
                                                        <span class="{{ $delta > 0 ? 'text-danger' : ($delta < 0 ? 'text-success' : 'text-muted') }}">
                                                            {{ $delta > 0 ? '+' : '' }}{{ number_format($delta, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal code QR -->
                <div class="modal fade" id="qrModal{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content text-center">
                            <div class="modal-header"><h5 class="modal-title">{{ $p->reference }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <canvas class="sf-qr-canvas" data-ref="{{ $p->reference }}"></canvas>
                                <p class="text-muted small mt-2 mb-0">{{ $p->nom }}</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-sm btn-sf-outline" onclick="window.print()">
                                    <i class="bi bi-printer me-1"></i> Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
                                            </select>
                                            <div class="form-text">"Critique" = une rupture bloquerait une opération importante.</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Seuil alerte</label>
                                            <input type="number" min="0" class="form-control" name="seuil_alerte" value="{{ $p->seuil_alerte }}" required>
                                            <div class="form-text">Sous ce niveau, alerte au tableau de bord.</div>
                                        </div>
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Stock max (capacité)</label>
                                            <input type="number" min="0" class="form-control" name="quantite_max" value="{{ $p->quantite_max }}">
                                            <div class="form-text">Niveau "plein" (100%) sur la jauge de stock.</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Prix d'achat</label>
                                            <input type="number" step="0.01" min="0" class="form-control" name="prix_achat" value="{{ $p->prix_achat }}" required>
                                            <div class="form-text">Sert au calcul de la valeur du stock.</div>
                                        </div>
                                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Prix de vente</label>
                                            <input type="number" step="0.01" min="0" class="form-control" name="prix_vente" value="{{ $p->prix_vente }}" required>
                                            <div class="form-text">Utile seulement si refacturé en interne.</div>
                                        </div>
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
                    <div class="sf-form-section">Identification</div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Nom</label>
                        <input class="form-control" name="nom" required placeholder="ex: Cartouche toner HP 26A"></div>
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
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Fournisseur</label>
                            <select class="form-select" name="fournisseur_id">
                                <option value="">Aucun</option>
                                @foreach($fournisseurs as $f)<option value="{{ $f->id }}">{{ $f->nom }}</option>@endforeach
                            </select></div>
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Emplacement</label>
                            <input class="form-control" name="emplacement" placeholder="Magasin général, Local technique...">
                            <div class="form-text">Lieu physique où trouver l'article.</div>
                        </div>
                    </div>

                    <div class="sf-form-section">Stock &amp; seuils</div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Quantité de départ</label>
                            <input type="number" min="0" class="form-control" name="quantite" value="0" required>
                            <div class="form-text">Stock au moment de la création. Ensuite, ne se modifie que via "Mouvements" (entrée/sortie), jamais en éditant l'article.</div>
                        </div>
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Seuil d'alerte</label>
                            <input type="number" min="0" class="form-control" name="seuil_alerte" value="5" required>
                            <div class="form-text">Sous ce niveau, l'article apparaît dans "Articles en alerte" au tableau de bord.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Stock max (capacité)</label>
                            <input type="number" min="0" class="form-control" name="quantite_max" placeholder="ex: 20">
                            <div class="form-text">Niveau considéré comme "plein" (100%) sur la jauge de stock.</div>
                        </div>
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Criticité</label>
                            <select class="form-select" name="criticite">
                                <option value="normal">Normal</option>
                                <option value="critique">Critique</option>
                            </select>
                            <div class="form-text">"Critique" = une rupture bloquerait une opération importante (priorité de réappro plus élevée).</div>
                        </div>
                    </div>

                    <div class="sf-form-section">Tarification</div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Prix d'achat</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="prix_achat" value="0" required>
                            <div class="form-text">Coût unitaire — sert à calculer la valeur totale du stock.</div>
                        </div>
                        <div class="col-6 mb-3"><label class="form-label small fw-semibold">Prix de vente</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="prix_vente" value="0" required>
                            <div class="form-text">Utile seulement si l'article est refacturé en interne. Sinon, laissez à 0.</div>
                        </div>
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

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
function majReferenceAuto() {
    const select = document.getElementById('createCategorySelect');
    const display = document.getElementById('createReferenceDisplay');
    const option = select.options[select.selectedIndex];
    display.value = option ? (option.getAttribute('data-next-ref') || '') : '';
}

// On génère le QR seulement à l'ouverture de son modal (le canvas n'est pas
// mesurable tant que le modal est caché), pas à 300 exemplaires au chargement de la page.
document.addEventListener('shown.bs.modal', function (event) {
    const canvas = event.target.querySelector('.sf-qr-canvas');
    if (canvas && !canvas.dataset.rendered) {
        QRCode.toCanvas(canvas, canvas.dataset.ref, { width: 180, margin: 1 });
        canvas.dataset.rendered = '1';
    }
});
</script>
@endsection
