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
            // Vérifier et rendre nullable les champs qui existent
            $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'vehicles'");
            $columnNames = collect($columns)->pluck('column_name')->toArray();

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

        echo "✅ Colonnes vehicles rendues nullable\n";
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
