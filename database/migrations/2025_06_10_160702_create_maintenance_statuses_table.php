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
    
    Schema::create('maintenance_statuses', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100)->unique(); // Ex: Planifiée, En cours, Terminée, Annulée
});
    
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_statuses');
    }
};
