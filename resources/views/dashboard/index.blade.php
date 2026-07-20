@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h2 class="fw-bold mb-0">Tableau de bord</h2>
        <p class="text-muted mb-0 small">Vue d'ensemble du stock administratif</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('dashboard', ['periode' => 7]) }}" class="btn btn-sm {{ $periode == 7 ? 'btn-sf-primary' : 'btn-sf-outline' }}">7 jours</a>
        <a href="{{ route('dashboard', ['periode' => 30]) }}" class="btn btn-sm {{ $periode == 30 ? 'btn-sf-primary' : 'btn-sf-outline' }}">30 jours</a>
        <a href="{{ route('dashboard', ['periode' => 90]) }}" class="btn btn-sm {{ $periode == 90 ? 'btn-sf-primary' : 'btn-sf-outline' }}">90 jours</a>
    </div>
</div>

<div class="row g-2 mb-2">
    <div class="col-md-3">
        <div class="sf-stat-card">
            <div class="sf-stat-label">Articles référencés</div>
            <div class="sf-stat-value">{{ $totalProduits }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sf-stat-card">
            <div class="sf-stat-label">Alertes stock faible</div>
            <div class="sf-stat-value {{ $produitsAlerte->count() ? 'danger' : '' }}">{{ $produitsAlerte->count() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sf-stat-card">
            <div class="sf-stat-label">Valeur du stock</div>
            <div class="sf-stat-value" style="font-size:19px;">{{ number_format($valeurStock, 0, ',', ' ') }} MAD</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sf-stat-card">
            <div class="sf-stat-label">Commandes en attente</div>
            <div class="sf-stat-value {{ $commandesEnAttente ? 'danger' : '' }}">{{ $commandesEnAttente }}</div>
        </div>
    </div>
</div>

<div class="row g-2 mb-2">
    <div class="col-md-7">
        <div class="sf-panel p-3 h-100">
            <h6 class="fw-bold mb-2">Entrées / Sorties — {{ $periode }} derniers jours</h6>
            <div style="height:170px">
                <canvas id="mvtChart"></canvas>
            </div>
            <div class="sf-mini-kpis mt-2">
                <div><span class="sf-mini-kpi-value text-success">+{{ $totalEntreesPeriode }}</span><span class="sf-mini-kpi-label">Entrées</span></div>
                <div><span class="sf-mini-kpi-value text-danger">-{{ $totalSortiesPeriode }}</span><span class="sf-mini-kpi-label">Sorties</span></div>
                <div><span class="sf-mini-kpi-value">{{ $totalEntreesPeriode - $totalSortiesPeriode >= 0 ? '+' : '' }}{{ $totalEntreesPeriode - $totalSortiesPeriode }}</span><span class="sf-mini-kpi-label">Solde net</span></div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="sf-panel p-3 h-100">
            <h6 class="fw-bold mb-2">Répartition par catégorie</h6>
            <div style="height:170px">
                <canvas id="catChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-2">
    <div class="col-md-6">
        <div class="sf-panel">
            <div class="sf-panel-head d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Articles en alerte</h6>
                @if($produitsAlerte->count())
                    <span class="sf-badge sf-badge-rust">{{ $produitsAlerte->count() }} à traiter</span>
                @endif
            </div>
            <table class="table sf-table sf-table-compact mb-0">
                <thead><tr><th>Article</th><th>Catégorie</th><th>Stock / seuil</th><th>Urgence</th><th></th></tr></thead>
                <tbody>
                    @forelse($produitsAlerte as $p)
                        @php
                            $urgence = $p->niveauUrgence();
                            $libelle = match($urgence) {
                                'rupture' => 'Rupture',
                                'critique' => 'Stock très bas',
                                default => 'Sous le seuil',
                            };
                            $badgeClass = match($urgence) {
                                'rupture', 'critique' => 'sf-badge-rust',
                                default => 'sf-badge-amber',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $p->nom }}</div>
                                <div class="text-muted" style="font-family:monospace;font-size:11px">{{ $p->reference }}</div>
                            </td>
                            <td class="text-muted">{{ $p->category->nom }}</td>
                            <td style="font-family:monospace">{{ $p->quantite }} / {{ $p->seuil_alerte }}</td>
                            <td>
                                <span class="sf-badge {{ $badgeClass }}">{{ $libelle }}</span>
                                @if($p->criticite === 'critique')
                                    <span class="sf-badge sf-crit-critique" title="Article déclaré critique — rupture = impact sur une opération importante">Article critique</span>
                                @endif
                            </td>
                            <td class="text-end"><a href="{{ route('commandes.index') }}" class="small">Commander →</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Aucune alerte, tous les stocks sont au-dessus du seuil.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sf-panel">
            <div class="sf-panel-head"><h6 class="fw-bold mb-0">Derniers mouvements</h6></div>
            <table class="table sf-table sf-table-compact mb-0">
                <thead><tr><th>Article</th><th>Type</th><th>Qté</th></tr></thead>
                <tbody>
                    @forelse($derniersMouvements as $m)
                        <tr>
                            <td>{{ $m->produit->nom }}</td>
                            <td>
                                <span class="sf-badge {{ $m->type === 'entree' ? 'sf-badge-teal' : 'sf-badge-rust' }}">
                                    {{ $m->type === 'entree' ? 'Entrée' : 'Sortie' }}
                                </span>
                            </td>
                            <td>{{ $m->quantite }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Aucun mouvement</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('mvtChart'), {
    type: 'line',
    data: {
        labels: @json($dates),
        datasets: [
            { label: 'Entrées', data: @json($entrees), borderColor: '#1F6F5C', backgroundColor: '#E1F0EC', tension: 0.3 },
            { label: 'Sorties', data: @json($sorties), borderColor: '#B5442E', backgroundColor: '#F6E6E2', tension: 0.3 },
        ]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8 } } } }
});

new Chart(document.getElementById('catChart'), {
    type: 'doughnut',
    data: {
        labels: @json($repartition->pluck('category.nom')),
        datasets: [{ data: @json($repartition->pluck('total')), backgroundColor: ['#1F6F5C','#C87F0A','#B5442E','#2B6CB0','#14213D'] }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8 } } } }
});
</script>
@endsection
