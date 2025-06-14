// database/migrations/YYYY_MM_DD_HHMMSS_add_custom_fields_to_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rendre la colonne 'name' nullable si elle ne l'est pas déjà,
            // car nous allons la dériver de first_name et last_name.
            // Ou, si on la garde NOT NULL, s'assurer qu'elle est remplie à la création.
            // Pour l'instant, nous ajoutons les champs. Breeze s'attend à 'name' NOT NULL.
            // Nous allons gérer la population de 'name' dans le modèle ou un observer.

            $table->string('first_name')->after('name')->nullable(); // Ou après 'id' si 'name' n'est plus le premier champ pertinent
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('phone', 50)->unique()->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'phone']);
        });
    }
};
