<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('supervisor_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->index(['supervisor_id', 'organization_id'], 'idx_drivers_supervisor_org');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropIndex('idx_drivers_supervisor_org');
            $table->dropColumn('supervisor_id');
        });
    }
};
