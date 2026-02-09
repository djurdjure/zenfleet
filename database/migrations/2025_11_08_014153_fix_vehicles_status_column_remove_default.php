<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ====================================================================
 * 🔧 ENTERPRISE FIX - Résolution Conflit Systèmes Statuts
 * ====================================================================
 *
 * PROBLÈME IDENTIFIÉ:
 * ------------------
 * Conflit entre deux systèmes de statuts pour les véhicules:
 * 1. LEGACY SYSTEM: status_id (bigint) → vehicle_statuses table
 * 2. NEW SYSTEM: status (varchar enum) → VehicleStatus enum
 *
 * ERREUR RENCONTRÉE:
 * -----------------
 * SQLSTATE[22P02]: Invalid text representation: 7 ERROR:
 * invalid input syntax for type bigint: "parking"
 * select count(*) as aggregate from "vehicle_statuses" where "id" = parking
 *
 * CAUSE RACINE:
 * ------------
 * La colonne 'status' (varchar) avait une DEFAULT VALUE 'parking'.
 * Lors de la création d'un véhicule:
 * 1. Formulaire envoie status_id = 1 (correct)
 * 2. BD insère avec status_id = 1 ET status = 'parking' (DEFAULT)
 * 3. Trait HasStatus essaie VehicleStatus::from('parking')
 * 4. Une validation quelque part cherche 'parking' dans vehicle_statuses.id
 * 5. PostgreSQL échoue car 'parking' (string) ≠ bigint
 *
 * SOLUTION ENTERPRISE-GRADE:
 * -------------------------
 * - Retirer DEFAULT 'parking' de la colonne status
 * - Laisser status = NULL par défaut
 * - Le système utilise status_id (bigint) comme source de vérité
 * - L'enum VehicleStatus est mappé depuis status_id (voir Vehicle::getStatusEnumAttribute)
 *
 * MAPPING ACTUEL (Table vehicle_statuses):
 * ---------------------------------------
 * status_id = 1 → "Actif"
 * status_id = 2 → "En maintenance"
 * status_id = 3 → "Inactif"
 *
 * COMPATIBILITÉ:
 * -------------
 * ✅ Formulaires de création/édition véhicules
 * ✅ VehicleComposer injection automatique
 * ✅ Validation FormRequest
 * ✅ Trait HasStatus
 * ✅ PostgreSQL 18 + PostGIS 3.6
 *
 * TESTS EFFECTUÉS:
 * ---------------
 * ✅ Création véhicule avec status_id = 1 (Actif)
 * ✅ Création véhicule avec status_id = 2 (En maintenance)
 * ✅ Création véhicule avec status_id = 3 (Inactif)
 * ✅ Validation que status (varchar) reste NULL
 *
 * @version 1.0-Enterprise-Fix
 * @author ZenFleet Chief Software Architect
 * @since 2025-11-08
 * @category Database Schema Fix
 * ====================================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('vehicles') || !Schema::hasColumn('vehicles', 'status')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql') {
            return;
        }

        // ✅ Retirer la DEFAULT VALUE 'parking' de la colonne status
        // Pour PostgreSQL, on doit faire un ALTER TABLE direct
        // car Blueprint ne supporte pas bien DROP DEFAULT
        DB::statement("ALTER TABLE vehicles ALTER COLUMN status DROP DEFAULT");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('vehicles') || !Schema::hasColumn('vehicles', 'status')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql') {
            return;
        }

        // ⚠️ Restaurer DEFAULT 'parking' si rollback nécessaire
        DB::statement("ALTER TABLE vehicles ALTER COLUMN status SET DEFAULT 'parking'");
    }
};
