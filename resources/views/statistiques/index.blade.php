@extends('layouts.app')
@section('title', 'Statistiques')

@section('content')
<div class="sf-page-header">
    <div>
        <h2 class="fw-bold mb-1">Statistiques de consommation</h2>
        <p class="text-muted mb-0">Articles qui sortent le plus vite du stock</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('statistiques.index', ['periode' => 30]) }}" class="btn btn-sm {{ $periode == 30 ? 'btn-sf-primary' : 'btn-sf-outline' }}">30 jours</a>
        <a href="{{ route('statistiques.index', ['periode' => 90]) }}" class="btn btn-sm {{ $periode == 90 ? 'btn-sf-primary' : 'btn-sf-outline' }}">90 jours</a>
        <a href="{{ route('statistiques.index', ['periode' => 365]) }}" class="btn btn-sm {{ $periode == 365 ? 'btn-sf-primary' : 'btn-sf-outline' }}">1 an</a>
    </div>
</div>

@if($topConsommation->isEmpty())
    <div class="sf-panel p-4 text-center text-muted">
        Aucune sortie de stock enregistrée sur cette période.
    </div>
@else
    <div class="row g-3">
        <div class="col-md-6">
            <div class="sf-panel p-3">
                <h6 class="fw-bold mb-2">Top 10 — quantité sortie</h6>
                <div style="height:280px">
                    <canvas id="consoChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="sf-panel">
                <div class="sf-panel-head"><h6 class="fw-bold mb-0">Détail</h6></div>
                <table class="table sf-table sf-table-compact mb-0">
                    <thead><tr><th>Article</th><th>Catégorie</th><th>Sorti</th><th>Valeur</th></tr></thead>
                    <tbody>
                        @foreach($topConsommation as $ligne)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $ligne->produit->nom }}</div>
                                    <div class="text-muted" style="font-family:monospace;font-size:11px">{{ $ligne->produit->reference }}</div>
                                </td>
                                <td class="text-muted">{{ $ligne->produit->category->nom ?? '—' }}</td>
                                <td style="font-family:monospace" class="fw-semibold">{{ $ligne->total_sorti }}</td>
                                <td>{{ number_format($ligne->valeur_sortie, 0, ',', ' ') }} MAD</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
@if($topConsommation->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('consoChart'), {
    type: 'bar',
    data: {
        labels: @json($topConsommation->pluck('produit.nom')),
        datasets: [{ label: 'Quantité sortie', data: @json($topConsommation->pluck('total_sorti')), backgroundColor: '#2B6CB0' }]
    },
    options: {
        indexAxis: 'y',
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});
</script>
@endif
@endsection
