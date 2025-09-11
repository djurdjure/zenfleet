<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $this->logMessage('Début de la transformation Enterprise RBAC');
        
        // 1. Transformer la table organizations
        $this->transformOrganizations();
        
        // 2. Enrichir la table users
        $this->enrichUsers();
        
        // 3. Créer les tables enterprise
        $this->createEnterpriseTables();
        
        // 4. Migrer les données existantes
        $this->migrateData();
        
        $this->logMessage('Transformation Enterprise RBAC terminée avec succès');
    }

    public function down(): void
    {
        $this->logMessage('Rollback de la transformation Enterprise RBAC');
        
        $tables = [
            'subscription_changes',
            'subscription_plans', 
            'organization_metrics',
            'comprehensive_audit_logs',
            'user_vehicle_assignments',
            'supervisor_driver_assignments',
            'granular_permissions'
        ];
        
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        
        $this->logMessage('Rollback terminé');
    }

    private function transformOrganizations(): void
    {
        $this->logMessage('Transformation de la table organizations');
        
        $existingColumns = Schema::getColumnListing('organizations');
        
        Schema::table('organizations', function (Blueprint $table) use ($existingColumns) {
            // Informations de base enrichies
            if (!in_array('slug', $existingColumns)) {
                $table->string('slug')->nullable()->after('name');
            }
            if (!in_array('legal_name', $existingColumns)) {
                $table->string('legal_name')->nullable()->after('name');
            }
            if (!in_array('brand_name', $existingColumns)) {
                $table->string('brand_name')->nullable()->after('legal_name');
            }
            if (!in_array('registration_number', $existingColumns)) {
                $table->string('registration_number')->nullable()->after('brand_name');
            }
            if (!in_array('tax_id', $existingColumns)) {
                $table->string('tax_id')->nullable()->after('registration_number');
            }
            
            // Contact enrichi
            if (!in_array('primary_email', $existingColumns)) {
                $table->string('primary_email')->nullable()->after('contact_email');
            }
            if (!in_array('billing_email', $existingColumns)) {
                $table->string('billing_email')->nullable()->after('primary_email');
            }
            if (!in_array('support_email', $existingColumns)) {
                $table->string('support_email')->nullable()->after('billing_email');
            }
            if (!in_array('primary_phone', $existingColumns)) {
                $table->string('primary_phone')->nullable()->after('support_email');
            }
            if (!in_array('mobile_phone', $existingColumns)) {
                $table->string('mobile_phone')->nullable()->after('primary_phone');
            }
            if (!in_array('website', $existingColumns)) {
                $table->string('website')->nullable()->after('mobile_phone');
            }
            
            // Adresses structurées
            if (!in_array('headquarters_address', $existingColumns)) {
                $table->json('headquarters_address')->nullable()->after('address');
            }
            if (!in_array('billing_address', $existingColumns)) {
                $table->json('billing_address')->nullable()->after('headquarters_address');
            }
            
            // Statut enrichi
            if (!in_array('compliance_status', $existingColumns)) {
                $table->enum('compliance_status', ['compliant', 'warning', 'non_compliant', 'under_review'])->default('under_review')->after('status');
            }
            if (!in_array('status_changed_at', $existingColumns)) {
                $table->timestamp('status_changed_at')->nullable()->after('compliance_status');
            }
            if (!in_array('status_reason', $existingColumns)) {
                $table->text('status_reason')->nullable()->after('status_changed_at');
            }
            
            // Abonnement
            if (!in_array('subscription_plan', $existingColumns)) {
                $table->enum('subscription_plan', ['trial', 'basic', 'professional', 'enterprise', 'custom'])->default('trial')->after('status_reason');
            }
            if (!in_array('subscription_tier', $existingColumns)) {
                $table->string('subscription_tier')->nullable()->after('subscription_plan');
            }
            if (!in_array('subscription_starts_at', $existingColumns)) {
                $table->timestamp('subscription_starts_at')->nullable()->after('subscription_tier');
            }
            if (!in_array('subscription_expires_at', $existingColumns)) {
                $table->timestamp('subscription_expires_at')->nullable()->after('subscription_starts_at');
            }
            if (!in_array('trial_ends_at', $existingColumns)) {
                $table->timestamp('trial_ends_at')->nullable()->after('subscription_expires_at');
            }
            if (!in_array('monthly_rate', $existingColumns)) {
                $table->decimal('monthly_rate', 8, 2)->nullable()->after('trial_ends_at');
            }
            if (!in_array('annual_rate', $existingColumns)) {
                $table->decimal('annual_rate', 8, 2)->nullable()->after('monthly_rate');
            }
            if (!in_array('currency', $existingColumns)) {
                $table->string('currency', 3)->default('EUR')->after('annual_rate');
            }
            
            // Configuration
            if (!in_array('plan_limits', $existingColumns)) {
                $table->json('plan_limits')->nullable()->after('currency');
            }
            if (!in_array('current_usage', $existingColumns)) {
                $table->json('current_usage')->nullable()->after('plan_limits');
            }
            if (!in_array('feature_flags', $existingColumns)) {
                $table->json('feature_flags')->nullable()->after('current_usage');
            }
            if (!in_array('settings', $existingColumns)) {
                $table->json('settings')->nullable()->after('feature_flags');
            }
            if (!in_array('branding', $existingColumns)) {
                $table->json('branding')->nullable()->after('settings');
            }
            if (!in_array('notification_preferences', $existingColumns)) {
                $table->json('notification_preferences')->nullable()->after('branding');
            }
            
            // Sécurité
            if (!in_array('two_factor_required', $existingColumns)) {
                $table->boolean('two_factor_required')->default(false)->after('notification_preferences');
            }
            if (!in_array('ip_restriction_enabled', $existingColumns)) {
                $table->boolean('ip_restriction_enabled')->default(false)->after('two_factor_required');
            }
            if (!in_array('password_policy_strength', $existingColumns)) {
                $table->integer('password_policy_strength')->default(2)->after('ip_restriction_enabled');
            }
            if (!in_array('session_timeout_minutes', $existingColumns)) {
                $table->integer('session_timeout_minutes')->default(480)->after('password_policy_strength');
            }
            if (!in_array('gdpr_compliant', $existingColumns)) {
                $table->boolean('gdpr_compliant')->default(false)->after('session_timeout_minutes');
            }
            if (!in_array('gdpr_consent_at', $existingColumns)) {
                $table->timestamp('gdpr_consent_at')->nullable()->after('gdpr_compliant');
            }
            
            // Métriques
            if (!in_array('last_activity_at', $existingColumns)) {
                $table->timestamp('last_activity_at')->nullable()->after('gdpr_consent_at');
            }
            if (!in_array('total_users', $existingColumns)) {
                $table->integer('total_users')->default(0)->after('last_activity_at');
            }
            if (!in_array('active_users', $existingColumns)) {
                $table->integer('active_users')->default(0)->after('total_users');
            }
            if (!in_array('total_vehicles', $existingColumns)) {
                $table->integer('total_vehicles')->default(0)->after('active_users');
            }
            if (!in_array('active_vehicles', $existingColumns)) {
                $table->integer('active_vehicles')->default(0)->after('total_vehicles');
            }
            
            // Géolocalisation
            if (!in_array('timezone', $existingColumns)) {
                $table->string('timezone')->default('Europe/Paris')->after('active_vehicles');
            }
            if (!in_array('country_code', $existingColumns)) {
                $table->string('country_code', 2)->nullable()->after('timezone');
            }
            if (!in_array('language', $existingColumns)) {
                $table->string('language', 2)->default('fr')->after('country_code');
            }
            if (!in_array('latitude', $existingColumns)) {
                $table->decimal('latitude', 10, 8)->nullable()->after('language');
            }
            if (!in_array('longitude', $existingColumns)) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            
            // Hiérarchie
            if (!in_array('parent_organization_id', $existingColumns)) {
                $table->foreignId('parent_organization_id')->nullable()->constrained('organizations')->onDelete('set null')->after('longitude');
            }
            if (!in_array('hierarchy_level', $existingColumns)) {
                $table->integer('hierarchy_level')->default(0)->after('parent_organization_id');
            }
            
            // Métadonnées
            if (!in_array('created_by', $existingColumns)) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('hierarchy_level');
            }
            if (!in_array('updated_by', $existingColumns)) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            }
            if (!in_array('onboarding_completed_at', $existingColumns)) {
                $table->timestamp('onboarding_completed_at')->nullable()->after('updated_by');
            }
            if (!in_array('deleted_at', $existingColumns)) {
                $table->softDeletes();
            }
        });

        // Générer les slugs
        $this->generateSlugs();
        
        // Créer les index
        $this->createOrganizationIndexes();
    }

    private function enrichUsers(): void
    {
        $this->logMessage('Enrichissement de la table users');
        
        $existingColumns = Schema::getColumnListing('users');
        
        Schema::table('users', function (Blueprint $table) use ($existingColumns) {
            // Relations hiérarchiques
            if (!in_array('supervisor_id', $existingColumns)) {
                $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null')->after('organization_id');
            }
            if (!in_array('manager_id', $existingColumns)) {
                $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null')->after('supervisor_id');
            }
            
            // Permissions
            if (!in_array('is_super_admin', $existingColumns)) {
                $table->boolean('is_super_admin')->default(false)->after('manager_id');
            }
            if (!in_array('permissions_cache', $existingColumns)) {
                $table->json('permissions_cache')->nullable()->after('is_super_admin');
            }
            
            // Profil enrichi
            if (!in_array('first_name', $existingColumns)) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!in_array('last_name', $existingColumns)) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!in_array('phone', $existingColumns)) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!in_array('job_title', $existingColumns)) {
                $table->string('job_title')->nullable()->after('phone');
            }
            if (!in_array('department', $existingColumns)) {
                $table->string('department')->nullable()->after('job_title');
            }
            if (!in_array('employee_id', $existingColumns)) {
                $table->string('employee_id')->nullable()->after('department');
            }
            
            // Dates
            if (!in_array('hire_date', $existingColumns)) {
                $table->date('hire_date')->nullable()->after('employee_id');
            }
            if (!in_array('birth_date', $existingColumns)) {
                $table->date('birth_date')->nullable()->after('hire_date');
            }
            
            // Sécurité
            if (!in_array('two_factor_enabled', $existingColumns)) {
                $table->boolean('two_factor_enabled')->default(false)->after('permissions_cache');
            }
            if (!in_array('failed_login_attempts', $existingColumns)) {
                $table->integer('failed_login_attempts')->default(0)->after('two_factor_enabled');
            }
            if (!in_array('locked_until', $existingColumns)) {
                $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            }
            if (!in_array('password_changed_at', $existingColumns)) {
                $table->timestamp('password_changed_at')->nullable()->after('locked_until');
            }
            
            // Activité
            if (!in_array('last_activity_at', $existingColumns)) {
                $table->timestamp('last_activity_at')->nullable()->after('password_changed_at');
            }
            if (!in_array('last_login_at', $existingColumns)) {
                $table->timestamp('last_login_at')->nullable()->after('last_activity_at');
            }
            if (!in_array('last_login_ip', $existingColumns)) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
            if (!in_array('login_count', $existingColumns)) {
                $table->integer('login_count')->default(0)->after('last_login_ip');
            }
            
            // Statut
            if (!in_array('is_active', $existingColumns)) {
                $table->boolean('is_active')->default(true)->after('login_count');
            }
            if (!in_array('user_status', $existingColumns)) {
                $table->enum('user_status', ['active', 'inactive', 'suspended', 'pending'])->default('pending')->after('is_active');
            }
            if (!in_array('timezone', $existingColumns)) {
                $table->string('timezone')->default('Europe/Paris')->after('user_status');
            }
            if (!in_array('language', $existingColumns)) {
                $table->string('language', 2)->default('fr')->after('timezone');
            }
            if (!in_array('preferences', $existingColumns)) {
                $table->json('preferences')->nullable()->after('language');
            }
            if (!in_array('notification_preferences', $existingColumns)) {
                $table->json('notification_preferences')->nullable()->after('preferences');
            }
        });

        $this->createUserIndexes();
    }

    private function createEnterpriseTables(): void
    {
        $this->logMessage('Création des tables enterprise');
        
        // Table des permissions granulaires
        if (!Schema::hasTable('granular_permissions')) {
            Schema::create('granular_permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('guard_name')->default('web');
                $table->string('module');
                $table->string('resource');
                $table->string('action');
                $table->enum('scope', ['global', 'organization', 'supervised', 'own'])->default('organization');
                $table->text('description')->nullable();
                $table->integer('risk_level')->default(1);
                $table->boolean('is_system')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['module', 'resource', 'action']);
                $table->index(['scope', 'is_active']);
            });
        }

        // Table des assignations superviseur-chauffeur
        if (!Schema::hasTable('supervisor_driver_assignments')) {
            Schema::create('supervisor_driver_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
                $table->foreignId('assigned_by')->constrained('users');
                $table->timestamp('assigned_at');
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->unique(['supervisor_id', 'driver_id']);
                $table->index(['supervisor_id', 'is_active']);
            });
        }

        // Table des assignations superviseur-véhicule
        if (!Schema::hasTable('user_vehicle_assignments')) {
            Schema::create('user_vehicle_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
                $table->foreignId('assigned_by')->constrained('users');
                $table->timestamp('assigned_at');
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->unique(['supervisor_id', 'vehicle_id']);
                $table->index(['supervisor_id', 'is_active']);
            });
        }

        // Table d'audit
        if (!Schema::hasTable('comprehensive_audit_logs')) {
            Schema::create('comprehensive_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->uuid('audit_uuid')->unique();
                $table->foreignId('organization_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                
                $table->string('event_category');
                $table->string('event_type');
                $table->string('event_action');
                $table->enum('severity_level', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->text('event_description');
                $table->json('event_data');
                $table->json('before_state')->nullable();
                $table->json('after_state')->nullable();
                
                $table->string('resource_type')->nullable();
                $table->unsignedBigInteger('resource_id')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('session_id')->nullable();
                
                $table->boolean('gdpr_relevant')->default(false);
                $table->timestamp('occurred_at');
                $table->timestamps();
                
                $table->index(['organization_id', 'occurred_at']);
                $table->index(['event_category', 'event_type']);
                $table->index(['resource_type', 'resource_id']);
            });
        }

        // Table des métriques
        if (!Schema::hasTable('organization_metrics')) {
            Schema::create('organization_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->onDelete('cascade');
                $table->date('metric_date');
                $table->string('metric_period');
                
                $table->integer('total_users')->default(0);
                $table->integer('active_users')->default(0);
                $table->integer('total_vehicles')->default(0);
                $table->integer('active_vehicles')->default(0);
                $table->decimal('total_distance_km', 12, 2)->default(0);
                $table->decimal('fuel_costs', 10, 2)->default(0);
                $table->decimal('maintenance_costs', 10, 2)->default(0);
                
                $table->timestamps();
                
                $table->unique(['organization_id', 'metric_date', 'metric_period']);
                $table->index(['metric_date', 'metric_period']);
            });
        }

        // Table des plans d'abonnement
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->enum('tier', ['trial', 'basic', 'professional', 'enterprise', 'custom']);
                $table->decimal('base_monthly_price', 8, 2)->default(0);
                $table->decimal('base_annual_price', 8, 2)->default(0);
                $table->json('feature_limits');
                $table->json('included_features');
                $table->integer('trial_days')->default(14);
                $table->boolean('is_public')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->index(['tier', 'is_active']);
                $table->index(['is_public', 'sort_order']);
            });
        }

        // Table des changements d'abonnement
        if (!Schema::hasTable('subscription_changes')) {
            Schema::create('subscription_changes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->onDelete('cascade');
                $table->foreignId('old_plan_id')->nullable()->constrained('subscription_plans');
                $table->foreignId('new_plan_id')->constrained('subscription_plans');
                $table->enum('change_type', ['upgrade', 'downgrade', 'renewal', 'cancellation']);
                $table->text('change_reason')->nullable();
                $table->decimal('amount_due', 8, 2)->nullable();
                $table->timestamp('effective_date');
                $table->foreignId('initiated_by')->constrained('users');
                $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
                $table->timestamps();
                
                $table->index(['organization_id', 'effective_date']);
                $table->index(['change_type', 'status']);
            });
        }
    }

    private function generateSlugs(): void
    {
        $this->logMessage('Génération des slugs pour organisations');
        
        $organizations = DB::table('organizations')->whereNull('slug')->get();
        
        foreach ($organizations as $org) {
            $slug = \Illuminate\Support\Str::slug($org->name);
            $originalSlug = $slug;
            $counter = 1;
            
            while (DB::table('organizations')->where('slug', $slug)->where('id', '!=', $org->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            DB::table('organizations')->where('id', $org->id)->update([
                'slug' => $slug,
                'legal_name' => $org->name,
                'primary_email' => $org->contact_email ?: 'contact@' . $slug . '.zenfleet.app',
                'headquarters_address' => json_encode([
                    'street' => $org->address,
                    'city' => null,
                    'postal_code' => null,
                    'country' => 'France'
                ]),
                'subscription_plan' => 'professional',
                'subscription_starts_at' => $org->created_at,
                'plan_limits' => json_encode([
                    'max_users' => 100,
                    'max_vehicles' => 500,
                    'max_drivers' => 200
                ]),
                'current_usage' => json_encode([
                    'users' => 0,
                    'vehicles' => 0,
                    'drivers' => 0
                ]),
                'feature_flags' => json_encode([
                    'advanced_analytics' => true,
                    'api_access' => true,
                    'supervisor_management' => true
                ]),
                'settings' => json_encode([
                    'timezone' => 'Europe/Paris',
                    'currency' => 'EUR',
                    'language' => 'fr'
                ]),
                'notification_preferences' => json_encode([
                    'email_notifications' => true,
                    'push_notifications' => true
                ])
            ]);
        }
    }

    private function migrateData(): void
    {
        $this->logMessage('Migration des données existantes');
        
        $plans = [
            [
                'name' => 'Trial',
                'slug' => 'trial',
                'tier' => 'trial',
                'description' => 'Essai gratuit 14 jours',
                'base_monthly_price' => 0.00,
                'base_annual_price' => 0.00,
                'feature_limits' => json_encode(['max_users' => 3, 'max_vehicles' => 10]),
                'included_features' => json_encode(['basic_management']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'tier' => 'professional',
                'description' => 'Solution complète',
                'base_monthly_price' => 299.00,
                'base_annual_price' => 2990.00,
                'feature_limits' => json_encode(['max_users' => 100, 'max_vehicles' => 500]),
                'included_features' => json_encode(['advanced_management', 'analytics', 'api']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'tier' => 'enterprise',
                'description' => 'Solution enterprise',
                'base_monthly_price' => 999.00,
                'base_annual_price' => 9990.00,
                'feature_limits' => json_encode(['max_users' => null, 'max_vehicles' => null]),
                'included_features' => json_encode(['everything', 'white_labeling', 'sla']),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($plans as $plan) {
            DB::table('subscription_plans')->insertOrIgnore($plan);
        }
    }

    private function createOrganizationIndexes(): void
    {
        $this->createIndex('organizations', ['slug']);
        $this->createIndex('organizations', ['status', 'subscription_expires_at']);
        $this->createIndex('organizations', ['subscription_plan', 'status']);
        $this->createIndex('organizations', ['parent_organization_id', 'hierarchy_level']);
        $this->createIndex('organizations', ['last_activity_at']);
    }

    private function createUserIndexes(): void
    {
        $this->createIndex('users', ['organization_id', 'user_status', 'is_active']);
        $this->createIndex('users', ['supervisor_id']);
        $this->createIndex('users', ['manager_id']);
        $this->createIndex('users', ['employee_id']);
        $this->createIndex('users', ['last_activity_at']);
        $this->createIndex('users', ['is_super_admin']);
    }

    private function createIndex(string $table, array $columns): void
    {
        try {
            $indexName = $table . '_' . implode('_', $columns) . '_index';
            
            $exists = DB::select(
                "SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                [$table, $indexName]
            );
            
            if (empty($exists)) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    $table->index($columns);
                });
            }
        } catch (\Exception $e) {
            Log::warning("Index creation failed for {$table}: " . $e->getMessage());
        }
    }

    private function logMessage(string $message): void
    {
        Log::info("[ZENFLEET MIGRATION] {$message}");
        if (app()->runningInConsole()) {
            echo "[INFO] {$message}\n";
        }
    }
};

