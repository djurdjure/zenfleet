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
    
    Schema::create('vehicle_handover_forms', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assignment_id')->unique()->constrained('assignments')->onDelete('cascade');
    $table->date('issue_date');
    $table->string('assignment_reason')->nullable();
    $table->unsignedBigInteger('current_mileage');
    $table->text('general_observations')->nullable();
    $table->text('additional_observations')->nullable();
    $table->string('signed_form_path', 512)->nullable();
    $table->boolean('is_latest_version')->default(true);
    $table->timestamps();
    $table->softDeletes();
	
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_handover_forms');
    }
};
