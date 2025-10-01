<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Créer les ENUMs requis pour PostgreSQL
        DB::statement("CREATE TYPE repair_priority_enum AS ENUM ('urgente', 'a_prevoir', 'non_urgente')");
        DB::statement("CREATE TYPE repair_status_enum AS ENUM ('en_attente', 'accord_initial', 'accordee', 'refusee', 'en_cours', 'terminee', 'annulee')");
        DB::statement("CREATE TYPE supervisor_decision_enum AS ENUM ('accepte', 'refuse')");
        DB::statement("CREATE TYPE manager_decision_enum AS ENUM ('valide', 'refuse')");

        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('requested_by'); // driver_id

            // Classification de la demande
            $table->enum('priority', ['urgente', 'a_prevoir', 'non_urgente'])->default('non_urgente');
            $table->enum('status', ['en_attente', 'accord_initial', 'accordee', 'refusee', 'en_cours', 'terminee', 'annulee'])->default('en_attente');

            // Détails de la demande
            $table->text('description');
            $table->string('location_description', 500)->nullable();
            $table->json('photos')->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();

            // Niveau 1: Validation Superviseur
            $table->enum('supervisor_decision', ['accepte', 'refuse'])->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->text('supervisor_comments')->nullable();
            $table->timestamp('supervisor_decided_at')->nullable();

            // Niveau 2: Validation Manager
            $table->enum('manager_decision', ['valide', 'refuse'])->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->text('manager_comments')->nullable();
            $table->timestamp('manager_decided_at')->nullable();

            // Exécution des travaux
            $table->unsignedBigInteger('assigned_supplier_id')->nullable();
            $table->timestamp('work_started_at')->nullable();
            $table->timestamp('work_completed_at')->nullable();

            // Métadonnées et audit
            $table->timestamp('requested_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->json('attachments')->nullable();
            $table->json('work_photos')->nullable(); // Photos pendant/après travaux
            $table->text('completion_notes')->nullable();
            $table->decimal('final_rating', 3, 2)->nullable(); // Note satisfaction 1-10

            // Timestamps standard
            $table->timestamps();
            $table->softDeletes();

            // Index pour performance
            $table->index(['organization_id', 'status']);
            $table->index(['vehicle_id', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['requested_at']);
            $table->index(['supervisor_id']);
            $table->index(['manager_id']);

            // Contraintes foreign key
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });

        // Contraintes business PostgreSQL
        DB::statement("
            ALTER TABLE repair_requests
            ADD CONSTRAINT valid_workflow CHECK (
                (status = 'accord_initial' AND supervisor_decision = 'accepte') OR
                (status = 'accordee' AND manager_decision = 'valide') OR
                (status = 'refusee' AND (supervisor_decision = 'refuse' OR manager_decision = 'refuse')) OR
                (status IN ('en_attente', 'en_cours', 'terminee', 'annulee'))
            )
        ");

        DB::statement("
            ALTER TABLE repair_requests
            ADD CONSTRAINT valid_completion CHECK (
                (status != 'terminee') OR
                (status = 'terminee' AND work_completed_at IS NOT NULL AND actual_cost IS NOT NULL)
            )
        ");

        DB::statement("
            ALTER TABLE repair_requests
            ADD CONSTRAINT valid_timing CHECK (
                (work_started_at IS NULL OR work_completed_at IS NULL OR work_started_at <= work_completed_at)
            )
        ");
    }

    public function down()
    {
        Schema::dropIfExists('repair_requests');
        DB::statement("DROP TYPE IF EXISTS repair_priority_enum");
        DB::statement("DROP TYPE IF EXISTS repair_status_enum");
        DB::statement("DROP TYPE IF EXISTS supervisor_decision_enum");
        DB::statement("DROP TYPE IF EXISTS manager_decision_enum");
    }
};