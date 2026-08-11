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
        <div class="sf-nav-group-label">Pilotage</div>
        <a href="{{ route('dashboard') }}" class="sf-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i><span>Tableau de bord</span>
        </a>
        <a href="{{ route('statistiques.index') }}" class="sf-nav-link {{ request()->routeIs('statistiques.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i><span>Statistiques</span>
        </a>

        <div class="sf-nav-group-label">Stock</div>
        <a href="{{ route('produits.index') }}" class="sf-nav-link {{ request()->routeIs('produits.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i><span>Pièces &amp; équipements</span>
        </a>
        <a href="{{ route('mouvements.index') }}" class="sf-nav-link {{ request()->routeIs('mouvements.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i><span>Mouvements</span>
        </a>
        <a href="{{ route('transferts.index') }}" class="sf-nav-link {{ request()->routeIs('transferts.*') ? 'active' : '' }}">
            <i class="bi bi-signpost-split"></i><span>Transferts</span>
        </a>

        <div class="sf-nav-group-label">Achats</div>
        <a href="{{ route('commandes.index') }}" class="sf-nav-link {{ request()->routeIs('commandes.*') ? 'active' : '' }}">
            <i class="bi bi-cart-check"></i><span>Commandes</span>
        </a>
        <a href="{{ route('fournisseurs.index') }}" class="sf-nav-link {{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i><span>Fournisseurs</span>
        </a>

        <div class="sf-nav-group-label">Administration</div>
        <a href="{{ route('categories.index') }}" class="sf-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i><span>Catégories</span>
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
        <button type="submit" class="btn btn-sf-outline w-100" style="border-color:#2A3A5F; color:#A9B4C9; background:transparent;">
            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
        </button>
    </form>
</aside>

<div class="sf-topbar">
    <form action="{{ route('produits.index') }}" method="GET" class="sf-topbar-search">
        <i class="bi bi-search"></i>
        <input type="text" name="search" placeholder="Rechercher un article, une référence...">
    </form>

    <div class="ms-auto sf-bell">
        <button type="button" class="sf-bell-btn" onclick="document.getElementById('sfBellMenu').classList.toggle('show')">
            <i class="bi bi-bell"></i>
            @if(($alertesCount ?? 0) > 0)
                <span class="sf-bell-dot">{{ $alertesCount > 9 ? '9+' : $alertesCount }}</span>
            @endif
        </button>
        <div class="sf-bell-menu" id="sfBellMenu">
            <div class="sf-bell-menu-head">Articles en alerte</div>
            @forelse(($alertesTop ?? []) as $p)
                <a href="{{ route('produits.index') }}" class="sf-bell-item">
                    <div class="t">{{ $p->nom }}</div>
                    <div class="s">{{ $p->category->nom ?? '' }} · stock {{ $p->quantite }} / seuil {{ $p->seuil_alerte }}</div>
                </a>
            @empty
                <div class="sf-bell-empty">Aucune alerte en cours.</div>
            @endforelse
            @if(($alertesCount ?? 0) > 0)
                <div class="sf-bell-foot"><a href="{{ route('dashboard') }}">Voir toutes les alertes</a></div>
            @endif
        </div>
    </div>
</div>

<main class="sf-main">
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
<script>
document.addEventListener('click', function (e) {
    const menu = document.getElementById('sfBellMenu');
    if (menu && !menu.contains(e.target) && !e.target.closest('.sf-bell-btn')) {
        menu.classList.remove('show');
    }
});
</script>
@yield('scripts')
</body>
</html>
