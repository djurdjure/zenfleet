<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🚀 Ajout des colonnes manquantes dans la table drivers
     * Migration enterprise-grade pour corriger les erreurs de colonnes
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // 🆔 Matricule employé (employee_number)
            if (!Schema::hasColumn('drivers', 'employee_number')) {
                $table->string('employee_number', 100)->nullable()->unique()->after('user_id');
            }

            // 📅 Date de naissance (birth_date)
            if (!Schema::hasColumn('drivers', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('date_of_birth');
            }

            // 📧 Email personnel (personal_email)
            if (!Schema::hasColumn('drivers', 'personal_email')) {
                $table->string('personal_email')->nullable()->unique()->after('email');
            }

            // 📍 Adresse complète
            if (!Schema::hasColumn('drivers', 'full_address')) {
                $table->text('full_address')->nullable()->after('address');
            }

            // 🚗 Informations permis détaillées
            if (!Schema::hasColumn('drivers', 'license_number')) {
                $table->string('license_number', 100)->nullable()->unique()->after('driver_license_number');
            }

            if (!Schema::hasColumn('drivers', 'license_category')) {
                $table->string('license_category', 50)->nullable()->after('license_number');
            }

            if (!Schema::hasColumn('drivers', 'license_issue_date')) {
                $table->date('license_issue_date')->nullable()->after('license_category');
            }

            if (!Schema::hasColumn('drivers', 'license_authority')) {
                $table->string('license_authority')->nullable()->after('license_issue_date');
            }

            // 📅 Dates de contrat
            if (!Schema::hasColumn('drivers', 'recruitment_date')) {
                $table->date('recruitment_date')->nullable()->after('hire_date');
            }

            if (!Schema::hasColumn('drivers', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable()->after('recruitment_date');
            }

            // 🩸 Informations médicales
            if (!Schema::hasColumn('drivers', 'blood_type')) {
                $table->string('blood_type', 10)->nullable()->after('contract_end_date');
            }

            // 🚨 Contact d'urgence détaillé
            if (!Schema::hasColumn('drivers', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('emergency_contact');
            }

            if (!Schema::hasColumn('drivers', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 50)->nullable()->after('emergency_contact_name');
            }

            // 🔄 Statut avec foreign key
            if (!Schema::hasColumn('drivers', 'status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->after('status');

                // Ajouter la clé étrangère si la table driver_statuses existe
                if (Schema::hasTable('driver_statuses')) {
                    $table->foreign('status_id')->references('id')->on('driver_statuses')->onDelete('set null');
                }
            }
        });

        // 📝 Commentaires sur la table
        DB::statement("COMMENT ON TABLE drivers IS 'Chauffeurs - Table enterprise avec colonnes étendues'");
    }

    /**
     * 🔄 Rollback de la migration
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $columnsToRemove = [
                'employee_number', 'birth_date', 'personal_email', 'full_address',
                'license_number', 'license_category', 'license_issue_date', 'license_authority',
                'recruitment_date', 'contract_end_date', 'blood_type',
                'emergency_contact_name', 'emergency_contact_phone', 'status_id'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('drivers', $column)) {
                    if ($column === 'status_id') {
                        $table->dropForeign(['status_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};