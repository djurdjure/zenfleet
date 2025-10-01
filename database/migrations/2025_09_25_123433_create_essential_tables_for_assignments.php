<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Create Essential Tables for Assignment Module
     */
    public function up(): void
    {
        // 1. Table vehicle_types (si pas existante)
        if (!Schema::hasTable('vehicle_types')) {
            Schema::create('vehicle_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
            echo "✅ Table vehicle_types créée\n";
        }

        // 2. Table vehicle_statuses (si pas existante)
        if (!Schema::hasTable('vehicle_statuses')) {
            Schema::create('vehicle_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('color_code')->default('#6b7280');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
            echo "✅ Table vehicle_statuses créée\n";
        }

        // 3. Table fuel_types (si pas existante)
        if (!Schema::hasTable('fuel_types')) {
            Schema::create('fuel_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
            echo "✅ Table fuel_types créée\n";
        }

        // 4. Table transmission_types (si pas existante)
        if (!Schema::hasTable('transmission_types')) {
            Schema::create('transmission_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
            echo "✅ Table transmission_types créée\n";
        }

        // 5. Table vehicles (principale)
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->onDelete('cascade');
                $table->string('registration_plate')->unique();
                $table->string('vin')->nullable()->unique();
                $table->string('brand');
                $table->string('model');
                $table->string('color')->nullable();
                $table->foreignId('vehicle_type_id')->constrained();
                $table->foreignId('fuel_type_id')->constrained();
                $table->foreignId('transmission_type_id')->constrained();
                $table->foreignId('status_id')->constrained('vehicle_statuses');
                $table->integer('manufacturing_year')->nullable();
                $table->date('acquisition_date')->nullable();
                $table->decimal('purchase_price', 12, 2)->nullable();
                $table->decimal('current_value', 12, 2)->nullable();
                $table->integer('initial_mileage')->default(0);
                $table->integer('current_mileage')->default(0);
                $table->integer('engine_displacement_cc')->nullable();
                $table->integer('power_hp')->nullable();
                $table->integer('seats')->nullable();
                $table->text('notes')->nullable();
                $table->string('photo')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
                $table->softDeletes();

                // Index pour performance
                $table->index(['organization_id', 'status', 'deleted_at']);
                $table->index(['registration_plate', 'deleted_at']);
            });
            echo "✅ Table vehicles créée\n";
        }

        // 6. Table drivers
        if (!Schema::hasTable('drivers')) {
            Schema::create('drivers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->nullable();
                $table->string('personal_phone')->nullable();
                $table->string('emergency_contact')->nullable();
                $table->string('emergency_phone')->nullable();
                $table->string('driver_license_number')->unique();
                $table->date('driver_license_expiry_date')->nullable();
                $table->string('driver_license_category')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('postal_code')->nullable();
                $table->date('hire_date')->nullable();
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->string('photo')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Index pour performance
                $table->index(['organization_id', 'status', 'deleted_at']);
                $table->index(['driver_license_number', 'deleted_at']);
            });
            echo "✅ Table drivers créée\n";
        }

        // 7. Table assignments (principale pour le module)
        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->onDelete('cascade');
                $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
                $table->foreignId('driver_id')->constrained()->onDelete('cascade');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->dateTime('start_datetime');
                $table->dateTime('end_datetime')->nullable();
                $table->integer('start_mileage');
                $table->integer('end_mileage')->nullable();
                $table->string('reason')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('active'); // active, completed, cancelled, scheduled
                $table->timestamps();
                $table->softDeletes();

                // Index pour performance enterprise
                $table->index(['organization_id', 'status', 'deleted_at']);
                $table->index(['vehicle_id', 'start_datetime', 'end_datetime']);
                $table->index(['driver_id', 'start_datetime', 'end_datetime']);
                $table->index(['start_datetime', 'end_datetime']);
            });
            echo "✅ Table assignments créée\n";
        }

        echo "🚀 Toutes les tables essentielles créées - Enterprise ready\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('transmission_types');
        Schema::dropIfExists('fuel_types');
        Schema::dropIfExists('vehicle_statuses');
        Schema::dropIfExists('vehicle_types');
    }
};
