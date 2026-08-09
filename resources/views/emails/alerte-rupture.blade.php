<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background:#F5F6F8; padding:24px;">
    <div style="max-width:520px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #E4E7EC;">
        <div style="background:#B5442E;color:#fff;padding:16px 24px;">
            <strong style="font-size:16px;">⚠ Rupture de stock — article critique</strong>
        </div>
        <div style="padding:24px;color:#14213D;">
            <p>Bonjour,</p>
            <p>L'article suivant, déclaré <strong>critique</strong>, vient d'atteindre un stock de <strong>0</strong> :</p>
            <table style="width:100%;border-collapse:collapse;margin:16px 0;">
                <tr><td style="padding:6px 0;color:#5B6472;">Article</td><td style="padding:6px 0;font-weight:600;">{{ $produit->nom }}</td></tr>
                <tr><td style="padding:6px 0;color:#5B6472;">Référence</td><td style="padding:6px 0;font-family:monospace;">{{ $produit->reference }}</td></tr>
                <tr><td style="padding:6px 0;color:#5B6472;">Catégorie</td><td style="padding:6px 0;">{{ $produit->category->nom ?? '—' }}</td></tr>
                <tr><td style="padding:6px 0;color:#5B6472;">Emplacement</td><td style="padding:6px 0;">{{ $produit->emplacement ?? '—' }}</td></tr>
                <tr><td style="padding:6px 0;color:#5B6472;">Fournisseur</td><td style="padding:6px 0;">{{ $produit->fournisseur->nom ?? '—' }}</td></tr>
            </table>
            <p>Un réapprovisionnement prioritaire est recommandé.</p>
            <p style="color:#8A93A3;font-size:12px;margin-top:24px;">Notification automatique — AeroStock, gestion de stock ONDA.</p>
        </div>
    </div>
</body>
</html>
