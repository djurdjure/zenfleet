<?php

// CORRECTION : Remplacement des points par des anti-slashs dans les déclarations 'use'
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // MODULE: GESTION DES COÛTS
        Schema::create('expense_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->foreignId('expense_type_id')->constrained('expense_types')->onDelete('restrict');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->text('description')->nullable();
            $table->string('receipt_path', 512)->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // MODULE: GESTION DU CARBURANT
        Schema::create('fuel_refills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->timestamp('refill_date');
            $table->decimal('quantity_liters', 8, 2);
            $table->decimal('price_per_liter', 8, 3); // Supporte 3 décimales pour plus de précision
            $table->decimal('total_cost', 10, 2);
            $table->unsignedBigInteger('mileage_at_refill');
            $table->boolean('full_tank')->default(true);
            $table->string('station_name')->nullable();
            $table->timestamps();
        });

        // MODULE: GESTION DES INCIDENTS
        Schema::create('incident_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
        });

        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->timestamp('incident_date');
            $table->string('type'); // Ex: 'Accident', 'Panne', 'Vol', etc.
            $table->string('severity'); // Ex: 'Faible', 'Moyenne', 'Élevée', 'Critique'
            $table->text('location')->nullable();
            $table->text('description');
            $table->boolean('third_party_involved')->default(false);
            $table->string('police_report_number')->nullable();
            $table->string('insurance_claim_number')->nullable();
            $table->foreignId('incident_status_id')->constrained('incident_statuses');
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // MODULE: GESTION DOCUMENTAIRE (Polymorphique)
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->morphs('documentable'); // Crée `documentable_id` (BIGINT) et `documentable_type` (VARCHAR)
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->string('title');
            $table->string('file_path', 512);
            $table->unsignedBigInteger('file_size'); // en octets
            $table->string('mime_type', 100);
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // On supprime les tables dans l'ordre inverse de leur création pour respecter les contraintes
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('incident_statuses');
        Schema::dropIfExists('fuel_refills');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_types');
    }
};
