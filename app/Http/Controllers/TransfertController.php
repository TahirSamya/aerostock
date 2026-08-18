<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\TransfertStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransfertController extends Controller
{
    public function index(Request $request)
    {
        $query = TransfertStock::with(['produit', 'user']);

        if ($request->filled('search')) {
            $s = $request->search;

            $query->whereHas('produit', function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                  ->orWhere('reference', 'like', "%{$s}%");
            });
        }

        $transferts = $query
            ->latest('date_transfert')
            ->paginate(15)
            ->withQueryString();

        $produits = Produit::orderBy('nom')->get();

        $totalTransferts = TransfertStock::count();

        $totalQuantite = TransfertStock::sum('quantite');

        $totalProduits = TransfertStock::distinct('produit_id')->count('produit_id');
        return view(
            'transferts.index',
             compact(
        'transferts',
        'produits',
        'totalTransferts',
        'totalQuantite',
        'totalProduits'
    )
);
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

                $produit = Produit::lockForUpdate()
                    ->findOrFail($data['produit_id']);

                if ($data['quantite'] > $produit->quantite) {
                    throw new \Exception(
                        'La quantité à transférer dépasse le stock disponible.'
                    );
                }

                TransfertStock::create([
                    'produit_id' => $produit->id,
                    'user_id' => $request->user()->id,
                    'emplacement_source' => $produit->emplacement,
                    'emplacement_destination' => $data['emplacement_destination'],
                    'quantite' => $data['quantite'],
                    'date_transfert' => now()->toDateString(),
                ]);

                if ($data['quantite'] === $produit->quantite) {
                    $produit->update([
                        'emplacement' => $data['emplacement_destination']
                    ]);
                }
            });

        } catch (\Exception $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }

        return back()->with(
            'success',
            'Transfert enregistré avec succès.'
        );
    }
    public function annuler(TransfertStock $transfert)
{
    if ($transfert->statut === 'annule') {
        return back()->with('error', 'Ce transfert est déjà annulé.');
    }

    $transfert->update([
        'statut' => 'annule'
    ]);

    return back()->with(
        'success',
        'Transfert annulé avec succès.'
    );
}
}