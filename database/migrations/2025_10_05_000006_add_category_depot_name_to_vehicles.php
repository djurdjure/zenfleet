<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('vehicle_name', 150)->nullable()->after('registration_plate');
            $table->foreignId('category_id')->nullable()->after('vehicle_type_id')->constrained('vehicle_categories')->nullOnDelete();
            $table->foreignId('depot_id')->nullable()->after('category_id')->constrained('vehicle_depots')->nullOnDelete();

            $table->index('vehicle_name', 'idx_vehicles_name');
            $table->index(['category_id', 'organization_id'], 'idx_vehicles_category_org');
            $table->index(['depot_id', 'organization_id'], 'idx_vehicles_depot_org');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['depot_id']);
            $table->dropIndex('idx_vehicles_name');
            $table->dropIndex('idx_vehicles_category_org');
            $table->dropIndex('idx_vehicles_depot_org');
            $table->dropColumn(['vehicle_name', 'category_id', 'depot_id']);
        });
    }
};
