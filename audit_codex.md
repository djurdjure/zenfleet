# Audit ZenFleet - Rapport Codex (actionnable)

Contexte
- Audit base sur lecture du code/migrations/vues. Pas de migrations ni tests executes.
- Objectif: fournir un plan d'action incremental, priorise, avec references precises.

## Priorites globales (du plus urgent au moins critique)
1) Isolation multi-tenant/RLS coherente sur web + API + jobs.
2) Securite des scopes et permissions (fuite vehicules, routes non mappees, naming heterogene).
3) Migrations PostgreSQL en conflit (doublons, fonctions IMMUTABLE avec NOW, trigger refresh MV).
4) Bugs fonctionnels connus (filtre date, job auto-termination, API vehicle show, webhook mileage).
5) Performance Livewire (listes non paginees, ILIKE sans indexes, caches non scopes).
6) Refactorisation progressive des controllers XXL et normalisation des services.

---

## 1) Architecture & Conventions

Constats
- Gravite: Moyenne | Impact: Maintenabilite | Effort: M
  - Action recommandee: definir un schema d'architecture cible (Controller -> Service/Action -> Repository/Query) et l'appliquer aux modules critiques (Vehicles, Assignments, Maintenance).
  - References: app/Http/Controllers/Admin/VehicleController.php, app/Services/*, app/Livewire/*.

- Gravite: Moyenne | Impact: Qualite | Effort: S
  - Action recommandee: documenter la couche multi-tenant (scopes Eloquent + RLS + policies) et la rendre explicite dans un doc unique.
  - References: app/Models/Concerns/BelongsToOrganization.php, app/Http/Middleware/SetTenantSession.php, database/migrations/2025_01_20_102000_create_multi_tenant_system.php.

- Gravite: Moyenne | Impact: Maintenabilite | Effort: S
  - Action recommandee: supprimer/aligner les middleware alias inexistants pour eviter confusion.
  - References: app/Http/Kernel.php (aliases same.organization, super.admin.only sans classes correspondantes).

---

## 2) Qualite du code (Laravel/PHP)

Constats
- Gravite: Haute | Impact: Maintenabilite | Effort: L
  - Action recommandee: decouper VehicleController (import/export, analytics, validation, logging) en services et actions.
  - References: app/Http/Controllers/Admin/VehicleController.php.

- Gravite: Moyenne | Impact: Qualite | Effort: M
  - Action recommandee: centraliser la logique de disponibilite vehicule/chauffeur (eviter divergences entre Livewire et Traits).
  - References: app/Traits/ResourceAvailability.php, app/Livewire/Admin/AssignmentWizard.php.

- Gravite: Moyenne | Impact: Qualite | Effort: M
  - Action recommandee: harmoniser les conventions de status (VehicleStatus/DriverStatus) via repository ou enum unique.
  - References: app/Models/VehicleStatus.php, app/Models/DriverStatus.php, app/Enums/*.

- Gravite: Moyenne | Impact: Qualite | Effort: S
  - Action recommandee: eviter Auth::check() dans les scopes globaux pour garantir un comportement deterministe en jobs/CLI.
  - References: app/Models/Concerns/BelongsToOrganization.php, app/Models/Scopes/UserVehicleAccessScope.php.

---

## 3) Base de donnees & Multi-tenancy (PostgreSQL 18)

Constats critiques
- Gravite: Critique | Impact: Securite | Effort: M
  - Action recommandee: appliquer l'injection RLS a tous les contextes (web + api + jobs), et resetter les variables RLS par requete.
  - References: app/Http/Middleware/SetTenantSession.php, app/Http/Kernel.php.
  - Bug probable: fuite inter-tenant si une connexion DB conserve un ancien app.current_organization_id.
    - Repro: requete A (org 1) puis requete B (org 2) sur meme worker/connexion.
    - Correction: SET LOCAL + reset explicite dans middleware + usage DB::afterExecuting ou terminate.
    - Test: test API multi-tenant (2 users, 2 orgs) sur meme worker.

- Gravite: Haute | Impact: Fiabilite | Effort: M
  - Action recommandee: resoudre les migrations dupliquees (ex: driver_statuses) et rendre les migrations idempotentes.
  - References: database/migrations/2025_01_26_120000_create_driver_statuses_table.php, database/migrations/2025_06_07_231226_create_driver_statuses_table.php.
  - Bug probable: migrate:fresh casse en clean install.
    - Correction: supprimer doublon ou fusionner.
    - Test: pipeline migrate:fresh.

- Gravite: Haute | Impact: Fiabilite/Perf | Effort: M
  - Action recommandee: corriger fonction assignment_computed_status declaree IMMUTABLE mais dependante de NOW(); et retirer refresh MV dans trigger.
  - References: database/migrations/2025_01_20_000000_add_gist_constraints_assignments.php.
  - Bug probable: erreurs en prod lors d'insert/update (REFRESH MATERIALIZED VIEW CONCURRENTLY interdit en trigger).
    - Correction: refresh MV via job schedule.
    - Test: insertion assignment + verif absence d'erreur.

- Gravite: Moyenne | Impact: Securite | Effort: M
  - Action recommandee: etendre RLS aux tables tenant-sensibles non couvertes (expenses, mileage, documents, etc) ou ajouter scopes globaux.
  - References: database/migrations/2025_01_20_102000_create_multi_tenant_system.php, app/Models/VehicleMileageReading.php.

- Gravite: Moyenne | Impact: Fiabilite | Effort: S
  - Action recommandee: assurer extension pgcrypto pour gen_random_uuid().
  - References: database/migrations/2025_01_20_100000_create_comprehensive_audit_logs.php.

Recommandations d'indexes (performances)
- Gravite: Moyenne | Impact: Performance | Effort: M
  - Action recommandee: trigram GIN sur vehicles.registration_plate, vehicles.brand, vehicles.model pour ILIKE.
  - References: app/Livewire/Admin/Vehicles/VehicleIndex.php.

- Gravite: Moyenne | Impact: Performance | Effort: M
  - Action recommandee: index composite drivers(organization_id, last_name, first_name) + trigram license_number.
  - References: app/Livewire/Admin/AssignmentWizard.php.

---

## 4) Securite

Top risques
- Gravite: Critique | Impact: Securite | Effort: M
  - Action recommandee: RLS session au niveau API et reset de session sur fin de requete.
  - References: app/Http/Middleware/SetTenantSession.php, routes/api.php.

- Gravite: Haute | Impact: Securite | Effort: S
  - Action recommandee: corriger le scope vehicule qui accepte des assignments d'autres chauffeurs (OR mal groupe).
  - References: app/Models/Scopes/UserVehicleAccessScope.php.
  - Bug probable:
    - Repro: chauffeur A voit un vehicule affecte au chauffeur B si end_datetime >= now.
    - Correction: regrouper orWhere avec driver_id dans la sous-requete.
    - Test: feature test d'acces vehicule par chauffeur.

- Gravite: Haute | Impact: Securite | Effort: M
  - Action recommandee: interdire l'acces par defaut aux routes non mappees dans EnterprisePermissionMiddleware.
  - References: app/Http/Middleware/EnterprisePermissionMiddleware.php.

- Gravite: Haute | Impact: Securite | Effort: M
  - Action recommandee: unifier le naming permissions (spaces vs underscores vs dots).
  - References: database/seeders/PermissionSeeder.php, database/seeders/EnterprisePermissionsSeeder.php, app/Policies/AssignmentPolicy.php.

- Gravite: Moyenne | Impact: Securite | Effort: M
  - Action recommandee: scoper les caches par organization_id pour eviter fuite inter-tenant.
  - References: app/Livewire/Admin/Vehicles/VehicleIndex.php.

- Gravite: Moyenne | Impact: Securite | Effort: M
  - Action recommandee: securiser webhooks (token par org, verification stricte, bypass scopes globaux + verification manuelle org).
  - References: routes/api.php.

---

## 5) Performance & Scalabilite

Constats
- Gravite: Moyenne | Impact: Performance | Effort: M
  - Action recommandee: eviter get()->filter() sur conflits d'affectations; utiliser SQL (range overlap) ou query dedicated.
  - References: app/Services/OverlapCheckService.php.

- Gravite: Moyenne | Impact: Performance | Effort: S
  - Action recommandee: limiter AssignmentWizard (pagination/lazy) pour lists vehicules/chauffeurs.
  - References: app/Livewire/Admin/AssignmentWizard.php.

- Gravite: Moyenne | Impact: Performance | Effort: M
  - Action recommandee: scoper analytics a l'organisation (count global -> count scoped) et reduire requetes.
  - References: app/Livewire/Admin/Vehicles/VehicleIndex.php.

KPIs a mesurer
- p95 temps rendu Livewire index < 500ms.
- p95 requetes recherche ILIKE < 150ms.
- taille payload Livewire < 200KB.
- taux echec jobs < 1%.

---

## 6) UI/UX & Frontend (Livewire + Blade + Tailwind + Alpine + SlimSelect)

Constats
- Gravite: Moyenne | Impact: UX/Performance | Effort: M
  - Action recommandee: pagination/virtualisation des listes dans AssignmentWizard.
  - References: app/Livewire/Admin/AssignmentWizard.php.

- Gravite: Moyenne | Impact: Accessibilite | Effort: S
  - Action recommandee: ajouter aria-expanded, aria-controls sur menus pliants; focus visible sur boutons icon-only.
  - References: resources/views/layouts/admin/catalyst.blade.php.

- Gravite: Moyenne | Impact: Accessibilite | Effort: S
  - Action recommandee: ajout aria-live pour toasts et erreurs de validation.
  - References: resources/views/layouts/admin/catalyst.blade.php, resources/js/admin/app.js.

- Gravite: Basse | Impact: Maintenabilite | Effort: S
  - Action recommandee: supprimer TomSelect legacy et unifier SlimSelect.
  - References: app/View/Components/TomSelect.php, resources/js/components/zenfleet-select.js.

- Gravite: Basse | Impact: Performance | Effort: S
  - Action recommandee: auto-heberger Iconify ou deferer le chargement.
  - References: resources/views/layouts/admin/catalyst.blade.php.

---

## 7) Bugs & Fiabilite

Bugs probables
- Gravite: Haute | Impact: Fiabilite | Effort: S
  - Action recommandee: ajouter relation organization() ou retirer with('organization').
  - References: app/Jobs/AutoTerminateExpiredAssignmentsJob.php, app/Models/Assignment.php.
  - Repro: execution job -> exception relation inconnue.
  - Test a ajouter: feature test job auto-termination.

- Gravite: Moyenne | Impact: Produit | Effort: S
  - Action recommandee: corriger filtre date first_registration_date vers acquisition_date ou ajouter colonne.
  - References: app/Livewire/Admin/Vehicles/VehicleIndex.php.
  - Repro: filtrer par date -> SQL error.
  - Test a ajouter: Livewire filter test.

- Gravite: Moyenne | Impact: Produit | Effort: S
  - Action recommandee: corriger parametres route API vehicles (mismatch vehicle/vehicleId).
  - References: routes/api.php.
  - Repro: GET /api/v1/vehicles/{id} -> 404/500.
  - Test a ajouter: API show test.

- Gravite: Moyenne | Impact: Produit | Effort: M
  - Action recommandee: webhook mileage update doit bypasser scope global et valider org via token.
  - References: routes/api.php, app/Models/Scopes/UserVehicleAccessScope.php.
  - Repro: webhook n'update jamais sans auth.
  - Test a ajouter: webhook integration test.

---

## 8) Roadmap priorisee (A -> B -> C)

Phase 0 (24-48h) - securite critique + quick wins
- Objectif: isolation multi-tenant sur web/api/jobs
  - Fichiers: app/Http/Middleware/SetTenantSession.php, app/Http/Kernel.php
  - Risques: compat API
  - Tests: multi-tenant API/web
  - Critere: aucune requete cross-tenant

- Objectif: corriger scope vehicule (fuite)
  - Fichiers: app/Models/Scopes/UserVehicleAccessScope.php
  - Tests: acces chauffeur
  - Critere: chauffeur ne voit que ses vehicules

- Objectif: corriger bugs rapides (filtre date, job, route API)
  - Fichiers: app/Livewire/Admin/Vehicles/VehicleIndex.php, app/Jobs/AutoTerminateExpiredAssignmentsJob.php, routes/api.php
  - Critere: tests passent

Phase 1 (Semaine 1) - perf + hardening multi-tenant
- Objectif: indexes trigram + scopes caches
  - Fichiers: nouvelles migrations + app/Livewire/Admin/Vehicles/VehicleIndex.php
  - Critere: p95 recherche < 150ms

- Objectif: nettoyer migrations conflit (driver_statuses, constraints)
  - Fichiers: database/migrations/*
  - Critere: migrate:fresh OK

Phase 2 (Semaine 2-3) - refactors + tests
- Objectif: split VehicleController
  - Fichiers: app/Http/Controllers/Admin/VehicleController.php
  - Tests: CRUD + import/export

- Objectif: unifier permissions
  - Fichiers: database/seeders/*Permissions*, app/Policies/*, app/Http/Middleware/EnterprisePermissionMiddleware.php

Phase 3 (Semaine 4+) - ameliorations produit
- Objectif: UX/accessibilite
  - Fichiers: resources/views/layouts/admin/catalyst.blade.php

- Objectif: Livewire performance AssignmentWizard
  - Fichiers: app/Livewire/Admin/AssignmentWizard.php

---

## Annexes - References rapides
- Multi-tenant trait: app/Models/Concerns/BelongsToOrganization.php
- RLS injection: app/Http/Middleware/SetTenantSession.php
- Permissions middleware: app/Http/Middleware/EnterprisePermissionMiddleware.php
- Vehicle scope: app/Models/Scopes/UserVehicleAccessScope.php
- Vehicle index Livewire: app/Livewire/Admin/Vehicles/VehicleIndex.php
- Assignment constraints: database/migrations/2025_01_20_000000_add_gist_constraints_assignments.php
- Multi-tenant system: database/migrations/2025_01_20_102000_create_multi_tenant_system.php
- Audit logs: database/migrations/2025_01_20_100000_create_comprehensive_audit_logs.php
