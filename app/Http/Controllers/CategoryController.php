<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('produits')->orderBy('nom')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255|unique:categories,nom',
            'code' => 'required|string|max:10|alpha|unique:categories,code',
            'description' => 'nullable|string',
        ]);

        $data['code'] = strtoupper($data['code']);

        Category::create($data);

        return back()->with('success', 'Catégorie ajoutée.');
    }

    public function destroy(Category $category)
    {
        if ($category->produits()->exists()) {
            return back()->with('error', 'Impossible de supprimer : des produits utilisent cette catégorie.');
        }

        $category->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }
}
