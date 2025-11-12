<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration pour enrichir la table vehicle_depots avec des fonctionnalités enterprise
     */
    public function up(): void
    {
        Schema::table('vehicle_depots', function (Blueprint $table) {
            // Nouvelles colonnes enterprise si elles n'existent pas
            if (!Schema::hasColumn('vehicle_depots', 'type')) {
                $table->string('type', 20)->default('main')->after('code');
                $table->index('type');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'status')) {
                $table->string('status', 20)->default('active')->after('type');
                $table->index('status');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'state_province')) {
                $table->string('state_province', 100)->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'country_code')) {
                $table->string('country_code', 2)->default('DZ')->after('postal_code');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'email')) {
                $table->string('email', 100)->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'timezone')) {
                $table->string('timezone', 50)->default('Africa/Algiers')->after('email');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'operating_hours')) {
                $table->json('operating_hours')->nullable()->after('timezone');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'manager_email')) {
                $table->string('manager_email', 100)->nullable()->after('manager_phone');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'current_occupancy')) {
                $table->integer('current_occupancy')->default(0)->after('current_count');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'utilization_rate')) {
                $table->decimal('utilization_rate', 5, 2)->default(0)->after('current_occupancy');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'coverage_radius_km')) {
                $table->decimal('coverage_radius_km', 10, 2)->nullable()->after('longitude');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'polygon_boundaries')) {
                $table->json('polygon_boundaries')->nullable()->after('coverage_radius_km');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'facilities')) {
                $table->json('facilities')->nullable()->comment('Équipements disponibles');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'services')) {
                $table->json('services')->nullable()->comment('Services offerts');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'certifications')) {
                $table->json('certifications')->nullable()->comment('Certifications ISO, etc');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'metadata')) {
                $table->json('metadata')->nullable()->comment('Données flexibles');
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'iot_config')) {
                $table->json('iot_config')->nullable()->comment('Configuration IoT/Sensors');
            }
            
            // Nouvelles colonnes booléennes pour les services
            if (!Schema::hasColumn('vehicle_depots', 'has_fuel_station')) {
                $table->boolean('has_fuel_station')->default(false);
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'has_wash_station')) {
                $table->boolean('has_wash_station')->default(false);
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'has_maintenance_facility')) {
                $table->boolean('has_maintenance_facility')->default(false);
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'has_charging_stations')) {
                $table->boolean('has_charging_stations')->default(false);
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'charging_stations_count')) {
                $table->integer('charging_stations_count')->default(0);
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'is_public')) {
                $table->boolean('is_public')->default(false)->comment('Accessible aux partenaires');
            }
            
            // Colonnes financières
            if (!Schema::hasColumn('vehicle_depots', 'monthly_cost')) {
                $table->decimal('monthly_cost', 12, 2)->nullable();
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'cost_currency')) {
                $table->string('cost_currency', 3)->default('DZD');
            }
            
            // Dates importantes
            if (!Schema::hasColumn('vehicle_depots', 'opened_at')) {
                $table->timestamp('opened_at')->nullable();
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'last_inspection_at')) {
                $table->timestamp('last_inspection_at')->nullable();
            }
            
            if (!Schema::hasColumn('vehicle_depots', 'next_inspection_at')) {
                $table->timestamp('next_inspection_at')->nullable();
            }
            
            // Index pour performance
            if (!Schema::hasIndex('vehicle_depots', 'vehicle_depots_organization_id_index')) {
                $table->index('organization_id');
            }
            
            if (!Schema::hasIndex('vehicle_depots', 'vehicle_depots_code_index')) {
                $table->index('code');
            }
            
            if (!Schema::hasIndex('vehicle_depots', 'vehicle_depots_is_active_index')) {
                $table->index('is_active');
            }
            
            // Index composés pour requêtes fréquentes
            if (!Schema::hasIndex('vehicle_depots', 'vehicle_depots_org_active_index')) {
                $table->index(['organization_id', 'is_active']);
            }
            
            if (!Schema::hasIndex('vehicle_depots', 'vehicle_depots_geo_index')) {
                $table->index(['latitude', 'longitude']);
            }
        });

        // Mise à jour des données existantes avec valeurs par défaut intelligentes
        $this->updateExistingData();
    }

    /**
     * Mise à jour des données existantes
     */
    private function updateExistingData(): void
    {
        // Définir le type par défaut pour les dépôts existants
        DB::table('vehicle_depots')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => 'main']);

        // Définir le statut par défaut
        DB::table('vehicle_depots')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'active']);

        // Calculer le taux d'utilisation pour les dépôts existants
        DB::statement("
            UPDATE vehicle_depots 
            SET utilization_rate = CASE 
                WHEN capacity IS NOT NULL AND capacity > 0 
                THEN ROUND((COALESCE(current_count, 0)::numeric / capacity::numeric) * 100, 2)
                ELSE 0 
            END
            WHERE utilization_rate = 0 OR utilization_rate IS NULL
        ");

        // Synchroniser current_occupancy avec current_count
        DB::statement("
            UPDATE vehicle_depots 
            SET current_occupancy = COALESCE(current_count, 0)
            WHERE current_occupancy = 0 OR current_occupancy IS NULL
        ");

        // Définir les horaires d'ouverture par défaut (8h-18h du lundi au samedi)
        $defaultHours = json_encode([
            'monday' => ['open' => '08:00', 'close' => '18:00'],
            'tuesday' => ['open' => '08:00', 'close' => '18:00'],
            'wednesday' => ['open' => '08:00', 'close' => '18:00'],
            'thursday' => ['open' => '08:00', 'close' => '18:00'],
            'friday' => ['open' => '08:00', 'close' => '18:00'],
            'saturday' => ['open' => '08:00', 'close' => '13:00'],
            'sunday' => ['open' => null, 'close' => null]
        ]);

        DB::table('vehicle_depots')
            ->whereNull('operating_hours')
            ->update(['operating_hours' => $defaultHours]);

        // Générer des codes uniques pour les dépôts qui n'en ont pas
        $depotsWithoutCode = DB::table('vehicle_depots')
            ->whereNull('code')
            ->orWhere('code', '')
            ->get();

        foreach ($depotsWithoutCode as $depot) {
            $prefix = strtoupper(substr($depot->city ?? 'DEP', 0, 3));
            $timestamp = now()->format('ymd');
            $random = strtoupper(substr(md5(uniqid()), 0, 4));
            $code = "D-{$prefix}-{$timestamp}-{$random}";
            
            DB::table('vehicle_depots')
                ->where('id', $depot->id)
                ->update(['code' => $code]);
        }
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('vehicle_depots', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées (dans l'ordre inverse)
            $columnsToRemove = [
                'next_inspection_at',
                'last_inspection_at',
                'opened_at',
                'cost_currency',
                'monthly_cost',
                'is_public',
                'charging_stations_count',
                'has_charging_stations',
                'has_maintenance_facility',
                'has_wash_station',
                'has_fuel_station',
                'iot_config',
                'metadata',
                'certifications',
                'services',
                'facilities',
                'polygon_boundaries',
                'coverage_radius_km',
                'utilization_rate',
                'current_occupancy',
                'manager_email',
                'operating_hours',
                'timezone',
                'email',
                'country_code',
                'state_province',
                'status',
                'type'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('vehicle_depots', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
