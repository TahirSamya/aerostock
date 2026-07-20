<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MouvementStockController extends Controller
{
    public function index(Request $request)
    {
        $query = MouvementStock::with(['produit', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('produit_id')) {
            $query->where('produit_id', $request->produit_id);
        }

        $mouvements = $query->latest('date_mouvement')->latest('id')->paginate(15)->withQueryString();
        $produits = Produit::orderBy('nom')->get();

        return view('mouvements.index', compact('mouvements', 'produits'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'motif' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                $produit = Produit::lockForUpdate()->findOrFail($data['produit_id']);

                if ($data['type'] === 'sortie' && $produit->quantite < $data['quantite']) {
                    throw new \Exception("Stock insuffisant : seulement {$produit->quantite} disponible(s).");
                }

                MouvementStock::create([
                    'produit_id' => $produit->id,
                    'user_id' => $request->user()->id,
                    'type' => $data['type'],
                    'quantite' => $data['quantite'],
                    'motif' => $data['motif'] ?? null,
                    'date_mouvement' => now()->toDateString(),
                ]);

                if ($data['type'] === 'entree') {
                    $produit->increment('quantite', $data['quantite']);
                } else {
                    $produit->decrement('quantite', $data['quantite']);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Mouvement enregistré avec succès.');
    }

    public function destroy(MouvementStock $mouvement)
    {
        DB::transaction(function () use ($mouvement) {
            $produit = Produit::lockForUpdate()->findOrFail($mouvement->produit_id);

            if ($mouvement->type === 'entree') {
                $produit->decrement('quantite', $mouvement->quantite);
            } else {
                $produit->increment('quantite', $mouvement->quantite);
            }

            $mouvement->delete();
        });

        return back()->with('success', 'Mouvement annulé, stock restauré.');
    }

    /**
     * Ajustement manuel du stock (inventaire physique, correction d'erreur...).
     * Contrairement à entrée/sortie, on saisit ici directement la NOUVELLE quantité réelle,
     * et le système calcule l'écart tout seul — réservé aux admins (voir routes/web.php).
     */
    public function ajuster(Request $request)
    {
        $data = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'nouvelle_quantite' => 'required|integer|min:0',
            'motif' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($data, $request) {
            $produit = Produit::lockForUpdate()->findOrFail($data['produit_id']);
            $ancienneQuantite = $produit->quantite;
            $nouvelleQuantite = $data['nouvelle_quantite'];
            $ecart = abs($nouvelleQuantite - $ancienneQuantite);

            MouvementStock::create([
                'produit_id' => $produit->id,
                'user_id' => $request->user()->id,
                'type' => 'ajustement',
                'quantite' => $ecart,
                'ancienne_quantite' => $ancienneQuantite,
                'nouvelle_quantite' => $nouvelleQuantite,
                'motif' => $data['motif'],
                'date_mouvement' => now()->toDateString(),
            ]);

            $produit->update(['quantite' => $nouvelleQuantite]);
        });

        return back()->with('success', 'Stock ajusté avec succès.');
    }

    /**
     * Génère un bon de mouvement en PDF (traçabilité physique de la sortie/entrée).
     */
    public function exportPdf(MouvementStock $mouvement)
    {
        $mouvement->load(['produit', 'user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mouvements.pdf-bon', compact('mouvement'));

        return $pdf->stream('bon-mouvement-' . $mouvement->id . '.pdf');
    }

    /**
     * Exporte l'historique des mouvements en CSV (compatible Excel).
     */
    public function exportCsv()
    {
        $mouvements = MouvementStock::with(['produit', 'user'])
            ->latest('date_mouvement')
            ->get();

        $callback = function () use ($mouvements) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour qu'Excel affiche correctement les accents
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Article', 'Référence', 'Type', 'Quantité', 'Motif', 'Agent']);

            foreach ($mouvements as $m) {
                fputcsv($handle, [
                    $m->date_mouvement,
                    $m->produit->nom,
                    $m->produit->reference,
                    $m->type === 'entree' ? 'Entrée' : 'Sortie',
                    $m->quantite,
                    $m->motif ?? '',
                    $m->user->name ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mouvements_stock_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
