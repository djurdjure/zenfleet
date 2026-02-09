# Etat Refactoring Graph - ZenFleet

Date de reference: 2026-02-09
Auteur: Codex (synthese technique projet)
Perimetre: standardisation graphique (charts), contrat payload unifie, cache analytics multi-tenant, exports analytics, stabilisation Livewire.

---

## 1) Objectif du document

Ce document sert de point de situation officiel pour:
- savoir exactement ce qui a ete livre sur le chantier "refactoring graph"
- identifier les ecarts restants avant cloture du chantier
- situer l'avancement par phase par rapport au plan `recommandation_graph.md`
- fournir une base de validation commune avant les prochaines evolutions

---

## 2) Resume executif

Le refactoring graphique a atteint un niveau avance et exploitable en production de dev:
- contrat `payload` unifie applique sur tous les widgets `x-charts.widget` recenses
- couche backend analytics renforcee (cache versionne + invalidation sur ecritures)
- endpoints d'export status analytics (CSV/PDF) implementes et testes
- stabilisation de l'integration Livewire/Charts confirmee (plus d'usage heterogene des props legacy sur les widgets recenses)

Etat global estime: **Phase 1 complete + Phase 2 quasi complete (~85-90%)**.

---

## 3) Chronologie des livraisons (commits clefs)

### 3.1 Base de convergence charts (historique recent)

- `5e311f4` `feat(charts): unify dashboard charts with apex lifecycle and remove cdn`
- `c802079` `feat(charts): standardize blade chart widget across dashboards`
- `a976da6` `feat(charts): complete expense analytics widget migration and livewire sync`
- `0a368e5` `refactor(charts): stabilize layout cache and remove legacy public chart init`
- `3068aae` `feat(charts): normalize theme and payload contract`

Impact: fondations du design system charts + suppression progressive des heterogeneites d'initialisation.

### 3.2 Refactoring analytics recent (lot actuel)

- `5c8a9de` `feat(analytics): unify chart payload contract and multi-tenant cache`
  - `ChartPayloadFactory`
  - normalisation payload dashboard status
  - cache TTL config
  - tests API status initiaux

- `57415d5` `feat(analytics): versioned cache invalidation and status exports`
  - `AnalyticsCacheVersion` (version de cache par module + org)
  - invalidation cache sur `VehicleExpense`, `MaintenanceOperation`, `StatusHistory`
  - exports CSV/PDF status analytics
  - migration supplementaire des dashboards admin/maintenance vers `payload`

- `f1f221c` `refactor(charts): unify expense analytics widgets to payload contract`
  - conversion des 6 widgets expense analytics Livewire vers `:payload`

---

## 4) Details techniques de ce qui a ete fait

## 4.1 Standardisation du contrat de donnees chart

### Contrat cible

Le widget `resources/views/components/charts/widget.blade.php` est pilote via un `payload` standard:
- `meta` (source, periode, filtres, contexte tenant)
- `chart` (id, type, height, ariaLabel)
- `labels`
- `series`
- `options`

### Couverture actuelle des ecrans recenses

- `resources/views/admin/dashboard.blade.php` -> `payload` OK
- `resources/views/admin/analytics/status-dashboard.blade.php` -> `payload` OK
- `resources/views/admin/maintenance/dashboard-enterprise.blade.php` -> `payload` OK
- `resources/views/livewire/admin/vehicle-expenses/expense-analytics.blade.php` -> `payload` OK (6 widgets)

Verification effectuee: toutes les occurrences `x-charts.widget` detectees passent par `:payload`.

## 4.2 Renforcement cache analytics multi-tenant

### Mecanisme ajoute

Fichier: `app/Support/Analytics/AnalyticsCacheVersion.php`

Fonctions:
- `current(module, organization_id)` -> version active
- `bump(module, organization_id)` -> incremente la version

Principe:
- les cles cache incluent `v:{version}`
- toute ecriture metier critique incremente la version
- invalidation logique immediate sans purge globale aggressive

### Services raccordes

- `app/Services/ExpenseAnalyticsService.php`
- `app/Services/Maintenance/MaintenanceService.php`
- `app/Http/Controllers/Admin/StatusAnalyticsController.php` (API daily stats)

## 4.3 Invalidation cache sur ecritures metier

Hooks model events ajoutes:
- `app/Models/VehicleExpense.php`: `created/updated/deleted/restored`
- `app/Models/MaintenanceOperation.php`: `created/updated/deleted/restored`
- `app/Models/StatusHistory.php`: `created/updated/deleted`

Effet:
- coherent avec logique "event-driven cache freshness"
- evite incoherences entre donnees CRUD et dashboards

## 4.4 Exports status analytics

Controleur: `app/Http/Controllers/Admin/StatusAnalyticsController.php`

Ajouts:
- `exportCsv(Request $request)`
- `exportPdf(Request $request)`

Vue PDF:
- `resources/views/admin/analytics/exports/status-report.blade.php`

Amelioration fonctionnelle ajoutee pendant stabilisation:
- l'historique recent utilise la periode filtree (`start_date`, `end_date`) dans dashboard + exports

## 4.5 Gouvernance tests

Tests feature mis a jour:
- `tests/Feature/Admin/StatusAnalyticsApiTest.php`
  - payload normalise API daily stats
  - isolation organisation
  - export CSV
  - export PDF

Tests valides sur le lot:
- `StatusAnalyticsApiTest` PASS
- `ExpenseAnalyticsChartsTest` PASS
- `ChartsWidgetComponentTest` PASS
- build frontend (`yarn build`) PASS

---

## 5) Fichiers principaux touches (refactoring graph)

Backend:
- `app/Support/Analytics/ChartPayloadFactory.php`
- `app/Support/Analytics/AnalyticsCacheVersion.php`
- `app/Http/Controllers/Admin/StatusAnalyticsController.php`
- `app/Services/ExpenseAnalyticsService.php`
- `app/Services/Maintenance/MaintenanceService.php`
- `app/Models/VehicleExpense.php`
- `app/Models/MaintenanceOperation.php`
- `app/Models/StatusHistory.php`
- `config/analytics.php`

Frontend/Views:
- `resources/views/components/charts/widget.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/analytics/status-dashboard.blade.php`
- `resources/views/admin/maintenance/dashboard-enterprise.blade.php`
- `resources/views/livewire/admin/vehicle-expenses/expense-analytics.blade.php`
- `resources/views/admin/analytics/exports/status-report.blade.php`

Tests:
- `tests/Feature/Admin/StatusAnalyticsApiTest.php`

---

## 6) Manquements restants et points a verifier

## 6.1 Manquements techniques mineurs (a traiter)

1. Commentaires routes obsoletes:
- `routes/analytics.php` contient encore "a implementer" alors que les endpoints existent.

2. Gouvernance test exports:
- dans les tests exports, middleware `Authorize` est desactive pour le scenario technique.
- action recommandee: ajouter un test explicite avec policy active + permissions valides.

3. Qualite suite de tests globale:
- nombreux warnings PHPUnit "metadata in doc-comments deprecated".
- pas bloquant aujourd'hui, mais a corriger avant migration PHPUnit 12 stricte.

4. Taille bundle charts:
- chunk `charts` important (ordre de grandeur > 500KB minifie selon build recent).
- optimisation possible: lazy load module charts selon pages analytics.

5. Scope tenant "Super Admin":
- certains calculs se reposent sur global scopes model.
- a verifier metier que l'attendu Super Admin (global vs org cible) est bien conforme a la politique securite voulue.

6. Uniformisation source payload:
- une partie des payloads est construite en Blade (pragmatique et fonctionnel).
- target enterprise recommandee: centraliser encore davantage cote service/controller pour auditabilite.

## 6.2 Verifications fonctionnelles recommandees (UAT)

1. Dashboard status:
- filtres dates + entity_type
- coherence chiffres vs export CSV/PDF
- controles permission export

2. Dashboard maintenance:
- rendu charts apres navigation/refresh
- coherence de palette/theme

3. Expense analytics Livewire:
- changements rapides de filtres
- switch de mode (`dashboard`, `tco`, `trends`, `suppliers`, `budgets`)
- verification absence de re-init defectueux

4. Multi-tenant:
- test comparatif sur 2 organisations
- verification isolation stricte en API et exports

---

## 7) Positionnement par phase (plan recommandation_graph)

## Phase 1 - Stabilisation (statut: **Complete**)

Objectifs:
- convergence bibliotheque charts
- suppression heterogeneite majeure d'initialisation
- stabilisation UI/Livewire

Etat:
- atteint sur le perimetre audite.

## Phase 2 - Normalisation (statut: **Quasi complete**)

Objectifs:
- contrat payload unifie
- couverture dashboards principaux
- tests API/feature de base

Etat:
- contrat payload applique sur tous widgets recenses
- tests critiques en place
- reste a finaliser:
  - nettoyage commentaires/routes
  - renforcement tests d'autorisation exports
  - migration progressive des doc-comments PHPUnit vers attributs

## Phase 3 - Optimisation (statut: **Initiee, non cloturee**)

Objectifs:
- perf fine (bundle + requetes + cache tuning)
- observabilite
- optimisation data volume

Etat:
- demarrage avec cache versionne/invalidation event-driven
- reste principal:
  - profiling frontend charts (lazy load + split plus fin)
  - instrumentation runtime (temps rendu par ecran)
  - eventuellement pre-aggregation/materialized views sur KPI lourds

---

## 8) Niveau de risque actuel

Risque de regression fonctionnelle:
- **Faible a modere** sur perimetre touche (tests cibles passent, build OK)

Risque de dette technique residuelle:
- **Modere** (warnings PHPUnit + optimisation perf non finalisee)

Risque securite multi-tenant:
- **Sous controle**, mais validation complementaire recommandee pour scenarios Super Admin et policies exports.

---

## 9) Definition de "Done" proposee pour cloturer ce chantier

Le chantier "refactoring graph" sera considere complet quand:

1. Tous les ecrans charts cibles sont sous contrat `payload` (fait sur perimetre recense)
2. Plus aucun commentaire/function obsolete "a implementer" sur routes analytics
3. Tests authorization exports couverts sans bypass middleware
4. Plan de traitement warnings PHPUnit formalise (ou execute)
5. Mini campagne UAT multi-tenant signee (status + maintenance + expense analytics)
6. Baseline perf capturee (temps rendu dashboard + poids JS par page)

---

## 10) Prochaine etape recommandee (ordre de priorite)

1. Hardening tests et policies:
- ajouter tests d'autorisation exports avec roles/permissions reels
- nettoyer `routes/analytics.php` (commentaires legacy)

2. Stabilisation qualite globale:
- lancer conversion progressive des tests doc-comments vers attributs PHPUnit

3. Optimisation frontend:
- lazy load chunk charts sur routes analytics uniquement
- verifier baisse poids initial pages non analytics

4. Validation metier:
- session UAT courte sur 2 tenants + 1 super admin

---

## 11) Conclusion

Le refactoring graph est deja a un niveau enterprise solide sur son coeur:
- standard de donnees unifie
- comportement cache robuste
- couverture ecrans charts principale et coherent
- validation technique outillee

Les ecarts restants sont principalement de finition qualite/gouvernance et d'optimisation, pas de refonte structurelle.

---

## 12) Correctifs RBAC multi-tenant et jeu de donnees de validation (2026-02-09)

### 12.1 Correctifs appliques (hors coeur charts, mais critiques pour la validation transverse)

1. Provisioning roles multi-tenant durci:
- roles globaux et roles organisation verifies/synchronises via `OrganizationRoleProvisioner`
- suppression du comportement ambigu autour de `Super Admin` scope tenant/global
- securisation de l'affectation des roles a la creation/modification utilisateur (contexte organisation cible)

2. Correctif trigger PostgreSQL depenses:
- nouveau patch DB: `database/migrations/2026_02_09_150000_fix_expense_audit_trigger_user_resolution.php`
- cause racine: fonction `log_expense_changes()` lisait `NEW.updated_by` alors que `vehicle_expenses.updated_by` n'existe pas
- correction: resolution utilisateur via `to_jsonb(NEW/OLD)` (robuste aux evolutions de schema)

3. Commande de seed enterprise-grade creee:
- `app/Console/Commands/SeedValidationFleetDataset.php`
- commande: `php artisan zenfleet:seed-validation-dataset`
- objectif: dataset coherent multi-modules pour valider affichages/calculs avant poursuite du refactoring

### 12.2 Donnees de validation injectees (organisation de test)

Organisation:
- `#12 - ZenFleet Validation Lab`

Comptes crees:
- 1 `Admin`
- 1 `Gestionnaire Flotte`
- 1 `Superviseur`
- 3 `Chauffeurs`

Donnees metier injectees:
- 10 vehicules
- 3 profils chauffeurs relies aux users chauffeur
- 4 affectations (3 actives + 1 terminee)
- 20 depenses vehicules
- 3 operations de maintenance
- 30 releves kilometriques
- 3 budgets de depenses mensuels
- historique de statuts (vehicules + chauffeurs) alimente

### 12.3 Verifications executees

Verification SQL directe (DB) par organisation:
- `users: 6`
- `vehicles: 10`
- `drivers: 3`
- `assignments: 4`
- `vehicle_expenses: 20`
- `maintenance_operations: 3`
- `vehicle_mileage_readings: 30`
- `expense_budgets: 3`
- `model_has_roles: 6`

Commande re-executable:
- re-run `zenfleet:seed-validation-dataset` confirme (idempotence fonctionnelle sur org #12)

### 12.4 Points d'attention restants

1. Plusieurs colonnes analytics/finance sont en colonnes generees PostgreSQL (ex: `vehicle_expenses.tva_amount`, `expense_budgets.remaining_amount`, `expense_budgets.status`):
- ne pas les alimenter explicitement dans les seeders/services d'ecriture
- laisser PostgreSQL calculer pour eviter erreurs `cannot insert a non-DEFAULT value into column ... generated column`

2. `Vehicle` utilise un global scope d'acces utilisateur:
- pour scripts CLI/seed, preferer `withoutGlobalScopes()` quand necessaire pour eviter faux-negatifs de lecture/`updateOrCreate`

3. A planifier en phase de stabilisation:
- ajouter un test d'integration DB qui valide explicitement `log_expense_changes()` sur INSERT/UPDATE/DELETE de `vehicle_expenses`
- ajouter une suite smoke test CLI (seed + health-check + dashboards API)
