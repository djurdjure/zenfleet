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
    Schema::create('validation_levels', function (Blueprint $table) {
        $table->id(); // Ou $table->serial('id')->primary(); pour SERIAL
        $table->smallInteger('level_number')->unique();
        $table->string('name', 100);
        $table->text('description')->nullable();
        $table->timestamps(); 
    });
  }




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_levels');
    }
};
