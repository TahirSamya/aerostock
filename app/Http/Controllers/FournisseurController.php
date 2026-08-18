<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function index(Request $request)
    {
        $query = Fournisseur::query();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where('nom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%");
        }

        $fournisseurs = $query
            ->orderBy('nom')
            ->paginate(10)
            ->withQueryString();

        return view(
            'fournisseurs.index',
            compact('fournisseurs')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255|unique:fournisseurs,nom',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:255',
        ]);

        Fournisseur::create($data);

        return back()->with(
            'success',
            'Fournisseur ajouté.'
        );
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $fournisseur->delete();

        return back()->with(
            'success',
            'Fournisseur supprimé.'
        );
    }
}