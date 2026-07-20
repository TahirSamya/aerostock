<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — AeroStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
<div class="sf-login-wrap">
    <div class="sf-login-card">
        <div class="text-center mb-4">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                 style="width:52px;height:52px;border-radius:12px;background:linear-gradient(135deg,#2B6CB0,#4a8fd6);color:#fff;font-size:22px;">
                ✈
            </div>
            <h4 class="fw-bold mb-0">AeroStock</h4>
            <small class="text-muted">Gestion du stock administratif — ONDA</small>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="admin@aerostock.ma" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">Mot de passe</label>
                <input type="password" name="password" class="form-control" value="password" required>
            </div>
            <button type="submit" class="btn btn-sf-primary w-100">Se connecter</button>
        </form>
    </div>
</div>
</body>
</html>
