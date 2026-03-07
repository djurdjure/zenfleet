# RAPPORT EXPERT TECHNIQUE ZENFLEET

Date: 2026-03-07  
Auteur: Audit technique automatisé (codebase complète)  
Portée: Architecture, stack, modules, sécurité multi-tenant, dette technique, feuille de route

---

# 1. VUE D'ENSEMBLE DU PROJET (ZenFleet)

ZenFleet est une plateforme SaaS de gestion de flotte multi-tenant construite sur Laravel 12 + Livewire 3, avec PostgreSQL 18/PostGIS, Redis, et un frontend Vite/Tailwind 4/Alpine.

## 1.1 Objectif métier couvert

- Gestion véhicules (cycle de vie, statuts, archivage, import/export)
- Gestion chauffeurs (profils, statuts, sanctions)
- Affectations véhicule-chauffeur avec contraintes métier
- Maintenance (opérations, planifications, alertes, calendrier, kanban)
- Kilométrage (relevés manuels/automatiques, statistiques, permissions granulaires)
- Dépenses véhicule (workflow, analytics, budgets, audit)
- Documents (catégories, échéances)
- Fournisseurs (gestion + scoring)
- Rôles & permissions RBAC (Spatie teams par organisation)
- Module demandes de réparation (double validation en cours de stabilisation)

## 1.2 Architecture générale

- Backend: monolithe Laravel modulaire avec services métier.
- Frontend: Blade + Livewire + Alpine, design system Tailwind unifié.
- Multi-tenant: isolation applicative + contexte DB PostgreSQL (variables de session SQL) + permissions scoped.
- Infra: Docker Compose (php, nginx, db, redis, node, scheduler, pdf-service).

## 1.3 Volumétrie technique actuelle (codebase)

- Fichiers versionnés: `1926`
- Contrôleurs: `59`
- Composants Livewire: `63`
- Modèles Eloquent: `55`
- Services métier: `36`
- Policies: `13`
- Migrations: `156`
- Seeders: `59`
- Vues Blade: `337`
- Routes enregistrées: `383` (dont `321` sous `/admin`)

---

# 2. ENVIRONNEMENT ET STACK TECHNIQUE (Ultra-détaillé)

## 2.1 Système / Exécution

- OS hôte (d’après documentation projet): Ubuntu/WSL2
- Runtime docker observé:
  - PHP: `8.3-fpm-alpine` (Dockerfile custom)
  - Nginx: `1.25-alpine`
  - PostgreSQL: `18` via `postgis/postgis:18-3.6-alpine`
  - Redis: `7-alpine`
  - Node: `20` (container dev)

## 2.2 Serveur Web et SSL

- Reverse proxy: Nginx (container dédié)
- Virtual host principal: `docker/nginx/zenfleet.conf`
- Port exposé: `80:80`
- Headers de sécurité présents:
  - `X-Frame-Options: SAMEORIGIN`
  - `X-XSS-Protection`
  - `X-Content-Type-Options`
- SSL/TLS non géré dans ce vhost local (pas de `listen 443` ni certificats dans cette conf).

## 2.3 Base de données

- SGBD principal: PostgreSQL 18 + PostGIS
- Configuration Docker DB: tuning agressif (`shared_buffers`, `work_mem`, `jit`, `pg_stat_statements`, etc.)
- Multi-tenant DB context:
  - middleware `SetTenantSession` injecte:
    - `SET app.current_user_id = ...`
    - `SET app.current_organization_id = ...`
- RLS: mécanisme partiellement prévu par contexte SQL + tests dédiés (fichier `routes/rls_test.php`, tests multi-tenant), mais l’isolation repose surtout sur scopes/policies/permissions.

### Sauvegarde / récupération (état observé)

- Présence de multiples dumps SQL (`backup_*`, `zenfleet_db_schema_*.sql`, dossiers `backups*`).
- Pas de stratégie unique standardisée documentée (rotation/chiffrement/restauration testée) dans un runbook opérationnel unique.

## 2.4 Langages / Frameworks

- PHP `^8.2` (runtime actuel 8.3)
- Laravel `^12.0` (réel: 12.28.1 selon README)
- Livewire `^3.0`
- JS ES modules + Alpine + Vite 6
- Tailwind CSS v4 (`@tailwindcss/vite`)

## 2.5 DevOps / orchestration

- Orchestrateur: Docker Compose
- Services:
  - `php`
  - `nginx`
  - `database`
  - `redis`
  - `node`
  - `scheduler`
  - `pdf-service`
- Scheduler:
  - service dédié exécutant `php artisan schedule:run` en boucle.
- Workers:
  - supervisord lance php-fpm + 2 workers queue (`queue:work`).

## 2.6 Écarts de configuration critiques

- `.env` runtime: PostgreSQL + Redis + queue redis (cohérent)
- `.env.example`: encore MySQL + file cache + sync queue (non aligné runtime)

---

# 3. DÉPENDANCES, BIBLIOTHÈQUES ET PLUGINS

## 3.1 Back-end (Composer)

### Dépendances runtime

| Nom | Version | Description / Rôle |
|---|---|---|
| php | ^8.2 | Runtime applicatif |
| barryvdh/laravel-dompdf | ^3.1 | Génération PDF côté PHP |
| blade-ui-kit/blade-icons | ^1.5 | Système d’icônes Blade |
| doctrine/dbal | ^3.9 | Introspection/schéma SQL avancée |
| guzzlehttp/guzzle | ^7.8 | Client HTTP |
| laravel/framework | ^12.0 | Framework principal |
| laravel/sanctum | ^4.0 | Auth API token |
| laravel/tinker | ^2.9 | REPL dev |
| league/csv | ^9.15 | Import/export CSV |
| league/flysystem-aws-s3-v3 | ^3.27 | Stockage S3 |
| livewire/livewire | ^3.0 | UI full-stack temps réel |
| maatwebsite/excel | ^3.1 | Import/export Excel |
| predis/predis | ^2.2 | Client Redis |
| spatie/laravel-medialibrary | ^11.0 | Gestion médias/fichiers |
| spatie/laravel-permission | ^6.0 | RBAC + teams |
| spatie/laravel-sluggable | ^3.7 | Slugs métier |

### Dépendances dev

| Nom | Version | Description / Rôle |
|---|---|---|
| fakerphp/faker | ^1.23 | Données de test |
| laravel-lang/lang | ^15.2 | Traductions |
| laravel/breeze | ^2.2 | Scaffolding auth |
| laravel/pint | ^1.13 | Formatage PHP |
| laravel/sail | ^1.28 | Dev Docker alternatif |
| mockery/mockery | ^1.6 | Mocks tests |
| nunomaduro/collision | ^8.1 | Reporting erreurs CLI |
| phpunit/phpunit | ^11.0 | Test runner |
| spatie/laravel-ignition | ^2.4 | Error page/debug |

## 3.2 Front-end (NPM/Yarn)

### DevDependencies

| Nom | Version | Description / Rôle |
|---|---|---|
| @tailwindcss/forms | ^0.5.9 | Styles formulaires |
| @tailwindcss/vite | ^4.0.0 | Intégration Tailwind v4 |
| axios | ^1.7.9 | HTTP client frontend |
| laravel-vite-plugin | ^1.1.1 | Pont Laravel/Vite |
| tailwindcss | ^4.0.0 | Framework CSS |
| vite | ^6.0.0 | Build/bundling |

### Dependencies runtime

| Nom | Version | Description / Rôle |
|---|---|---|
| alpinejs | ^3.14.3 | Interactivité UI légère |
| apexcharts | ^4.2.0 | Graphiques analytiques |
| flatpickr | ^4.6.13 | Date/time picker |
| flowbite-datepicker | ^1.3.1 | Datepicker Flowbite |
| slim-select | ^2.9.2 | Select avancé/search |
| sortablejs | ^1.15.6 | Drag & drop |
| svg.js | ^2.7.1 | Manipulation SVG |

## 3.3 Écosystème Laravel (usage)

| Composant | Usage constaté |
|---|---|
| Livewire 3 | Modules dynamiques (maintenance table, alerts center, expenses analytics, etc.) |
| Blade | Couche de rendu principale |
| Policies/Gates | Contrôle d’accès granulaire module par module |
| Spatie Permission (teams) | Scoping permissions par `organization_id` |
| Scheduler/Jobs | Automatisation affectations, maintenance, batch stats |
| Sanctum | API sécurisée (routes `/api/v1/...`) |

---

# 4. STRUCTURE DU PROJET ET ARCHITECTURE DES COMPOSANTS

## 4.1 Arborescence fonctionnelle (synthèse)

```text
app/
  Http/Controllers/      # Web/API/Admin
  Livewire/              # UI temps réel
  Models/                # Domaine + scopes + concerns
  Services/              # Logique métier (maintenance, dépenses, permissions...)
  Policies/              # Autorisations métier
  Jobs/ Events/Listeners # Automatisation et événements
  Repositories/          # Couches d’accès orientées module
config/                  # Configuration Laravel
database/
  migrations/            # Schéma historique (156)
  seeders/               # Provisioning rôle/données/test
resources/
  views/                 # Blade (337)
  js/                    # Bootstraps admin + composants JS
  css/                   # Design system ZenFleet
routes/
  web.php / api.php / maintenance.php / handovers.php / analytics.php
docker/
  php/ nginx/ node_dev/ scripts
pdf-service/             # Microservice PDF Node
tests/
  Feature/ Unit
```

## 4.2 Multi-tenant et contrôle d’accès (mécanisme combiné)

1. Permissions et rôles via Spatie teams (`organization_id`)  
2. Team resolver custom: `app/Services/OrganizationTeamResolver.php`  
3. Middleware de contexte SQL tenant: `app/Http/Middleware/SetTenantSession.php`  
4. Scopes globaux et scopes ciblés:
   - `BelongsToOrganization`
   - `UserVehicleAccessScope`  
5. Policies dédiées (Vehicle, Driver, Maintenance, Expenses, RepairRequest, Mileage, etc.)  
6. Middleware permission custom enterprise (`EnterprisePermissionMiddleware`) + alias de permissions.

## 4.3 Intégration protocoles matériels GPS / télématique

### État observé

- Migration riche avec tables télématiques et partitionnement:
  - `telematics_data` + partitions mensuelles
  - indexes dédiés (temps, géoloc, événements conduite)
  - routines cleanup historisées
- Présence de webhooks API (maintenance/mileage), mais pas de couche d’ingestion protocoles trackers complète visible (GT06E/Teltonika parser TCP/UDP) dans le code applicatif courant.

### Conclusion technique

- Base DB prête pour télématique avancée.
- Pipeline protocolaire tracker réel semble incomplet/non activé dans le monolithe actuel.

---

# 5. MODULES DÉVELOPPÉS ET FONCTIONNALITÉS

## 5.1 Couverture modules (route prefix + composants)

- Dashboard / analytics
- Organisations
- Utilisateurs
- Rôles & permissions
- Véhicules
- Chauffeurs
- Affectations
- Dépôts
- Kilométrage
- Maintenance (table, kanban, calendrier, planning)
- Alertes
- Documents
- Fournisseurs
- Dépenses véhicule (gestion + analytics/dashboard)
- Demandes de réparation (workflow)
- Handovers (fiches de remise)
- Rapports

## 5.2 Interactions front-back

- Back:
  - Controllers + Services + Policies
  - Jobs/events pour tâches asynchrones et transitions
- Front:
  - Livewire pour filtres, actions, polling, rendu dynamique
  - Alpine pour interactions locales
  - SlimSelect/Datepicker/ApexCharts intégrés via bundle Vite
- Données:
  - PostgreSQL + Redis cache/queue

---

# 6. AUDIT TECHNIQUE ET FEUILLE DE ROUTE

## 6.1 Dette technique prioritaire (à corriger impérativement)

### Criticité Haute

1. **Conflits/duplications de routes maintenance**
- Déclarations maintenance dans `routes/web.php` et `routes/maintenance.php`.
- Risque: collisions de noms, handlers inattendus, maintenance difficile.

2. **Drift de configuration environnement**
- `.env.example` ne reflète pas le runtime PostgreSQL/Redis.
- Risque onboarding + erreurs de démarrage + CI cassée.

3. **Surface de code parasite en racine**
- Très nombreux fichiers hors structure standard:
  - `109` fichiers `test_*.php` à la racine
  - `417` fichiers `.md` à la racine
  - multiples backups/artefacts (`.old`, `.bak`, `Zone.Identifier`)
- Risque: erreurs humaines, confusion, dette d’exploitation.

### Criticité Moyenne

4. **Multi-tenant complexe et hétérogène**
- Scopes globaux + filtres explicites + policies + middleware + teams resolver.
- Risque: bug de fuite de données sur cas edge.

5. **Design system en transition**
- Gros volume de refactoring UI en cours (worktree sale).
- Risque: divergence visuelle + régressions locales.

6. **Télématique incomplète côté ingestion**
- Schéma DB avancé présent, chaîne d’ingestion protocolaire non finalisée.

## 6.2 Sécurité et performances

## Sécurité

- Points positifs:
  - Policies présentes et étendues.
  - Gate `Super Admin` explicite.
  - Scoping par organisation.
  - Logs dédiés (`audit`, `security`, `authentication`, etc.).
- Risques:
  - Multiplication de mécanismes d’autorisation (potentiel d’incohérences).
  - Routes legacy/commentées/dupliquées compliquent la preuve de conformité.

## Performances

- Points positifs:
  - PostgreSQL tuning avancé.
  - Redis cache/queue.
  - Scheduler + jobs dédiés.
  - `AnalyticsCacheVersion` déjà en place pour invalidation ciblée.
- Risques:
  - Polling Livewire sur pages lourdes.
  - Views volumineuses + duplication historique de templates.

## 6.3 Plan d’amélioration recommandé (exécutable)

### Phase 1 — Stabilisation architecture (immédiat)

1. Unifier le module maintenance dans un seul namespace de routes.
2. Aligner `.env.example` sur PostgreSQL/Redis/queue redis.
3. Établir une politique de nettoyage dépôt:
   - déplacer scripts/debug dans `tools/` ou `docs/archives/`
   - supprimer artefacts Windows `:Zone.Identifier`
   - exclure backups non nécessaires du repo.

### Phase 2 — Sécurité multi-tenant (court terme)

1. Matrice de tests d’accès par rôle (Super Admin/Admin/Superviseur/Chauffeur).
2. Contrats de sécurité automatiques sur modules sensibles:
   - vehicles, maintenance, mileage, expenses, repair requests.
3. Centraliser le pattern de scoping pour réduire les divergences.

### Phase 3 — Excellence produit (moyen terme)

1. Finaliser design system unique ZenFleet (composants partagés).
2. Stabiliser module Repair Requests (workflow double validation + notifications).
3. Implémenter pipeline télématique réel:
   - ingestion protocolaire tracker
   - normalisation événements
   - scoring conduite/alertes enrichies.

## 6.4 Recommandations dépendances / maintenance technique

- Standardiser versions documentées (README + Dev_environnement).
- Introduire un changelog technique orienté architecture.
- Ajouter une CI stricte:
  - lint/format
  - tests feature ciblés multi-tenant
  - build Vite
  - route:list collision check.

---

# 7. OBSERVATIONS FINALES (Onboarding Senior)

## 7.1 Forces majeures du projet

- Couverture métier très large et réaliste pour un SaaS flotte.
- Architecture Laravel riche (services, policies, jobs, events).
- Base technique compatible scale (PostgreSQL 18 + Redis + dockerisé).
- Système permissions multi-tenant déjà avancé.

## 7.2 Risques de reprise projet

- Complexité de la couche d’accès (autorisations/scopes).
- Dette documentaire et artefacts nombreux.
- Worktree courant avec modifications massives non consolidées.

## 7.3 Checklist d’onboarding recommandée (J+1)

1. Vérifier stack runtime (`docker compose ps`, `artisan about`).
2. Vérifier routes effectives (`route:list`) et collisions.
3. Valider seeders RBAC sur une org de test.
4. Exécuter tests feature critiques multi-tenant.
5. Faire une passe UI guidée module par module avant toute release.

---

# Annexes

## A. Éléments notables détectés

- `routes/web.php` contient encore un bloc maintenance complet + `require routes/maintenance.php`.
- `app/Livewire/Admin/AlertsIndex.php` et `app/Services/AlertCenterService.php` introduisent un centre d’alertes live avec cache court.
- `app/Models/RepairRequest.php.old` présent (artefact legacy).
- Multiples vues backup dans `resources/views/admin/*` et `resources/views/livewire/admin/*`.

## B. Conclusion opérationnelle

ZenFleet est fonctionnel, riche et proche d’un niveau enterprise sur plusieurs modules, mais doit passer par une **phase de consolidation architecture + hygiène dépôt + normalisation multi-tenant** avant de viser une montée en charge et une qualité “plateforme internationale” durable.

