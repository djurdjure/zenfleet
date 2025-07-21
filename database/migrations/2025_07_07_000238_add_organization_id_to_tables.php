<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        $tables = ['users', 'vehicles', 'drivers', 'assignments', 'maintenance_plans', 'maintenance_logs', 'vehicle_handover_forms'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            });
        }
    }
    public function down(): void {
        // ... (logique pour supprimer les colonnes) ...
    }
};
