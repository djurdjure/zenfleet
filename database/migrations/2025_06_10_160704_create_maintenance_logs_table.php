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
    
    Schema::create('maintenance_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
    $table->foreignId('maintenance_plan_id')->nullable()->constrained('maintenance_plans')->onDelete('set null');
    $table->foreignId('maintenance_type_id')->constrained('maintenance_types');
    $table->foreignId('maintenance_status_id')->constrained('maintenance_statuses');
    $table->date('performed_on_date');
    $table->bigInteger('performed_at_mileage');
    $table->decimal('cost', 12, 2)->nullable();
    // La table `suppliers` n'existant pas encore, nous laissons cette clé en commentaire.
    // $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
    $table->text('details')->nullable();
    $table->string('performed_by')->nullable();
    $table->softDeletes();
    $table->timestamps();
});
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
