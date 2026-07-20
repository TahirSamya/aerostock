<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function un_magasinier_ne_peut_pas_supprimer_une_categorie()
    {
        $magasinier = User::factory()->create(['role' => 'magasinier']);
        $category = Category::create(['nom' => 'Test']);

        $response = $this->actingAs($magasinier)
            ->delete(route('categories.destroy', $category));

        // Redirigé vers le dashboard avec message d'erreur, pas de suppression
        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    #[Test]
    public function un_admin_peut_supprimer_une_categorie_sans_produit()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['nom' => 'Test']);

        $response = $this->actingAs($admin)
            ->delete(route('categories.destroy', $category));

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    #[Test]
    public function un_magasinier_ne_peut_pas_acceder_a_la_page_utilisateurs()
    {
        $magasinier = User::factory()->create(['role' => 'magasinier']);

        $response = $this->actingAs($magasinier)->get(route('users.index'));

        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function un_admin_peut_acceder_a_la_page_utilisateurs()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertOk();
    }

    #[Test]
    public function un_admin_ne_peut_pas_supprimer_son_propre_compte()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete(route('users.destroy', $admin));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
