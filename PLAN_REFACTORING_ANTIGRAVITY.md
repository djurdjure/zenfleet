# 🛡️ PLAN DE REFACTORING & AUDIT - ZENFLEET (ANTIGRAVITY)

**Date:** 25 Novembre 2025
**Auteur:** Antigravity (Google DeepMind)
**Statut:** 🔴 CRITIQUE (Sécurité compromise, Architecture incohérente)

Ce document détaille avec une précision chirurgicale les étapes nécessaires pour transformer Zenfleet d'un "prototype avancé" en une véritable application Enterprise-Grade sécurisée et maintenable.

---

## 🚨 1. DIAGNOSTIC CRITIQUE

### 1.1. 🔓 Faille de Sécurité Majeure (RLS Inactif)
Le système de **Row Level Security (RLS)** PostgreSQL, défini dans la migration `2025_01_20_102000_create_multi_tenant_system.php`, est actuellement **INOPÉRANT**.
-   **Cause :** La variable de session `app.current_user_id` requise par les politiques RLS n'est **jamais définie** par l'application.
-   **Preuve :** Aucune trace de `set_config` ou `DB::statement("SET app.current_user_id...")` dans les Middleware ou ServiceProviders.
-   **Conséquence :** Si RLS était activé, toutes les requêtes échoueraient. Actuellement, la sécurité repose uniquement sur des clauses `where` manuelles dans les contrôleurs, ce qui est fragile et sujet aux erreurs humaines (fuites de données).

### 1.2. 🏗️ Architecture "Potemkine" (Façade)
L'application prétend être une SPA moderne (Livewire 3), mais le cœur du système (Gestion Véhicules) est un **monolithe legacy** déguisé.
-   **God Controller :** `VehicleController.php` fait **3 266 lignes**. Il mélange tout : validation, requêtes SQL complexes, logique métier, et rendu de vue.
-   **Code Mort (Dead Code) :** Des composants Livewire sophistiqués comme `VehicleBulkActions` (avec WebSockets !) existent mais ne sont **pas utilisés** dans la vue `index.blade.php`, qui utilise une boucle `foreach` standard.
-   **Incohérence :** Le fichier `Kernel.php` fait référence à un middleware `OrganizationScope` qui **n'existe pas** physiquement sur le disque.

---

## 🛠️ 2. PLAN D'IMPLÉMENTATION DÉTAILLÉ

### PHASE 1 : SÉCURISATION & FONDATIONS (URGENT)
**Objectif :** Activer la sécurité RLS au niveau base de données et nettoyer les références brisées.

#### Étape 1.1 : Création du Middleware de Session Tenant
Ce middleware injectera l'ID utilisateur et l'ID organisation dans la session PostgreSQL à chaque requête.

**Fichier :** `app/Http/Middleware/SetTenantSession.php` (À CRÉER)
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetTenantSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // Récupération de l'org ID via le user (suppose que user->organization_id est chargé)
            $orgId = Auth::user()->organization_id; 

            // Injection dans la session PostgreSQL pour RLS
            // "local" signifie que la variable n'existe que pour la transaction en cours
            DB::statement("SET LOCAL app.current_user_id = '{$userId}'");
            
            if ($orgId) {
                DB::statement("SET LOCAL app.current_organization_id = '{$orgId}'");
            }
        }

        return $next($request);
    }
}
```

#### Étape 1.2 : Enregistrement & Nettoyage du Kernel
Corriger les références inexistantes et enregistrer le nouveau middleware.

**Fichier :** `app/Http/Kernel.php`
**Action :**
1.  Supprimer `'organization.scope' => \App\Http\Middleware\OrganizationScope::class,` (Fichier inexistant).
2.  Ajouter `'tenant.session' => \App\Http\Middleware\SetTenantSession.php` dans le groupe `web` ou en global.

#### Étape 1.3 : Vérification RLS (Test SQL)
Exécuter une requête brute pour confirmer que RLS bloque l'accès sans variable de session.

```sql
-- Test 1: Doit retourner 0 lignes (ou erreur)
SELECT count(*) FROM vehicles;

-- Test 2: Doit retourner les véhicules de l'utilisateur 1
SET app.current_user_id = '1';
SELECT count(*) FROM vehicles;
```

---

### PHASE 2 : MODERNISATION (REFONTE VÉHICULES)
**Objectif :** Remplacer le "God Controller" par une architecture Livewire modulaire et réactive.

#### Étape 2.1 : Création du Composant Livewire Index
Nous allons créer un vrai composant Livewire qui gère le listing, les filtres et la pagination.

**Fichier :** `app/Livewire/Admin/Vehicles/VehicleIndex.php` (À CRÉER)
**Responsabilités :**
-   Utiliser le trait `WithPagination`.
-   Intégrer la logique de filtrage (actuellement dans `VehicleController::buildAdvancedQuery`).
-   Gérer la sélection multiple (récupérer la logique de `VehicleBulkActions`).
-   Supporter le tri dynamique.

#### Étape 2.2 : Migration de la Vue
Refondre `resources/views/admin/vehicles/index.blade.php` pour qu'elle soit une vue Livewire native.
-   Remplacer la boucle `@foreach` Blade par `@foreach` dans le template Livewire.
-   Connecter les filtres (input search, selects) directement aux propriétés Livewire (`wire:model.live`).
-   Connecter les actions de masse aux méthodes Livewire.

#### Étape 2.3 : Nettoyage du Contrôleur Legacy
Une fois le composant Livewire fonctionnel :
1.  Modifier `routes/web.php` pour pointer vers le composant Livewire :
    ```php
    Route::get('/vehicles', \App\Livewire\Admin\Vehicles\VehicleIndex::class)->name('vehicles.index');
    ```
2.  **SUPPRIMER** la méthode `index` et `buildAdvancedQuery` de `VehicleController`.
3.  Ne garder dans le contrôleur que les méthodes CRUD complexes qui nécessitent des redirections (create/store/edit/update) ou les migrer vers des "Form Objects" Livewire.

---

### PHASE 3 : OPTIMISATION & NETTOYAGE
**Objectif :** Éliminer le code mort et optimiser les performances.

#### Étape 3.1 : Suppression du Code Mort
-   Supprimer `app/Livewire/Admin/VehicleBulkActions.php` (car sa logique sera intégrée dans `VehicleIndex`).
-   Supprimer les vues partielles non utilisées.

#### Étape 3.2 : Optimisation des Requêtes (N+1)
-   Dans `VehicleIndex.php`, s'assurer que les relations sont chargées (`with(['vehicleType', 'status', 'assignments.driver'])`).
-   Vérifier que le calcul des "badges" (statut) ne génère pas de requêtes supplémentaires.

---

## 📝 COMMENT PROCÉDER ?

Je suis prêt à exécuter la **PHASE 1** immédiatement.
1.  Création du Middleware.
2.  Correction du Kernel.
3.  Test de sécurité.

Attente de votre feu vert pour démarrer.
