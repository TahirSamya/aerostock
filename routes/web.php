<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommandeFournisseurController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\TransfertController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- Authentification ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Routes accessibles à tout utilisateur connecté (admin ou magasinier) ---
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/produits', [ProduitController::class, 'index'])->name('produits.index');
    Route::get('/produits/export/csv', [ProduitController::class, 'exportCsv'])->name('produits.export.csv');
    Route::post('/produits', [ProduitController::class, 'store'])->name('produits.store');
    Route::put('/produits/{produit}', [ProduitController::class, 'update'])->name('produits.update');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    Route::get('/fournisseurs', [FournisseurController::class, 'index'])->name('fournisseurs.index');
    Route::post('/fournisseurs', [FournisseurController::class, 'store'])->name('fournisseurs.store');

    Route::get('/mouvements', [MouvementStockController::class, 'index'])->name('mouvements.index');
    Route::get('/mouvements/export/csv', [MouvementStockController::class, 'exportCsv'])->name('mouvements.export.csv');
    Route::get('/mouvements/{mouvement}/pdf', [MouvementStockController::class, 'exportPdf'])->name('mouvements.export.pdf');
    Route::post('/mouvements', [MouvementStockController::class, 'store'])->name('mouvements.store');

    Route::get('/transferts', [TransfertController::class, 'index'])->name('transferts.index');
    Route::post('/transferts', [TransfertController::class, 'store'])->name('transferts.store');

    Route::get('/commandes', [CommandeFournisseurController::class, 'index'])->name('commandes.index');
    Route::post('/commandes', [CommandeFournisseurController::class, 'store'])->name('commandes.store');
    Route::post('/commandes/{commande}/receptionner', [CommandeFournisseurController::class, 'receptionner'])->name('commandes.receptionner');

    // --- Routes réservées aux administrateurs uniquement ---
    Route::middleware('admin')->group(function () {
        Route::delete('/produits/{produit}', [ProduitController::class, 'destroy'])->name('produits.destroy');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::delete('/fournisseurs/{fournisseur}', [FournisseurController::class, 'destroy'])->name('fournisseurs.destroy');
        Route::delete('/mouvements/{mouvement}', [MouvementStockController::class, 'destroy'])->name('mouvements.destroy');
        Route::post('/mouvements/ajuster', [MouvementStockController::class, 'ajuster'])->name('mouvements.ajuster');
        Route::put('/commandes/{commande}/annuler', [CommandeFournisseurController::class, 'annuler'])->name('commandes.annuler');

        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/utilisateurs/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
