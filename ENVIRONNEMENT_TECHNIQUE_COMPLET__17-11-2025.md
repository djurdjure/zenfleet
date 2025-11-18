# ENVIRONNEMENT TECHNIQUE COMPLET - ZENFLEET
**Date de génération : 17 Novembre 2025**
**Version du projet : 2.1 Ultra-Pro**

---

## TABLE DES MATIÈRES

1. [Résumé Exécutif](#1-résumé-exécutif)
2. [Stack Technologique Principal](#2-stack-technologique-principal)
3. [Infrastructure Docker](#3-infrastructure-docker)
4. [Backend - Laravel & PHP](#4-backend---laravel--php)
5. [Frontend - JavaScript & Assets](#5-frontend---javascript--assets)
6. [Base de Données](#6-base-de-données)
7. [Architecture Applicative](#7-architecture-applicative)
8. [Domaine Métier](#8-domaine-métier)
9. [Conventions & Patterns](#9-conventions--patterns)
10. [Contraintes Techniques CRITIQUES](#10-contraintes-techniques-critiques)
11. [Recommandations pour Développement Futur](#11-recommandations-pour-développement-futur)

---

## 1. RÉSUMÉ EXÉCUTIF

**ZenFleet** est une application de **gestion de flotte de véhicules** orientée vers le marché algérien. C'est une application **monolithique Laravel** avec une architecture **modulaire**, utilisant **Livewire 3** pour l'interactivité côté serveur et **Alpine.js** pour les interactions côté client.

### Caractéristiques Clés
- **Type d'application** : Application web monolithique (SPA-like avec Livewire)
- **Domaine métier** : Gestion de flotte (véhicules, chauffeurs, affectations, maintenance, dépenses)
- **Marché cible** : Algérie (fuseau horaire Africa/Algiers, langue française)
- **Niveau de maturité** : Production-ready avec fonctionnalités Enterprise-grade
- **Base de code** : Laravel 12 + PHP 8.3 + PostgreSQL 18 + PostGIS

---

## 2. STACK TECHNOLOGIQUE PRINCIPAL

### Backend
| Technologie | Version | Rôle |
|-------------|---------|------|
| **PHP** | 8.3+ (8.3-fpm-alpine) | Langage serveur principal |
| **Laravel** | 12.0 | Framework applicatif |
| **Livewire** | 3.0 | Composants réactifs côté serveur |
| **Composer** | Latest | Gestionnaire de dépendances PHP |

### Frontend
| Technologie | Version | Rôle |
|-------------|---------|------|
| **Node.js** | 18.20.8 | Runtime JavaScript |
| **NPM** | 10.8.2 | Gestionnaire de paquets |
| **Vite** | 6.3.6 | Build tool & bundler |
| **Tailwind CSS** | 3.1.0 | Framework CSS utilitaire |
| **Alpine.js** | 3.4.2 | Framework JS léger |

### Base de Données & Cache
| Technologie | Version | Rôle |
|-------------|---------|------|
| **PostgreSQL** | 18 | Base de données principale |
| **PostGIS** | 3.6 | Extension géospatiale |
| **Redis** | 7-alpine | Cache et sessions |

### Infrastructure
| Technologie | Version | Rôle |
|-------------|---------|------|
| **Docker** | Multi-container | Conteneurisation |
| **Nginx** | 1.25-alpine | Serveur web |
| **Supervisor** | Latest | Process manager PHP-FPM |

---

## 3. INFRASTRUCTURE DOCKER

### Services Déployés

```yaml
# 7 services dans docker-compose.yml
services:
  php:         # Application Laravel (PHP 8.3-FPM)
  nginx:       # Serveur web reverse proxy
  database:    # PostgreSQL 18 + PostGIS 3.6
  redis:       # Cache et sessions
  node:        # Build tools (Vite, NPM)
  scheduler:   # Laravel Scheduler (cron jobs)
  pdf-service: # Microservice génération PDF
```

### Configuration PostgreSQL Enterprise-Grade

La base de données est optimisée pour de hautes performances :

```bash
# Paramètres clés PostgreSQL
shared_buffers=2GB
work_mem=32MB
maintenance_work_mem=1GB
effective_cache_size=6GB
max_parallel_workers_per_gather=4
max_parallel_workers=8
jit=on  # Compilation Just-In-Time activée
```

### Volumes Persistants

```yaml
volumes:
  zenfleet_postgres_data:  # Données PostgreSQL (EXTERNE - CRITIQUE)
  zenfleet_redis_data:     # Données Redis (EXTERNE)
```

**ATTENTION** : Les volumes sont déclarés `external: true` pour éviter toute perte de données accidentelle.

### Dockerfile PHP Personnalisé

```dockerfile
FROM php:8.3-fpm-alpine

# Extensions PHP installées :
- gd (images)
- pdo_pgsql, pgsql (PostgreSQL)
- zip, exif, intl, bcmath, sockets, opcache
- redis (cache)

# Utilisateur non-root : zenfleet_user
```

---

## 4. BACKEND - LARAVEL & PHP

### Dépendances Composer Principales

#### Production (`require`)
```json
{
  "php": "^8.2",
  "laravel/framework": "^12.0",
  "livewire/livewire": "^3.0",
  "spatie/laravel-permission": "^6.0",
  "spatie/laravel-sluggable": "^3.7",
  "maatwebsite/excel": "^3.1",
  "league/csv": "^9.15",
  "league/flysystem-aws-s3-v3": "^3.27",
  "predis/predis": "^2.2",
  "guzzlehttp/guzzle": "^7.8",
  "laravel/sanctum": "^4.0",
  "doctrine/dbal": "^3.9",
  "blade-ui-kit/blade-icons": "^1.5"
}
```

#### Développement (`require-dev`)
```json
{
  "laravel/breeze": "^2.2",
  "laravel/pint": "^1.13",
  "laravel/sail": "^1.28",
  "phpunit/phpunit": "^11.0",
  "fakerphp/faker": "^1.23",
  "mockery/mockery": "^1.6",
  "nunomaduro/collision": "^8.1",
  "spatie/laravel-ignition": "^2.4",
  "laravel-lang/lang": "^15.2"
}
```

### Configuration Laravel

#### Fichier `config/app.php`
```php
'timezone' => 'Africa/Algiers',  // Fuseau horaire Algérie
'locale' => 'fr',                 // Langue française
'fallback_locale' => 'en',
'cipher' => 'AES-256-CBC',       // Chiffrement sécurisé

// Service Providers personnalisés
'providers' => [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,  // Gestion permissions
    App\Providers\RepositoryServiceProvider::class,      // Pattern Repository
]
```

### Architecture des Répertoires App

```
app/
├── Console/           # Commandes Artisan personnalisées
├── Enums/             # Énumérations PHP 8.1+
├── Events/            # Événements applicatifs
├── Exceptions/        # Gestion des exceptions
├── Exports/           # Exports Excel (Maatwebsite)
├── Helpers/           # Fonctions utilitaires
├── Http/
│   ├── Controllers/   # Contrôleurs MVC
│   ├── Middleware/    # Middlewares HTTP
│   └── Requests/      # Form Requests (validation)
├── Jobs/              # Jobs asynchrones (Queue)
├── Listeners/         # Event Listeners
├── Livewire/          # Composants Livewire 3 (MAJEUR)
├── Logging/           # Configuration logs personnalisée
├── Models/            # Modèles Eloquent (~50 modèles)
├── Notifications/     # Notifications Laravel
├── Observers/         # Model Observers
├── Policies/          # Authorization Policies
├── Providers/         # Service Providers
├── Repositories/      # Pattern Repository
├── Rules/             # Validation Rules personnalisées
├── Services/          # Services métier
├── Traits/            # Traits réutilisables
└── View/              # View Composers/Components
```

### Système de Permissions

Utilisation de **Spatie Laravel Permission v6** :
- Gestion RBAC (Role-Based Access Control)
- Permissions granulaires par fonctionnalité
- Configuration dans `config/permission.php`

---

## 5. FRONTEND - JAVASCRIPT & ASSETS

### Dépendances NPM

#### Dépendances de Production
```json
{
  "apexcharts": "^3.49.1",       // Graphiques interactifs
  "flatpickr": "^4.6.13",        // Sélecteur de dates
  "slim-select": "^2.8.2",       // Select amélioré (remplace TomSelect)
  "sortablejs": "^1.15.2",       // Drag & Drop listes
  "tom-select": "^2.3.1"         // Select (legacy/fallback)
}
```

#### Dépendances de Développement
```json
{
  "vite": "^6.3.6",                // Build tool principal
  "laravel-vite-plugin": "^1.0",   // Intégration Laravel-Vite
  "tailwindcss": "^3.1.0",         // Framework CSS
  "@tailwindcss/forms": "^0.5.2",  // Plugin forms
  "autoprefixer": "^10.4.2",       // PostCSS
  "postcss": "^8.4.31",            // Pipeline CSS
  "alpinejs": "^3.4.2",            // Framework JS réactif
  "axios": "^1.6.4"                // Client HTTP
}
```

### Configuration Vite (`vite.config.js`)

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',        // Point d'entrée public
                'resources/js/admin/app.js',  // Point d'entrée admin
            ],
            refresh: [
                'resources/views/**/*.blade.php',  // Hot reload Blade
            ],
        }),
    ],

    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        sourcemap: false,

        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor-common': ['axios'],
                    'ui-public': ['alpinejs', 'slim-select', 'flatpickr', 'sortablejs'],
                    'charts': ['apexcharts'],
                },
            },
        },
        chunkSizeWarningLimit: 600,
    },

    server: {
        hmr: { host: 'localhost' },
        watch: { usePolling: true, interval: 1000 },  // Important pour Docker
    },
});
```

### Configuration Tailwind CSS (`tailwind.config.js`)

```javascript
export default {
    darkMode: 'class',  // Dark mode désactivé (classe .dark jamais ajoutée)

    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',  // Support Vue.js (non utilisé actuellement)
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            // Palette de couleurs ZenFleet personnalisée
            colors: {
                zenfleet: {
                    primary: '#0ea5e9',    // Bleu ciel
                    secondary: '#1e293b',  // Gris foncé
                    success: '#22c55e',    // Vert
                    warning: '#f59e0b',    // Orange
                    danger: '#ef4444',     // Rouge
                    info: '#06b6d4',       // Cyan
                },
                primary: { /* 50-950 scale */ },
                secondary: { /* 50-950 scale */ },
                success: { /* 50-950 scale */ },
                warning: { /* 50-950 scale */ },
                danger: { /* 50-950 scale */ },
                info: { /* 50-950 scale */ },
            },

            // Spacing personnalisé
            spacing: {
                'sidebar': '280px',
                'sidebar-collapsed': '80px',
                'header': '70px',
                'content': '1200px',
            },

            // Animations personnalisées
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-in': 'slideIn 0.3s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
        },
    },

    plugins: [
        forms,  // @tailwindcss/forms
        // Plugin personnalisé ZenFleet avec composants :
        // - .zenfleet-card
        // - .zenfleet-btn
        // - .zenfleet-input
    ],
};
```

### Architecture JavaScript (`resources/js/app.js`)

Le fichier principal expose des **objets globaux** et initialise plusieurs systèmes :

```javascript
// Objets globaux exposés sur window
window.Alpine = Alpine;
window.ZenFleetSelect = ZenFleetSelect;  // Wrapper SlimSelect
window.Sortable = Sortable;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;

// Configuration Alpine.js avec composant zenfleet
Alpine.data('zenfleet', () => ({
    version: '2.1',
    // Gestionnaires : alertes, validation, raccourcis clavier, erreurs
    // Initialisateurs : ZenFleetSelect, Flatpickr, Sortable, ApexCharts
}));

// Utilitaires globaux window.ZenFleet
window.ZenFleet = {
    version: '2.1',
    formatDate(),
    formatCurrency(),
    formatNumber(),
    confirm(),
    storage: { get(), set(), remove() }
};
```

### Points d'Entrée CSS

```
resources/css/
├── app.css              # Point d'entrée principal
│   ├── @import flatpickr
│   ├── @import zenfleet-select.css
│   ├── @import vehicle-status.css
│   ├── @tailwind base
│   ├── @tailwind components
│   └── @tailwind utilities
├── admin/               # Styles admin séparés
├── components/          # Composants CSS réutilisables
└── vehicle-status.css   # Styles spécifiques statuts véhicules
```

---

## 6. BASE DE DONNÉES

### Configuration Principale

- **Moteur** : PostgreSQL 18 avec PostGIS 3.6
- **Connexion** : `pgsql` (définie dans `config/database.php`)
- **Encodage** : UTF-8
- **Search Path** : `public`
- **SSL Mode** : `prefer`

### Extensions PostgreSQL Activées

```sql
-- Dans les migrations
- PostGIS (géospatial)
- pg_stat_statements (monitoring queries)
- Partitionnement (audit logs)
- Index GiST (contraintes temporelles)
```

### Migrations (60+ fichiers)

Structure des migrations avec conventions :
```
database/migrations/
├── 2025_01_15_000000_create_organizations_table.php
├── 2025_01_19_200000_create_algeria_tables_simple.php
├── 2025_01_20_120000_create_assignments_enhanced_table.php
├── 2025_01_21_100000_create_maintenance_types_table.php
├── 2025_11_08_020000_optimize_postgresql_configuration.php
└── ...
```

### Modèles Eloquent Principaux (~50 modèles)

```
app/Models/
├── User.php                    # Utilisateurs
├── Organization.php            # Multi-tenant
├── Vehicle.php                 # Véhicules (17K+ lignes)
├── Driver.php                  # Chauffeurs
├── Assignment.php              # Affectations (23K+ lignes)
├── Depot.php                   # Dépôts (21K+ lignes)
├── VehicleExpense.php          # Dépenses véhicules
├── MaintenanceOperation.php    # Opérations maintenance
├── RepairRequest.php           # Demandes réparation
├── Supplier.php                # Fournisseurs
├── Document.php                # Documents GED
├── AlgeriaWilaya.php           # Wilayas algériennes
├── AlgeriaCommune.php          # Communes algériennes
└── ... (~35 autres modèles)
```

### Fonctionnalités Avancées

1. **Multi-tenant** via Organization
2. **Contraintes temporelles** (GiST indexes)
3. **Historique des statuts** (StatusHistory)
4. **Audit trail** (ExpenseAuditLog avec partitionnement)
5. **Géolocalisation** (PostGIS)

---

## 7. ARCHITECTURE APPLICATIVE

### Livewire 3 (Cœur de l'Application)

La majorité des fonctionnalités sont implémentées en **Livewire 3** :

```
app/Livewire/
├── Admin/                       # Composants d'administration
├── AssignmentForm.php          # Formulaire affectations (15K+ lignes)
├── AssignmentGantt.php         # Vue Gantt (17K+ lignes)
├── AssignmentTable.php         # Table des affectations
├── Assignments/                 # Sous-composants affectations
├── Depots/                      # Gestion des dépôts
├── Entity/                      # Composants génériques
├── RepairRequestsIndex.php     # Liste demandes réparation
└── Vehicles/                    # Gestion véhicules
```

### Vues Blade

```
resources/views/
├── layouts/              # Layouts principaux (public, admin)
├── livewire/             # Vues Livewire
├── admin/                # 26+ sous-dossiers admin
├── components/           # Blade Components
├── auth/                 # Authentification (Laravel Breeze)
├── dashboard/            # Tableaux de bord
├── exports/              # Templates d'export
└── vendor/               # Vues vendor personnalisées
```

### Routes

```
routes/
├── web.php          # Routes web principales (54K+ lignes)
├── api.php          # API REST (14K+ lignes)
├── auth.php         # Authentification Laravel Breeze
├── maintenance.php  # Routes maintenance
├── analytics.php    # Routes analytiques
├── channels.php     # Broadcasting channels
└── console.php      # Commandes console
```

### Pattern Repository

```
app/Repositories/
└── (Pattern Repository implémenté via RepositoryServiceProvider)
```

---

## 8. DOMAINE MÉTIER

### Entités Principales

1. **Véhicules** (`Vehicle`)
   - Catégories, types, statuts
   - Historique kilométrage
   - Documents associés
   - Dépenses et maintenance

2. **Chauffeurs** (`Driver`)
   - Licences, statuts
   - Sanctions et historique
   - Affectations

3. **Affectations** (`Assignment`)
   - Assignation véhicule-chauffeur
   - Contraintes temporelles (pas de chevauchement)
   - Historique des dépôts

4. **Dépôts** (`Depot`)
   - Gestion géographique
   - Véhicules par dépôt
   - Historique d'affectation

5. **Maintenance**
   - Types de maintenance
   - Planifications
   - Alertes automatiques
   - Fournisseurs

6. **Dépenses** (`VehicleExpense`)
   - Catégorisation
   - Budgets
   - Audit trail complet
   - Groupes de dépenses

7. **Réparations** (`RepairRequest`)
   - Workflow d'approbation
   - Historique
   - Notifications

8. **Géographie Algérienne**
   - Wilayas (48)
   - Communes
   - Intégration PostGIS

---

## 9. CONVENTIONS & PATTERNS

### Conventions de Nommage

```php
// Modèles : PascalCase singulier
Vehicle.php, Driver.php, Assignment.php

// Tables : snake_case pluriel
vehicles, drivers, assignments

// Migrations : YYYY_MM_DD_HHMMSS_action_table_name.php
2025_11_08_000001_update_vehicle_statuses.php

// Livewire : PascalCase
AssignmentForm.php, VehicleTable.php

// Vues Blade : kebab-case
assignment-form.blade.php, vehicle-table.blade.php
```

### Patterns Architecturaux

1. **MVC** (Model-View-Controller) - Laravel standard
2. **Repository Pattern** - Abstraction accès données
3. **Service Layer** - Logique métier (`app/Services/`)
4. **Observer Pattern** - Événements modèles (`app/Observers/`)
5. **Policy Pattern** - Autorisation (`app/Policies/`)
6. **Enum Pattern** - PHP 8.1+ (`app/Enums/`)
7. **Trait Pattern** - Réutilisation (`app/Traits/`)

### Standards de Code

- **Laravel Pint** (PHP CS Fixer) pour le formatage PHP
- **ESLint/Prettier** implicite via Vite
- **Autoload PSR-4** standard

---

## 10. CONTRAINTES TECHNIQUES CRITIQUES

### 🔴 OBLIGATOIRE - Ne Pas Changer

1. **Laravel 12** - Framework principal
   - NE PAS migrer vers d'autres frameworks
   - Respecter les conventions Laravel

2. **Livewire 3** - Composants réactifs
   - NE PAS introduire Vue.js/React/Inertia
   - Utiliser Alpine.js pour JS côté client
   - Respecter le cycle de vie Livewire

3. **PostgreSQL 18 + PostGIS**
   - NE PAS migrer vers MySQL/SQLite
   - Utiliser les fonctionnalités PostgreSQL natives
   - Exploiter PostGIS pour géolocalisation

4. **Tailwind CSS 3**
   - NE PAS introduire Bootstrap/Bulma
   - Utiliser la palette ZenFleet définie
   - Respecter les classes utilitaires

5. **Alpine.js 3**
   - NE PAS introduire jQuery
   - Utiliser `x-data`, `x-on`, `x-bind`, etc.
   - Intégration avec Livewire via `@entangle`

6. **Vite 6**
   - NE PAS revenir à Laravel Mix/Webpack
   - Respecter la configuration de build
   - Utiliser les chunks définis

### 🟡 ATTENTION - Compatibilité

1. **SlimSelect 2.8** (via ZenFleetSelect)
   - Remplace TomSelect progressivement
   - Wrapper personnalisé dans `resources/js/components/`

2. **Flatpickr 4.6**
   - Sélecteur de dates standard
   - Localisation française configurée

3. **ApexCharts 3.49**
   - Bibliothèque de graphiques
   - Thème ZenFleet pré-configuré

4. **Spatie Permissions 6**
   - Système RBAC en place
   - NE PAS changer de système d'autorisation

5. **Maatwebsite Excel 3.1**
   - Exports Excel/CSV
   - Jobs asynchrones pour gros volumes

### 🟢 EXTENSIBLE - Peut Ajouter

1. **Nouvelles migrations** - Suivre les conventions
2. **Nouveaux modèles** - Étendre l'existant
3. **Composants Livewire** - Dans `app/Livewire/`
4. **Services métier** - Dans `app/Services/`
5. **Jobs asynchrones** - Dans `app/Jobs/`
6. **Notifications** - Dans `app/Notifications/`

---

## 11. RECOMMANDATIONS POUR DÉVELOPPEMENT FUTUR

### À FAIRE

1. **Respecter la structure existante**
   ```bash
   # Création d'un nouveau modèle
   php artisan make:model NouveauModele -m

   # Création d'un composant Livewire
   php artisan make:livewire Admin/NouveauComposant
   ```

2. **Utiliser les conventions de couleurs**
   ```html
   <!-- Utiliser la palette ZenFleet -->
   <div class="bg-primary-500 text-white">...</div>
   <button class="zenfleet-btn-primary">Action</button>
   ```

3. **Exploiter Alpine.js avec Livewire**
   ```html
   <div x-data="{ open: false }">
       <button @click="open = !open">Toggle</button>
       <div x-show="open" x-transition>Content</div>
   </div>
   ```

4. **Tester avec PHPUnit**
   ```bash
   php artisan test
   php artisan test --filter=NomDuTest
   ```

### À NE PAS FAIRE

1. ❌ **NE PAS** installer Vue.js, React, Angular, Svelte
2. ❌ **NE PAS** utiliser jQuery ou autres bibliothèques legacy
3. ❌ **NE PAS** changer la base de données vers MySQL
4. ❌ **NE PAS** introduire Bootstrap, Bulma, ou autres frameworks CSS
5. ❌ **NE PAS** modifier les volumes Docker externes
6. ❌ **NE PAS** désactiver les contraintes PostgreSQL
7. ❌ **NE PAS** ignorer les migrations existantes

### Environnement de Développement

```bash
# Démarrage Docker
docker-compose up -d

# Installation dépendances PHP (dans conteneur)
docker exec zenfleet_php composer install

# Installation dépendances JS (dans conteneur)
docker exec zenfleet_node_dev npm install

# Build assets (développement)
docker exec zenfleet_node_dev npm run dev

# Build assets (production)
docker exec zenfleet_node_dev npm run build

# Migrations
docker exec zenfleet_php php artisan migrate

# Cache
docker exec zenfleet_php php artisan optimize
```

### URLs de Développement

- **Application** : http://localhost (via Nginx)
- **PostgreSQL** : localhost:5432
- **Redis** : localhost:6379
- **PDF Service** : http://localhost:3000/health

---

## ANNEXE A - Fichiers de Configuration Clés

| Fichier | Rôle |
|---------|------|
| `composer.json` | Dépendances PHP |
| `package.json` | Dépendances JavaScript |
| `vite.config.js` | Configuration build Vite |
| `tailwind.config.js` | Configuration Tailwind CSS |
| `postcss.config.js` | Pipeline PostCSS |
| `docker-compose.yml` | Services Docker |
| `.env` | Variables d'environnement |
| `config/app.php` | Configuration Laravel |
| `config/database.php` | Configuration BDD |
| `config/permission.php` | Configuration Spatie |

## ANNEXE B - Commandes Artisan Importantes

```bash
# Gestion du cache
php artisan optimize        # Optimise l'application
php artisan config:clear    # Vide le cache de config
php artisan cache:clear     # Vide le cache applicatif
php artisan view:clear      # Vide le cache des vues

# Migrations
php artisan migrate         # Exécute les migrations
php artisan migrate:fresh   # Reset + migrate (DANGER)
php artisan migrate:status  # Statut des migrations

# Livewire
php artisan livewire:make   # Crée un composant
php artisan livewire:publish --assets  # Publie les assets

# Permissions
php artisan permission:create-permission  # Crée une permission
php artisan permission:create-role       # Crée un rôle

# Queue
php artisan queue:work      # Démarre le worker
php artisan queue:failed    # Liste les jobs échoués
```

---

**Document généré automatiquement - ZenFleet v2.1 Ultra-Pro**
**Pour toute question : consulter les fichiers source directement**
