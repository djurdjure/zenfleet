<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('drivers')) {
            return;
        }

        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship', 100)->nullable();
            }

            if (!Schema::hasColumn('drivers', 'notes')) {
                $table->text('notes')->nullable();
            }

            if (!Schema::hasColumn('drivers', 'full_address')) {
                $table->text('full_address')->nullable();
            }
        });

        if (!Schema::hasColumn('drivers', 'license_categories')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->json('license_categories')
                    ->nullable()
                    ->after('license_number')
                    ->comment('Categories de permis (JSON array: ["B", "C", "D", etc.])');
            });
        }

        $sourceColumn = null;
        if (Schema::hasColumn('drivers', 'license_category_old')) {
            $sourceColumn = 'license_category_old';
        } elseif (Schema::hasColumn('drivers', 'license_category')) {
            $sourceColumn = 'license_category';
        }

        if ($sourceColumn) {
            $drivers = DB::table('drivers')->whereNotNull($sourceColumn)->get();
            foreach ($drivers as $driver) {
                DB::table('drivers')
                    ->where('id', $driver->id)
                    ->update([
                        'license_categories' => json_encode([$driver->$sourceColumn]),
                    ]);
            }

            if (DB::getDriverName() !== 'sqlite') {
                Schema::table('drivers', function (Blueprint $table) use ($sourceColumn) {
                    if (Schema::hasColumn('drivers', $sourceColumn)) {
                        $table->dropColumn($sourceColumn);
                    }
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('drivers')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (Schema::hasColumn('drivers', 'license_categories')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->dropColumn('license_categories');
            });
        }
    }
};
