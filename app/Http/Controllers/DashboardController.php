<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periode = (int) $request->get('periode', 7);
        $periode = in_array($periode, [7, 30, 90]) ? $periode : 7;

        $totalProduits = Produit::count();

        // Les articles en alerte sont classés par ordre d'urgence décroissant
        // (rupture totale d'abord, puis critique, puis simple seuil bas dépassé)
        // pour que les cas les plus urgents remontent toujours en haut de la liste.
        $ordreUrgence = ['rupture' => 0, 'critique' => 1, 'bas' => 2, 'ok' => 3];
        $produitsAlerte = Produit::whereColumn('quantite', '<=', 'seuil_alerte')
            ->with(['category', 'fournisseur'])
            ->get()
            ->sortBy(fn (Produit $p) => $ordreUrgence[$p->niveauUrgence()] ?? 9)
            ->values();

        $valeurStock = Produit::sum(DB::raw('quantite * prix_achat'));

        $mouvementsPeriode = MouvementStock::selectRaw('date_mouvement, type, SUM(quantite) as total')
            ->where('date_mouvement', '>=', now()->subDays($periode)->toDateString())
            ->where('type', '!=', 'ajustement')
            ->groupBy('date_mouvement', 'type')
            ->orderBy('date_mouvement')
            ->get();

        $dates = collect();
        for ($i = $periode - 1; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->toDateString());
        }
        $entrees = $dates->map(fn ($d) => (int) ($mouvementsPeriode->firstWhere(fn ($m) => $m->date_mouvement === $d && $m->type === 'entree')->total ?? 0));
        $sorties = $dates->map(fn ($d) => (int) ($mouvementsPeriode->firstWhere(fn ($m) => $m->date_mouvement === $d && $m->type === 'sortie')->total ?? 0));
        $totalEntreesPeriode = $entrees->sum();
        $totalSortiesPeriode = $sorties->sum();

        $repartition = Produit::selectRaw('category_id, COUNT(*) as total')
            ->with('category:id,nom')
            ->groupBy('category_id')
            ->get();

        $derniersMouvements = MouvementStock::with(['produit', 'user'])
            ->latest('date_mouvement')->latest('id')->take(8)->get();

        $commandesEnAttente = \App\Models\CommandeFournisseur::whereIn('statut', ['en_attente', 'partiellement_recue'])->count();

        return view('dashboard.index', compact(
            'totalProduits', 'produitsAlerte', 'valeurStock', 'periode',
            'dates', 'entrees', 'sorties', 'totalEntreesPeriode', 'totalSortiesPeriode',
            'repartition', 'derniersMouvements', 'commandesEnAttente'
        ));
    }
}
