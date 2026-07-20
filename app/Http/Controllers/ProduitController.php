<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::with(['category', 'fournisseur']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")->orWhere('reference', 'like', "%{$s}%");
            });
        }

        // Tri par catégorie (ordre alphabétique des catégories), puis par référence
        // à l'intérieur de chaque catégorie, pour respecter la numérotation SEC-001, SEC-002...
        $produits = $query
            ->join('categories', 'categories.id', '=', 'produits.category_id')
            ->orderBy('categories.nom')
            ->orderBy('produits.reference')
            ->select('produits.*')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        // Prochaine référence disponible pour chaque catégorie, calculée à la volée,
        // pour pré-remplir automatiquement le champ (verrouillé) du formulaire de création.
        $nextReferences = $categories->mapWithKeys(
            fn (Category $c) => [$c->id => $this->nextReference($c)]
        );

        return view('produits.index', compact('produits', 'categories', 'fournisseurs', 'nextReferences'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'quantite' => 'required|integer|min:0',
            'seuil_alerte' => 'required|integer|min:0',
            'quantite_max' => 'nullable|integer|min:0',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'emplacement' => 'nullable|string|max:255',
            'criticite' => 'required|in:normal,critique',
        ]);

        // La référence n'est jamais saisie par l'utilisateur : elle est toujours générée
        // côté serveur à partir de la catégorie choisie, pour garantir la continuité de la
        // numérotation même si le champ affiché côté client a été modifié/désactivé en JS.
        $produit = DB::transaction(function () use ($data) {
            $category = Category::whereKey($data['category_id'])->lockForUpdate()->firstOrFail();
            $data['reference'] = $this->nextReference($category);

            return Produit::create($data);
        });

        return back()->with('success', "Produit ajouté avec succès (référence {$produit->reference}).");
    }

    public function update(Request $request, Produit $produit)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'seuil_alerte' => 'required|integer|min:0',
            'quantite_max' => 'nullable|integer|min:0',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'emplacement' => 'nullable|string|max:255',
            'criticite' => 'required|in:normal,critique',
        ]);
        // La quantité ne se modifie pas ici : uniquement via les mouvements de stock.
        // La référence non plus : elle est figée dès la création de l'article.

        $produit->update($data);

        return back()->with('success', 'Produit modifié avec succès.');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();

        return back()->with('success', 'Produit supprimé.');
    }

    /**
     * Calcule la prochaine référence disponible pour une catégorie donnée,
     * en se basant sur le plus grand numéro déjà utilisé (préfixe-XXX).
     * Exemple : dernier SEC-002 en base -> retourne SEC-003.
     */
    private function nextReference(Category $category): string
    {
        $prefix = $category->code ?: strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->nom), 0, 3));

        $dernierNumero = Produit::where('category_id', $category->id)
            ->where('reference', 'like', $prefix . '-%')
            ->get()
            ->map(function (Produit $p) use ($prefix) {
                return (int) substr($p->reference, strlen($prefix) + 1);
            })
            ->max();

        $prochainNumero = ($dernierNumero ?? 0) + 1;

        return $prefix . '-' . str_pad((string) $prochainNumero, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Exporte l'inventaire complet en CSV (compatible Excel).
     */
    public function exportCsv()
    {
        $produits = Produit::with(['category', 'fournisseur'])->orderBy('nom')->get();

        $callback = function () use ($produits) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Nom', 'Référence', 'Catégorie', 'Fournisseur', 'Emplacement', 'Criticité', 'Quantité', 'Seuil alerte', 'Stock max', 'Prix achat', 'Prix vente']);

            foreach ($produits as $p) {
                fputcsv($handle, [
                    $p->nom,
                    $p->reference,
                    $p->category->nom ?? '',
                    $p->fournisseur->nom ?? '',
                    $p->emplacement ?? '',
                    $p->criticite,
                    $p->quantite,
                    $p->seuil_alerte,
                    $p->quantite_max,
                    $p->prix_achat,
                    $p->prix_vente,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inventaire_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
