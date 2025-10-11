<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🏢 ZENFLEET MULTI-TENANT ENTERPRISE SYSTEM
 *
 * Système multi-tenant ultra-professionnel:
 * - Support holdings et partenariats
 * - Isolation sécurisée des données
 * - Gestion permissions granulaires
 * - Architecture évolutive SaaS
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
        // ===== TABLE PIVOT USER-ORGANIZATIONS =====
        $this->createUserOrganizationsTable();

        // ===== EXTENSIONS ORGANISATIONS ENTERPRISE =====
        $this->extendOrganizationsTable();

        // ===== HIÉRARCHIE ORGANISATIONNELLE =====
        $this->createOrganizationHierarchy();

        // ===== SYSTÈME PERMISSIONS GRANULAIRES =====
        $this->createGranularPermissionsSystem();

        // ===== ROW LEVEL SECURITY GLOBAL =====
        $this->enableGlobalRowLevelSecurity();

        // ===== FONCTIONS UTILITAIRES =====
        $this->createUtilityFunctions();

        echo "✅ Système multi-tenant enterprise créé\n";
    }

    /**
     * Table pivot user-organizations pour multi-appartenance
     */
    private function createUserOrganizationsTable(): void
    {
        Schema::create('user_organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');

            // Rôle dans cette organisation
            $table->string('role', 100)->default('member');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);

            // Permissions spécifiques
            $table->json('specific_permissions')->nullable();
            $table->json('scope_limitations')->nullable();

            // Audit trail
            $table->foreignId('granted_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            $table->timestamps();

            // Contraintes uniques
            $table->unique(['user_id', 'organization_id'], 'user_org_unique');
            $table->index(['organization_id', 'is_active']);
            $table->index(['user_id', 'is_primary']);
            $table->index(['expires_at']);
        });

        // ===== CONTRAINTES BUSINESS (PostgreSQL uniquement) =====
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('
                ALTER TABLE user_organizations
                ADD CONSTRAINT chk_one_primary_per_user
                EXCLUDE (user_id WITH =) WHERE (is_primary = true AND is_active = true)
            ');
        }

        echo "✅ Table user_organizations créée\n";
    }

    /**
     * Extensions de la table organizations
     */
    private function extendOrganizationsTable(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // Hiérarchie organisationnelle - vérifier si existe déjà
            if (!Schema::hasColumn('organizations', 'parent_organization_id')) {
                $table->foreignId('parent_organization_id')->nullable()->constrained('organizations')->onDelete('set null');
            }
            if (!Schema::hasColumn('organizations', 'organization_level')) {
                $table->string('organization_level')->default('company'); // company, division, department, branch
            }
            if (!Schema::hasColumn('organizations', 'hierarchy_depth')) {
                $table->integer('hierarchy_depth')->default(0);
            }
            if (!Schema::hasColumn('organizations', 'hierarchy_path')) {
                $table->string('hierarchy_path')->nullable(); // Format: /1/5/12/
            }

            // Configuration multi-tenant
            if (!Schema::hasColumn('organizations', 'is_tenant_root')) {
                $table->boolean('is_tenant_root')->default(true);
            }
            if (!Schema::hasColumn('organizations', 'allows_sub_organizations')) {
                $table->boolean('allows_sub_organizations')->default(false);
            }
            if (!Schema::hasColumn('organizations', 'tenant_settings')) {
                $table->json('tenant_settings')->nullable();
            }

            // Compliance et audit
            if (!Schema::hasColumn('organizations', 'data_retention_period')) {
                $table->integer('data_retention_period')->default(24); // mois
            }
            if (!Schema::hasColumn('organizations', 'audit_log_enabled')) {
                $table->boolean('audit_log_enabled')->default(true);
            }
            if (!Schema::hasColumn('organizations', 'compliance_level')) {
                $table->string('compliance_level')->default('standard'); // basic, standard, high, critical
            }
            if (!Schema::hasColumn('organizations', 'compliance_certifications')) {
                $table->json('compliance_certifications')->nullable();
            }

            // Fonctionnalités activées
            if (!Schema::hasColumn('organizations', 'enabled_features')) {
                $table->json('enabled_features')->nullable();
            }
            if (!Schema::hasColumn('organizations', 'feature_limits')) {
                $table->json('feature_limits')->nullable();
            }

            // Sécurité
            if (!Schema::hasColumn('organizations', 'enforce_2fa')) {
                $table->boolean('enforce_2fa')->default(false);
            }
            if (!Schema::hasColumn('organizations', 'password_policy_level')) {
                $table->integer('password_policy_level')->default(1);
            }
            if (!Schema::hasColumn('organizations', 'security_settings')) {
                $table->json('security_settings')->nullable();
            }

            // Billing et limites - utiliser les colonnes existantes si possibles
            if (!Schema::hasColumn('organizations', 'max_users')) {
                $table->integer('max_users')->nullable();
            }
            if (!Schema::hasColumn('organizations', 'max_vehicles')) {
                $table->integer('max_vehicles')->nullable();
            }
            if (!Schema::hasColumn('organizations', 'monthly_cost')) {
                $table->decimal('monthly_cost', 10, 2)->nullable();
            }

            // Index pour performance
            $table->index(['parent_organization_id', 'hierarchy_depth']);
            $table->index(['is_tenant_root', 'status']);

            // Index subscription_tier seulement si la colonne existe
            if (Schema::hasColumn('organizations', 'subscription_tier')) {
                $table->index(['subscription_tier', 'status']);
            }
        });

        echo "✅ Table organizations étendue\n";
    }

    /**
     * Système de hiérarchie organisationnelle
     */
    private function createOrganizationHierarchy(): void
    {
        // Skip si PostgreSQL n'est pas utilisé
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // ===== FONCTION DE MISE À JOUR DU CHEMIN HIÉRARCHIQUE =====
        DB::statement('
            CREATE OR REPLACE FUNCTION update_organization_hierarchy()
            RETURNS TRIGGER AS $$
            BEGIN
                IF TG_OP = \'INSERT\' OR (TG_OP = \'UPDATE\' AND OLD.parent_organization_id != NEW.parent_organization_id) THEN
                    -- Calcul du niveau et du chemin
                    IF NEW.parent_organization_id IS NULL THEN
                        NEW.hierarchy_depth := 0;
                        NEW.hierarchy_path := \'/\' || NEW.id || \'/\';
                    ELSE
                        SELECT hierarchy_depth + 1, hierarchy_path || NEW.id || \'/\'
                        INTO NEW.hierarchy_depth, NEW.hierarchy_path
                        FROM organizations
                        WHERE id = NEW.parent_organization_id;
                    END IF;

                    -- Validation profondeur maximale
                    IF NEW.hierarchy_depth > 5 THEN
                        RAISE EXCEPTION \'Profondeur hiérarchique maximale dépassée (5 niveaux)\';
                    END IF;

                    -- Prévention cycle
                    IF NEW.hierarchy_path LIKE \'%/\' || NEW.parent_organization_id || \'/%\' THEN
                        RAISE EXCEPTION \'Référence circulaire détectée dans la hiérarchie\';
                    END IF;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // ===== TRIGGER DE HIÉRARCHIE =====
        DB::statement('
            CREATE TRIGGER trg_organization_hierarchy
            BEFORE INSERT OR UPDATE ON organizations
            FOR EACH ROW
            EXECUTE FUNCTION update_organization_hierarchy()
        ');

        echo "✅ Système de hiérarchie organisationnelle créé\n";
    }

    /**
     * Système de permissions granulaires
     */
    private function createGranularPermissionsSystem(): void
    {
        // ===== TABLE SCOPES DE PERMISSIONS =====
        Schema::create('permission_scopes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('scope_definition'); // Définition JSON du scope
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ===== PERMISSIONS CONTEXTUELLES =====
        Schema::create('contextual_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('permission_scope_id')->constrained('permission_scopes')->onDelete('cascade');

            $table->string('permission_name');
            $table->json('context_filters')->nullable(); // Filtres contextuels
            $table->timestamp('valid_from')->useCurrent();
            $table->timestamp('valid_until')->nullable();

            $table->foreignId('granted_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['user_id', 'organization_id', 'permission_scope_id', 'permission_name'], 'contextual_perm_unique');
            $table->index(['organization_id', 'permission_name']);
            $table->index(['valid_until']);
        });

        echo "✅ Système de permissions granulaires créé\n";
    }

    /**
     * Row Level Security global
     */
    private function enableGlobalRowLevelSecurity(): void
    {
        // Skip si PostgreSQL n'est pas utilisé
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'vehicles', 'drivers', 'assignments', 'maintenance_plans',
            'maintenance_logs', 'documents', 'suppliers'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'organization_id')) {
                // Active RLS
                DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY");

                // Policy d'isolation par organisation
                DB::statement("
                    CREATE POLICY {$table}_organization_isolation
                    ON {$table}
                    USING (
                        organization_id IN (
                            SELECT uo.organization_id
                            FROM user_organizations uo
                            WHERE uo.user_id = current_setting('app.current_user_id', true)::BIGINT
                            AND uo.is_active = true
                            AND (uo.expires_at IS NULL OR uo.expires_at > NOW())
                        )
                    )
                ");

                // Policy pour Super Admins
                DB::statement("
                    CREATE POLICY {$table}_super_admin_access
                    ON {$table}
                    USING (
                        EXISTS (
                            SELECT 1 FROM users u
                            JOIN model_has_roles mhr ON u.id = mhr.model_id
                            JOIN roles r ON mhr.role_id = r.id
                            WHERE u.id = current_setting('app.current_user_id', true)::BIGINT
                            AND r.name = 'Super Admin'
                            AND mhr.model_type = 'App\\\\Models\\\\User'
                        )
                    )
                ");
            }
        }

        echo "✅ Row Level Security global activé\n";
    }

    /**
     * Fonctions utilitaires enterprise
     */
    private function createUtilityFunctions(): void
    {
        // Skip si PostgreSQL n'est pas utilisé
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // ===== FONCTION D'OBTENTION DES ORGANISATIONS ACCESSIBLES =====
        DB::statement('
            CREATE OR REPLACE FUNCTION get_user_accessible_organizations(p_user_id BIGINT)
            RETURNS TABLE(org_id BIGINT, org_name TEXT, role_name TEXT, is_primary BOOLEAN) AS $$
            BEGIN
                RETURN QUERY
                SELECT
                    uo.organization_id,
                    o.name::TEXT,
                    uo.role::TEXT,
                    uo.is_primary
                FROM user_organizations uo
                JOIN organizations o ON uo.organization_id = o.id
                WHERE uo.user_id = p_user_id
                AND uo.is_active = true
                AND (uo.expires_at IS NULL OR uo.expires_at > NOW())
                AND o.status = \'active\'
                ORDER BY uo.is_primary DESC, o.name;
            END;
            $$ LANGUAGE plpgsql SECURITY DEFINER;
        ');

        // ===== FONCTION DE VALIDATION PERMISSIONS =====
        DB::statement('
            CREATE OR REPLACE FUNCTION user_has_permission(
                p_user_id BIGINT,
                p_organization_id BIGINT,
                p_permission TEXT
            )
            RETURNS BOOLEAN AS $$
            DECLARE
                has_permission BOOLEAN := FALSE;
            BEGIN
                -- Vérifie permissions directes
                SELECT EXISTS (
                    SELECT 1 FROM contextual_permissions cp
                    WHERE cp.user_id = p_user_id
                    AND cp.organization_id = p_organization_id
                    AND cp.permission_name = p_permission
                    AND cp.valid_from <= NOW()
                    AND (cp.valid_until IS NULL OR cp.valid_until > NOW())
                ) INTO has_permission;

                -- Vérifie via rôles Spatie si pas de permission directe
                IF NOT has_permission THEN
                    SELECT EXISTS (
                        SELECT 1 FROM users u
                        JOIN model_has_permissions mhp ON u.id = mhp.model_id
                        JOIN permissions p ON mhp.permission_id = p.id
                        WHERE u.id = p_user_id
                        AND p.name = p_permission
                        AND mhp.model_type = \'App\\\\Models\\\\User\'
                    ) INTO has_permission;
                END IF;

                RETURN has_permission;
            END;
            $$ LANGUAGE plpgsql SECURITY DEFINER;
        ');

        echo "✅ Fonctions utilitaires créées\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip PostgreSQL-specific cleanup si pas PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Désactive RLS
            $tables = ['vehicles', 'drivers', 'assignments', 'maintenance_plans', 'maintenance_logs', 'documents', 'suppliers'];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::statement("ALTER TABLE {$table} DISABLE ROW LEVEL SECURITY");
                    DB::statement("DROP POLICY IF EXISTS {$table}_organization_isolation ON {$table}");
                    DB::statement("DROP POLICY IF EXISTS {$table}_super_admin_access ON {$table}");
                }
            }

            // Supprime fonctions
            DB::statement('DROP FUNCTION IF EXISTS get_user_accessible_organizations(BIGINT)');
            DB::statement('DROP FUNCTION IF EXISTS user_has_permission(BIGINT, BIGINT, TEXT)');
            DB::statement('DROP FUNCTION IF EXISTS update_organization_hierarchy()');

            // Supprime triggers
            DB::statement('DROP TRIGGER IF EXISTS trg_organization_hierarchy ON organizations');
        }

        // Supprime tables
        Schema::dropIfExists('contextual_permissions');
        Schema::dropIfExists('permission_scopes');
        Schema::dropIfExists('user_organizations');

        // Supprime colonnes ajoutées
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['parent_organization_id']);
            $table->dropColumn([
                'parent_organization_id', 'organization_level', 'hierarchy_depth', 'hierarchy_path',
                'is_tenant_root', 'allows_sub_organizations', 'tenant_settings',
                'data_retention_period', 'audit_log_enabled', 'compliance_level', 'compliance_certifications',
                'enabled_features', 'feature_limits',
                'enforce_2fa', 'password_policy_level', 'security_settings',
                'subscription_tier', 'max_users', 'max_vehicles', 'monthly_cost'
            ]);
        });

        echo "✅ Système multi-tenant supprimé\n";
    }
};