<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prix_achat_historiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->decimal('prix_achat', 10, 2);
            $table->date('date_changement');
            $table->timestamps();
        });

        // On enregistre le prix actuel de chaque article déjà existant comme
        // premier point de l'historique, pour que le graphique ne parte pas de zéro.
        DB::table('produits')->get()->each(function ($p) {
            DB::table('prix_achat_historiques')->insert([
                'produit_id' => $p->id,
                'prix_achat' => $p->prix_achat,
                'date_changement' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prix_achat_historiques');
    }
};
