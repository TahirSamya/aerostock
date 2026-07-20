<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Helvetica, sans-serif; font-size: 13px; color: #1C2530; }
    .header { display: flex; justify-content: space-between; border-bottom: 3px solid #14213D; padding-bottom: 12px; margin-bottom: 20px; }
    .brand { font-size: 20px; font-weight: bold; }
    .subtitle { color: #555; font-size: 12px; }
    .doc-title { text-align: right; }
    .doc-title h2 { margin: 0; font-size: 16px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th { background: #F4F4F4; text-align: left; padding: 8px; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #ddd; }
    td { padding: 10px 8px; border-bottom: 1px solid #eee; }
    .badge { padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
    .badge-entree { background: #E1F0EC; color: #1F6F5C; }
    .badge-sortie { background: #F6E6E2; color: #B5442E; }
    .footer { margin-top: 40px; font-size: 11px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    .signature { margin-top: 60px; display: flex; justify-content: space-between; }
    .signature div { width: 45%; text-align: center; border-top: 1px solid #333; padding-top: 6px; font-size: 11px; }
</style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">AeroStock</div>
            <div class="subtitle">Gestion du stock administratif — ONDA</div>
        </div>
        <div class="doc-title">
            <h2>Bon de mouvement N°{{ str_pad($mouvement->id, 5, '0', STR_PAD_LEFT) }}</h2>
            <div class="subtitle">Émis le {{ now()->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    <table>
        <tr><th style="width:30%">Champ</th><th>Détail</th></tr>
        <tr><td>Article</td><td>{{ $mouvement->produit->nom }} ({{ $mouvement->produit->reference }})</td></tr>
        <tr><td>Emplacement</td><td>{{ $mouvement->produit->emplacement ?? '—' }}</td></tr>
        <tr><td>Type de mouvement</td>
            <td>
                <span class="badge {{ $mouvement->type === 'entree' ? 'badge-entree' : 'badge-sortie' }}">
                    {{ strtoupper($mouvement->type) }}
                </span>
            </td>
        </tr>
        <tr><td>Quantité</td><td>{{ $mouvement->quantite }}</td></tr>
        <tr><td>Motif</td><td>{{ $mouvement->motif ?? '—' }}</td></tr>
        <tr><td>Date du mouvement</td><td>{{ $mouvement->date_mouvement }}</td></tr>
        <tr><td>Agent responsable</td><td>{{ $mouvement->user->name ?? '—' }}</td></tr>
    </table>

    <div class="signature">
        <div>Signature agent magasin</div>
        <div>Signature responsable technique</div>
    </div>

    <div class="footer">
        Document généré automatiquement par AeroStock — usage interne, prototype académique (PFE).
    </div>
</body>
</html>
