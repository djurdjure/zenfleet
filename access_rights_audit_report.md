# Audit Expert Enterprise du Systeme de Roles, Permissions et Droits d'Acces

## 1. Objet du document

Ce document remplace et corrige le rapport precedent apres verification du code reel de Zenfleet.
L'objectif est double :

- valider ce qui etait juste dans le rapport initial
- corriger ce qui etait incomplet, obsolete ou inexact
- fournir une feuille de route de refactoring exploitable pour amener le systeme RBAC/ABAC multi-tenant a un niveau enterprise-grade international

Perimetre analyse :

- configuration `Spatie Permission`
- middleware d'autorisation
- `Gate` et `Policies`
- controleurs et `FormRequest`
- migrations RBAC
- seeders roles/permissions
- conventions de nommage des roles et permissions
- isolation multi-tenant au niveau applicatif

## 2. Resume executif

### Verdict

Le systeme de gestion des acces de Zenfleet est **fonctionnel mais heterogene**, avec plusieurs correctifs deja en place, toutefois **pas encore au niveau enterprise-grade international** pour un SaaS multi-tenant sensible.

Le rapport precedent avait raison sur le fait qu'il existe une derive de nomenclature et une dette technique de securite. En revanche, il etait trop absolu sur plusieurs points et ne reflettait plus l'etat reel du code.

### Evaluation synthétique

| Axe | Etat actuel | Verdict |
|---|---|---|
| Isolation multi-tenant des roles | `teams=true` actif, resolver custom en place | Correct mais incomplet |
| Standardisation des permissions | Plusieurs conventions coexistent | Insuffisant |
| Compatibilite legacy | Partiellement geree | Incomplete et incoherente |
| Policies et Gates | Nombreuses policies, mais logique dispersee | Risque eleve de drift |
| Qualite seeders RBAC | Multiples seeders contradictoires | Dette technique critique |
| Controle des escalations | Garde-fous presents | Correct mais non centralise |
| Observabilite securite | Commandes d'audit presentes | Bonne base |
| Niveau enterprise international | Non atteint | Refactoring requis |

### Preuves runtime verifiees

Audit execute dans l'environnement Docker du projet :

```bash
docker compose exec -u zenfleet_user php php artisan permissions:audit --json
```

Resultat observe :

```json
{
  "legacy_permissions": 34,
  "orphan_role_permissions": 0,
  "orphan_user_permissions": 0,
  "duplicate_permissions": 0
}
```

Puis :

```bash
docker compose exec -u zenfleet_user php php artisan security:health-check --strict
```

Resultat observe :

- `legacy_permissions = 34`
- `duplicate_permissions = 0`
- `orphan_role_permissions = 0`
- `orphan_user_permissions = 0`
- `orphan_user_roles = 0`
- `organizations missing roles = 0`
- commande en echec strict a cause des permissions legacy restantes

Conclusion immediate :

- la base n'est pas corrompue structurellement
- le systeme RBAC n'est pas proprement normalise
- la dette principale n'est plus l'absence de structure, mais la coexistence de plusieurs modeles d'autorisation

## 3. Ce que le rapport precedent avait juste

Les constats suivants sont confirmes :

- il existe bien une derive majeure de nomenclature des permissions
- le systeme a longtemps melange permissions legacy et permissions canoniques
- les checks d'appartenance a l'organisation sont encore dupliques dans plusieurs policies
- la surface d'autorisation est fragile car elle repose sur plusieurs mecanismes paralleles

## 4. Ce que le rapport precedent disait de faux ou de partiel

### 4.1 "Spatie n'utilise pas Teams"

Ce n'est plus vrai.

Constat reel :

- `config/permission.php` active `teams => true`
- `team_foreign_key => organization_id`
- `team_resolver => App\Services\OrganizationTeamResolver`

Le projet utilise donc deja la fonctionnalite Teams de Spatie.

### 4.2 "PermissionAliases n'est utilise que lors de la sauvegarde"

C'est faux ou, plus precisement, incomplet.

Constat reel :

- `PermissionAliases` est utilise dans `RoleController`
- `PermissionAliases` est utilise dans `PermissionMatrix`
- `PermissionAliases` est utilise dans `UserPermissionManager`
- surtout, `EnterprisePermissionMiddleware` l'utilise a l'execution via `PermissionAliases::resolve()`

En revanche, la compatibilite alias **n'est pas globale** :

- elle n'est pas centralisee dans `User::hasPermissionTo()`
- elle n'est pas centralisee dans `Gate::before()`
- elle n'est pas automatiquement appliquee a tous les `FormRequest`
- elle n'est pas uniformement appliquee aux appels `can(...)` disperses dans le code

La bonne formulation est donc :

> la compatibilite legacy existe, mais seulement sur une partie du chemin d'autorisation. Elle est partielle, non uniforme et source de drift.

### 4.3 "Les roles sont casses out-of-the-box"

Ce n'est plus une formulation exacte.

Le projet contient des migrations et seeders de rattrapage :

- `2026_02_03_000000_add_canonical_permissions.php`
- `2026_02_04_130000_normalize_legacy_permissions.php`
- `OrganizationRoleProvisioner`
- `security:health-check`

Le systeme n'est donc plus "brise" au sens strict. Il est **stabilise partiellement**, mais reste **architecturalement incoherent**.

## 5. Architecture reelle du controle d'acces dans Zenfleet

### 5.1 Couche roles et permissions

Base technique :

- package `spatie/laravel-permission`
- `teams=true`
- scoping par `organization_id`
- bypass global `Super Admin` via `Gate::before` dans `AuthServiceProvider`

### 5.2 Couche middleware

Deux logiques principales coexistent :

- `EnterprisePermissionMiddleware` pour le mapping route -> permission
- middleware Laravel standard `can:...`

Cela cree une double source de verite :

- une autorisation par route mappee
- une autorisation par Gate/Policy

### 5.3 Couche policy

Le projet dispose de policies pour plusieurs modules critiques :

- `VehiclePolicy`
- `DriverPolicy`
- `AssignmentPolicy`
- `RepairRequestPolicy`
- `VehicleMileageReadingPolicy`
- `VehicleExpensePolicy`
- `SupplierPolicy`
- `DocumentPolicy`
- `RolePolicy`

Mais certaines references de policies dans `AuthServiceProvider` ne correspondent a aucun fichier existant :

- `UserPolicy`
- `OrganizationPolicy`

Cela traduit une divergence entre design annonce et implementation reelle.

### 5.4 Couche multi-tenant

Trois mecanismes coexistent :

- Spatie Teams avec `organization_id`
- trait `BelongsToOrganization` avec global scope Eloquent
- checks manuels `organization_id === user->organization_id` dans les policies

Cette defense en profondeur est utile, mais actuellement non uniformisee.

### 5.5 Couche base de donnees

Le projet va plus loin que le simple RBAC applicatif :

- middleware `SetTenantSession`
- injection de contexte PostgreSQL :
  - `SET app.current_user_id`
  - `SET app.current_organization_id`

Cela indique une orientation vers un modele plus robuste de type RLS ou contexte SQL securise. C'est un bon signal enterprise.

## 6. Constats critiques prioritaires

## 6.1 Critique P0 - Derive de vocabulaire structurelle

Zenfleet utilise simultanement plusieurs dialectes de permissions :

- legacy avec espaces : `manage vehicles`
- canonique dot notation : `vehicles.update`
- variante dot avec verbe different : `vehicles.edit`
- legacy snake_case ou formats ponctuels : `edit_organizations`, `update-vehicle-status`

Le probleme ne se limite donc pas a `legacy vs canonique`. Il existe au moins quatre styles concurrents.

Exemples concrets :

- `app/Enums/Permission.php` utilise `users.update`, `vehicles.update`
- `database/seeders/ZenFleetRolesPermissionsSeeder.php` utilise `users.edit`, `vehicles.edit`
- `database/seeders/RolesAndPermissionsSeeder.php` utilise `manage vehicles`, `manage permissions`
- `PermissionController` protege ses actions avec `can:manage permissions`

Impact :

- droits accordes de facon non predictible
- complexite de maintenance elevee
- difficultes de tests
- risque d'erreurs silencieuses lors des evolutions

## 6.2 Critique P0 - Compatibilite legacy partielle et non globale

Le middleware enterprise sait resoudre certains alias. Les policies et `FormRequest`, eux, ne suivent pas tous la meme logique.

Exemple typique :

- une route peut passer via `EnterprisePermissionMiddleware`
- le controleur ou `FormRequest` peut ensuite appeler `user()->can('vehicles.update')`
- si l'utilisateur ne possede qu'une permission legacy ou une variante alternative, le resultat devient dependant du chemin d'execution

Impact :

- faux positifs ou faux negatifs d'autorisation
- comportements differents entre web, Livewire, API et import/export

## 6.3 Critique P0 - Multiplication des sources d'autorisation

Le code melange :

- `enterprise.permission`
- `can:permission`
- `authorize('permission-string')`
- `authorize('action', Model::class)`
- checks manuels `hasRole(...)`
- bypass explicites `Super Admin`

Ce n'est pas un modele d'autorisation unifie. C'est une federation artisanale de mecanismes.

Impact :

- forte probabilite de regression
- impossibilite de raisonner rapidement sur un cas d'acces
- audit externe plus couteux

## 6.4 Critique P0 - Drift des noms de roles

Le projet utilise plusieurs variantes pour un meme role :

- `Super Admin`
- `Admin`
- `Gestionnaire Flotte`
- `Fleet Manager`
- `fleet_manager`
- `Superviseur`
- `Supervisor`
- `supervisor`
- `Chauffeur`
- `Driver`
- `driver`

Impact :

- policies fragiles
- bypass role-based incertains
- assignation de roles difficile a fiabiliser
- ambiguite sur les roles de reference a provisionner par organisation

## 6.5 Critique P0 - Assignation des roles avec SQL manuel

`UserController::secureRoleAssignment()` ecrit directement dans `model_has_roles`.

Problemes :

- contourne une partie des abstractions Spatie
- augmente le risque d'ecarts avec l'evolution future du package
- rend le cache et les evenements plus delicats a garantir
- impose une logique metier dans un controleur au lieu d'un service transactionnel dedie

Pour un systeme enterprise, cette logique doit etre encapsulee dans un service RBAC unique.

## 6.6 Critique P1 - Isolation multi-tenant non uniforme au niveau modele

Le trait `BelongsToOrganization` est utilise sur plusieurs modeles sensibles, mais pas sur tous.

Cas notables observes sans ce trait :

- `RepairRequest`
- `VehicleMileageReading`

Ces modeles reposent donc principalement sur :

- les policies
- les filtres de requetes applicatifs
- les checks manuels

Ce n'est pas un niveau de blindage suffisant pour une application multi-tenant de flotte si des requetes ad hoc, exports, jobs ou endpoints annexes passent en dehors du chemin nominal.

## 6.7 Critique P1 - References de policies non resolues

`AuthServiceProvider` declare :

- `User::class => UserPolicy::class`
- `Organization::class => OrganizationPolicy::class`

Mais ces fichiers n'existent pas dans `app/Policies`.

Impact :

- dette technique cachee
- illusion d'une couverture d'autorisation plus complete qu'en realite
- risque de comportements imprevus si ces policies sont appelees ensuite

## 6.8 Critique P1 - `PermissionController` incoherent avec le modele cible

Ce controleur utilise :

```php
$this->middleware('can:manage permissions')
```

Problemes :

- il repose sur une permission legacy
- il n'est pas aligne avec la nomenclature cible `permissions.manage`
- il ne semble pas etre le point d'entree route principal actuellement

Conclusion :

- soit il doit etre supprime
- soit il doit etre remis en service proprement
- mais dans l'etat il constitue une dette technique et un piege

## 6.9 Critique P1 - Mapping alias incomplet

`PermissionAliases` mappe :

- `permissions.manage` vers `manage roles`

mais ne mappe pas explicitement :

- `manage permissions`

Cela signifie qu'une partie du legacy n'est pas couverte par le mecanisme de compatibilite.

## 6.10 Critique P1 - Seeders RBAC contradictoires

Le projet contient plusieurs seeders RBAC avec philosophies incompatibles :

- `RolesAndPermissionsSeeder`
- `ZenFleetRolesPermissionsSeeder`
- `MasterPermissionsSeeder`
- `EnterpriseRbacSeeder`
- `PermissionSeeder`
- `InitialRbacSeeder`
- `SecurityEnhancedRbacSeeder`

Ils introduisent :

- des conventions de nommage differentes
- des noms de roles differents
- des matrices de permissions differentes
- des modeles conceptuels differents

Pour un systeme enterprise, un seul registre canonique doit faire foi.

## 7. Forces existantes a conserver

Tout n'est pas a refaire. Zenfleet dispose deja de fondations solides :

- `Spatie Teams` est actif
- `OrganizationTeamResolver` est present
- `Gate::before` pour `Super Admin` existe
- `PreventPrivilegeEscalation` bloque certaines promotions illegitimes
- `OrganizationRoleProvisioner` structure la creation des roles par organisation
- `permissions:audit` et `security:health-check` existent deja
- `SetTenantSession` montre une vraie intention de securisation PostgreSQL enterprise
- plusieurs policies metiers sont deja de bonne qualite, notamment sur les workflows complexes

La bonne strategie n'est pas la re-ecriture aveugle. C'est une **convergence architecturee**.

## 8. Cible enterprise recommandee

## 8.1 Modele cible

Zenfleet doit converger vers un modele simple :

- **RBAC canonique** pour le droit brut
- **ABAC metier** via `Policies` pour les regles contextuelles
- **tenant isolation** par scoping technique systematique
- **super admin** comme exception explicite et minimale

### Regles de conception cibles

1. Une seule convention de permission.
2. Une seule convention de nommage des roles.
3. Une seule source de verite pour la matrice role -> permissions.
4. Les routes n'embarquent pas la logique metier fine.
5. Les `Policies` gerent la logique contextuelle.
6. Le multi-tenant est applique par defaut, pas au cas par cas.

## 8.2 Convention de permission recommandee

Format recommande :

```text
module.action
module.action.scope
module.submodule.action
```

Exemples :

- `vehicles.view`
- `vehicles.create`
- `vehicles.update`
- `repair-requests.approve.level1`
- `mileage-readings.view.team`
- `expenses.audit.view`

Interdits a terme :

- permissions avec espaces
- `edit` si la convention retenue est `update`
- roles utilises comme substitut de permission metier

## 8.3 Convention de roles recommandee

Roles de reference conseilles :

- `Super Admin`
- `Admin`
- `Gestionnaire Flotte`
- `Superviseur`
- `Chauffeur`
- `Comptable`
- `Mecanicien`

Puis :

- traductions UI si necessaire
- mais identifiants persistants uniques cote code et base

## 9. Plan de refactoring recommande

## Phase 0 - Gel de surface

Objectif :

- empecher l'augmentation de la dette

Actions :

- interdire toute nouvelle permission non canonique
- interdire tout nouveau role hors registre central
- documenter officiellement la convention cible
- faire echouer la CI si `permissions:audit` remonte des permissions legacy supplementaires

## Phase 1 - Registre canonique unique

Objectif :

- definir la source de verite

Actions :

- creer un registre unique des permissions canonique
- remplacer la multiplication des seeders RBAC par un seed central
- conserver les autres seeders comme historique archive ou les supprimer
- introduire un enum ou un catalogue unique pour les noms de roles

Livrable :

- un seul seeder ou service de provisioning RBAC de reference

## Phase 2 - Normalisation des donnees

Objectif :

- supprimer les 34 permissions legacy restantes

Actions :

- inventorier les 34 permissions legacy encore presentes
- verifier qu'elles ont toutes leur equivalent canonique
- migrer les affectations roles et permissions directes vers les noms cibles
- supprimer ensuite les lignes legacy de la table `permissions`
- rerun `permissions:audit` puis `security:health-check --strict`

Livrable :

- `legacy_permissions = 0`

## Phase 3 - Centralisation de la resolution des permissions

Objectif :

- faire disparaitre les differences entre route, policy, Livewire, API et `FormRequest`

Actions :

- centraliser la compatibilite temporaire legacy dans un seul resolver global
- brancher ce resolver dans `User::hasPermissionTo()` ou via un composant d'autorisation dedie
- supprimer la logique alias du middleware une fois la migration terminee
- eliminer les checks opportunistes `hasRole(...)` quand une permission metier existe deja

Livrable :

- un comportement identique quel que soit le point d'entree

## Phase 4 - Rationalisation des layers d'autorisation

Objectif :

- reduire les doubles verifications contradictoires

Actions :

- garder `enterprise.permission` uniquement pour le controle grossier d'entree de module, ou le retirer si inutile
- deplacer la logique metier fine dans les `Policies`
- bannir les autorisations par string arbitraire dans les controleurs quand une policy de ressource existe
- convertir progressivement vers :

```php
$this->authorize('update', $vehicle);
```

plutot que :

```php
$this->authorize('vehicles.update');
```

## Phase 5 - Durcissement multi-tenant

Objectif :

- passer d'un multi-tenant "souvent correct" a un multi-tenant "systematiquement enforce"

Actions :

- appliquer `BelongsToOrganization` ou un equivalent robuste a tous les modeles sensibles
- traiter en priorite :
  - `RepairRequest`
  - `VehicleMileageReading`
- auditer toutes les requetes de listing, export, batch, job et API
- verifier que tous les `Route Model Binding` sensibles sont compatibles avec le scoping tenant
- clarifier le role exact du contexte PostgreSQL injecte par `SetTenantSession`

## Phase 6 - Refactor service d'assignation des roles

Objectif :

- sortir la logique critique des controleurs

Actions :

- creer un service `RoleAssignmentService`
- encapsuler :
  - validation du role
  - verification d'organisation
  - prevention d'escalation
  - assignation
  - invalidation du cache
  - journalisation
- supprimer l'ecriture SQL manuelle depuis `UserController`
- revenir a une utilisation compatible Spatie Teams

## Phase 7 - Observabilite, audit trail et CI securite

Objectif :

- rendre les regressions visibles immediatement

Actions :

- historiser toutes les modifications de roles et permissions
- journaliser :
  - qui a change quoi
  - sur quel utilisateur ou role
  - dans quelle organisation
  - avant/apres
- ajouter des tests automatiques :
  - cross-tenant denial
  - privilege escalation denial
  - legacy permission denial apres migration
  - super admin invariants
- rendre `security:health-check --strict` obligatoire en CI

## 10. Quick wins recommandes

Ces actions peuvent etre lancees immediatement sans re-architecture totale :

1. Corriger `PermissionAliases` pour couvrir aussi `manage permissions`.
2. Supprimer ou rehabiliter proprement `PermissionController`.
3. Supprimer les references vers `UserPolicy` et `OrganizationPolicy` tant qu'elles n'existent pas, ou creer ces policies immediatement.
4. Standardiser la paire `edit/update` en choisissant un seul verbe.
5. Introduire un catalogue officiel des noms de roles.
6. Lancer la purge des permissions legacy jusqu'a atteindre `0`.
7. Centraliser l'assignation de roles dans un service unique.
8. Appliquer le scoping organisationnel sur `RepairRequest` et `VehicleMileageReading`.
9. Auditer tous les `hasRole(...)` et les remplacer par des permissions la ou pertinent.
10. Ajouter un test de non-regression pour chaque role majeur et chaque module critique.

## 11. Ordre d'execution conseille

Ordre recommande pour limiter le risque :

1. Figement du vocabulaire RBAC.
2. Registre canonique unique.
3. Migration des permissions legacy.
4. Refactor du role assignment.
5. Harmonisation policies et `FormRequest`.
6. Durcissement multi-tenant des modeles.
7. CI securite et audit trail complet.

## 12. Conclusion finale

Le rapport initial etait pertinent sur le fond, mais il n'etait plus suffisamment exact ni complet pour servir de reference senior.

L'etat reel de Zenfleet est le suivant :

- le socle enterprise existe deja
- la securite n'est pas anarchique
- plusieurs garde-fous sont presents
- mais le systeme souffre d'un **drift architectural important**

La priorite n'est pas de re-creer un RBAC from scratch.
La priorite est de **faire converger** l'existant vers un modele unique, testable, tenant-safe et maintenable.

### Verdict final

Zenfleet n'est pas encore au niveau international enterprise-grade sur la gouvernance des permissions et des acces, mais il est **proche d'un bon standard** si la roadmap ci-dessus est executee avec discipline.

### Cible de sortie attendue

Le systeme devra atteindre les criteres suivants :

- `legacy_permissions = 0`
- un seul registre canonique roles/permissions
- aucun check d'acces critique base sur un nom de role non normalise
- toutes les ressources sensibles scopees tenant par defaut
- aucune assignation de role par SQL manuel hors service RBAC dedie
- `security:health-check --strict` vert en continu

