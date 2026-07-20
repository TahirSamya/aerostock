<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MouvementStockTest extends TestCase
{
    use RefreshDatabase;

    protected function creerProduit(int $quantite = 10, int $seuil = 5): Produit
    {
        $category = Category::create(['nom' => 'Test']);

        return Produit::create([
            'nom' => 'Produit test',
            'reference' => 'REF-TEST-' . uniqid(),
            'category_id' => $category->id,
            'quantite' => $quantite,
            'seuil_alerte' => $seuil,
            'prix_achat' => 10,
            'prix_vente' => 20,
            'criticite' => 'normal',
        ]);
    }

    #[Test]
    public function une_entree_de_stock_augmente_la_quantite_du_produit()
    {
        $user = User::factory()->create(['role' => 'magasinier']);
        $produit = $this->creerProduit(quantite: 10);

        $this->actingAs($user)->post(route('mouvements.store'), [
            'produit_id' => $produit->id,
            'type' => 'entree',
            'quantite' => 5,
        ]);

        $this->assertEquals(15, $produit->fresh()->quantite);
    }

    #[Test]
    public function une_sortie_de_stock_diminue_la_quantite_du_produit()
    {
        $user = User::factory()->create(['role' => 'magasinier']);
        $produit = $this->creerProduit(quantite: 10);

        $this->actingAs($user)->post(route('mouvements.store'), [
            'produit_id' => $produit->id,
            'type' => 'sortie',
            'quantite' => 4,
        ]);

        $this->assertEquals(6, $produit->fresh()->quantite);
    }

    #[Test]
    public function une_sortie_est_refusee_si_le_stock_est_insuffisant()
    {
        $user = User::factory()->create(['role' => 'magasinier']);
        $produit = $this->creerProduit(quantite: 3);

        $response = $this->actingAs($user)->post(route('mouvements.store'), [
            'produit_id' => $produit->id,
            'type' => 'sortie',
            'quantite' => 10,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(3, $produit->fresh()->quantite);
    }

    #[Test]
    public function annuler_un_mouvement_entree_restaure_le_stock_precedent()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $produit = $this->creerProduit(quantite: 15);

        $mouvement = MouvementStock::create([
            'produit_id' => $produit->id,
            'user_id' => $admin->id,
            'type' => 'entree',
            'quantite' => 5,
            'date_mouvement' => now(),
        ]);

        $this->actingAs($admin)->delete(route('mouvements.destroy', $mouvement));

        $this->assertEquals(10, $produit->fresh()->quantite);
        $this->assertDatabaseMissing('mouvements_stock', ['id' => $mouvement->id]);
    }

    #[Test]
    public function un_magasinier_ne_peut_pas_annuler_un_mouvement()
    {
        $magasinier = User::factory()->create(['role' => 'magasinier']);
        $produit = $this->creerProduit(quantite: 10);

        $mouvement = MouvementStock::create([
            'produit_id' => $produit->id,
            'user_id' => $magasinier->id,
            'type' => 'entree',
            'quantite' => 5,
            'date_mouvement' => now(),
        ]);

        $this->actingAs($magasinier)->delete(route('mouvements.destroy', $mouvement));

        $this->assertDatabaseHas('mouvements_stock', ['id' => $mouvement->id]);
    }
}
