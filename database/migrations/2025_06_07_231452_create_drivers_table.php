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
        Schema::create('drivers', function (Blueprint $table) {
        $table->id();
        // Lien optionnel vers un compte utilisateur système.
        $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null');

        $table->string('employee_number', 100)->nullable()->unique();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('photo_path', 512)->nullable();
        $table->date('birth_date')->nullable();
        $table->string('blood_type', 10)->nullable();
        $table->text('address')->nullable();
        $table->string('personal_phone', 50)->nullable();
        $table->string('personal_email', 255)->nullable();
        $table->string('license_number', 100)->nullable();
        $table->string('license_category', 50)->nullable();
        $table->date('license_issue_date')->nullable();
        $table->string('license_authority')->nullable();
        $table->date('recruitment_date')->nullable();
        $table->date('contract_end_date')->nullable();

        $table->foreignId('status_id')->nullable()->constrained('driver_statuses')->onDelete('set null');

        $table->string('emergency_contact_name')->nullable();
        $table->string('emergency_contact_phone', 50)->nullable();

        $table->timestamps();
        $table->softDeletes(); // Pour la suppression douce
    	});
      }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
