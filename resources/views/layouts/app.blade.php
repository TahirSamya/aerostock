<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AeroStock') — ONDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>

<aside class="sf-sidebar d-flex flex-column">
    <div class="sf-brand">
        <div class="sf-brand-mark">✈</div>
        <span class="fw-bold text-white">AeroStock</span>
    </div>
    <div class="sf-brand-sub">ONDA — Gestion de stock</div>

    <nav class="flex-grow-1">
        <a href="{{ route('dashboard') }}" class="sf-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i><span>Tableau de bord</span>
            @if(($alertesCount ?? 0) > 0)
                <span class="badge rounded-pill bg-danger ms-auto">{{ $alertesCount }}</span>
            @endif
        </a>
        <a href="{{ route('produits.index') }}" class="sf-nav-link {{ request()->routeIs('produits.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i><span>Pièces &amp; équipements</span>
        </a>
        <a href="{{ route('mouvements.index') }}" class="sf-nav-link {{ request()->routeIs('mouvements.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i><span>Mouvements</span>
        </a>
        <a href="{{ route('transferts.index') }}" class="sf-nav-link {{ request()->routeIs('transferts.*') ? 'active' : '' }}">
            <i class="bi bi-signpost-split"></i><span>Transferts</span>
        </a>
        <a href="{{ route('commandes.index') }}" class="sf-nav-link {{ request()->routeIs('commandes.*') ? 'active' : '' }}">
            <i class="bi bi-cart-check"></i><span>Commandes</span>
        </a>
        <a href="{{ route('categories.index') }}" class="sf-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i><span>Catégories</span>
        </a>
        <a href="{{ route('fournisseurs.index') }}" class="sf-nav-link {{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i><span>Fournisseurs</span>
        </a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('users.index') }}" class="sf-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i><span>Utilisateurs</span>
            </a>
        @endif
    </nav>

    <div class="text-white-50 small mb-2" style="padding: 0 6px;">
        <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name ?? 'Utilisateur' }}
        <span class="sf-badge {{ auth()->user()->isAdmin() ? 'sf-badge-sky' : 'sf-badge-teal' }} ms-1">
            {{ auth()->user()->isAdmin() ? 'Admin' : 'Magasinier' }}
        </span>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-sf-outline w-100" style="border-color:#2A3A5F; color:#A9B4C9;">
            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
        </button>
    </form>
</aside>

<main class="sf-main">
    @if(($alertesCount ?? 0) > 0 && !request()->routeIs('dashboard'))
        <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert">
            <div>
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>{{ $alertesCount }}</strong> article(s) sont sous le seuil d'alerte de stock.
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark">Voir le détail</a>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
