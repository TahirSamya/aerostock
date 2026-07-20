<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProduitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function un_visiteur_non_connecte_est_redirige_vers_login()
    {
        $response = $this->get(route('produits.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function on_peut_creer_un_produit_avec_des_donnees_valides()
    {
        $user = User::factory()->create(['role' => 'magasinier']);
        $category = Category::create(['nom' => 'Électronique']);

        $this->actingAs($user)->post(route('produits.store'), [
            'nom' => 'Clavier',
            'reference' => 'REF-100',
            'category_id' => $category->id,
            'quantite' => 5,
            'seuil_alerte' => 2,
            'prix_achat' => 100,
            'prix_vente' => 150,
            'criticite' => 'normal',
        ]);

        $this->assertDatabaseHas('produits', ['reference' => 'REF-100']);
    }

    #[Test]
    public function la_reference_dun_produit_doit_etre_unique()
    {
        $user = User::factory()->create(['role' => 'magasinier']);
        $category = Category::create(['nom' => 'Électronique']);
        Produit::create([
            'nom' => 'Clavier', 'reference' => 'REF-DOUBLE', 'category_id' => $category->id,
            'quantite' => 1, 'seuil_alerte' => 1, 'prix_achat' => 1, 'prix_vente' => 1, 'criticite' => 'normal',
        ]);

        $response = $this->actingAs($user)->post(route('produits.store'), [
            'nom' => 'Autre clavier',
            'reference' => 'REF-DOUBLE',
            'category_id' => $category->id,
            'quantite' => 1,
            'seuil_alerte' => 1,
            'prix_achat' => 1,
            'prix_vente' => 1,
            'criticite' => 'normal',
        ]);

        $response->assertSessionHasErrors('reference');
    }

    #[Test]
    public function un_magasinier_ne_peut_pas_supprimer_un_produit()
    {
        $magasinier = User::factory()->create(['role' => 'magasinier']);
        $category = Category::create(['nom' => 'Test']);
        $produit = Produit::create([
            'nom' => 'Test', 'reference' => 'REF-300', 'category_id' => $category->id,
            'quantite' => 5, 'seuil_alerte' => 1, 'prix_achat' => 1, 'prix_vente' => 1, 'criticite' => 'normal',
        ]);

        $this->actingAs($magasinier)->delete(route('produits.destroy', $produit));

        $this->assertDatabaseHas('produits', ['id' => $produit->id]);
    }
}
