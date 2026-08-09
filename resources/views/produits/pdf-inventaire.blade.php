<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Helvetica, sans-serif; font-size: 11px; color: #1C2530; }
    .header { display: flex; justify-content: space-between; border-bottom: 3px solid #14213D; padding-bottom: 12px; margin-bottom: 16px; }
    .brand { font-size: 20px; font-weight: bold; }
    .subtitle { color: #555; font-size: 12px; }
    .doc-title { text-align: right; }
    .doc-title h2 { margin: 0; font-size: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #F4F4F4; text-align: left; padding: 6px; font-size: 9.5px; text-transform: uppercase; border-bottom: 2px solid #ddd; }
    td { padding: 6px; border-bottom: 1px solid #eee; font-size: 10.5px; }
    .cat-row td { background: #FAFBFC; font-weight: bold; padding: 5px 6px; }
    .badge { padding: 2px 7px; border-radius: 10px; font-size: 9.5px; font-weight: bold; }
    .badge-critique { background: #F6E6E2; color: #B5442E; }
    .badge-normal { background: #EEF1F5; color: #5B6472; }
    .totaux { margin-top: 16px; text-align: right; font-size: 12px; }
    .totaux strong { font-size: 14px; }
</style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">AeroStock</div>
            <div class="subtitle">Gestion du stock administratif — ONDA</div>
        </div>
        <div class="doc-title">
            <h2>Inventaire complet</h2>
            <div class="subtitle">Émis le {{ now('Africa/Casablanca')->format('d/m/Y à H:i') }} — {{ $produits->count() }} article(s)</div>
        </div>
    </div>

    <table>
        <tr>
            <th style="width:20%">Article</th>
            <th style="width:10%">Référence</th>
            <th style="width:14%">Fournisseur</th>
            <th style="width:12%">Emplacement</th>
            <th style="width:9%">Criticité</th>
            <th style="width:8%">Qté</th>
            <th style="width:8%">Seuil</th>
            <th style="width:9%">Prix achat</th>
            <th style="width:10%">Valeur</th>
        </tr>
        @php $categoriePrecedente = null; @endphp
        @foreach($produits as $p)
            @if($categoriePrecedente !== $p->category_id)
                @php $categoriePrecedente = $p->category_id; @endphp
                <tr class="cat-row"><td colspan="9">{{ $p->category->nom }}</td></tr>
            @endif
            <tr>
                <td>{{ $p->nom }}</td>
                <td style="font-family:monospace">{{ $p->reference }}</td>
                <td>{{ $p->fournisseur->nom ?? '—' }}</td>
                <td>{{ $p->emplacement ?? '—' }}</td>
                <td><span class="badge {{ $p->criticite === 'critique' ? 'badge-critique' : 'badge-normal' }}">{{ ucfirst($p->criticite) }}</span></td>
                <td>{{ $p->quantite }}</td>
                <td>{{ $p->seuil_alerte }}</td>
                <td>{{ number_format($p->prix_achat, 2) }} MAD</td>
                <td>{{ number_format($p->quantite * $p->prix_achat, 2) }} MAD</td>
            </tr>
        @endforeach
    </table>

    <div class="totaux">
        Valeur totale du stock : <strong>{{ number_format($valeurTotale, 2, ',', ' ') }} MAD</strong>
    </div>
</body>
</html>
