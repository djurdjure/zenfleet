<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_expenses')) {
            return;
        }

        if (Schema::hasColumn('vehicle_expenses', 'payment_due_date')) {
            return;
        }

        Schema::table('vehicle_expenses', function (Blueprint $table) {
            $table->date('payment_due_date')->nullable()->after('payment_date');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('vehicle_expenses')) {
            return;
        }

        if (!Schema::hasColumn('vehicle_expenses', 'payment_due_date')) {
            return;
        }

        Schema::table('vehicle_expenses', function (Blueprint $table) {
            $table->dropColumn('payment_due_date');
        });
    }
};
