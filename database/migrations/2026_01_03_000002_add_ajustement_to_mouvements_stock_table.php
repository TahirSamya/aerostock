<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // On élargit l'enum 'type' pour accepter aussi les ajustements manuels
        DB::statement("ALTER TABLE mouvements_stock MODIFY type ENUM('entree', 'sortie', 'ajustement') NOT NULL");

        Schema::table('mouvements_stock', function (Blueprint $table) {
            // Snapshot de l'ancienne et nouvelle quantité, utile uniquement pour les ajustements
            $table->integer('ancienne_quantite')->nullable()->after('quantite');
            $table->integer('nouvelle_quantite')->nullable()->after('ancienne_quantite');
        });
    }

    public function down(): void
    {
        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->dropColumn(['ancienne_quantite', 'nouvelle_quantite']);
        });

        DB::statement("ALTER TABLE mouvements_stock MODIFY type ENUM('entree', 'sortie') NOT NULL");
    }
};
