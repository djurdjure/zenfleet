<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour ajouter les champs de gestion de disponibilité
 * aux tables vehicles et drivers
 * 
 * SYSTÈME ENTERPRISE-GRADE ULTRA-PRO
 * 
 * @version 2.0.0
 * @since 2025-11-09
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajout des champs sur la table vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            // Champs de disponibilité et statut d'affectation
            if (!Schema::hasColumn('vehicles', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('status')
                    ->comment('Indique si le véhicule est disponible pour affectation');
            }
            
            if (!Schema::hasColumn('vehicles', 'current_driver_id')) {
                $table->unsignedBigInteger('current_driver_id')->nullable()->after('is_available')
                    ->comment('ID du chauffeur actuellement affecté');
            }
            
            if (!Schema::hasColumn('vehicles', 'assignment_status')) {
                $table->enum('assignment_status', ['available', 'assigned', 'maintenance', 'reserved'])
                    ->default('available')->after('current_driver_id')
                    ->comment('Statut d\'affectation du véhicule');
            }
            
            if (!Schema::hasColumn('vehicles', 'last_assignment_end')) {
                $table->timestamp('last_assignment_end')->nullable()->after('assignment_status')
                    ->comment('Date/heure de fin de la dernière affectation');
            }
            
            if (!Schema::hasColumn('vehicles', 'current_mileage')) {
                $table->integer('current_mileage')->nullable()->after('last_assignment_end')
                    ->comment('Kilométrage actuel du véhicule');
            }
            
            // Index pour les performances
            $table->index('is_available', 'idx_vehicles_availability');
            $table->index('assignment_status', 'idx_vehicles_assignment_status');
            $table->index(['is_available', 'assignment_status'], 'idx_vehicles_availability_status');
            
            // Foreign key pour current_driver_id
            if (!Schema::hasColumn('vehicles', 'current_driver_id')) {
                $table->foreign('current_driver_id')
                    ->references('id')->on('drivers')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });

        // Ajout des champs sur la table drivers
        Schema::table('drivers', function (Blueprint $table) {
            // Champs de disponibilité et statut d'affectation
            if (!Schema::hasColumn('drivers', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('status')
                    ->comment('Indique si le chauffeur est disponible pour affectation');
            }
            
            if (!Schema::hasColumn('drivers', 'current_vehicle_id')) {
                $table->unsignedBigInteger('current_vehicle_id')->nullable()->after('is_available')
                    ->comment('ID du véhicule actuellement affecté');
            }
            
            if (!Schema::hasColumn('drivers', 'assignment_status')) {
                $table->enum('assignment_status', ['available', 'assigned', 'on_leave', 'training'])
                    ->default('available')->after('current_vehicle_id')
                    ->comment('Statut d\'affectation du chauffeur');
            }
            
            if (!Schema::hasColumn('drivers', 'last_assignment_end')) {
                $table->timestamp('last_assignment_end')->nullable()->after('assignment_status')
                    ->comment('Date/heure de fin de la dernière affectation');
            }
            
            // Index pour les performances
            $table->index('is_available', 'idx_drivers_availability');
            $table->index('assignment_status', 'idx_drivers_assignment_status');
            $table->index(['is_available', 'assignment_status'], 'idx_drivers_availability_status');
            
            // Foreign key pour current_vehicle_id
            if (!Schema::hasColumn('drivers', 'current_vehicle_id')) {
                $table->foreign('current_vehicle_id')
                    ->references('id')->on('vehicles')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });

        // Ajout de champs supplémentaires sur la table assignments si nécessaire
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'ended_by_user_id')) {
                $table->unsignedBigInteger('ended_by_user_id')->nullable()
                    ->after('ended_at')
                    ->comment('ID de l\'utilisateur ayant terminé l\'affectation');
                    
                $table->foreign('ended_by_user_id')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            }
            
            // Index pour améliorer les performances des requêtes d'affectations expirées
            $table->index(['end_datetime', 'ended_at'], 'idx_assignments_expiry');
        });
        
        // Créer la table mileage_histories si elle n'existe pas
        if (!Schema::hasTable('mileage_histories')) {
            Schema::create('mileage_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vehicle_id');
                $table->unsignedBigInteger('driver_id')->nullable();
                $table->unsignedBigInteger('assignment_id')->nullable();
                $table->integer('mileage_value');
                $table->timestamp('recorded_at');
                $table->enum('type', ['assignment_start', 'assignment_end', 'manual', 'service']);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('organization_id');
                $table->timestamps();
                
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
                $table->foreign('driver_id')->references('id')->on('drivers')->nullOnDelete();
                $table->foreign('assignment_id')->references('id')->on('assignments')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
                
                $table->index(['vehicle_id', 'recorded_at'], 'idx_mileage_vehicle_date');
                $table->index('organization_id', 'idx_mileage_org');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer les colonnes de la table vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['current_driver_id']);
            $table->dropIndex('idx_vehicles_availability');
            $table->dropIndex('idx_vehicles_assignment_status');
            $table->dropIndex('idx_vehicles_availability_status');
            
            $table->dropColumn([
                'is_available',
                'current_driver_id',
                'assignment_status',
                'last_assignment_end',
                'current_mileage'
            ]);
        });

        // Retirer les colonnes de la table drivers
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['current_vehicle_id']);
            $table->dropIndex('idx_drivers_availability');
            $table->dropIndex('idx_drivers_assignment_status');
            $table->dropIndex('idx_drivers_availability_status');
            
            $table->dropColumn([
                'is_available',
                'current_vehicle_id',
                'assignment_status',
                'last_assignment_end'
            ]);
        });

        // Retirer les colonnes de la table assignments
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'ended_by_user_id')) {
                $table->dropForeign(['ended_by_user_id']);
                $table->dropColumn('ended_by_user_id');
            }
            $table->dropIndex('idx_assignments_expiry');
        });
        
        // Supprimer la table mileage_histories
        Schema::dropIfExists('mileage_histories');
    }
};
