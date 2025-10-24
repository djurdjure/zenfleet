# 🚀 MODULE MAINTENANCE - GUIDE DE DÉMARRAGE RAPIDE

## ⚡ Installation et Configuration (5 minutes)

### Étape 1: Inclure les routes maintenance

Ajouter dans `routes/web.php`:

```php
// À la fin du fichier, avant la dernière accolade
require __DIR__.'/maintenance.php';
```

### Étape 2: Créer les controllers manquants

```bash
# Créer les controllers restants
php artisan make:controller Admin/Maintenance/MaintenanceDashboardController
php artisan make:controller Admin/Maintenance/MaintenanceScheduleController
php artisan make:controller Admin/Maintenance/MaintenanceAlertController
php artisan make:controller Admin/Maintenance/MaintenanceReportController
php artisan make:controller Admin/Maintenance/MaintenanceTypeController
php artisan make:controller Admin/Maintenance/MaintenanceProviderController
```

### Étape 3: Vider le cache

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Étape 4: Tester l'accès

Naviguer vers: `http://votre-domaine/admin/maintenance/operations`

---

## 📂 FICHIERS CRÉÉS (À VÉRIFIER)

### ✅ Services (3 fichiers)

- `app/Services/Maintenance/MaintenanceService.php`
- `app/Services/Maintenance/MaintenanceScheduleService.php`
- `app/Services/Maintenance/MaintenanceAlertService.php`

### ✅ Controllers (1 fichier principal)

- `app/Http/Controllers/Admin/Maintenance/MaintenanceOperationController.php`

### ✅ Livewire Components (4 fichiers)

- `app/Livewire/Admin/Maintenance/MaintenanceTable.php`
- `app/Livewire/Admin/Maintenance/MaintenanceStats.php`
- `app/Livewire/Admin/Maintenance/MaintenanceKanban.php`
- `app/Livewire/Admin/Maintenance/MaintenanceCalendar.php`

### ✅ Views (1 vue principale)

- `resources/views/admin/maintenance/operations/index.blade.php`

### ✅ Routes

- `routes/maintenance.php`

---

## 🔧 PROCHAINES ÉTAPES CRITIQUES

### 1. Créer les vues Livewire manquantes

```bash
# Créer les fichiers dans resources/views/livewire/admin/maintenance/
touch resources/views/livewire/admin/maintenance/maintenance-table.blade.php
touch resources/views/livewire/admin/maintenance/maintenance-stats.blade.php
touch resources/views/livewire/admin/maintenance/maintenance-kanban.blade.php
touch resources/views/livewire/admin/maintenance/maintenance-calendar.blade.php
```

### 2. Créer les vues CRUD restantes

```bash
# Créer les fichiers dans resources/views/admin/maintenance/operations/
touch resources/views/admin/maintenance/operations/show.blade.php
touch resources/views/admin/maintenance/operations/create.blade.php
touch resources/views/admin/maintenance/operations/edit.blade.php
touch resources/views/admin/maintenance/operations/kanban.blade.php
touch resources/views/admin/maintenance/operations/calendar.blade.php
```

### 3. Mettre à jour la navigation (Sidebar)

Dans `resources/views/layouts/admin/partials/sidebar.blade.php` ou équivalent:

```blade
{{-- Menu Maintenance --}}
<li x-data="{ open: {{ request()->routeIs('admin.maintenance.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 transition-colors duration-200"
            :class="open ? 'bg-blue-50 text-blue-600' : 'text-gray-700'">
        <div class="flex items-center gap-3">
            <x-iconify icon="lucide:wrench" class="w-5 h-5" />
            <span>Maintenance</span>
        </div>
        <x-iconify icon="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" 
                   :class="open ? 'rotate-180' : ''" />
    </button>
    
    <ul x-show="open" x-transition class="mt-2 space-y-1 pl-11">
        <li>
            <a href="{{ route('admin.maintenance.dashboard') }}" 
               class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.dashboard') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700' }}">
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.maintenance.operations.index') }}" 
               class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.operations.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700' }}">
                Opérations
            </a>
        </li>
        <li>
            <a href="{{ route('admin.maintenance.schedules.index') }}" 
               class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.schedules.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700' }}">
                Planifications
            </a>
        </li>
        <li>
            <a href="{{ route('admin.maintenance.alerts.index') }}" 
               class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.alerts.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700' }}">
                Alertes
                @if($alertCount > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                        {{ $alertCount }}
                    </span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('admin.maintenance.reports.index') }}" 
               class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.reports.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700' }}">
                Rapports
            </a>
        </li>
        <li class="border-t border-gray-200 mt-2 pt-2">
            <a href="{{ route('admin.maintenance.types.index') }}" 
               class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.types.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600' }}">
                <x-iconify icon="lucide:settings" class="w-4 h-4 inline mr-2" />
                Types
            </a>
        </li>
        <li>
            <a href="{{ route('admin.maintenance.providers.index') }}" 
               class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.providers.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600' }}">
                <x-iconify icon="lucide:building" class="w-4 h-4 inline mr-2" />
                Fournisseurs
            </a>
        </li>
    </ul>
</li>
```

### 4. Créer une Policy (Permissions)

```bash
php artisan make:policy MaintenanceOperationPolicy --model=MaintenanceOperation
```

Dans `app/Policies/MaintenanceOperationPolicy.php`:

```php
<?php

namespace App\Policies;

use App\Models\MaintenanceOperation;
use App\Models\User;

class MaintenanceOperationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view maintenance') || 
               $user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte']);
    }

    public function view(User $user, MaintenanceOperation $operation): bool
    {
        return $user->organization_id === $operation->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create maintenance') || 
               $user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte']);
    }

    public function update(User $user, MaintenanceOperation $operation): bool
    {
        return ($user->organization_id === $operation->organization_id) &&
               ($user->hasPermissionTo('edit maintenance') || 
                $user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte']));
    }

    public function delete(User $user, MaintenanceOperation $operation): bool
    {
        return ($user->organization_id === $operation->organization_id) &&
               ($user->hasPermissionTo('delete maintenance') || 
                $user->hasRole(['Super Admin', 'Admin']));
    }
}
```

Enregistrer dans `app/Providers/AuthServiceProvider.php`:

```php
protected $policies = [
    // ...
    \App\Models\MaintenanceOperation::class => \App\Policies\MaintenanceOperationPolicy::class,
];
```

---

## 🧪 TESTS MANUELS

### Test 1: Accès à la liste

```
URL: /admin/maintenance/operations
Résultat attendu: Page avec 8 cards métriques + table
```

### Test 2: Recherche

```
Action: Taper dans barre de recherche
Résultat attendu: Filtrage en temps réel
```

### Test 3: Filtres avancés

```
Action: Cliquer "Filtres avancés"
Résultat attendu: Panel s'ouvre avec 8 filtres
```

### Test 4: Tri

```
Action: Changer sélecteur "Trier par"
Résultat attendu: Table se réordonne
```

### Test 5: Actions inline

```
Action: Cliquer icône œil/édition/suppression
Résultat attendu: Navigation ou action appropriée
```

---

## 🔍 DÉPANNAGE

### Erreur: Route not found

**Solution:**
```bash
php artisan route:clear
php artisan route:list | grep maintenance
```

### Erreur: Class MaintenanceService not found

**Solution:**
```bash
composer dump-autoload
```

### Erreur: View not found

**Vérifier:**
- Fichier existe: `resources/views/admin/maintenance/operations/index.blade.php`
- Syntaxe Blade correcte
- Pas d'erreurs PHP

### Erreur: Call to undefined method

**Vérifier:**
- Service injecté dans constructeur
- Méthode existe dans service
- Type hints corrects

---

## 📊 MÉTRIQUES DE SUCCÈS

Après implémentation complète, vous devriez avoir:

✅ **Performance:**
- Temps chargement page < 200ms
- Queries DB < 10 par page
- Cache hit ratio > 80%

✅ **UX:**
- 0 clics inutiles
- Filtres réactifs instantanés
- Actions contextuelles visibles

✅ **Code Quality:**
- 0 erreurs PHPStan niveau 5
- 0 erreurs ESLint
- Couverture tests > 80%

---

## 🎉 FÉLICITATIONS!

Si tous les tests passent, vous avez un module maintenance **enterprise-grade world-class**!

**Prochaine étape:** Compléter les vues restantes et tester en environnement de production.

---

**Support:** Consultez `MAINTENANCE_MODULE_REFACTORING_COMPLETE.md` pour documentation détaillée.
