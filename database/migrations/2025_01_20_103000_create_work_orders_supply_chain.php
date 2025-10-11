<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🔧 ZENFLEET WORK ORDERS & SUPPLY CHAIN ENTERPRISE
 *
 * Système de maintenance et chaîne d'approvisionnement ultra-professionnel:
 * - Work orders avec workflow avancé
 * - Gestion fournisseurs et pièces
 * - Système de garanties intelligent
 * - Intégration IoT et télématique
 *
 * @version 1.0 Enterprise
 * @author ZenFleet Architecture Team
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier que les tables essentielles existent
        if (!Schema::hasTable('vehicles')) {
            echo "⚠️  Table vehicles n'existe pas encore, skip work orders system\n";
            return;
        }

        // ===== SYSTÈME FOURNISSEURS ÉVOLUÉ =====
        $this->createAdvancedSuppliersSystem();

        // ===== CATALOGUE PIÈCES INTELLIGENT =====
        $this->createIntelligentPartsSystem();

        // ===== WORK ORDERS ENTERPRISE =====
        $this->createWorkOrdersSystem();

        // ===== SYSTÈME GARANTIES =====
        $this->createWarrantySystem();

        // ===== INTÉGRATION IOT =====
        $this->createIoTIntegration();

        // ===== OPTIMISATIONS PERFORMANCE =====
        $this->addPerformanceOptimizations();

        echo "✅ Système work orders & supply chain enterprise créé\n";
    }

    /**
     * Système fournisseurs évolué avec rating et contracts
     */
    private function createAdvancedSuppliersSystem(): void
    {
        // Extension de la table suppliers existante (si elle existe)
        if (!Schema::hasTable('suppliers')) {
            return; // Skip si la table n'existe pas
        }

        Schema::table('suppliers', function (Blueprint $table) {
            // Classification fournisseur
            $table->string('supplier_category')->default('parts'); // parts, service, fuel, insurance
            $table->integer('reliability_score')->default(50); // 0-100
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('total_orders')->default(0);

            // Informations financières
            $table->string('payment_terms')->nullable(); // "30 jours", "comptant"
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->decimal('current_balance', 12, 2)->default(0.00);

            // Certifications et compliance
            $table->json('certifications')->nullable();
            $table->boolean('is_certified_oem')->default(false);
            $table->boolean('is_preferred_vendor')->default(false);

            // Géolocalisation et livraison
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('average_delivery_days')->nullable();
            $table->json('delivery_zones')->nullable();

            // Métadonnées business
            $table->json('business_hours')->nullable();
            $table->json('emergency_contact')->nullable();
            $table->text('notes')->nullable();

            $table->index(['supplier_category', 'is_preferred_vendor']);
            $table->index(['reliability_score', 'average_rating']);
        });

        // Table contracts fournisseurs
        Schema::create('supplier_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');

            $table->string('contract_number')->unique();
            $table->string('contract_type'); // maintenance, parts, service, fuel
            $table->text('description');

            // Période du contrat
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('auto_renewal')->default(false);
            $table->integer('renewal_notice_days')->default(30);

            // Conditions financières
            $table->decimal('contract_value', 15, 2);
            $table->string('currency', 3)->default('DZD');
            $table->json('pricing_terms')->nullable();
            $table->json('sla_terms')->nullable(); // Service Level Agreement

            // Documents et compliance
            $table->json('document_paths')->nullable();
            $table->string('status')->default('active'); // draft, active, expired, terminated

            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['supplier_id', 'end_date']);
        });

        echo "✅ Système fournisseurs évolué créé\n";
    }

    /**
     * Catalogue pièces intelligent avec compatibilité
     */
    private function createIntelligentPartsSystem(): void
    {
        // Création de la table parts si elle n'existe pas
        if (!Schema::hasTable('parts')) {
            Schema::create('parts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
                $table->string('part_number')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('unit_price', 10, 2)->default(0.00);
                $table->integer('current_stock')->default(0);
                $table->string('unit_of_measure')->default('piece'); // piece, liter, kg, etc.
                $table->timestamps();

                $table->index(['organization_id', 'part_number']);
            });
        }

        // Extension de la table parts existante
        if (Schema::hasTable('parts')) {
            Schema::table('parts', function (Blueprint $table) {
                // Classification technique
                $table->string('part_category')->default('general'); // engine, transmission, brake, electrical, body
                $table->string('part_subcategory')->nullable();
                $table->boolean('is_oem')->default(false);
                $table->boolean('is_critical')->default(false);

                // Spécifications techniques
                $table->json('technical_specs')->nullable();
                $table->decimal('weight', 8, 3)->nullable(); // en kg
                $table->json('dimensions')->nullable(); // L x W x H
                $table->string('material')->nullable();

                // Gestion stock intelligent
                $table->integer('reorder_point')->default(10);
                $table->integer('max_stock_level')->default(100);
                $table->integer('safety_stock')->default(5);
                $table->decimal('carrying_cost_rate', 5, 4)->default(0.2500); // 25% par défaut

                // Pricing et coûts
                $table->decimal('average_cost', 10, 2)->default(0.00);
                $table->decimal('last_purchase_price', 10, 2)->nullable();
                $table->date('last_purchase_date')->nullable();

                // Garantie et maintenance
                $table->integer('warranty_months')->nullable();
                $table->integer('expected_life_months')->nullable();
                $table->json('maintenance_schedule')->nullable();

                $table->index(['part_category', 'is_critical']);
                $table->index(['reorder_point', 'current_stock']);
            });
        }

        // Table compatibilité véhicules-pièces
        Schema::create('vehicle_part_compatibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');

            // Compatibilité par marque/modèle
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_year_from', 4)->nullable();
            $table->string('vehicle_year_to', 4)->nullable();
            $table->string('engine_type')->nullable();

            // Compatibilité spécifique véhicule
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('cascade');

            // Métadonnées installation
            $table->decimal('installation_time_hours', 4, 2)->nullable();
            $table->text('installation_notes')->nullable();
            $table->json('required_tools')->nullable();
            $table->string('skill_level')->default('intermediate'); // basic, intermediate, advanced, expert

            $table->timestamps();

            $table->index(['vehicle_make', 'vehicle_model']);
            $table->index(['part_id', 'vehicle_id']);
        });

        // Table mouvements de stock
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');

            $table->string('movement_type'); // in, out, adjustment, transfer
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_value', 12, 2)->nullable();

            // Référence document
            $table->string('reference_type')->nullable(); // purchase_order, work_order, adjustment
            $table->bigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();

            // Détails mouvement
            $table->text('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('movement_date')->useCurrent();

            // Localisation
            $table->string('location_from')->nullable();
            $table->string('location_to')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'movement_date']);
            $table->index(['part_id', 'movement_type']);
        });

        echo "✅ Catalogue pièces intelligent créé\n";
    }

    /**
     * Système work orders enterprise avec workflow
     */
    private function createWorkOrdersSystem(): void
    {
        // Table work orders principale
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('work_order_number')->unique();

            // Références
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('maintenance_plan_id')->nullable()->constrained('maintenance_plans')->onDelete('set null');
            $table->foreignId('assigned_driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->foreignId('technician_id')->nullable()->constrained('users')->onDelete('set null');

            // Classification
            $table->string('work_order_type'); // preventive, corrective, emergency, inspection
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('category'); // mechanical, electrical, body, safety, compliance

            // Planning
            $table->timestamp('scheduled_start')->nullable();
            $table->timestamp('scheduled_end')->nullable();
            $table->timestamp('actual_start')->nullable();
            $table->timestamp('actual_end')->nullable();
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->decimal('actual_hours', 6, 2)->nullable();

            // Description et diagnostic
            $table->text('description');
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('work_performed')->nullable();
            $table->text('technician_notes')->nullable();

            // Coûts
            $table->decimal('estimated_cost', 12, 2)->default(0.00);
            $table->decimal('actual_cost', 12, 2)->default(0.00);
            $table->decimal('labor_cost', 10, 2)->default(0.00);
            $table->decimal('parts_cost', 10, 2)->default(0.00);
            $table->decimal('external_cost', 10, 2)->default(0.00); // sous-traitance

            // État et workflow
            $table->string('status')->default('draft'); // draft, scheduled, in_progress, completed, cancelled, on_hold
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            // Kilométrage et mesures
            $table->bigInteger('odometer_start')->nullable();
            $table->bigInteger('odometer_end')->nullable();
            $table->json('measurements_before')->nullable();
            $table->json('measurements_after')->nullable();

            // Garantie et follow-up
            $table->boolean('is_warranty_work')->default(false);
            $table->date('warranty_expiry')->nullable();
            $table->boolean('requires_followup')->default(false);
            $table->date('followup_date')->nullable();

            // Compliance et certification
            $table->json('compliance_checks')->nullable();
            $table->boolean('passed_inspection')->nullable();
            $table->text('inspection_notes')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['vehicle_id', 'scheduled_start']);
            $table->index(['work_order_type', 'priority']);
            $table->index(['technician_id', 'status']);
        });

        // Table pièces utilisées dans work orders
        Schema::create('work_order_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');

            $table->integer('quantity_requested');
            $table->integer('quantity_used');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 12, 2);

            // État de la pièce
            $table->string('condition_before')->nullable(); // new, good, fair, poor, failed
            $table->string('condition_after')->nullable();
            $table->boolean('is_warranty_replacement')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['work_order_id', 'part_id']);
        });

        // Table checklist pour work orders
        Schema::create('work_order_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');

            $table->string('checklist_category'); // safety, quality, compliance, cleanup
            $table->string('item_description');
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_required')->default(true);
            $table->foreignId('completed_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['work_order_id', 'checklist_category']);
        });

        echo "✅ Système work orders enterprise créé\n";
    }

    /**
     * Système de garanties intelligent
     */
    private function createWarrantySystem(): void
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');

            // Type de garantie
            $table->string('warranty_type'); // vehicle, part, service, extended
            $table->morphs('warrantable'); // vehicle_id ou part_id

            // Détails garantie
            $table->string('warranty_provider'); // manufacturer, dealer, insurance
            $table->string('warranty_number')->nullable();
            $table->text('coverage_description');
            $table->json('coverage_details')->nullable(); // ce qui est couvert/exclu

            // Période de garantie
            $table->date('start_date');
            $table->date('end_date');
            $table->bigInteger('mileage_limit')->nullable();
            $table->bigInteger('mileage_at_start')->nullable();

            // Conditions
            $table->json('terms_conditions')->nullable();
            $table->json('maintenance_requirements')->nullable(); // maintenance obligatoire
            $table->decimal('deductible_amount', 10, 2)->default(0.00);

            // Contact et documents
            $table->json('provider_contact')->nullable();
            $table->json('document_paths')->nullable();

            // État
            $table->string('status')->default('active'); // active, expired, claimed, voided
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'warranty_type']);
            $table->index(['end_date', 'status']);
        });

        // Table réclamations garantie
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_id')->constrained('warranties')->onDelete('cascade');
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->onDelete('set null');

            $table->string('claim_number')->unique();
            $table->date('claim_date');
            $table->text('problem_description');
            $table->decimal('claim_amount', 12, 2);

            // Statut réclamation
            $table->string('status')->default('submitted'); // submitted, approved, rejected, paid
            $table->text('provider_response')->nullable();
            $table->date('response_date')->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();

            // Documents
            $table->json('supporting_documents')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['warranty_id', 'status']);
            $table->index(['claim_date', 'status']);
        });

        echo "✅ Système de garanties créé\n";
    }

    /**
     * Intégration IoT et télématique
     */
    private function createIoTIntegration(): void
    {
        // Table capteurs véhicules
        Schema::create('vehicle_sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');

            $table->string('sensor_type'); // gps, fuel, temperature, pressure, vibration, obd
            $table->string('sensor_identifier')->unique(); // MAC address, serial number
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();

            // Configuration
            $table->json('configuration')->nullable();
            $table->integer('reading_interval_seconds')->default(300); // 5 minutes
            $table->boolean('is_active')->default(true);

            // Installation
            $table->date('installation_date');
            $table->bigInteger('installation_mileage')->nullable();
            $table->text('installation_notes')->nullable();

            $table->timestamps();

            $table->index(['vehicle_id', 'sensor_type']);
            $table->index(['sensor_type', 'is_active']);
        });

        // Table données télématiques (partitioned par date)
        DB::statement('
            CREATE TABLE telematics_data (
                id BIGSERIAL,
                vehicle_id BIGINT NOT NULL,
                sensor_id BIGINT,

                -- Données de position
                latitude DECIMAL(10, 8),
                longitude DECIMAL(11, 8),
                altitude DECIMAL(8, 2),
                speed DECIMAL(6, 2),
                heading DECIMAL(5, 2),

                -- Données moteur
                engine_rpm INTEGER,
                fuel_level DECIMAL(5, 2),
                engine_temperature DECIMAL(5, 2),
                oil_pressure DECIMAL(6, 2),

                -- État véhicule
                odometer BIGINT,
                engine_hours DECIMAL(8, 2),
                battery_voltage DECIMAL(4, 2),

                -- Données comportement conduite
                harsh_acceleration BOOLEAN DEFAULT FALSE,
                harsh_braking BOOLEAN DEFAULT FALSE,
                harsh_cornering BOOLEAN DEFAULT FALSE,
                speeding BOOLEAN DEFAULT FALSE,

                -- Métadonnées
                signal_strength INTEGER,
                data_quality VARCHAR(20) DEFAULT \'good\',
                recorded_at TIMESTAMPTZ DEFAULT NOW(),

                -- Contraintes
                CONSTRAINT fk_telematics_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
                CONSTRAINT fk_telematics_sensor FOREIGN KEY (sensor_id) REFERENCES vehicle_sensors(id) ON DELETE SET NULL,
                CONSTRAINT chk_latitude CHECK (latitude BETWEEN -90 AND 90),
                CONSTRAINT chk_longitude CHECK (longitude BETWEEN -180 AND 180),
                CONSTRAINT chk_speed CHECK (speed >= 0),
                CONSTRAINT chk_fuel_level CHECK (fuel_level BETWEEN 0 AND 100)
            ) PARTITION BY RANGE (recorded_at)
        ');

        // Création partitions télématiques initiales
        for ($i = 0; $i < 12; $i++) {
            $date = now()->addMonths($i);
            $startDate = $date->startOfMonth()->format('Y-m-d');
            $endDate = $date->copy()->addMonth()->startOfMonth()->format('Y-m-d');
            $partitionName = 'telematics_data_' . $date->format('Y_m');

            DB::statement("
                CREATE TABLE {$partitionName} PARTITION OF telematics_data
                FOR VALUES FROM ('{$startDate}') TO ('{$endDate}')
            ");
        }

        // Index optimisés pour télématique
        $telematicsIndexes = [
            'CREATE INDEX idx_telematics_vehicle_time ON telematics_data (vehicle_id, recorded_at DESC)',
            'CREATE INDEX idx_telematics_location ON telematics_data (latitude, longitude) WHERE latitude IS NOT NULL',
            'CREATE INDEX idx_telematics_alerts ON telematics_data (vehicle_id, recorded_at) WHERE harsh_acceleration OR harsh_braking OR harsh_cornering OR speeding',
        ];

        foreach ($telematicsIndexes as $indexSql) {
            DB::statement($indexSql);
        }

        echo "✅ Intégration IoT et télématique créée\n";
    }

    /**
     * Optimisations performance globales
     */
    private function addPerformanceOptimizations(): void
    {
        // Fonction de nettoyage données télématiques
        DB::statement('
            CREATE OR REPLACE FUNCTION cleanup_old_telematics_data()
            RETURNS void AS $$
            DECLARE
                retention_days INTEGER := 90; -- 3 mois par défaut
                cutoff_date DATE;
                partition_name TEXT;
            BEGIN
                cutoff_date := CURRENT_DATE - (retention_days || \' days\')::INTERVAL;

                -- Supprime les partitions trop anciennes
                FOR partition_name IN
                    SELECT schemaname||\'.\'||tablename
                    FROM pg_tables
                    WHERE tablename LIKE \'telematics_data_%\'
                    AND tablename < \'telematics_data_\' || to_char(cutoff_date, \'YYYY_MM\')
                LOOP
                    EXECUTE \'DROP TABLE IF EXISTS \' || partition_name || \' CASCADE\';
                    RAISE NOTICE \'Dropped telematics partition: %\', partition_name;
                END LOOP;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Fonction calcul métriques véhicule
        DB::statement('
            CREATE OR REPLACE FUNCTION calculate_vehicle_metrics(p_vehicle_id BIGINT, p_days INTEGER DEFAULT 30)
            RETURNS TABLE(
                avg_fuel_consumption DECIMAL,
                total_distance DECIMAL,
                avg_speed DECIMAL,
                harsh_events_count INTEGER,
                engine_hours DECIMAL
            ) AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    COALESCE(AVG(td.fuel_level), 0)::DECIMAL,
                    COALESCE(MAX(td.odometer) - MIN(td.odometer), 0)::DECIMAL,
                    COALESCE(AVG(td.speed), 0)::DECIMAL,
                    COALESCE(SUM(CASE WHEN td.harsh_acceleration OR td.harsh_braking OR td.harsh_cornering THEN 1 ELSE 0 END), 0)::INTEGER,
                    COALESCE(MAX(td.engine_hours) - MIN(td.engine_hours), 0)::DECIMAL
                FROM telematics_data td
                WHERE td.vehicle_id = p_vehicle_id
                AND td.recorded_at >= NOW() - (p_days || \' days\')::INTERVAL;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Vues matérialisées pour dashboards
        DB::statement('
            CREATE MATERIALIZED VIEW mv_vehicle_summary AS
            SELECT
                v.id,
                v.registration_plate,
                v.organization_id,
                v.status_id as status,
                v.current_mileage,
                COUNT(DISTINCT wo.id) as total_work_orders,
                COUNT(DISTINCT wo.id) FILTER (WHERE wo.created_at >= NOW() - INTERVAL \'30 days\') as recent_work_orders,
                COALESCE(SUM(wo.actual_cost), 0) as total_maintenance_cost,
                MAX(td.recorded_at) as last_telematics_update
            FROM vehicles v
            LEFT JOIN work_orders wo ON v.id = wo.vehicle_id
            LEFT JOIN telematics_data td ON v.id = td.vehicle_id
            WHERE v.deleted_at IS NULL
            GROUP BY v.id, v.registration_plate, v.organization_id, v.status_id, v.current_mileage
        ');

        DB::statement('CREATE UNIQUE INDEX idx_mv_vehicle_summary_id ON mv_vehicle_summary (id)');

        echo "✅ Optimisations performance ajoutées\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprime vues matérialisées
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_vehicle_summary');

        // Supprime fonctions
        DB::statement('DROP FUNCTION IF EXISTS cleanup_old_telematics_data()');
        DB::statement('DROP FUNCTION IF EXISTS calculate_vehicle_metrics(BIGINT, INTEGER)');

        // Supprime partitions télématiques
        $partitions = DB::select("
            SELECT schemaname||'.'||tablename as full_name
            FROM pg_tables
            WHERE tablename LIKE 'telematics_data_%'
        ");

        foreach ($partitions as $partition) {
            DB::statement("DROP TABLE IF EXISTS {$partition->full_name} CASCADE");
        }

        // Supprime tables
        Schema::dropIfExists('telematics_data');
        Schema::dropIfExists('vehicle_sensors');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('work_order_checklists');
        Schema::dropIfExists('work_order_parts');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('vehicle_part_compatibility');
        Schema::dropIfExists('supplier_contracts');

        // Supprime colonnes ajoutées aux tables existantes
        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn([
                    'supplier_category', 'reliability_score', 'average_rating', 'total_orders',
                    'payment_terms', 'credit_limit', 'current_balance',
                    'certifications', 'is_certified_oem', 'is_preferred_vendor',
                    'latitude', 'longitude', 'average_delivery_days', 'delivery_zones',
                    'business_hours', 'emergency_contact', 'notes'
                ]);
            });
        }

        if (Schema::hasTable('parts')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->dropColumn([
                    'part_category', 'part_subcategory', 'is_oem', 'is_critical',
                    'technical_specs', 'weight', 'dimensions', 'material',
                    'reorder_point', 'max_stock_level', 'safety_stock', 'carrying_cost_rate',
                    'average_cost', 'last_purchase_price', 'last_purchase_date',
                    'warranty_months', 'expected_life_months', 'maintenance_schedule'
                ]);
            });
        }

        echo "✅ Système work orders & supply chain supprimé\n";
    }
};