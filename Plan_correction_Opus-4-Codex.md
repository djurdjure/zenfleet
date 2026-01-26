# Plan de Correction Expert - ZenFleet Audit Codex

Date de mise a jour : 2026-01-24
Base : audit_codex.md
Contexte : Dev_environnement.md (Laravel 12 + Livewire 3 + PostgreSQL 18 + Docker)
Statut : Phase 0 COMMITEE - Phase 1 + 1.5 PLANIFIÉE

---

## Resume Executif

Le plan couvre la majorite des constats de l audit, mais il a ete corrige pour etre operationnel en production :
- RLS ajustee pour eviter les effets de bord du SET LOCAL sans transaction.
- Ordre des middlewares corrige pour l API (RLS apres auth:sanctum).
- Bugs manquants ajoutes (API vehicle show, webhook mileage update).
- Migrations traitees en mode prod-safe (pas de fusion destructive).
- Suivi d execution ajoute pour pilotage.
- **AJOUT CRITIQUE**: Phase 1.5 pour sécuriser les acquis UI/UX Enterprise (Véhicules/Affectations).

---

## Mecanisme de suivi de l execution (obligatoire)

Regle : chaque action doit avoir un identifiant, un statut, une date et une note.

Statuts autorises : TODO, IN_PROGRESS, DONE, BLOCKED

Phase courante : Phase 1.5 (Anti-Régression)
Etat global : IN_PROGRESS
Derniere mise a jour : 2026-01-24

Tableau de suivi

| ID | Phase | Action | Statut | Owner | Date cible | Derniere maj | Notes |
|----|-------|--------|--------|-------|------------|--------------|-------|
| P0-01 | 0 | RLS session web + API (SET + RESET) | DONE | | 2026-01-21 | 2026-01-21 | SetTenantSession + reset defensif |
| P0-02 | 0 | RLS apres auth:sanctum pour API | DONE | | 2026-01-21 | 2026-01-21 | tenant.session sur routes API protegees |
| P0-03 | 0 | Fix scope vehicule (OR mal groupe) | DONE | | 2026-01-21 | 2026-01-21 | UserVehicleAccessScope corrige |
| P0-04 | 0 | Fix API vehicle show param | DONE | | 2026-01-21 | 2026-01-21 | Param route aligne |
| P0-05 | 0 | Fix webhook mileage update | DONE | | 2026-01-21 | 2026-01-21 | Token map + scope bypass |
| P0-06 | 0 | Fix routes non mappees (deny by default) | DONE | | 2026-01-21 | 2026-01-21 | Deny hors local/dev |
| P0-07 | 0 | Tests RLS + scope vehicule + API show + webhook | DONE | | 2026-01-21 | 2026-01-21 | 4 tests ajoutes + conversion PHPUnit |
| P0-08 | 0 | NOUVEAU: Fix tests Spatie Permission multi-tenant | TODO | | | | Setup incorrect dans certains tests |
| **P1.5-1** | **1.5** | **Check intégrité UI Véhicules (Livewire)** | **TODO** | **Expert** | **Immédiat** | | **Protection vehicle-index.blade.php** |
| **P1.5-2** | **1.5** | **Check intégrité UI Affectations (Livewire)** | **TODO** | **Expert** | **Immédiat** | | **Protection assignment-index.blade.php** |
| P1-01 | 1 | Validation indexes trigram existants | TODO | | | | Migration 2026_01_21_000000 deja creee |
| P1-02 | 1 | Validation caches scopes par organization | TODO | | | | VehicleIndex deja modifie |
| P1-03 | 1 | Validation analytics scopes | TODO | | | | withoutGlobalScope deja implemente |
| P1-04 | 1 | Tests de non-regression Phase 1 | TODO | | | | Verifier indexes + cache invalidation |
| P1-05 | 1 | Corriger tests existants defaillants | TODO | | | | AssignmentManagementTest, etc. |
| P2-01 | 2 | Split VehicleController | TODO | | | | |
| P2-02 | 2 | Unifier naming permissions | TODO | | | | |
| P3-01 | 3 | Accessibilite ARIA | TODO | | | | |
| P3-02 | 3 | Perf AssignmentWizard (pagination/lazy) | TODO | | | | |

---

## Bilan Phase 0 (COMMITEE - 3aa5cf6)

### Commit
```
[Phase 0] Securite Critique - RLS, Scopes, API, Deny by Default
104 files changed, 3939 insertions(+), 1082 deletions(-)
```

### Changements Implementes
1. **SetTenantSession.php** : terminate() + resetTenantContext() + supportsTenantSession()
2. **UserVehicleAccessScope.php** : Fix groupement OR sur end_datetime
3. **EnterprisePermissionMiddleware.php** : Deny by default en production
4. **routes/api.php** : Middleware tenant.session + validation webhook stricte
5. **VehicleIndex.php** : Cache brands scope par org_id + analytics sans scope user
6. **Kernel.php** : Job RefreshAssignmentStatsMaterializedView toutes les 15 min
7. **Tests** : Conversion Pest -> PHPUnit pour compatibilite

### Tests Phase 0 Valides
- MultiTenantRLSTest: 1 test passe
- VehicleScopeTest: 1 test passe
- ApiVehicleShowTest: 1 test passe
- WebhookMileageTest: 2 tests passes
- CreateRepairRequestTest: 9 tests passes
- **Total: 14 tests, 30 assertions - 100% succes**

### Lecons Apprises Phase 0

1. **Configuration Spatie Permission Multi-tenant**
   - Probleme : Les tests existants ne configurent pas correctement organization_id dans model_has_roles
   - Impact : AssignmentManagementTest et autres echouent avec QueryException (contrainte unique)
   - Solution : Ajouter a Phase 1 la correction du setup des tests

2. **Conversion Pest -> PHPUnit**
   - Les tests Pest utilisent beforeEach() qui n est pas compatible PHPUnit
   - Migration vers setUp() + class TestCase necessaire
   - Pattern a suivre : voir CreateRepairRequestTest.php apres correction

3. **Deny by Default**
   - ATTENTION : Les routes non mappees dans routePermissionMap sont maintenant refusees en prod
   - Verifier que toutes les routes utilisees sont mappees avant deploiement

---

## Phase 1.5 : Sécurisation des Acquis (Anti-Régression) [PRIORITÉ ABSOLUE]

Avant toute modification ultérieure, nous verrouillons l'état actuel des interfaces critiques.

### 1.5.1 Véhicules (Livewire Component)
- **Fichier critique** : `resources/views/livewire/admin/vehicles/vehicle-index.blade.php`
- **Controller** : `App\Livewire\Admin\Vehicles\VehicleIndex`
- **Elements à vérifier** :
    - Presence de `x-page-analytics-grid` (Dashboard)
    - Presence de `{{-- BULK ACTIONS FLOATING MENU - Enterprise Grade --}}`
    - Presence de `x-slim-select` dans les filtres
    - Pas de `@extends` dans le fichier blade (il doit user `->extends()` cêté PHP)

### 1.5.2 Affectations (Livewire Component)
- **Fichier critique** : `resources/views/livewire/admin/assignments/assignment-index.blade.php`
- **Elements à vérifier** :
    - Header "ULTRA-PRO DESIGN"
    - Modale de fin d'affectation avec `SlimSelect`
    - Design "Enterprise" des badges de statut

---

## Phase 1 : Validation + Correction Tests (A FAIRE)

### 1.1 Valider les migrations Phase 0/1 existantes

Les migrations suivantes ont ete creees mais doivent etre validees :
- `2026_01_21_000000_add_trigram_indexes_vehicles.php`
- `2026_01_21_010000_fix_assignment_stats_refresh.php`
- `2026_01_21_020000_ensure_driver_license_categories.php`
- `2026_01_21_021000_ensure_vehicles_is_archived.php`
- `2026_01_21_030000_add_payment_due_date_to_vehicle_expenses.php`

Commande de validation :
```bash
docker compose exec -u zenfleet_user php php artisan migrate:status
docker compose exec database psql -U DB_USERNAME -c "SELECT indexname FROM pg_indexes WHERE tablename = 'vehicles';"
```

### 1.2 Corriger les tests existants defaillants

Fichiers a corriger (setup Spatie Permission) :
- tests/Feature/AssignmentManagementTest.php
- tests/Feature/Admin/OrganizationTableTest.php
- tests/Feature/Admin/OrganizationTest.php
- tests/Feature/Admin/VehicleEnterpriseTest.php
- tests/Feature/Assignment/CreateAssignmentTest.php
- tests/Feature/ExpenseManagementTest.php

Pattern de correction (voir CreateRepairRequestTest.php) :
```php
protected function setUp(): void
{
    parent::setUp();
    
    $permissionRegistrar = app(PermissionRegistrar::class);
    $permissionRegistrar->forgetCachedPermissions();
    
    $this->organization = Organization::factory()->create();
    app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
    $permissionRegistrar->setPermissionsTeamId($this->organization->id);
    
    $role = Role::firstOrCreate([
        'name' => 'Admin',
        'guard_name' => 'web',
        'organization_id' => $this->organization->id, // IMPORTANT
    ]);
    
    // Apres assignRole, mettre a jour model_has_roles
    DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', User::class)
        ->update(['organization_id' => $this->organization->id]);
}
```

### 1.3 Ajouter routes manquantes au mapping

Verifier et ajouter les routes non mappees :
```bash
docker compose exec -u zenfleet_user php php artisan route:list --name=admin | grep -v "mapped"
```

### 1.4 Tests de validation Phase 1

Objectif : 100% des tests passent

```bash
docker compose exec -u zenfleet_user php php artisan test --testsuite=Feature
docker compose exec -u zenfleet_user php php artisan test --testsuite=Unit
```

---

## Phase 2 : Refactorisation & Tests (Semaine 2-3)

### 2.1 Split VehicleController

Objectif : decouper le controller pour SRP.
Fichiers :
- app/Http/Controllers/Admin/VehicleController.php
- nouveaux controllers ou services

### 2.2 Unifier les permissions

Probleme : naming heterogene.
Decision : adopter module.action (ex: vehicles.create).

Fichiers :
- database/seeders/PermissionSeeder.php
- database/seeders/EnterprisePermissionsSeeder.php
- app/Policies/*
- app/Http/Middleware/EnterprisePermissionMiddleware.php

Note : prevoir migration de donnees pour renommer les permissions existantes.

---

## Phase 3 : UX/Accessibilite + Perf Livewire (Semaine 4+)
... (rest inchangé)
