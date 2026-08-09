<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\StockEmplacement;
use Illuminate\Http\Request;

class StockEmplacementController extends Controller
{
    /**
     * Ajoute ou met à jour la quantité d'un article dans un emplacement précis.
     * On refuse si la somme des emplacements dépasserait le stock total réel de l'article
     * (le stock total reste toujours celui géré par les Mouvements — cette table ne fait
     * que répartir ce total entre plusieurs lieux physiques).
     */
    public function store(Request $request, Produit $produit)
    {
        $data = $request->validate([
            'emplacement' => 'required|string|max:255',
            'quantite' => 'required|integer|min:0',
        ]);

        $produit->load('emplacements');
        $autresEmplacements = $produit->emplacements->where('emplacement', '!=', $data['emplacement'])->sum('quantite');

        if ($autresEmplacements + $data['quantite'] > $produit->quantite) {
            return back()->with('error', "Répartition impossible : le total dépasserait le stock réel de l'article ({$produit->quantite}).");
        }

        StockEmplacement::updateOrCreate(
            ['produit_id' => $produit->id, 'emplacement' => $data['emplacement']],
            ['quantite' => $data['quantite']]
        );

        return back()->with('success', 'Répartition mise à jour.');
    }

    public function destroy(Produit $produit, StockEmplacement $emplacement)
    {
        abort_unless($emplacement->produit_id === $produit->id, 404);

        $emplacement->delete();

        return back()->with('success', 'Emplacement retiré.');
    }
}
