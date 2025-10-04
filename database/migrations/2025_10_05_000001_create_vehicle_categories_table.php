<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 20);
            $table->text('description')->nullable();
            $table->string('color_code', 20)->default('#3B82F6');
            $table->string('icon', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'name'], 'unq_vehicle_categories_org_name');
            $table->unique(['organization_id', 'code'], 'unq_vehicle_categories_org_code');
            $table->index(['organization_id', 'is_active'], 'idx_vehicle_categories_org_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_categories');
    }
};
