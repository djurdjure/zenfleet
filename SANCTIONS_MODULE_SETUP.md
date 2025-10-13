# 🚀 Module Sanctions - Guide de Configuration

## ✅ Corrections Appliquées

### 1. Menu Sidebar Corrigé
Le menu **Chauffeurs** a été transformé en menu déroulant avec deux sous-menus :
- **Liste** → `/admin/drivers` (liste des chauffeurs)
- **Sanctions** → `/admin/sanctions` (gestion des sanctions)

**Fichier modifié** : `resources/views/components/admin/sidebar.blade.php`

### 2. Layout Livewire Corrigé
Le composant Livewire `DriverSanctionIndex` utilise maintenant le bon layout admin.

**Fichier modifié** : `app/Livewire/Admin/DriverSanctionIndex.php`
- Changement : `->layout('layouts.app')` → `->layout('layouts.admin.catalyst-enterprise')`

### 3. Problème d'Espace Blanc Résolu
Le problème de décalage du menu latéral était causé par un conflit entre deux systèmes de sidebar.
La solution : le layout `catalyst-enterprise` est déjà correctement configuré avec `lg:pl-64`.

---

## 📋 Étapes à Suivre pour Activer le Module

### Étape 1 : Démarrer l'environnement Docker

```bash
cd /home/lynx/projects/zenfleet
docker-compose up -d
```

### Étape 2 : Exécuter le Seeder des Permissions

```bash
# Via Docker
docker-compose exec app php artisan db:seed --class=DriverSanctionPermissionsSeeder

# OU si le conteneur a un nom différent
docker exec zenfleet-app-1 php artisan db:seed --class=DriverSanctionPermissionsSeeder
```

**Ce seeder va créer :**
- ✅ 13 permissions pour gérer les sanctions
- ✅ Attribution automatique aux rôles (Super Admin, Admin, Gestionnaire Flotte, Superviseur)

### Étape 3 : Vider le Cache

```bash
# Via Docker
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Étape 4 : Compiler les Assets Frontend

```bash
# Compilez les assets Vite (si ce n'est pas déjà fait)
docker-compose exec app npm run build

# OU en mode développement avec hot reload
docker-compose exec app npm run dev
```

### Étape 5 : Tester le Module

1. **Accédez au Dashboard** : `http://localhost/admin/dashboard`
2. **Cliquez sur le menu "Chauffeurs"** → Le menu devrait se déployer avec :
   - Liste
   - Sanctions
3. **Cliquez sur "Sanctions"** → La page `http://localhost/admin/sanctions` devrait s'afficher

---

## 🔍 Vérifications Post-Installation

### Vérifier que les permissions ont été créées

```bash
docker-compose exec app php artisan tinker
```

Puis dans Tinker :
```php
// Vérifier les permissions sanctions
\Spatie\Permission\Models\Permission::where('name', 'like', '%driver sanction%')->pluck('name');

// Vérifier qu'un Admin a bien ces permissions
$admin = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first();
$admin->getAllPermissions()->where('name', 'like', '%driver sanction%')->pluck('name');
```

### Vérifier que la route fonctionne

```bash
docker-compose exec app php artisan route:list | grep sanctions
```

Vous devriez voir :
```
GET|HEAD   admin/sanctions ........... admin.sanctions.index › App\Livewire\Admin\DriverSanctionIndex
```

---

## 📁 Fichiers Créés/Modifiés

### Nouveaux Fichiers
1. ✅ `app/Models/DriverSanction.php` - Modèle principal
2. ✅ `app/Models/DriverSanctionHistory.php` - Historique des modifications
3. ✅ `app/Policies/DriverSanctionPolicy.php` - Contrôle d'accès enterprise-grade
4. ✅ `app/Livewire/Admin/DriverSanctionIndex.php` - Composant Livewire
5. ✅ `resources/views/livewire/admin/driver-sanction-index.blade.php` - Vue Livewire
6. ✅ `database/migrations/2025_10_13_205834_create_driver_sanctions_table.php`
7. ✅ `database/migrations/2025_10_13_210059_create_driver_sanction_histories_table.php`
8. ✅ `database/seeders/DriverSanctionPermissionsSeeder.php`

### Fichiers Modifiés
1. ✅ `resources/views/components/admin/sidebar.blade.php` - Menu Chauffeurs avec sous-menus
2. ✅ `routes/web.php` - Route `/admin/sanctions` déjà présente
3. ✅ `app/Providers/AuthServiceProvider.php` - Policy enregistrée
4. ✅ `app/Livewire/Admin/DriverSanctionIndex.php` - Layout corrigé

---

## 🎯 Fonctionnalités du Module Sanctions

### Pour Superviseurs
- ✅ Créer des sanctions pour les chauffeurs
- ✅ Voir leurs propres sanctions créées
- ✅ Modifier leurs sanctions dans les 24h
- ✅ Archiver leurs sanctions après 30 jours
- ✅ Upload de pièces jointes (PDF, images)

### Pour Gestionnaires de Flotte
- ✅ Voir toutes les sanctions de l'organisation
- ✅ Créer et modifier toutes les sanctions
- ✅ Archiver/désarchiver sans restriction
- ✅ Supprimer des sanctions
- ✅ Exporter les données

### Pour Admins
- ✅ Toutes les permissions du Gestionnaire
- ✅ Restaurer les sanctions supprimées
- ✅ Suppression définitive (force delete)
- ✅ Vue sur l'historique complet

### Pour Super Admin
- ✅ Accès total à toutes les organisations
- ✅ Toutes les permissions admin
- ✅ Audit trail complet

---

## 🔒 Sécurité & Multi-Tenant

### Système de Permissions (Spatie)
Le module utilise 13 permissions granulaires :
- `view own driver sanctions`
- `view team driver sanctions`
- `view all driver sanctions`
- `create driver sanctions`
- `update own driver sanctions`
- `update any driver sanctions`
- `delete driver sanctions`
- `force delete driver sanctions`
- `restore driver sanctions`
- `archive driver sanctions`
- `unarchive driver sanctions`
- `export driver sanctions`
- `view driver sanction statistics`
- `view driver sanction history`

### Isolation Multi-Tenant
- ✅ Chaque sanction est liée à une `organization_id`
- ✅ Les admins ne voient que les sanctions de leur organisation
- ✅ Les superviseurs ne voient que leurs sanctions ou celles de leur équipe
- ✅ Seul Super Admin peut voir toutes les organisations

### Policy Enterprise-Grade
- ✅ Contrôle d'accès basé sur les rôles ET l'organisation
- ✅ Règles métier : modification limitée dans le temps pour superviseurs
- ✅ Protection contre l'archivage prématuré (30 jours minimum)
- ✅ Impossible de modifier une sanction archivée

---

## 📊 Types de Sanctions

Le système gère 4 types de sanctions avec sévérité croissante :

1. **Avertissement Verbal** (sévérité 1)
   - Badge jaune 🟡
   - Le moins sévère

2. **Avertissement Écrit** (sévérité 2)
   - Badge orange 🟠
   - Avec documentation obligatoire

3. **Mise à Pied** (sévérité 3)
   - Badge rouge 🔴
   - Suspension temporaire

4. **Mise en Demeure** (sévérité 4)
   - Badge rouge foncé 🔴
   - La plus sévère, dernière étape avant licenciement

---

## 🐛 Dépannage

### Problème : Page blanche sur `/admin/sanctions`

**Solution** :
1. Vérifiez que le seeder a bien été exécuté
2. Vérifiez les logs : `storage/logs/laravel.log`
3. Assurez-vous que l'utilisateur a au moins une permission `view * driver sanctions`

### Problème : Menu Chauffeurs ne s'affiche pas

**Solution** :
1. Vérifiez que vous avez la permission `view-drivers` ou `view drivers`
2. Videz le cache : `php artisan cache:clear`
3. Vérifiez que Alpine.js est chargé correctement

### Problème : Erreur 403 Forbidden

**Solution** :
1. Vérifiez que votre utilisateur a bien un rôle assigné
2. Vérifiez que le rôle a les permissions nécessaires
3. Vérifiez que `organization_id` est bien défini sur votre utilisateur

### Problème : Fichiers ne s'uploadent pas

**Solution** :
1. Vérifiez que le dossier `storage/app/public/sanctions` existe et est accessible en écriture
2. Créez le lien symbolique : `php artisan storage:link`
3. Vérifiez les limites PHP : `upload_max_filesize` et `post_max_size` dans `php.ini`

---

## 📞 Support

En cas de problème persistant, vérifiez :
1. Les logs Laravel : `storage/logs/laravel.log`
2. Les logs Docker : `docker-compose logs -f app`
3. La console navigateur pour les erreurs JavaScript

---

## 🎉 Module Prêt !

Une fois toutes les étapes suivies, le module Sanctions est opérationnel et prêt à l'emploi avec :
- ✅ Interface moderne et intuitive
- ✅ Sécurité enterprise-grade
- ✅ Multi-tenant parfaitement isolé
- ✅ Permissions granulaires
- ✅ Historique complet des modifications
- ✅ Upload de pièces jointes
- ✅ Export des données

**Bon travail ! 🚀**
