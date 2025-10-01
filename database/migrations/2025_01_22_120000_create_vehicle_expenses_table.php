<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Créer l'ENUM pour les catégories de dépenses
        DB::statement("CREATE TYPE expense_category_enum AS ENUM (
            'maintenance_preventive', 'reparation', 'pieces_detachees',
            'carburant', 'assurance', 'controle_technique',
            'vignette', 'amendes', 'peage', 'parking',
            'lavage', 'transport', 'formation_chauffeur', 'autre'
        )");

        Schema::create('vehicle_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable(); // Chauffeur associé à la dépense
            $table->unsignedBigInteger('repair_request_id')->nullable(); // Lié à une demande de réparation

            // Catégorisation détaillée
            $table->enum('expense_category', [
                'maintenance_preventive', 'reparation', 'pieces_detachees',
                'carburant', 'assurance', 'controle_technique',
                'vignette', 'amendes', 'peage', 'parking',
                'lavage', 'transport', 'formation_chauffeur', 'autre'
            ]);
            $table->string('expense_type', 100); // Type spécifique (ex: "Vidange moteur", "Essence")
            $table->string('expense_subtype', 100)->nullable(); // Sous-type (ex: "5W30", "Sans plomb")

            // Montants en DZD avec TVA
            $table->decimal('amount_ht', 15, 2); // Montant HT
            $table->decimal('tva_rate', 5, 2)->default(19.00); // Taux TVA (19% en Algérie)
            $table->decimal('tva_amount', 15, 2)->storedAs('(amount_ht * tva_rate / 100)'); // TVA calculée
            $table->decimal('total_ttc', 15, 2)->storedAs('(amount_ht + (amount_ht * tva_rate / 100))'); // Total TTC

            // Documents fiscaux DZ
            $table->string('invoice_number', 100)->nullable()->index();
            $table->date('invoice_date')->nullable();
            $table->string('receipt_number', 100)->nullable();
            $table->boolean('fiscal_receipt')->default(false); // Reçu fiscal conforme DZ

            // Contexte véhicule (pour carburant, maintenance, etc.)
            $table->integer('odometer_reading')->nullable()->index(); // Kilométrage
            $table->decimal('fuel_quantity', 10, 3)->nullable(); // Quantité carburant (litres)
            $table->decimal('fuel_price_per_liter', 8, 3)->nullable(); // Prix au litre
            $table->string('fuel_type', 50)->nullable(); // Type carburant (essence, gasoil)

            // Géolocalisation
            $table->point('expense_location')->nullable(); // Coordonnées GPS
            $table->string('expense_city', 100)->nullable()->index();
            $table->string('expense_wilaya', 50)->nullable()->index();

            // Workflow d'approbation
            $table->boolean('needs_approval')->default(false);
            $table->boolean('approved')->default(false)->index();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_comments')->nullable();

            // Paiement
            $table->string('payment_status', 50)->default('pending'); // pending, paid, rejected
            $table->string('payment_method', 50)->nullable(); // virement, cheque, especes, carte
            $table->date('payment_date')->nullable();
            $table->string('payment_reference', 100)->nullable();

            // Traçabilité maximale
            $table->unsignedBigInteger('recorded_by'); // Qui a enregistré la dépense
            $table->date('expense_date')->index(); // Date de la dépense
            $table->text('description'); // Description détaillée
            $table->text('internal_notes')->nullable(); // Notes internes

            // Métadonnées flexibles
            $table->json('tags')->default('[]'); // Tags pour classification
            $table->json('custom_fields')->default('{}'); // Champs personnalisés

            // Documents attachés
            $table->json('attachments')->default('[]'); // Factures, reçus, photos

            // Récurrence (pour abonnements, assurances, etc.)
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern', 50)->nullable(); // monthly, quarterly, yearly
            $table->date('next_due_date')->nullable();
            $table->unsignedBigInteger('parent_expense_id')->nullable(); // Pour les dépenses récurrentes

            // Validation et contrôle
            $table->boolean('requires_audit')->default(false);
            $table->boolean('audited')->default(false);
            $table->unsignedBigInteger('audited_by')->nullable();
            $table->timestamp('audited_at')->nullable();

            // Performance et analyse
            $table->decimal('budget_allocated', 15, 2)->nullable(); // Budget alloué
            $table->decimal('variance_percentage', 5, 2)->nullable(); // Écart budget vs réel

            $table->timestamps();
            $table->softDeletes();

            // Index pour performance et requêtes
            $table->index(['organization_id', 'expense_category']);
            $table->index(['organization_id', 'expense_date']);
            $table->index(['vehicle_id', 'expense_date']);
            $table->index(['supplier_id', 'expense_date']);
            $table->index(['expense_category', 'expense_date']);
            $table->index(['approved', 'expense_date']);
            $table->index(['payment_status', 'expense_date']);
            $table->index(['needs_approval', 'approved']);

            // Index pour reporting
            $table->index(['expense_wilaya', 'expense_date']);
            $table->index(['fuel_type', 'expense_date']);
            $table->index(['is_recurring', 'next_due_date']);

            // Contraintes foreign key
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('repair_request_id')->references('id')->on('repair_requests')->onDelete('set null');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('audited_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('parent_expense_id')->references('id')->on('vehicle_expenses')->onDelete('cascade');
        });

        // Contraintes business PostgreSQL
        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_amounts CHECK (
                amount_ht >= 0 AND
                tva_rate >= 0 AND
                tva_rate <= 100
            )
        ");

        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_fuel_data CHECK (
                (fuel_quantity IS NULL AND fuel_price_per_liter IS NULL) OR
                (fuel_quantity > 0 AND fuel_price_per_liter > 0)
            )
        ");

        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_approval_workflow CHECK (
                (NOT needs_approval) OR
                (needs_approval AND approved AND approved_by IS NOT NULL AND approved_at IS NOT NULL) OR
                (needs_approval AND NOT approved)
            )
        ");

        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_payment_data CHECK (
                (payment_status != 'paid') OR
                (payment_status = 'paid' AND payment_date IS NOT NULL)
            )
        ");

        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_recurring_data CHECK (
                (NOT is_recurring) OR
                (is_recurring AND recurrence_pattern IS NOT NULL)
            )
        ");

        DB::statement("
            ALTER TABLE vehicle_expenses
            ADD CONSTRAINT valid_expense_date CHECK (
                expense_date <= CURRENT_DATE
            )
        ");

        // Index géospatial si nécessaire
        DB::statement("CREATE INDEX vehicle_expenses_location_idx ON vehicle_expenses USING gist(expense_location)");

        // Index de recherche textuelle
        DB::statement("CREATE INDEX vehicle_expenses_search_idx ON vehicle_expenses USING gin(to_tsvector('french', description || ' ' || expense_type))");
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_expenses');
        DB::statement("DROP TYPE IF EXISTS expense_category_enum");
    }
};