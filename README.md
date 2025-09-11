# ZenFleet

Bienvenue sur le dépôt GitHub de ZenFleet, un projet de gestion de flotte robuste et standardisé.

## Table des Matières

- [Introduction](#introduction)
- [Environnement de Développement et Standards](#environnement-de-développement-et-standards)
  - [Stack Technologique](#stack-technologique)
  - [Outils et Commandes Standards](#outils-et-commandes-standards)
- [Catalogue des Commandes Standards du Projet ZENFLEET](#catalogue-des-commandes-standards-du-projet-zenfleet)
  - [Analyse des Commandes Spécifiques](#analyse-des-commandes-spécifiques)
  - [Pour la Base de Données](#pour-la-base-de-données)
  - [Pour la Logique Applicative](#pour-la-logique-applicative)
  - [Pour l'Interface (Vues & Composants)](#pour-linterface-vues--composants)

## Introduction

Ce document fournit une description détaillée de l'environnement, des standards, de la structure des fichiers et de la base de données du projet ZenFleet. Notre objectif est de garantir une reproductibilité parfaite et une cohérence maximale dans le développement.

## Environnement de Développement et Standards

Notre environnement est standardisé et conteneurisé pour garantir une reproductibilité parfaite.

### Stack Technologique

| Composant | Technologie/Version | Rôle |
| :--- | :--- | :--- |
| Serveur Web | Nginx 1.25-alpine | Point d'entrée HTTP. |
| Backend | PHP 8.2-fpm-alpine | Moteur de l'application Laravel. |
| Framework | Laravel 10.x | Cœur de l'application. |
| Base de Données| PostgreSQL 15-alpine | Stockage des données. |
| Cache & Jobs | Redis alpine | Gestion du cache et des files d'attente. |
| Frontend | Node.js 20-bullseye | Compilation des assets et serveur Vite. |
| CSS | Tailwind CSS | Framework de design. |
| JavaScript| Alpine.js | Interactivité de l'interface. |
| Icônes | blade-ui-kit/blade-icons | Gestion centralisée des icônes SVG. |

### Outils et Commandes Standards

Il est impératif d'utiliser les mêmes outils et commandes pour garantir la cohérence.

**Gestion des Dépendances Backend (PHP) :**

- **Outil :** Composer
- **Exécution :** `docker compose exec -u zenfleet_user php composer <commande>`
- **Exemple :** `docker compose exec -u zenfleet_user php composer require nouvelle/librairie`

**Gestion des Dépendances Frontend (JavaScript) :**

- **Outil :** Yarn (et non npm).
- **Raison :** Nous utilisons Yarn pour sa gestion stricte et reproductible des versions de paquets via le fichier `yarn.lock`, ce qui élimine les conflits.
- **Exécution :** `docker compose exec -u zenfleet_user node yarn <commande>`
- **Exemples :**
  - Installer les dépendances : `docker compose exec -u zenfleet_user node yarn install`
  - Lancer le serveur de dev : `docker compose exec -u zenfleet_user node yarn dev`
  - Compiler pour la prod : `docker compose exec -u zenfleet_user node yarn build`

**Commandes Laravel (Artisan) :**

- **Exécution :** `docker compose exec -u zenfleet_user php php artisan <commande>`
- **Exemple (migration) :** `docker compose exec -u zenfleet_user php php artisan migrate`

## Catalogue des Commandes Standards du Projet ZENFLEET

Voici le guide de référence des commandes Artisan pour ZENFLEET.

### Analyse des Commandes Spécifiques

1.  `make:service SupplierService`
    - **Analyse :** Cette commande a généré une erreur car `make:service` n'est pas une commande standard de Laravel.
    - **Objectif visé :** Créer une classe de service dans le dossier `app/Services`.
    - **Notre Procédure Standard :** Comme cette commande n'existe pas, nous créons les fichiers de service manuellement.
      - Créer le répertoire : `mkdir -p app/Services`
      - Créer le fichier : `touch app/Services/SupplierService.php` et le remplir.

2.  `make:controller Admin/SupplierController --resource --model=Supplier`
    - **Analyse :** C'est la commande parfaite et standard pour notre projet.
    - **Décomposition d'Expert :**
      - `make:controller Admin/SupplierController` : Crée le contrôleur `SupplierController.php` dans le bon sous-dossier `app/Http/Controllers/Admin/`, ce qui maintient notre code organisé.
      - `--resource` : Génère automatiquement les 7 méthodes CRUD (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`), nous faisant gagner du temps et garantissant une structure cohérente.
      - `--model=Supplier` : Lie le contrôleur au modèle `Supplier`. C'est une pratique "ultra pro" qui active le "route-model binding", simplifiant notre code dans les méthodes comme `edit(Supplier $supplier)`.

### Pour la Base de Données

**Créer un Modèle Eloquent :**

- **Commande :** `docker compose exec -u zenfleet_user php php artisan make:model NomDuModele -m`
- **Description :** Crée un fichier de modèle dans `app/Models/` et son fichier de migration (`-m`) associé.
- **Exemple :** `... make:model Document -m`

**Créer une Migration (Nouvelle Table) :**

- **Commande :** `docker compose exec -u zenfleet_user php php artisan make:migration create_nom_de_la_table_table`
- **Description :** Crée un fichier de migration pour une nouvelle table.
- **Exemple :** `... make:migration create_documents_table`

**Créer une Migration (Modifier une Table) :**

- **Commande :** `docker compose exec -u zenfleet_user php php artisan make:migration add_colonne_to_nom_de_la_table_table`
- **Description :** Crée un fichier de migration pour modifier une table existante.
- **Exemple :** `... make:migration add_expiry_date_to_documents_table`

**Créer un Seeder :**

- **Commande :** `docker compose exec -u zenfleet_user php php artisan make:seeder NomSeeder`
- **Description :** Crée un fichier pour peupler la base de données.
- **Exemple :** `... make:seeder DocumentTypeSeeder`

### Pour la Logique Applicative

**Créer un Contrôleur :** (Voir analyse ci-dessus)

**Créer une Form Request :**

- **Commande :** `docker compose exec -u zenfleet_user php php artisan make:request Admin/NomDuModule/StoreNomRequest`
- **Description :** Crée une classe de validation pour sécuriser les données d'un formulaire. C'est une de nos pratiques clés.
- **Exemple :** `... make:request Admin/Document/StoreDocumentRequest`

**Créer un Service Provider :**

- **Commande :** `docker compose exec -u zenfleet_user php php artisan make:provider NomServiceProvider`
- **Description :** Crée un fournisseur de services, comme nous l'avons fait pour `RepositoryServiceProvider`.
- **Exemple :** `... make:provider DocumentServiceProvider`

### Pour l'Interface (Vues & Composants)

**Créer un Composant Blade :**

- **Commande :** `docker compose exec -u zenfleet_user php php artisan make:component NomDuComposant`
- **Description :** Crée une classe et une vue pour un composant Blade réutilisable.
- **Exemple :** `... make:component Forms/DocumentUploader`

En respectant ce catalogue de commandes, nous garantissons que chaque nouvelle partie de l'application est construite de manière cohérente, propre et professionnelle.

//////////////////////////////   PUIS MIS à JOUR AVEC CE QUI SUIT :

# ZenFleet

Bienvenue sur le dépôt GitHub de **ZenFleet**, plateforme SaaS de gestion de flotte conçue pour les exigences Enterprise-grade : performance, sécurité, UX moderne et code 100 % conforme aux standards Laravel.

---

## Table des Matières
- [Introduction](#introduction)
- [Environnement de Développement et Standards](#environnement-de-développement-et-standards)
  - [Stack Technologique](#stack-technologique)
  - [Outils et Commandes Standards](#outils-et-commandes-standards)
- [Catalogue des Commandes Standards ZenFleet](#catalogue-des-commandes-standards-zenfleet)
  - [Pour la Base de Données](#pour-la-base-de-données)
  - [Pour la Logique Applicative](#pour-la-logique-applicative)
  - [Pour l’Interface (Vues & Composants)](#pour-linterface-vues--composants)
- [Flux CI/CD & Qualité](#flux-cicd--qualité)
- [Démarrage Rapide](#démarrage-rapide)

---

## Introduction
Ce document décrit la stack, la structure projet, les bonnes pratiques et les commandes incontournables afin de garantir **reproductibilité** et **robustesse** sur tous les environnements.

---

## Environnement de Développement et Standards
Le projet est entièrement conteneurisé via **Docker Compose v2** : un simple `make up` suffit pour disposer d’un stack fonctionnel identique à celui de production.

### Stack Technologique

| Composant      | Version / Technologie  | Rôle                                 |
| :------------- | :--------------------- | :----------------------------------- |
| Serveur Web    | **Nginx 1.25-alpine**  | Point d’entrée HTTP/3, gzip & brotli |
| Backend        | **PHP 8.3-fpm-alpine** | Application Laravel                  |
| Framework      | **Laravel 12.x**       | Cœur applicatif, policies & jobs     |
| UI temps réel  | **Livewire 3.x**       | Composants dynamiques (table, stats) |
| Base de données| **PostgreSQL 16-postgis** | Stockage + géo-requêtes             |
| Cache / Queue  | **Redis 7-alpine**     | Cache, files d’attente               |
| Frontend build | **Node 20-bullseye**   | Vite + Tailwind                      |
| CSS Framework  | **Tailwind CSS 3.4**   | Design system ZenFleet Admin         |
| JS utilitaire  | **Alpine.js 3.13**     | Micro-interactions                   |
| Tests          | **Pest ^2**            | Unit & Feature tests                 |
| CI             | **GitHub Actions**     | Lint + PHPStan 6 + coverage 85 %      |

### Outils et Commandes Standards
| Besoin              | Outil / Wrapper                                                |
| ------------------- | -------------------------------------------------------------- |
| Dépendances PHP     | `composer` (via container) : `docker compose exec php composer …` |
| Dépendances JS      | `yarn` : `docker compose exec node yarn …`                    |
| Laravel Artisan     | `docker compose exec php php artisan …`                       |
| Docker helpers      | `make up / down / test / seed / debug-on`                     |

---

## Catalogue des Commandes Standards ZenFleet

### Pour la Base de Données
| Action                           | Commande |
| --------------------------------|----------|
| Créer modèle + migration        | `php artisan make:model Vehicle -m` |
| Créer migration additionnelle   | `php artisan make:migration add_idx_to_vehicles_table` |
| Seeder de démo                  | `php artisan make:seeder DemoSeeder` |

### Pour la Logique Applicative
| Élément          | Commande Exemple |
| ---------------- | ---------------- |
| Contrôleur CRUD  | `php artisan make:controller Admin/VehicleController --resource --model=Vehicle` |
| Form Request     | `php artisan make:request Admin/Vehicle/StoreVehicleRequest` |
| Event / Listener | `php artisan make:event VehicleAssigned` |

### Pour l’Interface (Vues & Composants)
| Type                       | Commande |
| -------------------------- | -------- |
| Composant Blade            | `php artisan make:component Tables/StatusBadge` |
| Composant Livewire         | `php artisan make:livewire OrganizationTable` |
| Publication assets Livewire| `php artisan livewire:publish --assets` |

---

## Flux CI/CD & Qualité
1. **Pre-commit Hooks** : Pint + Prettier + Tailwind lint.  
2. **GitHub Actions** :  
   - Build & tests (Pest, coverage ≥ 85 %, PHPStan 6)  
   - Static analysis JS (ESLint)  
   - Docker image push sur `ghcr.io/zenfleet/zenfleet-app` tag =`sha`.  
3. **Deploy** : auto-promotion vers staging via Argo CD (k8s).

---

## Démarrage Rapide


