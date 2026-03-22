# Phase 0 RBAC Freeze Governance

## Objectif

Ce document fixe les garde-fous minimaux de la Phase 0 de la refonte RBAC de Zenfleet.

Le but n'est pas encore de normaliser completement le systeme, mais de :

- geler l'aggravation de la dette
- proteger les entrypoints actifs
- definir le perimetre officiel de reference

## Decision d'architecture

### Entry points actifs autorises

Le bootstrap standard RBAC doit continuer a passer par :

- `DatabaseSeeder`
- le workflow CI principal

### Seeders RBAC historiques consideres comme deprecies

Les seeders suivants restent presents pour l'historique ou des usages ponctuels de maintenance, mais ne doivent plus etre relies aux flux standards :

- `RolesAndPermissionsSeeder`
- `MasterPermissionsSeeder`
- `PermissionSeeder`
- `EnterpriseRbacSeeder`
- `SecurityEnhancedRbacSeeder`
- `EnterprisePermissionsSeeder`
- `InitialRbacSeeder`
- `SuperAdminSeeder`
- `EnterpriseUsersSeeder`

### Catalogue provisoire des roles officiels

Le gel Phase 0 reconnait comme roles de reference :

- `Super Admin`
- `Admin`
- `Gestionnaire Flotte`
- `Superviseur`
- `Chauffeur`
- `Comptable`
- `Mecanicien`

Ce catalogue est provisoire et sera remplace par un `RoleCatalog` canonique dans la phase suivante.

## Guardrail technique

La commande suivante constitue le garde-fou de Phase 0 :

```bash
php artisan rbac:freeze-check
```

Elle verifie au minimum :

- l'absence de seeders RBAC historiques dans les entrypoints actifs
- l'unicite des roles officiels declares
- la presence ou non de dettes structurelles deja connues en warning

## Portee du gel

Le gel Phase 0 n'a volontairement pas pour but de :

- supprimer les permissions legacy
- refactorer les policies
- changer le comportement d'autorisation en production

Il sert uniquement a stabiliser le terrain avant les migrations plus profondes.
