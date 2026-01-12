# 🏗️ ZenFleet - Environnement de Développement (Dev_environnement.md)

Ce document recense en détail l'environnement technique actuel de l'application ZenFleet. Il sert de référence pour le développement, la maintenance et l'intégration de nouveaux développeurs ou experts.

---

## 🖥️ 1. Système et Infrastructure Locale

### Système d'Exploitation (Hôte / WSL2)
*   **OS** : Ubuntu 22.04.5 LTS (Jammy Jellyfish)
*   **Environnement** : WSL2 (Windows Subsystem for Linux)
*   **Kernel** : 5.15.153.1-microsoft-standard-WSL2 (x86_64)

### Docker & Orchestration
*   **Docker Version** : 24.0.5 (build 24.0.5-0ubuntu1~22.04.1)
*   **Docker Compose** : v2.20.2
*   **Architecture** : Conteneurs isolés via réseau `zenfleet_network`

---

## 🐳 2. Stack Docker (Services)

L'architecture repose sur `docker-compose.yml` avec les services suivants :

| Service | Image / Build | Configuration Spéciale |
| :--- | :--- | :--- |
| **App (PHP)** | Custom Build (`./docker/php/Dockerfile`) | PHP 8.2+ CLI, Extensions requises |
| **Web (Nginx)** | `nginx:1.25-alpine` | Config personnalisée `zenfleet.conf` |
| **Database** | `postgis/postgis:18-3.6-alpine` | **Enterprise-Grade Tuning** : Shared buffers 2GB, JIT on, Parallel workers, Logging avancé |
| **Cache/Queue** | `redis:7-alpine` | Persistance via volume externe `zenfleet_redis_data` |
| **Scheduler** | Custom Build (PHP) | Exécution continue (`while true`) de `php artisan schedule:run` |
| **Node Dev** | Custom Build (`./docker/node_dev/Dockerfile`) | Environnement Node 18+ pour Vite/Yarn |
| **PDF Service** | Custom Microservice (Node/Express) | Port 3000, Microservice dédié à la génération PDF haute performance |

> **Note Importante sur les Volumes** : Les données sont persistées dans des volumes externes (`zenfleet_postgres_data`, `zenfleet_redis_data`) pour éviter la perte de données lors des redémarrages de conteneurs.

---

## 🛠️ 3. Stack Backend (Laravel)

### Cœur du Framework
*   **PHP Version** : `^8.2`
*   **Laravel Framework** : `^12.0` (Version avancée/Preview)
*   **Architecture** : Monolithe modulaire avec Livewire

### Packages Principaux (`composer.json`)
*   **Livewire** : `^3.0` (Full-stack framework pour interfaces dynamiques)
*   **Base de Données** : `doctrine/dbal` `^3.9`
*   **Gestion des Permissions** : `spatie/laravel-permission` `^6.0` (RBAC complet)
*   **Gestion de Médias** : `spatie/laravel-medialibrary` `^11.0`
*   **Export/Import** :
    *   Excel/CSV : `maatwebsite/excel` `^3.1`, `league/csv` `^9.15`
    *   PDF : `barryvdh/laravel-dompdf` `^3.1` (Note: Un microservice Node PDF est aussi présent)
*   **Utils** : `spatie/laravel-sluggable` `^3.7`
*   **Storage** : `league/flysystem-aws-s3-v3` `^3.27` (Support S3 prêt)

---

## 🎨 4. Stack Frontend (Vite & JS)

### Build & Bundling
*   **Build Tool** : `vite` `^6.3.6`
*   **Plugin Laravel** : `laravel-vite-plugin` `^1.0`
*   **Gestionnaire de Paquets** : `yarn` `1.22.22` (Node `v18.19.1`)

### Frameworks & Librairies
*   **CSS Framework** : `tailwindcss` `^4.1.18` (Migré vers v4)
    *   **Configuration** : `@tailwindcss/vite` plugin.
    *   **Thème** : Configuré via CSS variables dans `resources/css/theme.css`.
    *   **Mode Sombre** : **STRICTEMENT DÉSACTIVÉ**. L'interface est conçue pour être "Light Mode Only" pour garantir une cohérence visuelle parfaite.
*   **Interactivité** : `alpinejs` `^3.4.2`
    *   Utilisé extensivement pour la logique UI (Modales, Dropdowns, État local).
*   **Composants UI** :
    *   **Selects** : `slim-select` `^2.8.2` (Wrappé dans `ZenFleetSelect` pour uniformisation). Note: `tom-select` présent mais déprécié.
    *   **Datepicker** : `flatpickr` `^4.6.13` (Thème "zenfleet" personnalisé), `flowbite-datepicker` `^2.0.0`
    *   **Charts** : `apexcharts` `^3.49.1`
    *   **Drag & Drop** : `sortablejs` `^1.15.2`

### Architecture JS (`resources/js/app.js`)
*   **Initialisation** : `initializeGlobals()` expose Alpine, ZenFleetSelect, ApexCharts à `window`.
*   **Globale `ZenFleet`** : Objet window contenant des utilitaires (formatage dates/monnaie, helpers storage, notifications).
*   **Directives Custom** : Intégration profonde d'Alpine.js avec des directives personnalisées pour SlimSelect.

---

## ⚙️ 5. Outils & Configurations Spécifiques

### Base de Données (PostgreSQL Enterprise Tuning)
Configuration `command` spécifique dans docker-compose pour haute performance :
*   `work_mem`: 32MB
*   `maintenance_work_mem`: 1GB
*   `effective_cache_size`: 6GB
*   `max_parallel_workers`: 8
*   Extensions actives : `postgis`, `pg_stat_statements`

### Design System (Tailwind Custom)
`tailwind.config.js` définit l'identité visuelle "ZenFleet" :
*   **Couleurs Thématiques** : Extensions `zenfleet.primary`, `zenfleet.secondary`, etc.
*   **Spacing** : Custom `sidebar`, `header`, `content`.
*   **Composants Custom** : `.zenfleet-card`, `.zenfleet-btn`, `.zenfleet-input` injectés via plugin.
*   **Animations** : `fade-in`, `slide-in`, `pulse-slow`.

### Sécurité & Qualité
*   **Permissions** : Système RBAC (Role-Based Access Control) via Spatie.
*   **Logs** : logging avancé configuré dans les conteneurs.
*   **Scheduler** : Monitoring continu des CRONs.

---

## ✅ État des Lieux pour l'Expert
L'environnement est **stable, conteneurisé et orienté "Enterprise-Grade"**.
La stack est moderne (Laravel 12 + Livewire 3 + Alpine + Tailwind), avec une attention particulière portée à la performance (Tuning PG) et à l'UX (Custom JS wrappers, SlimSelect, Flatpickr).

**Points d'attention pour la suite :**
1.  **Uniformisation Selects** : Finaliser la transition complète de TomSelect vers SlimSelect (`ZenFleetSelect`).
2.  **Migration Tailwind** : Migration **complète vers v4.1 effectuée**.
    *   Le système utilise désormais `@theme` et l'architecture CSS native.
    *   Dark mode désactivé pour simplifier la maintenance UI.
3.  **PDF** : Coexistence de DOMPDF (PHP) et d'un microservice Node PDF. Clarifier l'usage définitif.
4.  **Laravel Version** : Le projet utilise une version `^12.0` de Laravel, ce qui implique potentiellement d'être sur une branche "bleeding edge" ou une configuration spécifique à surveiller.
