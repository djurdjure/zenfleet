<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToOrganizationsTable extends Migration
{
    /**
     * 🚀 AJOUT DES COLONNES MANQUANTES - ORGANIZATIONS ENTERPRISE
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // Vérifier et ajouter chaque colonne seulement si elle n'existe pas
            
            // Colonnes de base manquantes
            if (!Schema::hasColumn('organizations', 'legal_name')) {
                $table->string('legal_name')->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'organization_type')) {
                $table->string('organization_type')->nullable()->index();
            }
            
            if (!Schema::hasColumn('organizations', 'industry')) {
                $table->string('industry', 100)->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'description')) {
                $table->text('description')->nullable();
            }
            
            // Informations légales
            if (!Schema::hasColumn('organizations', 'siret')) {
                $table->string('siret', 20)->nullable()->unique();
            }
            
            if (!Schema::hasColumn('organizations', 'vat_number')) {
                $table->string('vat_number', 20)->nullable()->unique();
            }
            
            if (!Schema::hasColumn('organizations', 'legal_form')) {
                $table->string('legal_form', 50)->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'registration_number')) {
                $table->string('registration_number', 50)->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'registration_date')) {
                $table->date('registration_date')->nullable();
            }
            
            // Contact et communication
            if (!Schema::hasColumn('organizations', 'phone')) {
                $table->string('phone', 20)->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'website')) {
                $table->string('website')->nullable();
            }
            
            // Adresse complète
            if (!Schema::hasColumn('organizations', 'address')) {
                $table->string('address')->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'address_line_2')) {
                $table->string('address_line_2')->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'city')) {
                $table->string('city', 100)->nullable()->index();
            }
            
            if (!Schema::hasColumn('organizations', 'postal_code')) {
                $table->string('postal_code', 20)->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'state_province')) {
                $table->string('state_province', 100)->nullable();
            }
            
            // ✅ COLONNE COUNTRY - CRITIQUE
            if (!Schema::hasColumn('organizations', 'country')) {
                $table->string('country', 2)->nullable()->index()->comment('Code pays ISO 3166-1 alpha-2');
            }
            
            // Paramètres régionaux
            if (!Schema::hasColumn('organizations', 'timezone')) {
                $table->string('timezone', 50)->nullable()->default('Europe/Paris');
            }
            
            if (!Schema::hasColumn('organizations', 'currency')) {
                $table->string('currency', 3)->nullable()->default('EUR');
            }
            
            if (!Schema::hasColumn('organizations', 'language')) {
                $table->string('language', 5)->nullable()->default('fr');
            }
            
            if (!Schema::hasColumn('organizations', 'date_format')) {
                $table->string('date_format', 20)->nullable()->default('d/m/Y');
            }
            
            if (!Schema::hasColumn('organizations', 'time_format')) {
                $table->string('time_format', 10)->nullable()->default('H:i');
            }
            
            // Logo et branding
            if (!Schema::hasColumn('organizations', 'logo_path')) {
                $table->string('logo_path')->nullable();
            }
            
            // Statut et abonnement
            if (!Schema::hasColumn('organizations', 'status')) {
                $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('active')->index();
            }
            
            if (!Schema::hasColumn('organizations', 'subscription_plan')) {
                $table->enum('subscription_plan', ['basic', 'professional', 'enterprise'])->default('basic');
            }
            
            if (!Schema::hasColumn('organizations', 'subscription_expires_at')) {
                $table->timestamp('subscription_expires_at')->nullable();
            }
            
            // Limites et quotas
            if (!Schema::hasColumn('organizations', 'max_vehicles')) {
                $table->integer('max_vehicles')->default(25);
            }
            
            if (!Schema::hasColumn('organizations', 'max_drivers')) {
                $table->integer('max_drivers')->default(25);
            }
            
            if (!Schema::hasColumn('organizations', 'max_users')) {
                $table->integer('max_users')->default(10);
            }
            
            // Configuration avancée
            if (!Schema::hasColumn('organizations', 'working_days')) {
                $table->json('working_days')->nullable()->comment('Jours ouvrés [1,2,3,4,5]');
            }
            
            if (!Schema::hasColumn('organizations', 'settings')) {
                $table->json('settings')->nullable()->comment('Configuration organisation');
            }
            
            // Audit et traçabilité
            if (!Schema::hasColumn('organizations', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
            
            if (!Schema::hasColumn('organizations', 'admin_user_id')) {
                $table->unsignedBigInteger('admin_user_id')->nullable();
            }
            
            // Compteurs de performance
            if (!Schema::hasColumn('organizations', 'total_users')) {
                $table->integer('total_users')->default(0);
            }
            
            if (!Schema::hasColumn('organizations', 'active_users')) {
                $table->integer('active_users')->default(0);
            }
            
            // Index pour performance
            $table->index(['status', 'subscription_plan']);
            $table->index(['country', 'city']);
        });
    }

    /**
     * 🔄 ROLLBACK - Supprimer les colonnes ajoutées
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $columnsToRemove = [
                'legal_name', 'organization_type', 'industry', 'description',
                'siret', 'vat_number', 'legal_form', 'registration_number', 'registration_date',
                'phone', 'website', 'address', 'address_line_2', 'city', 'postal_code', 
                'state_province', 'country', 'timezone', 'currency', 'language', 
                'date_format', 'time_format', 'logo_path', 'status', 'subscription_plan',
                'subscription_expires_at', 'max_vehicles', 'max_drivers', 'max_users',
                'working_days', 'settings', 'created_by', 'updated_by', 'admin_user_id',
                'total_users', 'active_users'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('organizations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}

