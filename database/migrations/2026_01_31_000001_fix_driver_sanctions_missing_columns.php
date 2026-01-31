<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('driver_sanctions')) {
            return;
        }

        Schema::table('driver_sanctions', function (Blueprint $table) {
            if (!Schema::hasColumn('driver_sanctions', 'severity')) {
                $table->string('severity', 20)->default('medium')->after('sanction_type');
            }

            if (!Schema::hasColumn('driver_sanctions', 'duration_days')) {
                $table->integer('duration_days')->nullable()->after('sanction_date');
            }

            if (!Schema::hasColumn('driver_sanctions', 'status')) {
                $table->string('status', 20)->default('active')->after('attachment_path');
            }

            if (!Schema::hasColumn('driver_sanctions', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });

        if (Schema::hasColumn('driver_sanctions', 'severity')) {
            DB::table('driver_sanctions')->whereNull('severity')->update(['severity' => 'medium']);
        }

        if (Schema::hasColumn('driver_sanctions', 'status')) {
            DB::table('driver_sanctions')->whereNull('status')->update(['status' => 'active']);
        }

        $this->ensureIndexes();
        $this->ensureConstraints();
    }

    public function down(): void
    {
        if (!Schema::hasTable('driver_sanctions')) {
            return;
        }

        $this->dropConstraints();
        $this->dropIndexes();

        Schema::table('driver_sanctions', function (Blueprint $table) {
            if (Schema::hasColumn('driver_sanctions', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('driver_sanctions', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('driver_sanctions', 'duration_days')) {
                $table->dropColumn('duration_days');
            }

            if (Schema::hasColumn('driver_sanctions', 'severity')) {
                $table->dropColumn('severity');
            }
        });
    }

    private function ensureIndexes(): void
    {
        if ($this->indexExists('driver_sanctions', 'idx_sanctions_severity') === false
            && Schema::hasColumn('driver_sanctions', 'severity')) {
            Schema::table('driver_sanctions', function (Blueprint $table) {
                $table->index('severity', 'idx_sanctions_severity');
            });
        }

        if ($this->indexExists('driver_sanctions', 'idx_sanctions_status') === false
            && Schema::hasColumn('driver_sanctions', 'status')) {
            Schema::table('driver_sanctions', function (Blueprint $table) {
                $table->index('status', 'idx_sanctions_status');
            });
        }

        if ($this->indexExists('driver_sanctions', 'idx_sanctions_org_status') === false
            && Schema::hasColumn('driver_sanctions', 'status')) {
            Schema::table('driver_sanctions', function (Blueprint $table) {
                $table->index(['organization_id', 'status'], 'idx_sanctions_org_status');
            });
        }
    }

    private function dropIndexes(): void
    {
        if ($this->indexExists('driver_sanctions', 'idx_sanctions_severity')) {
            Schema::table('driver_sanctions', function (Blueprint $table) {
                $table->dropIndex('idx_sanctions_severity');
            });
        }

        if ($this->indexExists('driver_sanctions', 'idx_sanctions_status')) {
            Schema::table('driver_sanctions', function (Blueprint $table) {
                $table->dropIndex('idx_sanctions_status');
            });
        }

        if ($this->indexExists('driver_sanctions', 'idx_sanctions_org_status')) {
            Schema::table('driver_sanctions', function (Blueprint $table) {
                $table->dropIndex('idx_sanctions_org_status');
            });
        }
    }

    private function ensureConstraints(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        if (Schema::hasColumn('driver_sanctions', 'severity')) {
            DB::statement("DO $$ BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint WHERE conname = 'driver_sanctions_severity_check'
                ) THEN
                    ALTER TABLE driver_sanctions
                    ADD CONSTRAINT driver_sanctions_severity_check
                    CHECK (severity IN ('low', 'medium', 'high', 'critical'));
                END IF;
            END $$;");
        }

        if (Schema::hasColumn('driver_sanctions', 'status')) {
            DB::statement("DO $$ BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint WHERE conname = 'driver_sanctions_status_check'
                ) THEN
                    ALTER TABLE driver_sanctions
                    ADD CONSTRAINT driver_sanctions_status_check
                    CHECK (status IN ('active', 'appealed', 'cancelled', 'archived'));
                END IF;
            END $$;");
        }
    }

    private function dropConstraints(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TABLE driver_sanctions DROP CONSTRAINT IF EXISTS driver_sanctions_severity_check");
        DB::statement("ALTER TABLE driver_sanctions DROP CONSTRAINT IF EXISTS driver_sanctions_status_check");
    }

    private function indexExists(string $table, string $index): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            return !empty(DB::select(
                'SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ? LIMIT 1',
                [$table, $index]
            ));
        }

        if ($driver === 'mysql') {
            return !empty(DB::select(
                'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
                [$table, $index]
            ));
        }

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$table}')");
            foreach ($indexes as $row) {
                if (($row->name ?? null) === $index) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }
};
