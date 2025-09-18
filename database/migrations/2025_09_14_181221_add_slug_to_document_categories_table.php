<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_categories', function (Blueprint $table) {
            // Ajouter la colonne slug. Longueur 100 recommandée pour indexation efficace.
            $table->string('slug', 100)->nullable()->after('name');

            // Index unique sur slug
            $table->unique('slug', 'document_categories_slug_unique');
        });

        // Optionnel: backfill des slugs pour les enregistrements existants
        // afin de pouvoir lever la nullabilité ensuite si souhaité.
        // Utiliser DB::table(...) ici si nécessaire.
        //
        // Exemple (décommenter si backfill voulu):
        // $categories = DB::table('document_categories')->select('id', 'name', 'slug')->get();
        // foreach ($categories as $cat) {
        //     if (empty($cat->slug) && !empty($cat->name)) {
        //         $base = \Illuminate\Support\Str::slug($cat->name);
        //         $slug = $base;
        //         $i = 1;
        //         while (DB::table('document_categories')->where('slug', $slug)->exists()) {
        //             $slug = $base.'-'.$i++;
        //         }
        //         DB::table('document_categories')->where('id', $cat->id)->update(['slug' => $slug]);
        //     }
        // }

        // Si vous avez fait un backfill complet, vous pouvez rendre la colonne non nulle:
        // Schema::table('document_categories', function (Blueprint $table) {
        //     $table->string('slug', 100)->nullable(false)->change();
        // });
    }

    public function down(): void
    {
        Schema::table('document_categories', function (Blueprint $table) {
            // Supprimer d’abord l’index unique, puis la colonne
            $table->dropUnique('document_categories_slug_unique');
            $table->dropColumn('slug');
        });
    }
};

