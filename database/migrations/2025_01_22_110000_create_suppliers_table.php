<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Créer l'ENUM pour les types de fournisseurs (avec protection si existe déjà)
        DB::statement("
            DO $$ BEGIN
                CREATE TYPE supplier_type_enum AS ENUM (
                    'mecanicien', 'assureur', 'station_service', 'pieces_detachees',
                    'peinture_carrosserie', 'pneumatiques', 'electricite_auto',
                    'controle_technique', 'transport_vehicules', 'autre'
                );
            EXCEPTION
                WHEN duplicate_object THEN null;
            END $$;
        ");

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');

            // Type de fournisseur
            $table->enum('supplier_type', [
                'mecanicien', 'assureur', 'station_service', 'pieces_detachees',
                'peinture_carrosserie', 'pneumatiques', 'electricite_auto',
                'controle_technique', 'transport_vehicules', 'autre'
            ]);

            // Identité juridique DZ (conformité réglementaire algérienne)
            $table->string('company_name')->index();
            $table->string('trade_register', 50)->nullable()->unique(); // RC - Registre Commerce
            $table->string('nif', 20)->nullable()->unique(); // Numéro d'Identification Fiscale
            $table->string('nis', 20)->nullable(); // Numéro d'Identification Statistique
            $table->string('ai', 20)->nullable(); // Article d'Imposition

            // Contact principal
            $table->string('contact_first_name', 100);
            $table->string('contact_last_name', 100);
            $table->string('contact_phone', 50)->index();
            $table->string('contact_email')->nullable();

            // Localisation DZ (spécifique à l'Algérie)
            $table->text('address');
            $table->string('city', 100)->index();
            $table->string('wilaya', 50)->index(); // Spécifique DZ - Wilaya
            $table->string('commune', 100)->nullable();
            $table->string('postal_code', 10)->nullable();

            // Communications additionnelles
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website', 500)->nullable();

            // Business intelligence
            $table->json('specialties')->default('[]'); // Spécialités du fournisseur
            $table->json('certifications')->default('[]'); // Certifications obtenues
            $table->json('service_areas')->default('[]'); // Zones de service (wilayas)

            // Métriques de performance
            $table->decimal('rating', 3, 2)->default(5.0)->index(); // Note 0-10
            $table->integer('response_time_hours')->default(24); // Temps de réponse en heures
            $table->decimal('quality_score', 3, 2)->default(5.0); // Score qualité 0-10
            $table->decimal('reliability_score', 3, 2)->default(5.0); // Score fiabilité 0-10

            // Termes commerciaux
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->integer('payment_terms')->default(30); // Jours de paiement
            $table->string('preferred_payment_method', 50)->default('virement');
            $table->decimal('credit_limit', 15, 2)->default(0); // Limite de crédit en DA

            // Informations bancaires DZ
            $table->string('bank_name')->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('rib', 20)->nullable(); // Relevé d'Identité Bancaire

            // Statut et gestion
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_preferred')->default(false)->index();
            $table->boolean('is_certified')->default(false); // Fournisseur certifié
            $table->boolean('blacklisted')->default(false)->index();
            $table->text('blacklist_reason')->nullable();

            // Documentation et attachements
            $table->json('documents')->default('[]'); // Documents légaux, certificats
            $table->text('notes')->nullable(); // Notes internes

            // Statistiques de performance (calculées)
            $table->integer('total_orders')->default(0);
            $table->decimal('total_amount_spent', 15, 2)->default(0);
            $table->timestamp('last_order_date')->nullable();
            $table->decimal('avg_order_value', 12, 2)->default(0);

            // Timestamps et soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Index composites pour performance
            $table->index(['organization_id', 'is_active']);
            $table->index(['organization_id', 'supplier_type']);
            $table->index(['organization_id', 'is_preferred']);
            $table->index(['wilaya', 'city']);
            $table->index(['rating', 'is_active']);

            // Contraintes foreign key
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });

        // Contraintes business PostgreSQL
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_rating CHECK (rating BETWEEN 0 AND 10)
        ");

        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_scores CHECK (
                quality_score BETWEEN 0 AND 10 AND
                reliability_score BETWEEN 0 AND 10
            )
        ");

        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_contract_dates CHECK (
                contract_start_date IS NULL OR
                contract_end_date IS NULL OR
                contract_start_date <= contract_end_date
            )
        ");

        // Contrainte pour NIF algérien (15 chiffres)
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_nif CHECK (
                nif IS NULL OR
                (char_length(nif) = 15 AND nif ~ '^[0-9]{15}$')
            )
        ");

        // Contrainte pour RC algérien (format XX/XX-XXXXXXX)
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_trade_register CHECK (
                trade_register IS NULL OR
                trade_register ~ '^[0-9]{2}/[0-9]{2}-[0-9]{7}$'
            )
        ");

        // Index de recherche textuelle
        DB::statement("CREATE INDEX suppliers_search_idx ON suppliers USING gin(to_tsvector('french', company_name || ' ' || coalesce(contact_first_name, '') || ' ' || coalesce(contact_last_name, '')))");
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
        DB::statement("DROP TYPE IF EXISTS supplier_type_enum");
    }
};