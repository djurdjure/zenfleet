# Plan de Correction Expert - ZenFleet Audit Codex

Date de mise a jour : 2026-01-21
Base : audit_codex.md
Contexte : Dev_environnement.md (Laravel 12 + Livewire 3 + PostgreSQL 18 + Docker)
Statut : Plan revise et pret pour validation

---

## Resume Executif

Le plan couvre la majorite des constats de l audit, mais il a ete corrige pour etre operationnel en production :
- RLS ajustee pour eviter les effets de bord du SET LOCAL sans transaction.
- Ordre des middlewares corrige pour l API (RLS apres auth:sanctum).
- Bugs manquants ajoutes (API vehicle show, webhook mileage update).
- Migrations traitees en mode prod-safe (pas de fusion destructive).
- Suivi d execution ajoute pour pilotage.

---

## Mecanisme de suivi de l execution (obligatoire)

Regle : chaque action doit avoir un identifiant, un statut, une date et une note.

Statuts autorises : TODO, IN_PROGRESS, DONE, BLOCKED

Phase courante : Phase 2
Etat global : IN_PROGRESS
Derniere mise a jour : 2026-01-21

Tableau de suivi

| ID | Phase | Action | Statut | Owner | Date cible | Derniere maj | Notes |
|----|-------|--------|--------|-------|------------|--------------|-------|
| P0-01 | 0 | RLS session web + API (SET + RESET) | DONE | | 2026-01-21 | 2026-01-21 | SetTenantSession + reset defensif |
| P0-02 | 0 | RLS apres auth:sanctum pour API | DONE | | 2026-01-21 | 2026-01-21 | tenant.session sur routes API protegees |
| P0-03 | 0 | Fix scope vehicule (OR mal groupe) | DONE | | 2026-01-21 | 2026-01-21 | UserVehicleAccessScope corrige |
| P0-04 | 0 | Fix API vehicle show param | DONE | | 2026-01-21 | 2026-01-21 | Param route aligne |
| P0-05 | 0 | Fix webhook mileage update | DONE | | 2026-01-21 | 2026-01-21 | Token map + scope bypass |
| P0-06 | 0 | Fix routes non mappees (deny by default) | DONE | | 2026-01-21 | 2026-01-21 | Deny hors local/dev |
| P0-07 | 0 | Tests RLS + scope vehicule + API show + webhook | DONE | | 2026-01-21 | 2026-01-21 | 4 tests ajoutes |
| P1-01 | 1 | Indexes trigram + drivers composite | DONE | | 2026-01-21 | 2026-01-21 | Migration 2026_01_21_000000_add_trigram_indexes_vehicles |
| P1-02 | 1 | Caches scoper par organization | DONE | | 2026-01-21 | 2026-01-21 | Cache depots + brands scoper org |
| P1-03 | 1 | Analytics scoper sans scope utilisateur | DONE | | 2026-01-21 | 2026-01-21 | Vehicle::withoutGlobalScope + org_id |
| P1-04 | 1 | Migrations driver_statuses prod-safe | DONE | | 2026-01-21 | 2026-01-21 | Guard create si table existe |
| P1-05 | 1 | Fonction IMMUTABLE corrigee | DONE | | 2026-01-21 | 2026-01-21 | assignment_computed_status STABLE + migration correctrice |
| P1-06 | 1 | Suppression trigger refresh MV + job planifie | DONE | | 2026-01-21 | 2026-01-21 | Trigger retire, job schedule toutes 15 min |
| P1-07 | 1 | Compatibilite tests SQLite / PHPUnit | DONE | | 2026-01-21 | 2026-01-21 | Fix migration sanctions + conversion test Pest |
| P2-01 | 2 | Split VehicleController | TODO | | | | |
| P2-02 | 2 | Unifier naming permissions | TODO | | | | |
| P3-01 | 3 | Accessibilite ARIA | TODO | | | | |
| P3-02 | 3 | Perf AssignmentWizard (pagination/lazy) | TODO | | | | |

Journal d execution
- 2026-01-21 : Phase 0 terminee (RLS, scope vehicule, API show, webhook, deny unmapped, tests).
- 2026-01-21 : Phase 1 terminee (indexes, caches scoper, analytics, driver_statuses, MV refresh).
- 2026-01-21 : Fix tests (Pest -> PHPUnit, migrations SQLite, RefreshDatabase).

---

## Phase 0 : Securite Critique (24-48h)

### 0.1 RLS session : SET + RESET (web + API)

Probleme : SET LOCAL ne fonctionne qu a l interieur d une transaction. En autocommit, il est annule immediatement.
Decision : Utiliser SET (persistant) + RESET defensif en terminate. Ajouter un reset pour les requetes non authentifiees.

Fichier : app/Http/Middleware/SetTenantSession.php

Patch propose (extrait) :
- Utiliser SET pour app.current_user_id et app.current_organization_id.
- En cas de non-auth, RESET explicite des variables.
- Ajouter terminate() pour RESET defensif.

Note : La remise a zero doit etre garantie meme en cas d exception (try/finally si besoin).

### 0.2 RLS apres auth:sanctum pour API

Probleme : Ajouter SetTenantSession dans le groupe api avant auth:sanctum ne fonctionne pas (Auth::check() false).
Decision : Ajouter SetTenantSession au groupe de routes API protegees (apres auth:sanctum).

Fichiers : routes/api.php, app/Http/Kernel.php

### 0.3 Correction scope vehicule (OR mal groupe)

Probleme : le OR sur end_datetime n est pas groupe avec driver_id, fuite possible.
Fichier : app/Models/Scopes/UserVehicleAccessScope.php

Patch propose (extrait) :
- Regrouper whereNull(end_datetime) OR end_datetime >= now() dans un where() interne.

### 0.4 Fix API vehicle show (param mismatch)

Probleme : route utilise {vehicle} mais handler prend vehicleId.
Fichier : routes/api.php

Patch propose :
- Aligner le parametre de fonction sur {vehicle} et utiliser le model binding.

### 0.5 Fix webhook mileage update (scope + org validation)

Probleme : le scope global peut bloquer la mise a jour. La validation org est faible.
Fichiers : routes/api.php, app/Models/Scopes/UserVehicleAccessScope.php

Patch propose :
- Vehicle::withoutGlobalScopes() pour recuperer le vehicule par ID.
- Verifier organization_id via token associe a l org (a definir dans config).
- Rejeter si mismatch.

### 0.6 Routes non mappees dans EnterprisePermissionMiddleware

Probleme : une route non mappee est autorisee par defaut.
Decision : deny by default en prod, allow en local avec log.
Fichier : app/Http/Middleware/EnterprisePermissionMiddleware.php

### 0.7 Tests Phase 0

A creer
- tests/Feature/MultiTenantRLSTest.php
- tests/Feature/VehicleScopeTest.php
- tests/Feature/ApiVehicleShowTest.php
- tests/Feature/WebhookMileageTest.php

---

## Phase 1 : Performance + Hardening Multi-tenant (Semaine 1)

### 1.1 Indexes trigram ILIKE

Fichier a creer : database/migrations/2026_01_21_000000_add_trigram_indexes_vehicles.php
- Extension pg_trgm
- Index GIN trigram sur vehicles.registration_plate, vehicles.brand, vehicles.model
- Index composite drivers(organization_id, last_name, first_name)
- Index trigram drivers.license_number

### 1.2 Caches scopes par organization

Probleme : cache marques non scope.
Fichier : app/Livewire/Admin/Vehicles/VehicleIndex.php

Patch propose :
- Key cache par organization_id pour brands.

### 1.3 Analytics scoper sans scope utilisateur

Probleme : UserVehicleAccessScope peut fausser les analytics.
Fichier : app/Livewire/Admin/Vehicles/VehicleIndex.php

Patch propose :
- Vehicle::withoutGlobalScope(UserVehicleAccessScope::class)
- Filtrer explicitement par organization_id

### 1.4 Migrations driver_statuses prod-safe

Probleme : doublons migrations.
Decision prod-safe : ne pas fusionner des migrations deja appliquees.

Actions :
- Ajouter une nouvelle migration de reconciliation (checks + colonnes manquantes).
- Garder les anciennes migrations intactes.
- Pour clean install, documenter l ordre et valider migrate:fresh.

Fichiers : database/migrations/*driver_statuses*

### 1.5 Fonction IMMUTABLE corrigee

Fichier : database/migrations/2025_01_20_000000_add_gist_constraints_assignments.php
- assignment_computed_status doit etre STABLE, pas IMMUTABLE.

### 1.6 Supprimer trigger REFRESH MV CONCURRENTLY

Probleme : interdit dans trigger.
Solution : Job planifie.

Fichiers :
- database/migrations/2025_01_20_000000_add_gist_constraints_assignments.php
- app/Jobs/RefreshAssignmentStatsMaterializedView.php
- app/Console/Kernel.php

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

### 3.1 Accessibilite ARIA

Fichier : resources/views/layouts/admin/catalyst.blade.php
- Ajouter aria-expanded, aria-controls sur menus
- Ajouter aria-live sur toasts

### 3.2 Performance AssignmentWizard

Fichier : app/Livewire/Admin/AssignmentWizard.php
- Pagination/lazy loading
- Indicateurs wire:loading

---

## Validation et Commandes (Docker Compose uniquement)

Tests
- docker compose exec -u zenfleet_user php php artisan test
- docker compose exec -u zenfleet_user php php artisan migrate:fresh --seed

PostgreSQL
- docker compose exec database psql -U DB_USERNAME -c SELECT extname FROM pg_extension WHERE extname IN ('pg_trgm','btree_gist','pgcrypto');
- docker compose exec database psql -U DB_USERNAME -c SELECT 1 FROM vehicles WHERE registration_plate ILIKE '%ABC%';

---

## Demarrage de l execution

Ce plan est PRET mais ne doit pas etre execute sans validation.
Merci de confirmer avant lancement de la phase 0.
