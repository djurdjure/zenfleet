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
        Schema::table('vehicles', function (Blueprint $table) {
            // Rendre les champs optionnels selon les exigences
            $table->string('brand')->nullable()->change();
            $table->string('model')->nullable()->change();
            $table->unsignedBigInteger('vehicle_type_id')->nullable()->change();
            $table->unsignedBigInteger('fuel_type_id')->nullable()->change();
            $table->unsignedBigInteger('transmission_type_id')->nullable()->change();
            $table->unsignedBigInteger('status_id')->nullable()->change();
            $table->integer('initial_mileage')->nullable()->change();
            $table->integer('current_mileage')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Restaurer les contraintes NOT NULL (attention: peut échouer si des NULL existent)
            $table->string('brand')->nullable(false)->change();
            $table->string('model')->nullable(false)->change();
            $table->unsignedBigInteger('vehicle_type_id')->nullable(false)->change();
            $table->unsignedBigInteger('fuel_type_id')->nullable(false)->change();
            $table->unsignedBigInteger('transmission_type_id')->nullable(false)->change();
            $table->unsignedBigInteger('status_id')->nullable(false)->change();
            $table->integer('initial_mileage')->nullable(false)->change();
            $table->integer('current_mileage')->nullable(false)->change();
            $table->string('status')->nullable(false)->change();
        });
    }
};
