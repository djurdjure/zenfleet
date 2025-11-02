# Documentation Technique Complète - ZenFleet

> **Version:** 1.0.0  
> **Date:** 2025-11-02  
> **Environnement:** Production-ready Docker Containerized  
> **Type:** SaaS Multi-tenant Fleet Management Platform

---

## Table des Matières

1. [Vue d'ensemble du projet](#1-vue-densemble-du-projet)
2. [Stack technologique détaillée](#2-stack-technologique-détaillée)
3. [Architecture et environnement](#3-architecture-et-environnement)
4. [Dépendances et packages](#4-dépendances-et-packages)
5. [Structure du projet](#5-structure-du-projet)
6. [Modules fonctionnels](#6-modules-fonctionnels)
7. [Base de données](#7-base-de-données)
8. [Configuration et déploiement](#8-configuration-et-déploiement)
9. [Standards et conventions](#9-standards-et-conventions)
10. [Commandes et workflows](#10-commandes-et-workflows)

---

## 1. Vue d'ensemble du projet

**ZenFleet** est une plateforme SaaS Enterprise-grade de gestion de flotte de véhicules, conçue pour offrir :
- ✅ **Multi-tenancy** avec isolation complète des données par organisation
- ✅ **Architecture moderne** basée sur Laravel 12 et technologies cloud-ready
- ✅ **Interface utilisateur** moderne avec Livewire 3, Alpine.js et Tailwind CSS
- ✅ **Sécurité renforcée** avec système de permissions granulaires (Spatie)
- ✅ **Performance optimisée** avec Redis, PostgreSQL et queues asynchrones
- ✅ **Design System cohérent** avec composants réutilisables

### Caractéristiques principales
- Gestion complète de flotte multi-tenant
- Système de maintenance préventive et corrective
- Suivi des dépenses et budgets
- Gestion des affectations de véhicules
- Gestion documentaire avec versioning
- Tableaux de bord analytiques en temps réel
- API RESTful pour intégrations tierces

---

## 2. Stack technologique détaillée

### 2.1 Backend

| Composant | Version | Description |
|-----------|---------|-------------|
| **PHP** | 8.3-fpm-alpine | Moteur backend avec optimisations FPM |
| **Laravel** | 12.x | Framework applicatif principal |
| **Livewire** | 3.0+ | Composants dynamiques temps réel |
| **Spatie Permission** | 6.0+ | Système RBAC (Role-Based Access Control) |
| **Doctrine DBAL** | 3.9+ | Abstraction base de données avancée |
| **Guzzle HTTP** | 7.8+ | Client HTTP pour API externes |
| **Laravel Sanctum** | 4.0+ | Authentification API token-based |
| **League CSV** | 9.15+ | Import/export CSV performant |
| **Maatwebsite Excel** | 3.1+ | Génération Excel/reports |
| **Predis** | 2.2+ | Client Redis pour cache et queues |

### 2.2 Frontend

| Composant | Version | Description |
|-----------|---------|-------------|
| **Vite** | 6.3.6+ | Build tool moderne et rapide |
| **Tailwind CSS** | 3.4+ | Framework CSS utilitaire |
| **Alpine.js** | 3.13+ | Framework JS léger pour interactions |
| **Tom Select** | 2.3.1 | Select boxes avancés |
| **Flatpickr** | 4.6.13 | Date/time picker |
| **ApexCharts** | 3.49.1 | Graphiques et visualisations |
| **Sortable.js** | 1.15.2 | Drag & drop interfaces |
| **Axios** | 1.6.4+ | Client HTTP JavaScript |

### 2.3 Infrastructure

| Service | Version | Rôle |
|---------|---------|------|
| **Nginx** | 1.25-alpine | Reverse proxy et serveur web |
| **PostgreSQL** | 16-postgis | Base de données principale avec extension géospatiale |
| **Redis** | 7-alpine | Cache, sessions, queues |
| **Node.js** | 20-bullseye | Serveur de développement Vite et build assets |
| **Docker** | Compose v2 | Orchestration conteneurs |
| **Supervisor** | Latest | Gestionnaire processus PHP (workers, scheduler) |

### 2.4 Développement et qualité

| Outil | Version | Usage |
|-------|---------|-------|
| **Laravel Pint** | 1.13+ | Code formatting (PSR-12) |
| **PHPUnit** | 11.0+ | Tests unitaires et fonctionnels |
| **Pest** | 2.0+ | Alternative moderne PHPUnit |
| **Faker** | 1.23+ | Génération données de test |
| **Laravel Ignition** | 2.4+ | Debugging error pages |
| **Collision** | 8.1+ | CLI error handler |

---

## 3. Architecture et environnement

### 3.1 Architecture Docker

Le projet utilise une architecture microservices conteneurisée :

```
┌─────────────────────────────────────────────────────┐
│                   Nginx (Port 80)                   │
│          Reverse Proxy + Static Assets              │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│              PHP-FPM 8.3 + Supervisor               │
│  ┌──────────────────────────────────────────────┐  │
│  │  Laravel 12 Application                      │  │
│  │  - Web Routes                                │  │
│  │  - API Routes                                │  │
│  │  - Livewire Components                       │  │
│  │  - Queue Workers (Supervisor)                │  │
│  │  - Scheduler (Supervisor)                    │  │
│  └──────────────────────────────────────────────┘  │
└────┬─────────────────────┬────────────────────┬─────┘
     │                     │                    │
     ▼                     ▼                    ▼
┌──────────┐    ┌──────────────────┐    ┌──────────┐
│PostgreSQL│    │      Redis       │    │  Node.js │
│   16+    │    │    (Cache/Q)     │    │   Dev    │
│ PostGIS  │    │                  │    │  Server  │
└──────────┘    └──────────────────┘    └──────────┘
     │                     │
     │                     │
     ▼                     ▼
┌──────────┐    ┌──────────────────┐
│ Volumes  │    │   PDF Service    │
│  Data    │    │   (Node.js)      │
└──────────┘    └──────────────────┘
```

### 3.2 Configuration Docker

#### Services définis (docker-compose.yml)

1. **php** - Container principal application Laravel
   - Image: Custom `php:8.3-fpm-alpine`
   - Extensions: pdo_pgsql, redis, gd, zip, bcmath, sockets, opcache
   - User: zenfleet_user (UID/GID 1000)
   - Supervisor: queue workers + scheduler

2. **nginx** - Serveur web
   - Image: `nginx:1.25-alpine`
   - Port: 80
   - Config: `/docker/nginx/zenfleet.conf`

3. **database** - PostgreSQL avec PostGIS
   - Image: `postgis/postgis:16-3.4-alpine`
   - Port: 5432
   - Volume persistant: `zenfleet_postgres_data`
   - Healthcheck: pg_isready

4. **redis** - Cache et queues
   - Image: `redis:7-alpine`
   - Volume persistant: `zenfleet_redis_data`
   - Healthcheck: redis-cli ping

5. **node** - Développement frontend
   - Image: Custom Node 20
   - Usage: Vite dev server et build

6. **pdf-service** - Microservice génération PDF
   - Context: `./pdf-service`
   - Port interne: 3000
   - Healthcheck: /health endpoint

### 3.3 Réseau et volumes

```yaml
networks:
  zenfleet_network:
    driver: bridge

volumes:
  zenfleet_postgres_data:
  zenfleet_redis_data:
```

### 3.4 Variables d'environnement

Configuration via fichier `.env` (non versionné) :

```bash
# Application
APP_NAME=ZenFleet
APP_ENV=production|staging|local
APP_DEBUG=false
APP_URL=http://localhost
APP_TIMEZONE=Africa/Algiers
APP_LOCALE=fr

# Database
DB_CONNECTION=pgsql
DB_HOST=database
DB_PORT=5432
DB_DATABASE=zenfleet
DB_USERNAME=zenfleet_user
DB_PASSWORD=***

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# PDF Service
PDF_SERVICE_URL=http://pdf-service:3000
```

---

## 4. Dépendances et packages

### 4.1 Dépendances PHP (composer.json)

#### Production
```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "livewire/livewire": "^3.0",
  "spatie/laravel-permission": "^6.0",
  "doctrine/dbal": "^3.9",
  "guzzlehttp/guzzle": "^7.8",
  "laravel/sanctum": "^4.0",
  "league/csv": "^9.15",
  "maatwebsite/excel": "^3.1",
  "predis/predis": "^2.2",
  "blade-ui-kit/blade-icons": "^1.5",
  "spatie/laravel-sluggable": "^3.7",
  "league/flysystem-aws-s3-v3": "^3.27"
}
```

#### Développement
```json
{
  "laravel/breeze": "^2.2",
  "laravel/pint": "^1.13",
  "phpunit/phpunit": "^11.0",
  "fakerphp/faker": "^1.23",
  "spatie/laravel-ignition": "^2.4",
  "laravel-lang/lang": "^15.2"
}
```

### 4.2 Dépendances JavaScript (package.json)

#### Production
```json
{
  "alpinejs": "^3.4.2",
  "apexcharts": "^3.49.1",
  "flatpickr": "^4.6.13",
  "sortablejs": "^1.15.2",
  "tom-select": "^2.3.1"
}
```

#### Développement
```json
{
  "@tailwindcss/forms": "^0.5.2",
  "autoprefixer": "^10.4.2",
  "axios": "^1.6.4",
  "laravel-vite-plugin": "^1.0",
  "postcss": "^8.4.31",
  "tailwindcss": "^3.1.0",
  "vite": "^6.3.6"
}
```

---

## 5. Structure du projet

### 5.1 Arborescence globale

```
zenfleet/
├── app/                          # Code source application
│   ├── Console/                  # Commandes Artisan
│   ├── Enums/                    # Énumérations (Permissions, etc.)
│   ├── Events/                   # Events système
│   ├── Exceptions/               # Gestion erreurs personnalisées
│   ├── Exports/                  # Classes export (Excel, CSV)
│   ├── Http/
│   │   ├── Controllers/          # Contrôleurs MVC
│   │   │   ├── Admin/            # Admin controllers
│   │   │   ├── Api/              # API controllers
│   │   │   ├── Auth/             # Authentication
│   │   │   └── Driver/           # Driver portal
│   │   ├── Middleware/           # Middleware HTTP
│   │   ├── Requests/             # Form Request validation
│   │   └── Resources/            # API Resources
│   ├── Jobs/                     # Queue jobs
│   ├── Listeners/                # Event listeners
│   ├── Livewire/                 # Composants Livewire
│   │   └── Admin/                # Admin Livewire components
│   ├── Models/                   # Eloquent models (47 modèles)
│   ├── Notifications/            # Système notifications
│   ├── Observers/                # Model observers
│   ├── Policies/                 # Authorization policies
│   ├── Providers/                # Service providers
│   ├── Repositories/             # Repository pattern
│   │   ├── Eloquent/             # Implémentations Eloquent
│   │   └── Interfaces/           # Contrats repository
│   ├── Rules/                    # Règles validation custom
│   ├── Services/                 # Business logic services
│   └── View/
│       └── Components/           # Blade components
├── bootstrap/                    # Bootstrap Laravel
├── config/                       # Configuration files
│   ├── app.php
│   ├── database.php
│   ├── expense_categories.php   # Config métier
│   └── ...
├── database/
│   ├── factories/                # Model factories
│   ├── migrations/               # 101 migrations
│   └── seeders/                  # Database seeders
├── docker/                       # Configuration Docker
│   ├── nginx/
│   │   └── zenfleet.conf
│   ├── php/
│   │   ├── Dockerfile
│   │   └── supervisord.conf
│   └── node_dev/
│       └── Dockerfile
├── public/                       # Point d'entrée web
│   ├── build/                    # Assets compilés Vite
│   └── index.php
├── resources/
│   ├── css/                      # Styles sources
│   ├── js/                       # JavaScript sources
│   │   ├── app.js                # Public entry
│   │   └── admin/
│   │       └── app.js            # Admin entry
│   └── views/                    # Templates Blade
│       ├── admin/                # Admin views
│       ├── auth/                 # Auth views
│       ├── components/           # Blade components
│       ├── layouts/              # Layouts
│       └── livewire/             # Livewire views
├── routes/
│   ├── web.php                   # Routes web principales
│   ├── api.php                   # Routes API
│   ├── auth.php                  # Routes authentification
│   ├── maintenance.php           # Routes maintenance module
│   ├── console.php               # Routes console
│   └── channels.php              # Broadcasting channels
├── storage/                      # Storage Laravel
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/                        # Tests automatisés
├── vendor/                       # Dépendances Composer
├── node_modules/                 # Dépendances NPM
├── .env                          # Configuration environnement
├── artisan                       # CLI Laravel
├── composer.json                 # Dépendances PHP
├── package.json                  # Dépendances JS
├── docker-compose.yml            # Orchestration Docker
├── tailwind.config.js            # Config Tailwind
├── vite.config.js                # Config Vite
└── README.md                     # Documentation
```

### 5.2 Structure des contrôleurs

**47 modèles Eloquent** identifiés couvrant tous les aspects métier :

- Gestion flotte: `Vehicle`, `VehicleType`, `VehicleStatus`, `VehicleCategory`
- Maintenance: `MaintenanceOperation`, `MaintenanceSchedule`, `MaintenanceAlert`, `MaintenanceType`
- Conducteurs: `Driver`, `DriverStatus`, `DriverSanction`
- Affectations: `Assignment`
- Dépenses: `VehicleExpense`, `ExpenseBudget`, `ExpenseGroup`, `ExpenseAuditLog`
- Documents: `Document`, `DocumentCategory`, `DocumentRevision`
- Fournisseurs: `Supplier`, `SupplierCategory`, `SupplierRating`
- Réparations: `RepairRequest`, `RepairCategory`, `RepairNotification`
- Organisation: `Organization`, `User`, `Role`, `Permission`
- Géographie: `AlgeriaWilaya`, `AlgeriaCommune`
- Kilométrage: `VehicleMileageReading`

---

## 6. Modules fonctionnels

### 6.1 Gestion de flotte

**Fichiers principaux :**
- `app/Http/Controllers/Admin/VehicleController.php`
- `app/Models/Vehicle.php`
- `app/Policies/VehiclePolicy.php`
- `app/Services/VehicleService.php`

**Fonctionnalités :**
- CRUD véhicules avec validation multi-étapes
- Gestion statuts (disponible, en maintenance, hors service, archivé)
- Import/export CSV/Excel
- Upload photos et documents
- Historique complet des modifications
- Filtres avancés et recherche full-text
- Archivage soft-delete

### 6.2 Maintenance

**Architecture modulaire :**
- `app/Http/Controllers/Admin/Maintenance/` (controllers spécialisés)
- `app/Services/Maintenance/` (business logic)
- `app/Livewire/Admin/Maintenance/` (composants temps réel)

**Sous-modules :**
1. **Planification** (`MaintenanceSchedule`)
   - Maintenance préventive récurrente
   - Calcul automatique des prochaines dates
   - Alertes par kilométrage ou date

2. **Opérations** (`MaintenanceOperation`)
   - Enregistrement interventions
   - Suivi coûts et pièces
   - Historique par véhicule

3. **Alertes** (`MaintenanceAlert`)
   - Notifications automatiques
   - Tableaux de bord temps réel
   - Escalade selon priorité

4. **Fournisseurs** (`MaintenanceProvider`)
   - Gestion prestataires externes
   - Évaluation qualité service

### 6.3 Gestion des conducteurs

**Fichiers clés :**
- `app/Http/Controllers/Admin/DriverController.php`
- `app/Models/Driver.php`
- `app/Livewire/Admin/Drivers/DriversTable.php`

**Fonctionnalités :**
- Fiche conducteur complète (permis, contact, photo)
- Gestion statuts (actif, inactif, suspendu, archivé)
- Suivi sanctions avec historique
- Import CSV en masse
- Alertes expiration permis
- Archivage automatique conducteurs inactifs

### 6.4 Affectations

**Repository pattern :**
- `app/Repositories/Interfaces/AssignmentRepositoryInterface.php`
- `app/Repositories/Eloquent/AssignmentRepository.php`
- `app/Services/AssignmentService.php`

**Fonctionnalités :**
- Affectation véhicule → conducteur avec dates
- Vérification chevauchements (overlap prevention)
- Validation contraintes métier (permissions, disponibilité)
- Gantt chart interactif
- Historique complet affectations
- Filtres multi-critères

### 6.5 Dépenses

**Module ultra-complet :**
- `app/Http/Controllers/Admin/ExpenseController.php`
- `app/Models/VehicleExpense.php`
- `app/Services/VehicleExpenseService.php`
- `config/expense_categories.php` (290 lignes de config métier)

**Catégories supportées :**
- Maintenance préventive (17 types)
- Réparations
- Carburant
- Assurance
- Taxes et impôts
- Pneumatiques
- Péages et parking
- Lavage et entretien
- Autres

**Fonctionnalités :**
- Validation multi-niveaux selon montant
- Workflow approbation
- Budget tracking avec alertes dépassement
- Analytics et reporting (ApexCharts)
- Export Excel détaillé
- Audit trail complet

### 6.6 Documents

**Système de GED (Gestion Électronique Documents) :**
- `app/Http/Controllers/Admin/DocumentController.php`
- `app/Models/Document.php`
- `app/Models/DocumentRevision.php`
- `app/Services/DocumentManagerService.php`

**Capacités :**
- Upload multi-fichiers
- Versioning automatique (revisions)
- Catégories personnalisables avec métadonnées JSON
- Association polymorphe (documents → véhicules, conducteurs, etc.)
- Full-text search PostgreSQL
- Expiration et alertes
- Contrôle accès granulaire

### 6.7 Demandes de réparation

**Workflow collaboratif :**
- `app/Http/Controllers/Driver/RepairRequestController.php` (soumission)
- `app/Http/Controllers/Admin/RepairRequestController.php` (validation)
- `app/Livewire/RepairRequestApprovalModal.php`

**Process :**
1. Conducteur soumet demande avec photos
2. Validation superviseur
3. Approbation gestionnaire flotte
4. Assignation fournisseur
5. Suivi intervention
6. Clôture avec facture

### 6.8 Tableaux de bord

**Analytics temps réel :**
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Livewire/Admin/Maintenance/MaintenanceStats.php`
- `app/Services/ExpenseAnalyticsService.php`

**KPIs suivis :**
- Coût total flotte (TCO)
- Disponibilité véhicules
- Taux maintenance préventive vs corrective
- Coûts par catégorie/véhicule/période
- Alertes en cours
- Taux utilisation flotte

---

## 7. Base de données

### 7.1 Informations générales

- **SGBD :** PostgreSQL 16 avec extension PostGIS 3.4
- **Encodage :** UTF-8
- **Timezone :** Africa/Algiers
- **Migrations :** 101 fichiers de migration
- **Models :** 47 modèles Eloquent

### 7.2 Tables principales (sélection)

| Table | Description | Champs clés |
|-------|-------------|-------------|
| `organizations` | Tenants multi-tenant | `id`, `name`, `slug`, `wilaya_id` |
| `users` | Utilisateurs système | `id`, `organization_id`, `email` |
| `vehicles` | Flotte véhicules | `id`, `registration_number`, `organization_id` |
| `drivers` | Conducteurs | `id`, `organization_id`, `license_number` |
| `assignments` | Affectations véhicule-conducteur | `id`, `vehicle_id`, `driver_id`, `start_date`, `end_date` |
| `maintenance_operations` | Interventions maintenance | `id`, `vehicle_id`, `cost`, `performed_at` |
| `maintenance_schedules` | Plans maintenance préventive | `id`, `vehicle_id`, `recurrence_value`, `next_due_date` |
| `vehicle_expenses` | Dépenses véhicules | `id`, `vehicle_id`, `expense_category`, `amount` |
| `repair_requests` | Demandes réparation | `id`, `vehicle_id`, `status`, `priority` |
| `documents` | Documents GED | `id`, `documentable_type`, `documentable_id` |
| `suppliers` | Fournisseurs | `id`, `organization_id`, `quality_score` |
| `vehicle_mileage_readings` | Relevés kilométrage | `id`, `vehicle_id`, `reading_date`, `mileage` |

### 7.3 Contraintes et indexes

**Isolation multi-tenant :**
- Chaque table métier possède `organization_id`
- Index composites: `(organization_id, other_columns)`
- Row-level security possible

**Contraintes métier :**
- Check constraints sur enums (statuts, catégories)
- Foreign keys avec `ON DELETE RESTRICT/CASCADE`
- Unique constraints multi-colonnes : `(organization_id, registration_number)` pour véhicules

**Indexes stratégiques :**
```sql
-- Exemple extrait de migrations
CREATE INDEX idx_vehicles_org_status ON vehicles(organization_id, status);
CREATE INDEX idx_assignments_dates ON assignments(start_date, end_date);
CREATE INDEX idx_expenses_date ON vehicle_expenses(expense_date);
```

**Full-text search (PostgreSQL) :**
```sql
-- Documents
CREATE INDEX idx_documents_fulltext ON documents USING gin(to_tsvector('french', title || ' ' || description));
```

### 7.4 Extensions PostgreSQL

- **PostGIS 3.4 :** Capacités géospatiales (prêt pour géolocalisation véhicules)
- **pg_trgm :** Recherche floue (fuzzy search)
- **uuid-ossp :** Génération UUIDs

---

## 8. Configuration et déploiement

### 8.1 Configuration Tailwind CSS

**Design System ZenFleet** (tailwind.config.js) :

**Palette de couleurs :**
- Primary: `#0ea5e9` (Sky blue) - 10 nuances
- Secondary: `#1e293b` (Slate) - 10 nuances
- Success: `#22c55e` (Green)
- Warning: `#f59e0b` (Amber)
- Danger: `#ef4444` (Red)
- Info: `#06b6d4` (Cyan)

**Composants custom :**
- `.zenfleet-card` : Cartes avec hover effects
- `.zenfleet-btn` : Boutons standardisés
- `.zenfleet-input` : Champs input avec focus states

**Spacing personnalisés :**
- `sidebar: 280px`
- `sidebar-collapsed: 80px`
- `header: 70px`

**Animations :**
- `fade-in`, `slide-in`, `pulse-slow`

### 8.2 Configuration Vite

**Points d'entrée multiples :**
```javascript
input: [
  'resources/js/app.js',        // Public pages
  'resources/js/admin/app.js'   // Admin panel
]
```

**Code splitting :**
- `vendor-common`: axios
- `ui-public`: alpinejs, tom-select, flatpickr, sortablejs
- `charts`: apexcharts

### 8.3 Workflows de développement

**Installation initiale :**
```bash
# Clone repository
git clone [repo-url] zenfleet
cd zenfleet

# Configuration environnement
cp .env.example .env
# Éditer .env avec vos paramètres

# Build et démarrage Docker
docker compose build
docker compose up -d

# Installation dépendances
docker compose exec php composer install
docker compose exec node yarn install

# Migrations et seed
docker compose exec php php artisan migrate --seed
docker compose exec php php artisan key:generate

# Build assets
docker compose exec node yarn build
```

**Développement quotidien :**
```bash
# Démarrer tous les services
docker compose up -d

# Vite dev server (hot reload)
docker compose exec node yarn dev

# Voir logs en temps réel
docker compose logs -f php nginx
```

**Tests et qualité :**
```bash
# Code formatting (Pint PSR-12)
docker compose exec php ./vendor/bin/pint

# Tests unitaires
docker compose exec php php artisan test

# Clear caches
docker compose exec php php artisan optimize:clear
```

### 8.4 Déploiement production

**Checklist pre-deployment :**
1. ✅ Variables `.env` configurées correctement
2. ✅ `APP_DEBUG=false`
3. ✅ `APP_ENV=production`
4. ✅ Générer `APP_KEY` unique
5. ✅ Configurer backup automatique PostgreSQL
6. ✅ SSL/TLS configuré (Let's Encrypt recommandé)
7. ✅ Firewall : autoriser uniquement ports 80, 443
8. ✅ Configurer monitoring (Sentry, Laravel Telescope, etc.)

**Commandes de build production :**
```bash
# Build optimized assets
docker compose exec node yarn build

# Optimize autoloader
docker compose exec php composer install --optimize-autoloader --no-dev

# Cache routes, config, views
docker compose exec php php artisan optimize

# Migrations (si nécessaire)
docker compose exec php php artisan migrate --force
```

---

## 9. Standards et conventions

### 9.1 Conventions de code PHP

**PSR-12 strict** via Laravel Pint :
- Indentation : 4 espaces
- Namespace : une ligne vide après
- Imports : triés alphabétiquement
- Méthodes : camelCase
- Classes : PascalCase
- Constants : UPPER_CASE

**Structure contrôleur type :**
```php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Vehicle\StoreVehicleRequest;
use App\Models\Vehicle;
use App\Services\VehicleService;

class VehicleController extends Controller
{
    public function __construct(
        private VehicleService $vehicleService
    ) {}
    
    public function index() { /* ... */ }
    public function create() { /* ... */ }
    public function store(StoreVehicleRequest $request) { /* ... */ }
    public function show(Vehicle $vehicle) { /* ... */ }
    public function edit(Vehicle $vehicle) { /* ... */ }
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle) { /* ... */ }
    public function destroy(Vehicle $vehicle) { /* ... */ }
}
```

### 9.2 Architecture pattern

**Repository pattern** pour entités complexes :
```
Interface (contrat) → app/Repositories/Interfaces/
Implementation      → app/Repositories/Eloquent/
Binding             → app/Providers/RepositoryServiceProvider.php
Usage               → Injection dans contrôleurs/services
```

**Service layer** pour business logic :
```php
// app/Services/VehicleService.php
class VehicleService
{
    public function calculateTotalCost(Vehicle $vehicle, Period $period): float
    {
        // Complex business logic here
    }
}
```

### 9.3 Conventions Blade

**Composants réutilisables :**
- Préfixe `x-` pour composants : `<x-button>`, `<x-card>`
- Props typés via `@props(['type' => 'primary'])`
- Slots nommés pour flexibilité

**Structure vue type :**
```blade
<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Page Title') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto">
            <x-card>
                {{-- Content --}}
            </x-card>
        </div>
    </div>
</x-app-layout>
```

### 9.4 Conventions JavaScript

**Alpine.js patterns :**
```html
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open" x-transition>Content</div>
</div>
```

**Livewire wire:model :**
```blade
<input type="text" wire:model.live="search" placeholder="Search...">
```

### 9.5 Gestion des permissions

**Spatie Permission** avec enum centralisé :
```php
// app/Enums/Permission.php
enum Permission: string
{
    case VIEW_VEHICLES = 'view_vehicles';
    case CREATE_VEHICLES = 'create_vehicles';
    case EDIT_VEHICLES = 'edit_vehicles';
    // ...
}
```

**Usage dans policies :**
```php
public function update(User $user, Vehicle $vehicle): bool
{
    return $user->hasPermissionTo(Permission::EDIT_VEHICLES->value)
        && $user->organization_id === $vehicle->organization_id;
}
```

---

## 10. Commandes et workflows

### 10.1 Commandes Artisan personnalisées

| Commande | Description |
|----------|-------------|
| `php artisan maintenance:check-schedules` | Vérifier plans maintenance dus |
| `php artisan drivers:archive-inactive` | Archiver conducteurs inactifs |
| `php artisan expenses:check-budgets` | Vérifier dépassements budget |
| `php artisan diagnose:database` | Diagnostics contraintes BDD |

### 10.2 Workflows Git

**Branches :**
- `master` : Production stable
- `develop` : Intégration features
- `feature/*` : Nouvelles fonctionnalités
- `fix/*` : Corrections bugs

**Commits :**
Format conventionnel :
```
feat(module): Description courte

Corps du message si nécessaire

Co-authored-by: factory-droid[bot] <138933559+factory-droid[bot]@users.noreply.github.com>
```

Types : `feat`, `fix`, `refactor`, `docs`, `test`, `chore`

### 10.3 Maintenance et monitoring

**Logs :**
- Application : `storage/logs/laravel.log`
- Nginx : `docker compose logs nginx`
- PHP-FPM : `docker compose logs php`
- Queue worker : `storage/logs/worker.log`

**Health checks :**
```bash
# Status services Docker
docker compose ps

# Status base de données
docker compose exec database pg_isready -U zenfleet_user

# Status Redis
docker compose exec redis redis-cli ping

# Queue monitoring
docker compose exec php php artisan queue:work --once --verbose
```

**Backup recommandé :**
```bash
# Backup PostgreSQL
docker compose exec database pg_dump -U zenfleet_user zenfleet > backup_$(date +%Y%m%d).sql

# Backup storage
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/app
```

---

## Conclusion

Cette documentation technique complète fournit tous les éléments nécessaires pour :
- ✅ Comprendre l'architecture globale du projet
- ✅ Identifier les technologies et versions utilisées
- ✅ Naviguer dans la structure du code
- ✅ Déployer et maintenir l'application
- ✅ Respecter les standards et conventions établis
- ✅ Continuer le développement avec cohérence

**Points forts de ZenFleet :**
- Architecture moderne et scalable
- Multi-tenant robuste avec isolation données
- Stack technologique à jour (PHP 8.3, Laravel 12, Vite 6)
- Design system cohérent
- Code bien structuré avec patterns établis
- 101 migrations pour schema complet
- 47 modèles couvrant tous les besoins métier
- Tests automatisés et CI/CD ready

**Pour toute question ou contribution :**
- Consulter les fichiers `*.md` à la racine (guides spécifiques)
- Respecter les conventions PSR-12 et patterns établis
- Tester localement via Docker avant commit
- Documenter les nouvelles fonctionnalités

---

*Document généré le 2025-11-02 - Version 1.0.0*  
*Environnement: Linux WSL2 - Docker Compose - PostgreSQL 16 - PHP 8.3 - Laravel 12*
