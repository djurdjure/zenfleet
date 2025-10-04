<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_depots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('code', 30);
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('wilaya', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('manager_name', 150)->nullable();
            $table->string('manager_phone', 50)->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('current_count')->default(0);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'name'], 'unq_vehicle_depots_org_name');
            $table->unique(['organization_id', 'code'], 'unq_vehicle_depots_org_code');
            $table->index(['organization_id', 'is_active'], 'idx_vehicle_depots_org_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_depots');
    }
};
