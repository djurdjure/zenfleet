# Plan d'Implémentation : Sécurisation et Standardisation ZenFleet

Ce plan vise à corriger la faille critique d'importation et à unifier le système de permissions.

## 🎯 Objectifs mesurables
- Zéro mise à jour cross‑tenant sur import (test automatisé + test manuel).
- Conventions de permissions unifiées `resource.action`.
- Middleware en mode "fail‑closed" (routes non mappées refusées).
- Audit logs pour actions critiques.
- Zéro régression fonctionnelle (tests automatisés + scénarios métiers).

## 🧩 Phase 0 : Préparation & Compatibilité (Safe‑by‑Design)
### 0.1. Gouvernance et sauvegardes
- **Snapshot:** tag git + export DB avant changement.
- **Plan de rollback:** scripts prêts pour réactiver les anciennes permissions et invalidations cache.

### 0.2. Inventaire RBAC (baseline)
- **BDD:** extraction de toutes les permissions et rôles existants (par org).
- **Code:** scan des occurrences `can()`, `@can`, `hasRole()` et middleware.
- **Sortie attendue:** mapping complet `route → permission → rôle`.

### 0.3. Migration "dual‑read/dual‑write"
- Ajouter une couche de compatibilité temporaire:
  - si `vehicles.view` absent, fallback `view vehicles`.
  - si `drivers.create` absent, fallback `create drivers`.
- Maintenir les deux noms en parallèle pendant la transition.

### 0.4. Cache permissions (Spatie)
- **Pré‑prod:** valider `permission:cache-reset`.
- **Prod:** prévoir flush à chaque release RBAC.

## 🚨 Phase 1 : Correctif de Sécurité Critique (Immédiat)

### 1.1. Sécuriser `DriversImport.php`
**Fichier Cible :** `app/Livewire/Admin/Drivers/DriversImport.php`
**Ligne actuelle à corriger :** `#L373` (recherche d’existant non scopée)

**Action :** Modifier la méthode `importDriver` pour scoper la recherche de doublons.

```php
// AVANT
$existing = Driver::where('license_number', $data['license_number'])->first();

// APRÈS
$existing = Driver::where('license_number', $data['license_number'])
    ->where('organization_id', auth()->user()->organization_id)
    ->first();
```

**Vérification :**
*   Test manuel : Essayer d'importer un chauffeur avec un numéro de permis existant dans une AUTRE organisation. Il doit être créé comme nouveau (ou rejeté selon règles métier), mais JAMAIS écraser l'existant de l'autre org.
*   Test automatisé : voir `tests/Feature/Security/DriverImportTest.php`.

### 1.2. Couverture imports/exports adjacents
- **Inventaire imports:** drivers, vehicles, maintenance, etc.
- **Règle:** toutes recherches d’existants DOIVENT être scoped par `organization_id`.
- **Règle:** aucune écriture cross‑tenant (audit logs + tests).

## 🛠️ Phase 2 : Standardisation RBAC (permissions)

### 2.1. Migration des données (Permissions)
Créer une migration Laravel ou un Seeder pour renommer les permissions en base.
*   `view vehicles` -> `vehicles.view`
*   `create drivers` -> `drivers.create`
*   ... et ainsi de suite pour toutes les ressources.

### 2.2. Mise à jour du Middleware
**Fichier Cible :** `app/Http/Middleware/EnterprisePermissionMiddleware.php`

**Action :** Mettre à jour le tableau `$routePermissionMap` pour utiliser exclusivement la notation par points.

### 2.3. Mise à jour du Code (Search & Replace)
Rechercher toutes les occurrences de `can('view vehicles')` etc. et les remplacer par la nouvelle notation.
*   Policies
*   Controllers
*   Vues Blade (`@can`)
*   Menus de navigation

### 2.4. Plan de déploiement safe (RBAC)
- **Étape A:** Ajouter alias temporaires (dual‑read).
- **Étape B:** Migrer BDD (permissions).
- **Étape C:** Mettre à jour code (can, policies, menus).
- **Étape D:** Flush cache permissions + validation.
- **Étape E:** Retirer alias legacy après validation.

## 🧪 Plan de Vérification

### Tests Automatisés
*   Créer un test unitaire `tests/Feature/Security/DriverImportTest.php` qui simule deux organisations et tente un import conflictuel.
*   Ajouter un test RBAC : vérifier qu’une route sans permission renvoie 403.
*   Ajouter un test de menu : les menus masqués ne s’affichent pas sans permission.

### Tests Manuels
1.  **Import Chauffeur :** Valider qu'un admin ne peut pas impacter les données d'un autre tenant.
2.  **Navigation :** Vérifier que tous les menus sont toujours visibles après le changement de nom des permissions.
3.  **Actions CRUD :** Vérifier que les boutons Modifier/Supprimer fonctionnent toujours.
4.  **Cache permissions :** valider un comportement correct après flush et après redéploiement.
5.  **Exports:** vérifier l’export avec un utilisateur sans permission (doit refuser).

## 🛡️ Phase 3 : Hardening & Logs
- **Fail‑Closed:** refuser toute route non mappée au middleware.
- **Audit logs:** actions critiques (create/update/delete/export) avec contexte (IP, org, user, old/new).
- **Exports & Storage:** vérifier le scope tenant sur fichiers et liens.
 - **Jobs/Queues:** vérifier l’absence de `withoutGlobalScopes()` et vérifier l’org dans chaque job.

### Reste à finaliser (Phase 3)
- **Validation UI RBAC:** tester sauvegarde rôles/permissions par organisation après migrations.
- **Audit legacy:** exécuter l’audit permissions et confirmer zéro legacy/orphans.
- **Vérification exports & dépôts:** confirmer refus quand permission retirée.

## 📊 Phase 4 : Monitoring & Gouvernance
- **Alertes:** déclencher alerte sur tentative d’accès cross‑tenant.
- **Dashboards:** taux d’échec permissions, opérations critiques, exports.
- **Revue périodique:** audit permissions trimestriel.

## 🧭 Phase 5 : Gouvernance Active & Auto‑remédiation
- **Health check RBAC:** contrôle hebdomadaire (legacy, doublons, orphelins).
- **Couverture des rôles par organisation:** détection des orgs sans rôles + auto‑provisionnement sécurisé.
- **Journalisation sécurité:** traces en canal `audit` pour chaque contrôle.
- **Commandes dédiées:** `security:health-check` et `roles:ensure-organizations`.

## 🧩 Phase 6 : Assurance Continue & Verrouillages
- **Provisionnement automatique des rôles:** observer `Organization` pour garantir des rôles à toute création (UI, seeder, script).
- **Mode strict CI/CD:** `security:health-check --strict` échoue si anomalies détectées.
- **Proof of compliance:** critères “zéro legacy/orphan/duplicate” requis avant déploiement.

## ✅ Checklist de sécurité avant release
- Backup DB effectué.
- Cache permissions invalidé.
- Tests sécurité passés.
- Journalisation activée.
- Monitoring actif.

## 🧾 Journal d’implémentation (à maintenir)
- **[02/02/2026]** Plan enrichi (threat model, dual‑read, monitoring, checklists).
- **[02/02/2026]** Correctif appliqué: `DriversImport.php` scoping par `organization_id` pour éviter toute mise à jour cross‑tenant.
- **[02/03/2026]** Phase 2 démarrée: alias permissions (dual‑read) via `PermissionAliases`, Gate::before, middleware en dot‑notation, migration de permissions canoniques, policies/controllers/views alignés.
- **[02/04/2026]** Phase 2 validée (import cross‑tenant): contraintes uniques chauffeurs scoping org (`license_number`, `employee_number`, `personal_email`) + restauration des enregistrements soft‑deleted à l'import, suppression des collisions inter‑org lors de la création/édition.
- **[02/04/2026]** Phase 3 (Hardening & Logs) : fail‑closed configurable activé dans `EnterprisePermissionMiddleware` + mapping complété pour routes admin users/roles, middleware d’audit enrichi (exports GET, contexte org, route params, durée), audit appliqué à toute la zone admin.
- **[02/04/2026]** Phase 3 (correctif validation) : permissions dépôts imposées côté Livewire (`ManageDepots`) + masquage UI des actions sans droits, guide validation mis à jour.
- **[02/04/2026]** Phase 3 (hardening RBAC) : désactivation par défaut des permissions directes utilisateur via `use_custom_permissions`, pour empêcher les accès résiduels après modification d’un rôle.
- **[02/04/2026]** Phase 3 (migrations) : migrations de normalisation exécutées avec succès, validation UI et audit legacy encore requis.
- **[02/06/2026]** Phase 5 : health‑check RBAC automatisé + provisioning des rôles par organisation (commande `security:health-check`, provisioner, tâche planifiée).
- **[02/06/2026]** Phase 6 : observer `Organization` pour auto‑provision des rôles + mode strict `security:health-check --strict`.
