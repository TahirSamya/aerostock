<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->integer('quantite_max')->nullable()->after('seuil_alerte')
                ->comment('Capacité / stock cible souhaité pour cet article — sert de référence à 100% pour la jauge de stock');
        });

        // Backfill raisonnable pour les articles déjà existants (pas de valeur définie par l'utilisateur) :
        // on part de 4x le seuil d'alerte, avec un minimum qui couvre la quantité déjà en stock.
        DB::table('produits')->whereNull('quantite_max')->orderBy('id')->get()->each(function ($p) {
            $suggested = max($p->seuil_alerte * 4, $p->quantite, 1);
            DB::table('produits')->where('id', $p->id)->update(['quantite_max' => $suggested]);
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn('quantite_max');
        });
    }
};
