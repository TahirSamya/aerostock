<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'admin' : accès total (gestion utilisateurs, suppressions)
            // 'magasinier' : peut gérer le stock au quotidien, mais pas supprimer
            //                de catégories/fournisseurs ni gérer les comptes
            $table->enum('role', ['admin', 'magasinier'])->default('magasinier')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
