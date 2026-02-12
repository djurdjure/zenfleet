# Rapport sur les Problèmes de Permissions (Admin Bypass)

## 🚨 Problème Identifié
Le rôle 'Admin' parvient à accéder à des fonctionnalités non autorisées (Menu Dépôts, Exports) malgré la révocation apparente des permissions via l'interface.

## 🔍 Analyse Technique (Root Cause)

### 1. Mécanisme "Dual-Read" (PermissionAliases)
Le fichier [AuthServiceProvider.php](file://wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/app/Providers/AuthServiceProvider.php) contient une logique de rétro-compatibilité dans `Gate::before` qui permet à un utilisateur de passer une vérification de permission CANONIQUE (`depots.view`) s'il possède la permission LEGACY (`view depots`).

```php
// Code responsable dans AuthServiceProvider.php
Gate::before(function (User $user, string $ability) {
    // ...
    foreach (PermissionAliases::resolve($ability) as $permission) {
        if ($permissionNames->contains($permission)) {
            return true; // ACCÈS ACCORDÉ SI PERMISSION LEGACY PRÉSENTE
        }
    }
});
```

### 2. Persistance des Permissions Legacy en Base de Données
Bien que l'interface administrateur montre les nouvelles permissions, la base de données contient probablement encore les anciennes permissions (`view depots`, `export vehicles`, etc.) assignées au rôle Admin. Ces permissions "fantômes" sont résolues par le mécanisme ci-dessus.

### 3. Contrôleurs & Middleware
*   **Dépôts**: [VehicleDepotController](file://wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/VehicleDepotController.php#27-255) est protégé par le middleware qui vérifie `depots.view`. À cause du point 1, l'Admin passe.
*   **Exports**: Les méthodes d'extension ([exportCsv](file://wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/VehicleControllerExtensions.php#29-64), etc.) dans `VehicleControllerExtensions` vérifient `$user->can('vehicles.export')`. `vehicles.export` est un alias de `export vehicles`. Si l'Admin a `export vehicles`, il passe.

### 4. Cas particulier : `VehicleController::index`
Le contrôleur [VehicleController](file://wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/VehicleController.php#67-3539) ne possède pas de méthode [index](file://wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/DashboardController.php#45-100). La route `/admin/vehicles` semble être gérée soit par une définition de route directe (`Route::view`) soit par une logique implicite non visible dans le contrôleur principal, ce qui peut masquer des vérifications de permissions manquantes.

## 🛠️ Solutions Proposées

### Solution Immédiate (Correctif)
Effectuer une migration de données pour nettoyer les permissions legacy de la base de données.

1.  **Renommer** toutes les permissions legacy vers leur version canonique (ex: `view depots` -> `depots.view`).
2.  **Supprimer** les doublons si les deux existent.
3.  **Vider le cache** des permissions ([app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();](file://wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/app/Http/Middleware/EnterprisePermissionMiddleware.php#598-618)).

### Solution Long Terme (Enterprise-Grade)
1.  **Supprimer [PermissionAliases](file://wsl.localhost/Ubuntu-22.04/home/lynx/projects/zenfleet/app/Support/PermissionAliases.php#5-288)** une fois la migration des données confirmée, pour forcer l'usage strict des permissions canoniques.
2.  **Audit Automatisé** : Ajouter un test qui vérifie qu'aucun rôle ne possède de permissions ne suivant pas la convention de nommage `resource.action`.

## 📋 Plan d'Action
1.  Créer la migration de nettoyage des permissions.
2.  Exécuter la migration.
3.  Vérifier que l'accès est bloqué pour l'Admin.

---

## ✅ Mise à jour Phase 4 (audit du 05/02/2026)
Résultat de `php artisan permissions:audit` :

```
Legacy permissions      : 89
Orphan role permissions : 0
Orphan user permissions : 0
Duplicate permissions   : 0
```

### Interprétation
- **Les 89 permissions legacy existent encore dans la table `permissions`.**
- **Aucun orphelin ni doublon** : la base est saine.
- Comme le **dual‑read a été supprimé**, ces legacy **ne donnent plus d’accès**.  
  ✅ OK pour la sécurité, mais il faut vérifier qu’aucun rôle n’utilise encore ces legacy (sinon perte d’accès).

---

## 🧪 Vérifications indispensables (Phase 4)

### 1) Vérifier si des permissions legacy sont encore assignées
Exécuter :
```
docker compose exec -u zenfleet_user php php artisan tinker --execute="
use App\Support\PermissionAliases;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
\$legacy = collect(PermissionAliases::legacyMap())->flatten()->unique();
\$legacyIds = Permission::whereIn('name', \$legacy)->pluck('id');
\$roleCount = DB::table('role_has_permissions')->whereIn('permission_id', \$legacyIds)->count();
\$userCount = DB::table('model_has_permissions')->whereIn('permission_id', \$legacyIds)->count();
echo \"legacy_assigned_roles=\$roleCount\\nlegacy_assigned_users=\$userCount\\n\";
"
```

**Attendu :**
```
legacy_assigned_roles=0
legacy_assigned_users=0
```

Si ce n’est pas 0 → il faut **migrer ces assignations vers la version canonique** avant suppression.

---

### 2) Nettoyer les permissions legacy (si non utilisées)
Si les deux compteurs sont à 0, on peut supprimer les legacy sans risque :
```
docker compose exec -u zenfleet_user php php artisan tinker --execute="
use App\Support\PermissionAliases;
use Spatie\Permission\Models\Permission;
\$legacy = collect(PermissionAliases::legacyMap())->flatten()->unique();
Permission::whereIn('name', \$legacy)->delete();
echo 'legacy_permissions_deleted';
"
```

Puis relancer l’audit :
```
docker compose exec -u zenfleet_user php php artisan permissions:audit
```

**Attendu :**
```
Legacy permissions      : 0
Orphan role permissions : 0
Orphan user permissions : 0
Duplicate permissions   : 0
```

---

### 3) Validation UI/RBAC (exemples concrets)
Tester avec un rôle **Admin** d’une organisation :
1. Retirer `depots.view` du rôle Admin.  
   ✅ Le menu Dépôts doit disparaître.  
   ✅ L’accès direct `/admin/depots` doit retourner 403.

2. Retirer `vehicles.export`.  
   ✅ Le bouton Export ne s’affiche plus.  
   ✅ L’URL d’export renvoie 403.

3. Modifier un rôle Admin dans **Org A**.  
   ✅ Les permissions d’Org B ne changent pas.

---

## ✅ Critères de validation Phase 4
- `permissions:audit` affiche **0 legacy / 0 orphans / 0 duplicates**.
- Aucun accès non autorisé (tests UI/route).
- Aucun rôle d’une organisation ne “fuit” vers une autre.
- Les exports/dépôts respectent strictement les permissions.
