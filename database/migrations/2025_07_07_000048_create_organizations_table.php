<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('organizations');
    }
};
