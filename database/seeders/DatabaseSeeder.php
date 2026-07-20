<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Fournisseur;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Utilisateurs ---
        $admin = User::create([
            'name' => 'Tahir',
            'email' => 'admin@aerostock.ma',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Salma Ouazzani',
            'email' => 's.ouazzani@onda-stock.ma',
            'password' => bcrypt('password'),
            'role' => 'magasinier',
        ]);

        User::create([
            'name' => 'Karim Benaissa',
            'email' => 'k.benaissa@onda-stock.ma',
            'password' => bcrypt('password'),
            'role' => 'magasinier',
        ]);

        // --- Catégories ---
        $bureau = Category::create([
            'nom' => 'Fournitures de bureau',
            'code' => 'BUR',
            'description' => 'Papeterie et consommables pour les services administratifs',
        ]);
        $informatique = Category::create([
            'nom' => 'Matériel informatique',
            'code' => 'INFO',
            'description' => 'Ordinateurs, imprimantes et accessoires informatiques',
        ]);
        $securite = Category::create([
            'nom' => 'Équipement de sécurité',
            'code' => 'SEC',
            'description' => 'Matériel de protection individuelle et de sécurité incendie',
        ]);
        $entretien = Category::create([
            'nom' => 'Produits d\'entretien',
            'code' => 'ENT',
            'description' => 'Produits de nettoyage et d\'hygiène pour les locaux',
        ]);

        // --- Fournisseurs ---
        $f1 = Fournisseur::create([
            'nom' => 'Bureau Plus Maroc',
            'telephone' => '05 22 24 56 78',
            'email' => 'contact@bureauplus.ma',
            'adresse' => '12 Rue Ibnou Sina, Casablanca',
        ]);
        $f2 = Fournisseur::create([
            'nom' => 'InfoTech Solutions',
            'telephone' => '05 22 33 44 55',
            'email' => 'ventes@infotech-solutions.ma',
            'adresse' => '45 Boulevard Zerktouni, Casablanca',
        ]);
        $f3 = Fournisseur::create([
            'nom' => 'Proteg Sécurité',
            'telephone' => '05 22 67 89 10',
            'email' => 'commercial@proteg-securite.ma',
            'adresse' => '8 Rue Al Massira, Casablanca',
        ]);
        $f4 = Fournisseur::create([
            'nom' => 'Clean Services Maroc',
            'telephone' => '05 22 11 22 33',
            'email' => 'contact@cleanservices.ma',
            'adresse' => 'Zone Industrielle, Aïn Sebaâ, Casablanca',
        ]);

        // --- Produits (stock administratif courant, rien de technique spécialisé) ---
        $produits = [
            ['nom' => 'Ordinateur de bureau', 'reference' => 'INFO-001', 'category_id' => $informatique->id, 'fournisseur_id' => $f2->id, 'quantite' => 8, 'seuil_alerte' => 3, 'quantite_max' => 10, 'prix_achat' => 5200, 'prix_vente' => 6200, 'emplacement' => 'Magasin informatique', 'criticite' => 'normal'],
            ['nom' => 'Imprimante laser', 'reference' => 'INFO-002', 'category_id' => $informatique->id, 'fournisseur_id' => $f2->id, 'quantite' => 3, 'seuil_alerte' => 2, 'quantite_max' => 6, 'prix_achat' => 1800, 'prix_vente' => 2100, 'emplacement' => 'Magasin informatique', 'criticite' => 'normal'],
            ['nom' => 'Cartouche toner', 'reference' => 'INFO-003', 'category_id' => $informatique->id, 'fournisseur_id' => $f2->id, 'quantite' => 26, 'seuil_alerte' => 10, 'quantite_max' => 40, 'prix_achat' => 450, 'prix_vente' => 520, 'emplacement' => 'Magasin informatique', 'criticite' => 'normal'],
            ['nom' => 'Ramette papier A4', 'reference' => 'BUR-001', 'category_id' => $bureau->id, 'fournisseur_id' => $f1->id, 'quantite' => 150, 'seuil_alerte' => 50, 'quantite_max' => 300, 'prix_achat' => 28, 'prix_vente' => 35, 'emplacement' => 'Magasin général', 'criticite' => 'normal'],
            ['nom' => 'Chaise de bureau', 'reference' => 'BUR-002', 'category_id' => $bureau->id, 'fournisseur_id' => $f1->id, 'quantite' => 2, 'seuil_alerte' => 5, 'quantite_max' => 15, 'prix_achat' => 650, 'prix_vente' => 780, 'emplacement' => 'Magasin général', 'criticite' => 'normal'],
            ['nom' => 'Extincteur portable 6kg', 'reference' => 'SEC-001', 'category_id' => $securite->id, 'fournisseur_id' => $f3->id, 'quantite' => 4, 'seuil_alerte' => 10, 'quantite_max' => 25, 'prix_achat' => 320, 'prix_vente' => 380, 'emplacement' => 'Local technique', 'criticite' => 'critique'],
            ['nom' => 'Gilet de sécurité haute visibilité', 'reference' => 'SEC-002', 'category_id' => $securite->id, 'fournisseur_id' => $f3->id, 'quantite' => 15, 'seuil_alerte' => 20, 'quantite_max' => 50, 'prix_achat' => 45, 'prix_vente' => 60, 'emplacement' => 'Magasin général', 'criticite' => 'normal'],
            ['nom' => 'Gants de protection (paire)', 'reference' => 'SEC-003', 'category_id' => $securite->id, 'fournisseur_id' => $f3->id, 'quantite' => 30, 'seuil_alerte' => 15, 'quantite_max' => 60, 'prix_achat' => 15, 'prix_vente' => 22, 'emplacement' => 'Magasin général', 'criticite' => 'normal'],
            ['nom' => 'Produit désinfectant sol 5L', 'reference' => 'ENT-001', 'category_id' => $entretien->id, 'fournisseur_id' => $f4->id, 'quantite' => 12, 'seuil_alerte' => 8, 'quantite_max' => 30, 'prix_achat' => 55, 'prix_vente' => 70, 'emplacement' => 'Local entretien', 'criticite' => 'normal'],
            ['nom' => 'Papier essuie-mains (carton)', 'reference' => 'ENT-002', 'category_id' => $entretien->id, 'fournisseur_id' => $f4->id, 'quantite' => 5, 'seuil_alerte' => 10, 'quantite_max' => 25, 'prix_achat' => 90, 'prix_vente' => 110, 'emplacement' => 'Local entretien', 'criticite' => 'normal'],
        ];

        foreach ($produits as $p) {
            $produit = Produit::create($p);

            MouvementStock::create([
                'produit_id' => $produit->id,
                'user_id' => $admin->id,
                'type' => 'entree',
                'quantite' => $produit->quantite,
                'motif' => 'Stock initial',
                'date_mouvement' => now()->subDays(rand(3, 12))->toDateString(),
            ]);
        }

        // Quelques mouvements récents pour peupler le graphique du dashboard
        $exemples = Produit::inRandomOrder()->take(4)->get();
        foreach ($exemples as $produit) {
            MouvementStock::create([
                'produit_id' => $produit->id,
                'user_id' => $admin->id,
                'type' => 'sortie',
                'quantite' => rand(1, 3),
                'motif' => 'Distribution aux services',
                'date_mouvement' => now()->subDays(rand(0, 5))->toDateString(),
            ]);
        }

        // Une commande fournisseur de démo, en attente de réception
        $produitToner = Produit::where('reference', 'INFO-003')->first();
        if ($produitToner) {
            \App\Models\CommandeFournisseur::create([
                'fournisseur_id' => $f2->id,
                'produit_id' => $produitToner->id,
                'user_id' => $admin->id,
                'quantite_commandee' => 20,
                'quantite_recue' => 0,
                'statut' => 'en_attente',
                'prix_unitaire' => 450,
                'date_commande' => now()->subDays(2)->toDateString(),
                'notes' => 'Réapprovisionnement suite à alerte stock bas',
            ]);
        }
    }
}
