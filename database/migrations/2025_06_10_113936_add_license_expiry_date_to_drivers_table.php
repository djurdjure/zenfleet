<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('drivers', function (Blueprint $table) {
            $table->date('license_expiry_date')->nullable()->after('license_authority');
        });
    }
    public function down(): void {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('license_expiry_date');
        });
    }
};
