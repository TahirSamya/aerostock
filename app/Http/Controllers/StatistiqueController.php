<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        $periode = (int) $request->get('periode', 30);
        $depuis = now()->subDays($periode)->toDateString();

        // Top articles qui sortent le plus vite sur la période (classement de consommation).
        $topConsommation = MouvementStock::query()
            ->where('type', 'sortie')
            ->where('date_mouvement', '>=', $depuis)
            ->with('produit.category')
            ->get()
            ->groupBy('produit_id')
            ->map(function ($mouvements) {
                $produit = $mouvements->first()->produit;
                return (object) [
                    'produit' => $produit,
                    'total_sorti' => $mouvements->sum('quantite'),
                    'valeur_sortie' => $mouvements->sum('quantite') * ($produit->prix_achat ?? 0),
                ];
            })
            ->filter(fn ($ligne) => $ligne->produit !== null)
            ->sortByDesc('total_sorti')
            ->take(10)
            ->values();

        return view('statistiques.index', compact('topConsommation', 'periode'));
    }
}
