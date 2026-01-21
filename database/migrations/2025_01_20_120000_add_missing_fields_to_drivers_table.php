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
        if (!Schema::hasTable('drivers')) {
            return;
        }

        Schema::table('drivers', function (Blueprint $table) {
            // Ajouter le lien de parenté pour le contact d'urgence
            if (!Schema::hasColumn('drivers', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship', 100)
                    ->nullable()
                    ->after('emergency_contact_phone')
                    ->comment('Lien de parenté avec le contact d\'urgence');
            }
            
            // Ajouter les notes professionnelles
            if (!Schema::hasColumn('drivers', 'notes')) {
                $table->text('notes')
                    ->nullable()
                    ->after('photo')
                    ->comment('Notes professionnelles, compétences, formations, remarques');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('drivers')) {
            return;
        }

        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'emergency_contact_relationship')) {
                $table->dropColumn('emergency_contact_relationship');
            }
            
            if (Schema::hasColumn('drivers', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
