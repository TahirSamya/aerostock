<?php

namespace App\Http\Middleware;

use App\Models\Produit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareGlobalStockData
{
    /**
     * Rend le nombre d'articles en alerte (et le top 5) disponible dans TOUTES
     * les vues, sans que chaque contrôleur ait à le recalculer — utilisé par la
     * pastille de la sidebar et la cloche de notifications du topbar.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $alertes = Produit::whereColumn('quantite', '<=', 'seuil_alerte')
                ->with('category')
                ->get()
                ->sortBy(fn (Produit $p) => match ($p->niveauUrgence()) {
                    'rupture' => 0,
                    'critique' => 1,
                    default => 2,
                })
                ->values();

            View::share('alertesCount', $alertes->count());
            View::share('alertesTop', $alertes->take(5));
        }

        return $next($request);
    }
}
