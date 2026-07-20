<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('quantite_commandee');
            $table->integer('quantite_recue')->default(0);
            $table->enum('statut', ['en_attente', 'partiellement_recue', 'recue', 'annulee'])->default('en_attente');
            $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->date('date_commande');
            $table->date('date_reception')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes_fournisseurs');
    }
};
