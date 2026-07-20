<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->after('nom')
                ->comment('Préfixe utilisé pour générer automatiquement la référence des articles (ex: SEC, INFO)');
        });

        // Backfill : pour les catégories déjà existantes, on déduit le code à partir
        // du préfixe déjà utilisé par leurs articles (ex: SEC-001 -> "SEC"), pour ne pas
        // casser la numérotation en cours. Si la catégorie n'a aucun article, on retombe
        // sur les 3 premières lettres du nom.
        $categories = DB::table('categories')->whereNull('code')->get();
        $used = [];

        foreach ($categories as $cat) {
            $sample = DB::table('produits')
                ->where('category_id', $cat->id)
                ->orderBy('id')
                ->value('reference');

            if ($sample && str_contains($sample, '-')) {
                $code = strtoupper(strstr($sample, '-', true));
            } else {
                $code = Str::of($cat->nom)->ascii()->replaceMatching('/[^A-Za-z]/', '')->upper()->substr(0, 3)->value();
                $code = $code ?: 'CAT';
            }

            $final = $code;
            $i = 1;
            while (in_array($final, $used)) {
                $final = $code . (++$i);
            }
            $used[] = $final;

            DB::table('categories')->where('id', $cat->id)->update(['code' => $final]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
