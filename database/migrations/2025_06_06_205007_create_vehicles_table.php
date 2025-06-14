<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('vehicles', function (Blueprint $table) {
        $table->id();
        $table->string('registration_plate', 50)->unique();
        $table->string('vin', 17)->unique()->nullable(); // VIN est nullable dès le départ
        $table->string('brand', 100)->nullable();
        $table->string('model', 100)->nullable();
        $table->string('color', 50)->nullable();

        $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->onDelete('set null');
        $table->foreignId('fuel_type_id')->nullable()->constrained('fuel_types')->onDelete('set null');
        $table->foreignId('transmission_type_id')->nullable()->constrained('transmission_types')->onDelete('set null');
        $table->foreignId('status_id')->nullable()->constrained('vehicle_statuses')->onDelete('set null');

        $table->smallInteger('manufacturing_year')->nullable();
        $table->date('acquisition_date')->nullable(); // CHAMP AJOUTÉ ICI
        $table->decimal('purchase_price', 12, 2)->nullable(); // CHAMP AJOUTÉ ICI
        $table->decimal('current_value', 12, 2)->nullable(); // CHAMP AJOUTÉ ICI

        $table->unsignedBigInteger('initial_mileage')->default(0);
        $table->unsignedBigInteger('current_mileage')->default(0);
        $table->integer('engine_displacement_cc')->nullable();
        $table->integer('power_hp')->nullable();
        $table->smallInteger('seats')->nullable();

        $table->text('status_reason')->nullable();
        $table->text('notes')->nullable();

        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
