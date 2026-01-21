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
        if (!Schema::hasTable('vehicles')) {
            return;
        }

        $columnNames = Schema::getColumnListing('vehicles');

        Schema::table('vehicles', function (Blueprint $table) use ($columnNames) {
            // Rendre nullable les champs présents.
            // Rendre les champs optionnels selon les exigences
            if (in_array('brand', $columnNames)) {
                $table->string('brand')->nullable()->change();
            }
            if (in_array('model', $columnNames)) {
                $table->string('model')->nullable()->change();
            }
            if (in_array('vehicle_type_id', $columnNames)) {
                $table->unsignedBigInteger('vehicle_type_id')->nullable()->change();
            }
            if (in_array('fuel_type_id', $columnNames)) {
                $table->unsignedBigInteger('fuel_type_id')->nullable()->change();
            }
            if (in_array('transmission_type_id', $columnNames)) {
                $table->unsignedBigInteger('transmission_type_id')->nullable()->change();
            }
            if (in_array('status_id', $columnNames)) {
                $table->unsignedBigInteger('status_id')->nullable()->change();
            }
            if (in_array('initial_mileage', $columnNames)) {
                $table->integer('initial_mileage')->nullable()->change();
            }
            if (in_array('current_mileage', $columnNames)) {
                $table->integer('current_mileage')->nullable()->change();
            }
            // Note: La colonne 'status' n'existe pas dans vehicles (utilise status_id à la place)
            if (in_array('status', $columnNames)) {
                $table->string('status')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('vehicles')) {
            return;
        }

        $columnNames = Schema::getColumnListing('vehicles');

        Schema::table('vehicles', function (Blueprint $table) use ($columnNames) {
            // Restaurer les contraintes NOT NULL (attention: peut échouer si des NULL existent)
            if (in_array('brand', $columnNames)) {
                $table->string('brand')->nullable(false)->change();
            }
            if (in_array('model', $columnNames)) {
                $table->string('model')->nullable(false)->change();
            }
            if (in_array('vehicle_type_id', $columnNames)) {
                $table->unsignedBigInteger('vehicle_type_id')->nullable(false)->change();
            }
            if (in_array('fuel_type_id', $columnNames)) {
                $table->unsignedBigInteger('fuel_type_id')->nullable(false)->change();
            }
            if (in_array('transmission_type_id', $columnNames)) {
                $table->unsignedBigInteger('transmission_type_id')->nullable(false)->change();
            }
            if (in_array('status_id', $columnNames)) {
                $table->unsignedBigInteger('status_id')->nullable(false)->change();
            }
            if (in_array('initial_mileage', $columnNames)) {
                $table->integer('initial_mileage')->nullable(false)->change();
            }
            if (in_array('current_mileage', $columnNames)) {
                $table->integer('current_mileage')->nullable(false)->change();
            }
            if (in_array('status', $columnNames)) {
                $table->string('status')->nullable(false)->change();
            }
        });
    }
};
