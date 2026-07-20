<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function index()
    {
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        return view('fournisseurs.index', compact('fournisseurs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:255',
        ]);

        Fournisseur::create($data);

        return back()->with('success', 'Fournisseur ajouté.');
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $fournisseur->delete();

        return back()->with('success', 'Fournisseur supprimé.');
    }
}
