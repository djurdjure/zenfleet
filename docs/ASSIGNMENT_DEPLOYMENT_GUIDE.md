# 🚀 Guide de Déploiement - Module Affectations

## 📋 Checklist Pré-Déploiement

### ✅ Prérequis Système
- [x] **PostgreSQL 16+** avec extension btree_gist
- [x] **PHP 8.3+** avec extensions: pdo_pgsql, gd, zip
- [x] **Laravel 12** framework configuré
- [x] **Livewire 3** package installé
- [x] **Node.js 18+** pour assets (Tailwind CSS, Alpine.js)

### ✅ Prérequis Application
- [x] **Modèles de base** : Organization, User, Vehicle, Driver
- [x] **Système de permissions** : Spatie Laravel Permission
- [x] **Middleware d'auth** : sanctum ou session
- [x] **Queue system** : Redis/Database pour jobs

### ✅ Validation des Dépendances

```bash
# Vérifier PostgreSQL et extensions
psql -d zenfleet -c "SELECT version();"
psql -d zenfleet -c "SELECT * FROM pg_extension WHERE extname = 'btree_gist';"

# Si extension manquante
psql -d zenfleet -c "CREATE EXTENSION IF NOT EXISTS btree_gist;"

# Vérifier Laravel
php artisan --version

# Vérifier Livewire
php artisan livewire:check

# Vérifier permissions Spatie
php artisan permission:cache-reset
```

## 🗄️ Déploiement Base de Données

### Étape 1: Sauvegarde Préventive

```bash
# Sauvegarde complète
pg_dump -h localhost -U postgres -d zenfleet > backup_pre_assignment_$(date +%Y%m%d_%H%M%S).sql

# Sauvegarde schema uniquement
pg_dump -h localhost -U postgres -d zenfleet --schema-only > schema_backup.sql
```

### Étape 2: Migration

```bash
# Mode maintenance
php artisan down --message="Déploiement module Affectations en cours"

# Exécution migration
php artisan migrate --path=/database/migrations/2025_01_20_120000_create_assignments_enhanced_table.php

# Vérification contraintes
psql -d zenfleet -c "
SELECT conname, contype, confdeltype
FROM pg_constraint
WHERE conrelid = 'assignments'::regclass;
"

# Test contrainte anti-chevauchement
psql -d zenfleet -c "
INSERT INTO assignments (organization_id, vehicle_id, driver_id, start_datetime, end_datetime, status)
VALUES (1, 1, 1, '2025-01-25 10:00:00', '2025-01-25 14:00:00', 'active');

-- Cette insertion doit échouer
INSERT INTO assignments (organization_id, vehicle_id, driver_id, start_datetime, end_datetime, status)
VALUES (1, 1, 2, '2025-01-25 12:00:00', '2025-01-25 16:00:00', 'active');
"
```

### Étape 3: Données de Test (optionnel)

```bash
# Seeder pour données de démo
php artisan db:seed --class=AssignmentSeeder

# Ou création manuelle
php artisan tinker
```

```php
// Dans tinker
$org = Organization::first();
$vehicle = Vehicle::where('organization_id', $org->id)->first();
$driver = Driver::where('organization_id', $org->id)->first();

Assignment::create([
    'organization_id' => $org->id,
    'vehicle_id' => $vehicle->id,
    'driver_id' => $driver->id,
    'start_datetime' => now()->addHours(2),
    'end_datetime' => now()->addHours(6),
    'status' => 'scheduled',
    'reason' => 'Mission de livraison',
    'created_by' => 1
]);
```

## 🔧 Configuration Application

### Étape 1: Variables d'Environnement

```bash
# .env additions
ASSIGNMENT_MODULE_ENABLED=true
ASSIGNMENT_OVERLAP_CHECK=strict
ASSIGNMENT_MAX_DURATION_HOURS=8760
ASSIGNMENT_GANTT_CACHE_TTL=300

# Performances
ASSIGNMENT_PAGINATION_SIZE=50
ASSIGNMENT_EXPORT_MAX_ROWS=100000

# Notifications
ASSIGNMENT_NOTIFICATIONS_ENABLED=true
ASSIGNMENT_EMAIL_NOTIFICATIONS=true
```

### Étape 2: Configuration Cache

```bash
# Configuration Redis pour cache Gantt
# config/cache.php
'gantt' => [
    'driver' => 'redis',
    'connection' => 'default',
    'prefix' => 'gantt:',
],
```

### Étape 3: Permissions et Rôles

```bash
php artisan tinker
```

```php
// Création des permissions
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$permissions = [
    'view assignments',
    'create assignments',
    'edit assignments',
    'delete assignments',
    'end assignments',
    'export assignments'
];

foreach ($permissions as $permission) {
    Permission::firstOrCreate(['name' => $permission]);
}

// Attribution aux rôles existants
$adminRole = Role::findByName('Super Admin');
$adminRole->givePermissionTo($permissions);

$fleetRole = Role::findByName('Gestionnaire Flotte');
$fleetRole->givePermissionTo([
    'view assignments',
    'create assignments',
    'edit assignments',
    'end assignments'
]);

$supervisorRole = Role::findByName('Supervisor');
$supervisorRole->givePermissionTo([
    'view assignments',
    'create assignments',
    'end assignments'
]);
```

## 🎨 Déploiement Assets

### Étape 1: Compilation Assets

```bash
# Installation dépendances
npm install

# Compilation pour production
npm run build

# Vérification assets
ls -la public/build/
```

### Étape 2: Optimisations Laravel

```bash
# Cache routes
php artisan route:cache

# Cache config
php artisan config:cache

# Cache views
php artisan view:cache

# Optimisation autoloader
composer dump-autoload --optimize

# Cache Livewire
php artisan livewire:discover
```

## 🔍 Tests de Validation

### Étape 1: Tests Unitaires

```bash
# Tests service anti-chevauchement
php artisan test tests/Unit/Services/AssignmentOverlapServiceTest.php --verbose

# Résultat attendu: PASSED (27 tests, 89 assertions)
```

### Étape 2: Tests d'Intégration

```bash
# Tests composants Livewire
php artisan test tests/Feature/Livewire/AssignmentTableTest.php --verbose
php artisan test tests/Feature/Livewire/AssignmentGanttTest.php --verbose

# Tests contrôleur
php artisan test tests/Feature/Controllers/AssignmentControllerTest.php --verbose

# Tests complets module
php artisan test --group=assignments --verbose
```

### Étape 3: Tests Fonctionnels

```bash
# Test manuel interface web
curl -H "Accept: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost/admin/assignments

# Test export CSV
curl -H "Authorization: Bearer YOUR_TOKEN" \
     "http://localhost/admin/assignments/export?format=csv" \
     -o test_export.csv

# Test API statistiques
curl -H "Accept: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost/admin/assignments/stats
```

## 🚀 Mise en Production

### Étape 1: Déploiement Code

```bash
# Via Git (exemple)
git pull origin main

# Via CI/CD
# Vérifier pipeline de déploiement automatique

# Permissions fichiers
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Étape 2: Migration Production

```bash
# Mode maintenance
php artisan down --message="Mise à jour système en cours"

# Sauvegarde DB production
pg_dump -h DB_HOST -U DB_USER -d DB_NAME > backup_prod_$(date +%Y%m%d_%H%M%S).sql

# Migration
php artisan migrate --force

# Cache refresh
php artisan optimize:clear
php artisan optimize

# Test rapide
php artisan assignment:healthcheck  # (si commande créée)
```

### Étape 3: Validation Post-Déploiement

```bash
# Sortie mode maintenance
php artisan up

# Tests smoke
curl -f http://domain.com/admin/assignments || echo "ERREUR: Endpoint inaccessible"

# Vérification logs
tail -f storage/logs/laravel.log

# Monitoring
# Vérifier tableaux de bord (New Relic, DataDog, etc.)
```

## 📊 Monitoring et Maintenance

### Métriques à Surveiller

```sql
-- Performance requêtes assignments
SELECT
    query,
    calls,
    total_time,
    mean_time,
    max_time
FROM pg_stat_statements
WHERE query LIKE '%assignments%'
ORDER BY total_time DESC
LIMIT 10;

-- Taille table et index
SELECT
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) as size
FROM pg_tables
WHERE tablename = 'assignments';

-- Statistiques contraintes
SELECT
    conname,
    (SELECT COUNT(*) FROM assignments) as total_rows,
    confupdtype,
    confdeltype
FROM pg_constraint
WHERE conrelid = 'assignments'::regclass;
```

### Alertes Recommandées

```yaml
# alerts.yml (exemple Prometheus/Grafana)
- alert: AssignmentConflictHigh
  expr: assignment_conflicts_per_hour > 10
  for: 5m
  labels:
    severity: warning
  annotations:
    summary: "Trop de conflits d'affectation détectés"

- alert: AssignmentTableGrowthHigh
  expr: rate(assignment_table_size[1h]) > 100MB
  for: 10m
  labels:
    severity: info
  annotations:
    summary: "Croissance rapide table assignments"

- alert: AssignmentQuerySlow
  expr: avg(assignment_query_duration) > 2s
  for: 5m
  labels:
    severity: critical
  annotations:
    summary: "Requêtes assignments lentes"
```

### Maintenance Périodique

```bash
#!/bin/bash
# scripts/assignment_maintenance.sh

# Nettoyage logs anciens
find storage/logs -name "*.log" -mtime +30 -delete

# Analyse table assignments
psql -d zenfleet -c "ANALYZE assignments;"

# Reindex si nécessaire (hors heures de pointe)
psql -d zenfleet -c "REINDEX TABLE assignments;"

# Purge cache ancien
php artisan cache:clear --tags=gantt,assignments

# Rapport santé
php artisan assignment:health-report --email=admin@company.com
```

### Scripts Utilitaires

```php
<?php
// app/Console/Commands/AssignmentHealthCheck.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assignment;
use App\Services\AssignmentOverlapService;

class AssignmentHealthCheck extends Command
{
    protected $signature = 'assignment:health-check';
    protected $description = 'Vérifie la santé du module assignments';

    public function handle()
    {
        $this->info('🔍 Vérification santé module Assignments...');

        // Test connexion DB
        try {
            $count = Assignment::count();
            $this->info("✅ Base de données: {$count} affectations trouvées");
        } catch (\Exception $e) {
            $this->error("❌ Erreur DB: " . $e->getMessage());
            return 1;
        }

        // Test service anti-chevauchement
        try {
            $service = app(AssignmentOverlapService::class);
            $this->info("✅ Service anti-chevauchement: opérationnel");
        } catch (\Exception $e) {
            $this->error("❌ Service défaillant: " . $e->getMessage());
            return 1;
        }

        // Test contraintes DB
        try {
            $conflicts = \DB::select("
                SELECT COUNT(*) as conflicts FROM (
                    SELECT a1.id
                    FROM assignments a1, assignments a2
                    WHERE a1.id != a2.id
                    AND a1.organization_id = a2.organization_id
                    AND (a1.vehicle_id = a2.vehicle_id OR a1.driver_id = a2.driver_id)
                    AND a1.status NOT IN ('cancelled', 'completed')
                    AND a2.status NOT IN ('cancelled', 'completed')
                    AND tsrange(a1.start_datetime, COALESCE(a1.end_datetime, 'infinity'::timestamp), '[)')
                        &&
                        tsrange(a2.start_datetime, COALESCE(a2.end_datetime, 'infinity'::timestamp), '[)')
                ) conflicts
            ");

            $conflictCount = $conflicts[0]->conflicts ?? 0;
            if ($conflictCount > 0) {
                $this->warn("⚠️  {$conflictCount} conflit(s) détecté(s) en base");
            } else {
                $this->info("✅ Aucun conflit détecté");
            }
        } catch (\Exception $e) {
            $this->error("❌ Erreur vérification conflits: " . $e->getMessage());
        }

        // Test permissions
        $permissions = ['view assignments', 'create assignments', 'edit assignments'];
        foreach ($permissions as $permission) {
            $exists = \Spatie\Permission\Models\Permission::where('name', $permission)->exists();
            if ($exists) {
                $this->info("✅ Permission '{$permission}': configurée");
            } else {
                $this->error("❌ Permission '{$permission}': manquante");
            }
        }

        $this->info('🎉 Vérification terminée');
        return 0;
    }
}
```

## 🔄 Plan de Rollback

### Procédure d'Urgence

```bash
#!/bin/bash
# scripts/rollback_assignment_module.sh

echo "🚨 ROLLBACK MODULE ASSIGNMENTS"

# 1. Mode maintenance
php artisan down --message="Rollback en cours"

# 2. Restauration DB
echo "Restauration base de données..."
pg_restore -h DB_HOST -U DB_USER -d DB_NAME backup_pre_assignment_TIMESTAMP.sql

# 3. Restauration code
echo "Restauration code précédent..."
git checkout HEAD~1  # ou version spécifique

# 4. Cache refresh
php artisan optimize:clear

# 5. Tests rapides
php artisan migrate:status
curl -f http://localhost/admin/dashboard

# 6. Sortie maintenance
php artisan up

echo "✅ Rollback terminé"
```

### Points de Contrôle

1. **Base de données** : Sauvegardes automatiques avant migration
2. **Code** : Tags Git pour versions stables
3. **Configuration** : Backup .env et configs
4. **Assets** : Versions buildées précédentes
5. **Cache** : Invalidation propre

---

## 📞 Support et Escalade

### Contacts d'Urgence
- **Équipe Dev** : dev-team@zenfleet.com
- **DBA** : dba@zenfleet.com
- **DevOps** : ops@zenfleet.com

### Procédure d'Escalade
1. **Niveau 1** : Support technique standard
2. **Niveau 2** : Équipe développement module
3. **Niveau 3** : Architecture team + DBA
4. **Niveau 4** : CTO + équipe produit

### Documentation Urgence
- **Logs** : `storage/logs/laravel.log`
- **Monitoring** : Tableau de bord Grafana
- **Base** : Requêtes diagnostics en annexe
- **Rollback** : Procédure ci-dessus

Ce guide garantit un déploiement sécurisé et une maintenance optimale du module Affectations en production.