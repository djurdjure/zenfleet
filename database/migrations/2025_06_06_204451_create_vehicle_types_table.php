<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            // Pas de timestamps pour cette table de référence simple
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_types');
    }
};
