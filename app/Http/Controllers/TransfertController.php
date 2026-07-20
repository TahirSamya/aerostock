<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\TransfertStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransfertController extends Controller
{
    public function index()
    {
        $transferts = TransfertStock::with(['produit', 'user'])
            ->latest('date_transfert')
            ->paginate(15);
        $produits = Produit::orderBy('nom')->get();

        return view('transferts.index', compact('transferts', 'produits'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'emplacement_destination' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                $produit = Produit::lockForUpdate()->findOrFail($data['produit_id']);

                if ($data['quantite'] > $produit->quantite) {
                    throw new \Exception('La quantité à transférer dépasse le stock disponible.');
                }

                TransfertStock::create([
                    'produit_id' => $produit->id,
                    'user_id' => $request->user()->id,
                    'emplacement_source' => $produit->emplacement,
                    'emplacement_destination' => $data['emplacement_destination'],
                    'quantite' => $data['quantite'],
                    'date_transfert' => now()->toDateString(),
                ]);

                // Si on transfère TOUT le stock, l'emplacement du produit change définitivement.
                // Si c'est un transfert partiel, on garde l'emplacement d'origine par simplicité
                // (une vraie gestion multi-emplacements demanderait une table de stock par
                // emplacement — hors périmètre de ce prototype, mentionné en perspective).
                if ($data['quantite'] === $produit->quantite) {
                    $produit->update(['emplacement' => $data['emplacement_destination']]);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Transfert enregistré avec succès.');
    }
}
