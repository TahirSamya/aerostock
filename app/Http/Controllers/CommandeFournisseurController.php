<?php

namespace App\Http\Controllers;

use App\Models\CommandeFournisseur;
use App\Models\Fournisseur;
use App\Models\MouvementStock;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeFournisseurController extends Controller
{
    public function index()
    {
        $commandes = CommandeFournisseur::with(['fournisseur', 'produit', 'user'])
            ->latest('date_commande')
            ->paginate(15);
        $produits = Produit::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('commandes.index', compact('commandes', 'produits', 'fournisseurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'produit_id' => 'required|exists:produits,id',
            'quantite_commandee' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        CommandeFournisseur::create([
            ...$data,
            'user_id' => $request->user()->id,
            'date_commande' => now()->toDateString(),
            'statut' => 'en_attente',
        ]);

        return back()->with('success', 'Commande créée avec succès.');
    }

    /**
     * Réceptionne une commande (totalement ou partiellement) : met à jour le stock
     * du produit ET crée automatiquement un mouvement d'entrée tracé, pour ne jamais
     * avoir de stock qui apparaît "de nulle part".
     */
    public function receptionner(Request $request, CommandeFournisseur $commande)
    {
        $data = $request->validate([
            'quantite_recue' => 'required|integer|min:1',
        ]);

        if ($commande->statut === 'recue' || $commande->statut === 'annulee') {
            return back()->with('error', 'Cette commande ne peut plus être réceptionnée.');
        }

        $restant = $commande->quantiteRestante();
        if ($data['quantite_recue'] > $restant) {
            return back()->with('error', "Impossible de réceptionner plus que le restant commandé ({$restant}).");
        }

        DB::transaction(function () use ($commande, $data, $request) {
            $produit = Produit::lockForUpdate()->findOrFail($commande->produit_id);

            // Mouvement d'entrée automatique et tracé
            MouvementStock::create([
                'produit_id' => $produit->id,
                'user_id' => $request->user()->id,
                'type' => 'entree',
                'quantite' => $data['quantite_recue'],
                'motif' => 'Réception commande #' . $commande->id . ' — ' . $commande->fournisseur->nom,
                'date_mouvement' => now()->toDateString(),
            ]);

            $produit->increment('quantite', $data['quantite_recue']);

            $nouvelleQuantiteRecue = $commande->quantite_recue + $data['quantite_recue'];
            $commande->update([
                'quantite_recue' => $nouvelleQuantiteRecue,
                'statut' => $nouvelleQuantiteRecue >= $commande->quantite_commandee ? 'recue' : 'partiellement_recue',
                'date_reception' => now()->toDateString(),
            ]);
        });

        return back()->with('success', 'Réception enregistrée, stock mis à jour automatiquement.');
    }

    public function annuler(CommandeFournisseur $commande)
    {
        if ($commande->statut === 'recue') {
            return back()->with('error', 'Impossible d\'annuler une commande déjà entièrement reçue.');
        }

        $commande->update(['statut' => 'annulee']);

        return back()->with('success', 'Commande annulée.');
    }
}
