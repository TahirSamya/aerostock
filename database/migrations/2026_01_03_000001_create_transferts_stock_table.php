<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transferts_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('emplacement_source')->nullable();
            $table->string('emplacement_destination');
            $table->integer('quantite');
            $table->date('date_transfert');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferts_stock');
    }
};
