<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('reference')->unique();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('fournisseur_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('quantite')->default(0);
            $table->integer('seuil_alerte')->default(5);
            $table->decimal('prix_achat', 10, 2)->default(0);
            $table->decimal('prix_vente', 10, 2)->default(0);
            $table->string('emplacement')->nullable()->comment('Hangar, zone piste, magasin technique...');
            $table->enum('criticite', ['normal', 'critique'])->default('normal')
                ->comment('Critique = pièce impactant directement la sécurité/disponibilité opérationnelle');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
