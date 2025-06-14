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
    Schema::create('user_validation_levels', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('validation_level_id')->constrained('validation_levels')->onDelete('cascade');
        $table->primary(['user_id', 'validation_level_id']);
        // Pas de timestamps ici généralement.
    });
  }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_validation_levels');
    }
};
