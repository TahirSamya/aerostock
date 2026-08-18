<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transferts_stock', function (Blueprint $table) {
            $table->string('statut')
                  ->default('effectue')
                  ->after('date_transfert');
        });
    }

    public function down(): void
    {
        Schema::table('transferts_stock', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};