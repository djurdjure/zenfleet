# 🚨 CORRECTION ERREUR 403 - MODULE DÉPENSES

## ⚡ Solution Rapide (30 secondes)

Exécutez ces commandes dans l'ordre :

```bash
# 1. Se connecter au conteneur Docker
docker exec -it zenfleet-app bash

# 2. Exécuter la migration des permissions
php artisan migrate --path=database/migrations/2025_10_28_000001_add_expense_permissions.php

# 3. Vider tous les caches
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset

# 4. Sortir du conteneur
exit

# 5. Redémarrer les conteneurs (optionnel mais recommandé)
docker-compose restart
```

## 🔧 Solution Alternative via Tinker

Si la méthode ci-dessus ne fonctionne pas :

```bash
# 1. Ouvrir tinker dans le conteneur
docker exec -it zenfleet-app php artisan tinker

# 2. Copier-coller ces lignes une par une :
```

```php
// Créer les permissions essentielles
\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view expenses', 'guard_name' => 'web']);
\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create expenses', 'guard_name' => 'web']);
\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'edit expenses', 'guard_name' => 'web']);
\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view expense analytics', 'guard_name' => 'web']);

// Donner les permissions au rôle Admin
$adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
if ($adminRole) {
    $adminRole->givePermissionTo(['view expenses', 'create expenses', 'edit expenses', 'view expense analytics']);
    echo "Permissions ajoutées au rôle Admin\n";
}

// Donner les permissions au rôle Super Admin
$superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
if ($superAdminRole) {
    $superAdminRole->givePermissionTo(['view expenses', 'create expenses', 'edit expenses', 'view expense analytics']);
    echo "Permissions ajoutées au rôle Super Admin\n";
}

// Vider le cache
app()['cache']->forget('spatie.permission.cache');
echo "Cache vidé!\n";

// Quitter
exit
```

## 🎯 Solution Directe pour un Utilisateur Spécifique

Si vous connaissez l'email de l'utilisateur qui doit avoir accès :

```php
// Dans tinker
$user = \App\Models\User::where('email', 'votre-email@example.com')->first();
$user->givePermissionTo(['view expenses', 'create expenses', 'edit expenses', 'view expense analytics']);
app()['cache']->forget('spatie.permission.cache');
echo "Permissions attribuées à {$user->email}\n";
exit
```

## ✅ Vérification

Après avoir appliqué une des solutions :

1. **Déconnectez-vous** de l'application
2. **Reconnectez-vous**
3. Accédez à : `http://localhost/admin/vehicle-expenses`

## 🔍 Diagnostic

Pour vérifier les permissions d'un utilisateur :

```bash
docker exec -it zenfleet-app php artisan tinker
```

```php
// Vérifier les permissions d'un utilisateur
$user = \App\Models\User::where('email', 'votre-email@example.com')->first();
echo "Rôles: " . $user->roles->pluck('name')->implode(', ') . "\n";
echo "Permissions directes: " . $user->permissions->pluck('name')->implode(', ') . "\n";
echo "Peut voir les dépenses? " . ($user->can('view expenses') ? 'OUI' : 'NON') . "\n";
exit
```

## 📋 Permissions Requises

Le module de dépenses nécessite au minimum ces permissions :

- `view expenses` - Pour accéder à la liste des dépenses
- `create expenses` - Pour créer une nouvelle dépense
- `edit expenses` - Pour modifier une dépense
- `view expense analytics` - Pour voir les statistiques

## 🔐 Rôles avec Accès Complet

Les rôles suivants ont accès complet au module :

- **Super Admin** - Accès total
- **Admin** - Gestion complète
- **Finance** - Gestion financière complète
- **Gestionnaire Flotte** - Accès limité (création et consultation)

## 💡 Notes Importantes

1. **Cache** : Toujours vider le cache après modification des permissions
2. **Session** : Se déconnecter/reconnecter après changement de permissions
3. **Multi-tenant** : Les permissions sont isolées par organisation

## 🆘 Support

Si le problème persiste après avoir suivi ces étapes :

1. Vérifiez les logs : `docker exec -it zenfleet-app tail -f storage/logs/laravel.log`
2. Vérifiez que le fichier Policy existe : `app/Policies/VehicleExpensePolicy.php`
3. Assurez-vous que les services existent :
   - `app/Services/VehicleExpenseService.php`
   - `app/Services/ExpenseAnalyticsService.php`
   - `app/Services/ExpenseApprovalService.php`

---

**📌 Fichiers créés pour résoudre ce problème :**

- `/database/migrations/2025_10_28_000001_add_expense_permissions.php` - Migration des permissions
- `/app/Policies/VehicleExpensePolicy.php` - Policy pour les autorisations
- `/fix_expense_permissions.php` - Script PHP de correction
- `/fix-expense-permissions.sh` - Script bash pour Docker
- `/grant_expense_access.php` - Script d'accès rapide
- `/tinker_fix_expenses.txt` - Commandes tinker
