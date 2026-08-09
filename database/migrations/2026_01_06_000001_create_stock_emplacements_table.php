<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_emplacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->string('emplacement');
            $table->integer('quantite')->default(0);
            $table->timestamps();

            $table->unique(['produit_id', 'emplacement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_emplacements');
    }
};
