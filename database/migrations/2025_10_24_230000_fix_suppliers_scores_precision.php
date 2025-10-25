<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * ====================================================================
     * 🔧 FIX SUPPLIERS SCORES PRECISION - ENTERPRISE GRADE
     * ====================================================================
     * 
     * PROBLÈME IDENTIFIÉ:
     * 1. quality_score DECIMAL(3,2) permet max 9.99 ❌
     * 2. reliability_score DECIMAL(3,2) permet max 9.99 ❌
     * 3. Formulaires/validation utilisent 0-100 (standard industrie) ✅
     * 4. rating: DB dit 0-10 mais formulaires/validation disent 0-5 ⚠️
     * 
     * ERREUR RENCONTRÉE:
     * SQLSTATE[22003]: Numeric value out of range: 7
     * ERROR: numeric field overflow
     * DETAIL: A field with precision 3, scale 2 must round to an absolute value less than 10^1
     * 
     * Valeurs soumises qui ont causé l'erreur:
     * - quality_score = 95 (> 9.99 max)
     * - reliability_score = 99 (> 9.99 max)
     * 
     * SOLUTION ENTERPRISE-GRADE:
     * ✅ quality_score: DECIMAL(3,2) → DECIMAL(5,2) pour 0-100
     * ✅ reliability_score: DECIMAL(3,2) → DECIMAL(5,2) pour 0-100
     * ✅ rating: Contrainte 0-10 → 0-5 (cohérence avec formulaires)
     * ✅ Mettre à jour contraintes CHECK PostgreSQL
     * ✅ Normaliser valeurs existantes si nécessaire
     * 
     * STANDARDS INDUSTRIE:
     * - Rating: 0-5 étoiles (standard universel)
     * - Scores qualité/fiabilité: 0-100% (standard métriques)
     * 
     * @version 1.0.0
     * @since 2025-10-24
     * @author ZenFleet Architecture Team
     * ====================================================================
     */
    public function up(): void
    {
        // 1️⃣ Supprimer les anciennes contraintes CHECK
        DB::statement("
            ALTER TABLE suppliers
            DROP CONSTRAINT IF EXISTS valid_scores
        ");
        
        DB::statement("
            ALTER TABLE suppliers
            DROP CONSTRAINT IF EXISTS valid_rating
        ");

        // 2️⃣ Modifier les colonnes pour accepter les bonnes plages
        Schema::table('suppliers', function (Blueprint $table) {
            // quality_score & reliability_score: DECIMAL(3,2) → DECIMAL(5,2)
            // Permet: 0.00 à 999.99 (largement suffisant pour 0-100%)
            $table->decimal('quality_score', 5, 2)->default(75.0)->change();
            $table->decimal('reliability_score', 5, 2)->default(75.0)->change();
            
            // rating: Garder DECIMAL(3,2) mais pour 0-5 (cohérence formulaires)
            // Default: 4.5/5 (excellent fournisseur par défaut)
            $table->decimal('rating', 3, 2)->default(4.5)->change();
        });

        // 3️⃣ Ajouter nouvelles contraintes CHECK cohérentes
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_scores CHECK (
                quality_score BETWEEN 0 AND 100 AND
                reliability_score BETWEEN 0 AND 100
            )
        ");
        
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_rating CHECK (
                rating BETWEEN 0 AND 5
            )
        ");

        // 4️⃣ Normaliser les valeurs existantes
        // Convertir rating 0-10 → 0-5 si nécessaire
        DB::statement("
            UPDATE suppliers
            SET 
                rating = CASE 
                    WHEN rating > 5 THEN rating / 2.0  -- Convertir 0-10 → 0-5
                    ELSE rating
                END,
                quality_score = LEAST(quality_score, 100),
                reliability_score = LEAST(reliability_score, 100)
            WHERE rating > 5 OR quality_score > 100 OR reliability_score > 100
        ");

        // 5️⃣ Ajouter index composite pour performance
        Schema::table('suppliers', function (Blueprint $table) {
            $table->index(['rating', 'quality_score', 'reliability_score'], 'idx_suppliers_scores');
        });
    }

    /**
     * Rollback: restaurer l'ancienne définition (avant fix)
     * ⚠️ ATTENTION: Peut causer perte de données si valeurs > 10
     */
    public function down(): void
    {
        // 1️⃣ Supprimer l'index composite
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex('idx_suppliers_scores');
        });

        // 2️⃣ Supprimer les nouvelles contraintes
        DB::statement("
            ALTER TABLE suppliers
            DROP CONSTRAINT IF EXISTS valid_scores
        ");
        
        DB::statement("
            ALTER TABLE suppliers
            DROP CONSTRAINT IF EXISTS valid_rating
        ");

        // 3️⃣ Restaurer les colonnes à DECIMAL(3,2) (0-10 max)
        Schema::table('suppliers', function (Blueprint $table) {
            $table->decimal('quality_score', 3, 2)->default(5.0)->change();
            $table->decimal('reliability_score', 3, 2)->default(5.0)->change();
            $table->decimal('rating', 3, 2)->default(5.0)->change();
        });

        // 4️⃣ Restaurer les anciennes contraintes CHECK (0-10)
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_scores CHECK (
                quality_score BETWEEN 0 AND 10 AND
                reliability_score BETWEEN 0 AND 10
            )
        ");
        
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_rating CHECK (
                rating BETWEEN 0 AND 10
            )
        ");
    }
};
