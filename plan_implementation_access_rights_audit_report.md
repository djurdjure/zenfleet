# Plan d'Implementation Expert International
## Refonte Enterprise du Systeme de Roles, Permissions et Droits d'Acces de Zenfleet

## Journal d'avancement

Cette section doit etre maintenue a jour au fil des implementations pour suivre :

- ce qui a ete effectivement realise
- ce qui reste a faire
- les incidents rencontres
- les arbitrages d'architecture

### Etat global

| Phase | Statut | Commentaire |
|---|---|---|
| Phase 0 - Preparation et securisation | En cours | garde-fous initiaux implementes |
| Phase 1 - Cartographie exhaustive | Non demarree | depend des exports d'inventaire complets |
| Phase 2 - Registre central | Non demarree | bloquee volontairement avant freeze complet |
| Phase 3 - Compatibilite transitoire | Non demarree | depend du catalogue canonique |
| Phase 4 - Migration donnees RBAC | Non demarree | interdite avant cartographie |
| Phase 5 - Refactor role assignment | Non demarree | depend du design service cible |
| Phase 6 - Convergence authorization | Non demarree | depend des phases 2 a 5 |
| Phase 7 - Durcissement multi-tenant et CI | Non demarree | depend de la convergence RBAC |

### Phase 0 - Implementations realisees

#### 1. Garde-fou de gel RBAC

Implementation ajoutee :

- [app/Console/Commands/RbacFreezeCheck.php](/home/lynx/projects/zenfleet/app/Console/Commands/RbacFreezeCheck.php)

Objectif :

- proteger les entrypoints actifs contre le rebranchement accidentel de seeders RBAC historiques
- formaliser un premier niveau de gouvernance sans modifier le comportement runtime des permissions

#### 2. Configuration centralisee du gel

Implementation ajoutee :

- [config/rbac_freeze.php](/home/lynx/projects/zenfleet/config/rbac_freeze.php)

Contenu introduit :

- roles officiels provisoires de Phase 0
- liste des seeders RBAC historiques deprecies
- entrypoints actifs proteges
- attentes minimales sur les policies referencees

#### 3. Documentation de gouvernance Phase 0

Implementation ajoutee :

- [RBAC_PHASE_0_FREEZE_GOVERNANCE.md](/home/lynx/projects/zenfleet/docs/reports/RBAC_PHASE_0_FREEZE_GOVERNANCE.md)

Objectif :

- documenter la doctrine de gel RBAC
- expliciter ce qui est autorise, deprecie et protege

#### 4. Integration CI

Implementation ajoutee :

- etape `RBAC Freeze Guardrail` dans [ci.yml](/home/lynx/projects/zenfleet/.github/workflows/ci.yml)

Effet :

- toute reference future a un seeder RBAC historique depuis les entrypoints standards devient bloquante

#### 5. Tests de non-regression du guardrail

Implementation ajoutee :

- [RbacFreezeCheckCommandTest.php](/home/lynx/projects/zenfleet/tests/Feature/Console/RbacFreezeCheckCommandTest.php)

Cas couverts :

- echec si un seeder RBAC deprecie est rebranche dans un entrypoint actif
- warnings toleres sans echec tant qu'on reste sur de la dette historique deja connue

### Phase 0 - Probleme rencontres

#### Probleme 1 - Le repository contient deja une dette RBAC significative

Constat :

- le code contient encore plusieurs seeders RBAC concurrents
- `UserPolicy` et `OrganizationPolicy` restent references sans fichier correspondant
- la base contient encore `34` permissions legacy

Impact :

- un guardrail strict base sur `warning = echec` aurait casse immediatement le flux CI

Correction apportee :

- le guardrail de Phase 0 distingue maintenant :
  - `errors` bloquants
  - `warnings` informatifs
- la CI execute le guardrail en mode bloquant uniquement sur les erreurs

Decision d'architecture :

- Phase 0 gele l'aggravation
- elle ne tente pas encore de supprimer la dette existante

#### Probleme 2 - Un gel trop agressif aurait modifie le comportement de production

Constat :

- certaines dettes sont structurelles mais actives
- les corriger maintenant modifierait le comportement runtime des acces

Correction apportee :

- la commande `rbac:freeze-check` est volontairement file-system oriented
- elle n'altère ni les permissions, ni les roles, ni les policies

Decision d'architecture :

- aucun changement runtime d'autorisation n'est introduit en Phase 0

#### Probleme 3 - Le chantier doit rester compatible avec l'etat actuel du bootstrap

Constat :

- `DatabaseSeeder` utilise encore une pile de seeders qui n'est pas finale mais reste la pile active de reference

Correction apportee :

- les seeders vraiment historiques et contradictoires ont ete marques comme deprecies dans la governance
- le seeding standard actuel n'a pas ete casse

Decision d'architecture :

- on gele d'abord les entrypoints
- on ne remplace la pile active qu'en phase catalogue

### Phase 0 - Etat de sortie courant

Acquis :

- gel RBAC technique introduit
- gouvernance Phase 0 documentee
- CI protegee contre la reintroduction de seeders historiques dans les entrypoints standards
- premiere traçabilite d'avancement ajoutee au plan

Verification effectuee :

- `docker compose exec -u zenfleet_user php php artisan rbac:freeze-check`
  - resultat : succes avec warnings attendus sur dette historique
- `docker compose exec -u zenfleet_user php php artisan test tests/Feature/Console/RbacFreezeCheckCommandTest.php`
  - resultat : `2` tests passes

Warnings actuellement confirmes par le guardrail :

- presence persistante de seeders RBAC historiques dans le repository
- absence de [UserPolicy.php](/home/lynx/projects/zenfleet/app/Policies/UserPolicy.php)
- absence de [OrganizationPolicy.php](/home/lynx/projects/zenfleet/app/Policies/OrganizationPolicy.php)

Non traite volontairement a ce stade :

- suppression des permissions legacy
- creation du catalogue canonique final
- correction des references `UserPolicy` / `OrganizationPolicy`
- refactor `secureRoleAssignment`
- convergence policies / middleware / requests

### Prochaine etape recommandee

Passer a la Phase 1 avec production des inventaires complets :

- inventaire des `34` permissions legacy
- inventaire des variantes `edit/update`
- inventaire des roles aliases
- cartographie des checks d'acces par module

## 1. Decision de validation

Apres reverification du rapport `access_rights_audit_report.md` contre le code reel et contre les audits runtime disponibles, **je valide la direction generale du rapport**, y compris la partie finale ajoutee par votre partenaire, avec les **reserves d'execution** suivantes :

- les observations de fond sont justes et peuvent servir de base de travail
- la trajectoire recommande vers un RBAC/ABAC unifie, tenant-safe et enterprise-grade est pertinente
- en revanche, certaines actions ne doivent **pas** etre executees en mode big-bang
- toute suppression des permissions legacy, tout retrait de middleware et toute re-centralisation de l'autorisation doivent etre effectues avec une strategie de transition, de compatibilite temporaire et de rollback

### Validation de fond

Les points suivants sont confirmes :

- `Spatie Teams` est actif
- `legacy_permissions = 34`
- les checks d'acces sont aujourd'hui disperses entre middleware, gates, policies, form requests, controleurs et checks de roles
- la nomenclature des permissions et des roles est incoherente
- l'assignation des roles est trop couplee au controleur utilisateur
- le blindage multi-tenant n'est pas uniforme sur tous les modeles sensibles

### Reserve d'execution

Les actions suivantes sont correctes sur le principe, mais **dangereuses si executees sans filet** :

- suppression immediate du support alias
- retrait brutal de `enterprise.permission`
- suppression directe des permissions legacy sans phase de coexistence
- reecriture globale des policies sans couverture de tests

## 2. Principes non negociables

Ce plan est construit pour elever Zenfleet au plus haut niveau **sans perte d'integrite applicative**.

### Objectifs non negociables

1. Preserver le fonctionnement des modules existants pendant toute la transition.
2. Eviter toute fuite inter-tenant pendant le chantier.
3. Maintenir la compatibilite fonctionnelle des roles existants tant que la migration n'est pas finalisee.
4. Assurer la tracabilite complete des changements de securite.
5. Rendre chaque phase mesurable, testable et reversible.

### Regles de conduite

- pas de migration destructrice sans snapshot prealable
- pas de suppression d'un mecanisme de securite avant mise en place et validation du mecanisme remplaçant
- pas de normalisation de vocabulaire sans registre central de reference
- pas de refactor des checks d'acces sans tests d'autorisation automatises

## 3. Cible architecturale

## 3.1 Modele cible

Zenfleet doit converger vers une architecture composee de quatre couches claires :

### Couche 1 - Registre canonique RBAC

Source unique de verite pour :

- permissions canoniques
- roles canoniques
- matrice role -> permissions

### Couche 2 - Service d'autorisation

Service unique responsable de :

- resolution temporaire des aliases
- checks de permission standards
- checks de role exceptionnels
- compatibilite de transition

### Couche 3 - Policies metier

Responsables uniquement de :

- regles contextuelles
- ownership
- workflow
- regles de perimetre metier

### Couche 4 - Isolation multi-tenant

Blindage systematique par :

- `Spatie Teams`
- scoping Eloquent standardise
- route model binding tenant-safe
- contexte PostgreSQL securise

## 3.2 Resultat cible

En sortie, le systeme devra respecter ces invariants :

- un seul vocabulaire de permissions
- un seul vocabulaire de roles
- aucun chemin critique base sur un alias legacy
- aucune ressource sensible accessible hors tenant
- aucune assignation de roles par SQL manuel depuis un controleur
- toutes les regressions RBAC detectees automatiquement par CI

## 4. Strategie generale de transformation

La bonne strategie n'est pas une reecriture brutale.
La bonne strategie est une **migration en 3 temps** :

1. **stabiliser**
2. **normaliser**
3. **converger**

### Strategie de compatibilite recommandee

Pendant la transition :

- **single-write canonical** : toute nouvelle ecriture doit utiliser uniquement le format canonique
- **dual-read controlled** : la lecture peut encore accepter les aliases legacy pendant une fenetre transitoire
- **sunset plan** : une fois les donnees nettoyees et les tests verts, la compatibilite legacy est retiree

## 5. Workstreams de mise en oeuvre

## Workstream A - Gouvernance et gel de surface

### Objectif

Eviter que la dette continue d'augmenter pendant le chantier.

### Actions

- declarer officiellement le chantier RBAC comme chantier transversal
- interdire toute nouvelle permission non canonique
- interdire toute creation de role hors catalogue de reference
- figer les seeders RBAC concurrents
- documenter la convention cible dans un document d'architecture court

### Livrables

- charte de nommage RBAC
- liste des roles officiels
- liste des permissions officielles
- decision record d'architecture

### Critere de sortie

- plus aucune nouvelle PR n'introduit de permissions legacy ou de variantes `edit/update` concurrentes

## Workstream B - Inventaire, cartographie et preuve d'impact

### Objectif

Etablir une cartographie exploitable avant toute migration.

### Actions

- inventorier toutes les permissions en base
- separer :
  - permissions legacy
  - permissions canoniques
  - permissions variantes
- lister tous les roles existants par organisation
- identifier les chemins d'acces par module :
  - middleware
  - policy
  - form request
  - gate
  - role check manuel
- etablir la matrice module -> mecanisme d'autorisation reel

### Livrables

- matrice de compatibilite complete
- mapping legacy -> canonique
- inventaire des roles par variante de nom

### Critere de sortie

- chaque permission legacy a un mapping valide
- chaque role ambigu a une cible officielle

## Workstream C - Registre central RBAC

### Objectif

Eliminer les contradictions entre seeders, enums et controleurs.

### Actions

- creer un registre unique des permissions canoniques
- creer un catalogue unique des roles
- choisir un verbe unique entre `update` et `edit`
- choisir une seule langue pour les identifiants persistants des roles
- remplacer la generation dispersee des permissions par un provisioning central

### Recommandation de design

Permissions :

```text
module.action
module.action.scope
module.submodule.action
```

Roles persistants :

- `Super Admin`
- `Admin`
- `Gestionnaire Flotte`
- `Superviseur`
- `Chauffeur`
- `Comptable`
- `Mecanicien`

### Livrables

- `PermissionCatalog`
- `RoleCatalog`
- seeder ou service unique `RbacProvisioner`

### Critere de sortie

- toute creation de permission ou role passe par le registre central

## Workstream D - Couche de compatibilite transitoire

### Objectif

Permettre une migration sans rupture applicative.

### Actions

- centraliser la resolution des aliases dans une seule couche
- ne plus disperser les conversions entre middleware, UI et controleurs
- garantir que `can(...)`, `authorize(...)`, Livewire, API et import/export obtiennent le meme resultat pendant la transition

### Recommandation technique

Mettre en place un resolver de compatibilite global temporaire, puis migrer progressivement vers :

- lecture canonique uniquement
- suppression des aliases une fois `legacy_permissions = 0`

### Critere de sortie

- meme decision d'autorisation quel que soit le point d'entree

## Workstream E - Refactor du moteur d'assignation des roles

### Objectif

Sortir la logique critique du controleur et la rendre auditable.

### Actions

- creer un `RoleAssignmentService`
- encapsuler :
  - validation du role cible
  - verification de l'organisation cible
  - prevention d'escalation
  - assignation via API compatible Spatie Teams
  - invalidation du cache
  - journalisation
- supprimer l'ecriture SQL manuelle de `model_has_roles` depuis `UserController`
- introduire des tests d'invariants :
  - pas d'auto-promotion
  - pas d'assignation cross-tenant
  - pas de suppression du dernier super admin

### Livrables

- service transactionnel unique
- tests unitaires et fonctionnels d'assignation

### Critere de sortie

- plus aucun controleur n'ecrit directement dans les tables pivots RBAC

## Workstream F - Rationalisation des layers d'autorisation

### Objectif

Reduire les divergences entre middleware, policies et checks imperatifs.

### Actions

- classifier les controles d'acces en deux familles :
  - controle grossier d'entree de module
  - controle metier fin
- limiter le middleware `enterprise.permission` au controle grossier si sa valeur est prouvee
- deplacer les regles fines dans les policies
- bannir progressivement les strings ad hoc dans les controleurs lorsqu'une policy de ressource existe
- documenter les cas ou un check de role est legitime

### Regle d'architecture

- route = acces grossier
- policy = regle metier
- role check = exception tres limitee

### Critere de sortie

- chaque module suit le meme pattern d'autorisation

## Workstream G - Durcissement multi-tenant integral

### Objectif

Passer d'une isolation "souvent correcte" a une isolation "systematiquement enforce".

### Actions

- appliquer un scoping uniforme aux modeles sensibles
- traiter en priorite :
  - `RepairRequest`
  - `VehicleMileageReading`
- verifier tous les listings, exports, endpoints API, jobs et actions Livewire
- auditer les `Route Model Bindings`
- formaliser le role exact du contexte PostgreSQL injecte par `SetTenantSession`
- etablir une strategie claire entre :
  - scope Eloquent
  - policy
  - RLS ou session DB

### Livrables

- matrice des ressources tenant-safe
- checklist de validation multi-tenant

### Critere de sortie

- aucune ressource sensible ne peut etre lue ou mutée hors organisation active

## Workstream H - Observabilite, audit trail et CI securite

### Objectif

Transformer la securite RBAC en systeme observable et monitorable.

### Actions

- historiser tous les changements de roles et permissions
- journaliser :
  - auteur
  - organisation
  - cible
  - diff avant/apres
  - origine web, API ou CLI
- integrer dans CI :
  - `permissions:audit`
  - `security:health-check --strict`
- ajouter une suite de tests d'autorisation par role et par module critique

### Critere de sortie

- une regression RBAC devient visible avant mise en production

## 6. Plan d'execution par phases

## Phase 0 - Preparation et securisation

### Duree indicative

3 a 5 jours

### Objectifs

- figer la surface
- preparer l'inventaire
- definir les invariants

### Actions

- backup complet des tables RBAC
- export de la matrice actuelle roles/permissions
- gel des seeders concurrents
- decision officielle sur les noms canoniques

### Go / No-Go

- pas de passage en phase 1 sans snapshot valide

## Phase 1 - Cartographie exhaustive

### Duree indicative

4 a 7 jours

### Objectifs

- savoir exactement ce qui existe

### Actions

- inventory des 34 permissions legacy
- inventory des variantes `edit/update`
- inventory des roles aliases FR/EN/snake_case
- inventory des checks d'acces par module

### Go / No-Go

- pas de phase 2 sans mapping complet legacy -> canonique

## Phase 2 - Mise en place du registre central

### Duree indicative

5 a 8 jours

### Objectifs

- installer l'autorite centrale sans casser l'existant

### Actions

- creation du catalogue canonique
- creation du provisioner RBAC unique
- harmonisation des enums et constantes
- interdiction des nouvelles permissions hors catalogue

### Go / No-Go

- pas de phase 3 sans provisioning central operationnel

## Phase 3 - Couche de compatibilite transitoire

### Duree indicative

4 a 6 jours

### Objectifs

- assurer la coexistence controlee

### Actions

- centralisation de la resolution alias
- suppression progressive des conversions dispersees
- verification sur web, API, Livewire et imports

### Go / No-Go

- pas de migration de donnees sans compatibilite runtime stable

## Phase 4 - Migration des donnees RBAC

### Duree indicative

3 a 6 jours

### Objectifs

- converger la base vers le canonique

### Actions

- migrer les affectations
- supprimer les permissions legacy apres verification
- revalider les roles des organisations
- rerun des commandes d'audit

### Critere de sortie

- `legacy_permissions = 0`

## Phase 5 - Refactor du role assignment

### Duree indicative

3 a 5 jours

### Objectifs

- fiabiliser la mutation des droits

### Actions

- mise en place du `RoleAssignmentService`
- refactor `UserController`
- tests d'invariants de securite

### Critere de sortie

- plus aucun SQL manuel RBAC en couche controleur

## Phase 6 - Convergence des layers d'autorisation

### Duree indicative

5 a 10 jours

### Objectifs

- rendre le systeme coherent

### Actions

- harmonisation middleware/policies/form requests
- reduction des checks ad hoc
- validation module par module

### Critere de sortie

- pattern unique d'autorisation documente et applique

## Phase 7 - Durcissement multi-tenant et CI

### Duree indicative

5 a 8 jours

### Objectifs

- obtenir un niveau enterprise durable

### Actions

- blindage final des modeles sensibles
- tests de non-fuite inter-tenant
- enforcement CI strict

### Critere de sortie

- green complet sur les checks securite

## 7. Strategie de tests et de validation

## 7.1 Tests obligatoires

- test par role majeur
- test par module critique
- test cross-tenant lecture
- test cross-tenant ecriture
- test d'escalation de privilege
- test de creation/modification/suppression utilisateur
- test des imports et exports
- test des endpoints API
- test Livewire sur les ecrans critiques

## 7.2 Jeux de donnees de validation

Prevoir au minimum :

- 2 organisations distinctes
- 1 super admin global
- 1 admin par organisation
- 1 gestionnaire flotte par organisation
- 1 superviseur par organisation
- 2 chauffeurs par organisation
- des vehicules, affectations, demandes de reparation, depenses et releves kilometrage dans chaque tenant

## 7.3 Commandes de validation cibles

Le plan doit viser un etat final ou les commandes suivantes sont vertes :

```bash
docker compose exec -u zenfleet_user php php artisan permissions:audit --json
docker compose exec -u zenfleet_user php php artisan security:health-check --strict
```

Etat cible :

- `legacy_permissions = 0`
- `duplicate_permissions = 0`
- `orphan_role_permissions = 0`
- `orphan_user_permissions = 0`
- `orphan_user_roles = 0`
- `organizations missing roles = 0`

## 8. Strategie de rollout et de rollback

## 8.1 Rollout recommande

- environnement de dev
- environnement de staging avec copie de donnees representative
- recette securite et recette metier
- production par fenetre controlee

## 8.2 Garde-fous obligatoires

- snapshot des tables RBAC avant chaque migration structurante
- scripts de verification post-migration
- logs d'audit renforces
- feature flag si retrait d'un mecanisme d'autorisation existant

## 8.3 Rollback

Chaque phase doit disposer de :

- point de restauration base de donnees
- commit ou lot de commits isolable
- liste des tables touchees
- procedure de retour arriere documentee

## 9. Risques majeurs et contre-mesures

| Risque | Impact | Contre-mesure |
|---|---|---|
| Suppression prematuree des permissions legacy | blocage fonctionnel | dual-read temporaire + tests complets |
| Refactor brutal des policies | regressions d'acces | migration module par module |
| Mauvaise normalisation des roles | perte de droits ou surelevation | catalogue central + tests d'invariants |
| Mauvaise gestion Teams/tenant | fuite inter-tenant | tests multi-tenant + review de binding |
| Refactor du role assignment incomplet | corruption des pivots RBAC | service transactionnel + audit trail |
| Suppression prematuree du middleware enterprise | trous de securite | conserver jusqu'a equivalence prouvee |

## 10. Definition of Done

Le chantier ne doit etre considere termine que lorsque les conditions suivantes sont simultanement vraies :

1. toutes les permissions sont canoniques
2. tous les roles sont normalises
3. aucun controleur n'ecrit directement dans les tables RBAC
4. tous les modeles sensibles sont tenant-safe
5. les policies couvrent les regles metier critiques
6. les checks d'autorisation suivent un pattern unique
7. les audits runtime sont verts
8. la CI bloque toute regression RBAC
9. le comportement fonctionnel des modules Zenfleet est preserve

## 11. Recommandation finale

Je valide le rapport comme **base strategique de tres bon niveau**, y compris la section de fin, a condition de l'executer avec une discipline d'implementation enterprise :

- migration progressive
- compatibilite transitoire
- zero improvisation sur les permissions
- zero refactor big-bang
- zero suppression sans preuve de couverture

La bonne ambition pour Zenfleet n'est pas seulement de "corriger les permissions".
La bonne ambition est d'installer un **socle d'autorisation industriel**, durable, testable, observable et compatible avec une croissance SaaS multi-tenant exigeante.

## 12. Plan d'implementation ultra detaille

Cette section transforme la strategie en **plan operatoire executable**.

## 12.1 Macro-sequencement recommande

Ordre d'execution recommande, sans chevauchement destructeur :

1. cadrage et gel
2. inventaire runtime + base + code
3. registre canonique
4. couche de compatibilite centralisee
5. migration des donnees RBAC
6. refactor assignation des roles
7. harmonisation policies / requests / middleware
8. durcissement multi-tenant
9. audit trail et CI securite
10. extinction du legacy

## 12.2 Regles de pilotage de projet

### Mode d'execution recommande

- loter les changements par domaine
- merger par increments courts
- eviter les branches longues
- imposer une revue securite sur chaque lot

### Branching recommande

- `feature/rbac-phase-0-freeze`
- `feature/rbac-phase-1-inventory`
- `feature/rbac-phase-2-catalog`
- `feature/rbac-phase-3-compat`
- `feature/rbac-phase-4-data-migration`
- `feature/rbac-phase-5-role-assignment`
- `feature/rbac-phase-6-auth-convergence`
- `feature/rbac-phase-7-tenant-hardening`
- `feature/rbac-phase-8-observability-ci`

### Rythme de livraison

- une phase = un lot coherent
- validation technique + validation metier + validation securite avant passage a la phase suivante

## 13. Backlog detaille par epic

## Epic A - Freeze RBAC

### Objectif

Empêcher toute aggravation de la dette RBAC pendant la transformation.

### Taches

1. Ajouter une note d'architecture RBAC de reference.
2. Geler les seeders RBAC historiques non canoniques.
3. Interdire toute nouvelle permission hors catalogue.
4. Interdire toute nouvelle variante de role.
5. Ajouter un controle CI simple sur les patterns interdits.

### Fichiers cibles

- [composer.json](/home/lynx/projects/zenfleet/composer.json)
- [app/Enums/Permission.php](/home/lynx/projects/zenfleet/app/Enums/Permission.php)
- [database/seeders/RolesAndPermissionsSeeder.php](/home/lynx/projects/zenfleet/database/seeders/RolesAndPermissionsSeeder.php)
- [database/seeders/ZenFleetRolesPermissionsSeeder.php](/home/lynx/projects/zenfleet/database/seeders/ZenFleetRolesPermissionsSeeder.php)
- [database/seeders/MasterPermissionsSeeder.php](/home/lynx/projects/zenfleet/database/seeders/MasterPermissionsSeeder.php)
- [database/seeders/EnterpriseRbacSeeder.php](/home/lynx/projects/zenfleet/database/seeders/EnterpriseRbacSeeder.php)
- [database/seeders/PermissionSeeder.php](/home/lynx/projects/zenfleet/database/seeders/PermissionSeeder.php)

### Critere d'acceptation

- aucune PR ne peut introduire une permission avec espace
- aucune PR ne peut introduire un role hors catalogue

## Epic B - Inventaire RBAC exhaustif

### Objectif

Obtenir une cartographie complete et fiable avant toute transformation.

### Taches code

1. Etendre `permissions:audit` pour remonter :
   - legacy permissions
   - variantes `edit/update`
   - permissions non cataloguees
   - roles non catalogues
2. Etendre `security:health-check` pour remonter :
   - utilisateurs sans role
   - roles globaux non conformes
   - pivot `model_has_roles` incoherent avec `organization_id`
3. Produire un export JSON versionne.

### Taches data

1. Extraire la matrice `role -> permissions`.
2. Extraire la matrice `organization -> roles`.
3. Lister les permissions directes utilisateurs.
4. Lister les checks de role par nom en code.

### Fichiers cibles

- [app/Console/Commands/AuditPermissions.php](/home/lynx/projects/zenfleet/app/Console/Commands/AuditPermissions.php)
- [app/Console/Commands/SecurityHealthCheck.php](/home/lynx/projects/zenfleet/app/Console/Commands/SecurityHealthCheck.php)
- [app/Console/Kernel.php](/home/lynx/projects/zenfleet/app/Console/Kernel.php)
- [app/Support/PermissionAliases.php](/home/lynx/projects/zenfleet/app/Support/PermissionAliases.php)

### Commandes cibles

```bash
docker compose exec -u zenfleet_user php php artisan permissions:audit --json
docker compose exec -u zenfleet_user php php artisan security:health-check --strict
```

### Critere d'acceptation

- rapport JSON exploitable
- mapping complet des `34` permissions legacy
- liste explicite des roles a normaliser

## Epic C - Catalogue canonique

### Objectif

Imposer un seul vocabulaire officiel.

### Taches

1. Creer `PermissionCatalog`.
2. Creer `RoleCatalog`.
3. Remplacer les enums ou constantes floues.
4. Definir clairement la convention :
   - `update` retenu
   - pas de `edit`
5. Aligner les alias de compatibilite.

### Recommandation d'implementation

Introduire deux classes de reference :

- `App\Security\PermissionCatalog`
- `App\Security\RoleCatalog`

### Fichiers cibles

- [app/Enums/Permission.php](/home/lynx/projects/zenfleet/app/Enums/Permission.php)
- [app/Support/PermissionAliases.php](/home/lynx/projects/zenfleet/app/Support/PermissionAliases.php)
- [config/enterprise_permissions.php](/home/lynx/projects/zenfleet/config/enterprise_permissions.php)
- [database/migrations/2026_02_03_000000_add_canonical_permissions.php](/home/lynx/projects/zenfleet/database/migrations/2026_02_03_000000_add_canonical_permissions.php)
- [database/migrations/2026_02_04_130000_normalize_legacy_permissions.php](/home/lynx/projects/zenfleet/database/migrations/2026_02_04_130000_normalize_legacy_permissions.php)

### Critere d'acceptation

- un seul catalogue utilise comme reference
- plus aucune nouvelle permission creee hors catalogue

## Epic D - Compatibilite de transition

### Objectif

Assurer le maintien en conditions operationnelles durant la convergence.

### Taches

1. Creer un resolver global temporaire.
2. Appliquer ce resolver :
   - aux checks `can(...)`
   - aux checks middleware
   - aux usages Livewire
3. Eviter les divergences entre route access et resource policy.

### Fichiers cibles

- [app/Models/User.php](/home/lynx/projects/zenfleet/app/Models/User.php)
- [app/Http/Middleware/EnterprisePermissionMiddleware.php](/home/lynx/projects/zenfleet/app/Http/Middleware/EnterprisePermissionMiddleware.php)
- [app/Providers/AuthServiceProvider.php](/home/lynx/projects/zenfleet/app/Providers/AuthServiceProvider.php)
- [app/Support/PermissionAliases.php](/home/lynx/projects/zenfleet/app/Support/PermissionAliases.php)
- [app/Http/Controllers/Admin/RoleController.php](/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/RoleController.php)
- [app/Livewire/Admin/PermissionMatrix.php](/home/lynx/projects/zenfleet/app/Livewire/Admin/PermissionMatrix.php)
- [app/Livewire/Admin/UserPermissionManager.php](/home/lynx/projects/zenfleet/app/Livewire/Admin/UserPermissionManager.php)

### Regle de migration

- write canonical
- read canonical + alias
- remove alias only after zero legacy

### Critere d'acceptation

- meme resultat d'autorisation sur web, API et Livewire pour un meme utilisateur

## Epic E - Migration des donnees et nettoyage

### Objectif

Assainir la base RBAC sans interruption fonctionnelle.

### Taches

1. Sauvegarder :
   - `permissions`
   - `roles`
   - `role_has_permissions`
   - `model_has_permissions`
   - `model_has_roles`
2. Migrer les liens vers le canonique.
3. Traiter les permissions directes utilisateurs.
4. Supprimer les permissions legacy de la table `permissions`.
5. Reexecuter audit et health check.

### Fichiers cibles

- [database/migrations/2026_02_03_000000_add_canonical_permissions.php](/home/lynx/projects/zenfleet/database/migrations/2026_02_03_000000_add_canonical_permissions.php)
- [database/migrations/2026_02_04_130000_normalize_legacy_permissions.php](/home/lynx/projects/zenfleet/database/migrations/2026_02_04_130000_normalize_legacy_permissions.php)
- nouvelle migration de purge finale

### Preconditions

- audit inventory complet
- compatibilite runtime de transition active

### Critere d'acceptation

- `legacy_permissions = 0`
- aucun role ni utilisateur ne perd ses droits legitimes

## Epic F - Refactor de l'assignation des roles

### Objectif

Decoupler la mutation des droits de la couche HTTP.

### Taches

1. Creer `RoleAssignmentService`.
2. Creer DTO ou structure de commande d'assignation.
3. Deplacer :
   - validation role assignable
   - logique tenant
   - blocage escalation
   - cache reset
   - logging
4. Refactor `UserController`.
5. Supprimer les insertions directes dans `model_has_roles`.

### Fichiers cibles

- [app/Http/Controllers/Admin/UserController.php](/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/UserController.php)
- [app/Policies/RoleAssignmentPolicy.php](/home/lynx/projects/zenfleet/app/Policies/RoleAssignmentPolicy.php)
- [app/Services/OrganizationRoleProvisioner.php](/home/lynx/projects/zenfleet/app/Services/OrganizationRoleProvisioner.php)
- nouveau service `app/Services/Security/RoleAssignmentService.php`

### Critere d'acceptation

- aucune ecriture manuelle des pivots RBAC depuis un controleur
- tests d'escalation verts

## Epic G - Convergence des policies, requests et middleware

### Objectif

Unifier la logique d'autorisation.

### Taches

1. Créer ou retirer proprement `UserPolicy` et `OrganizationPolicy`.
2. Revoir tous les `FormRequest` d'administration.
3. Remplacer les authorizations string ad hoc par des policies de ressource la ou possible.
4. Garder `enterprise.permission` uniquement la ou sa valeur est prouvee.
5. Documenter le pattern definitif.

### Fichiers cibles prioritaires

- [app/Providers/AuthServiceProvider.php](/home/lynx/projects/zenfleet/app/Providers/AuthServiceProvider.php)
- [app/Http/Middleware/EnterprisePermissionMiddleware.php](/home/lynx/projects/zenfleet/app/Http/Middleware/EnterprisePermissionMiddleware.php)
- [app/Http/Requests/Admin/Vehicle/StoreVehicleRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/Admin/Vehicle/StoreVehicleRequest.php)
- [app/Http/Requests/Admin/Vehicle/UpdateVehicleRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/Admin/Vehicle/UpdateVehicleRequest.php)
- [app/Http/Requests/Admin/Driver/StoreDriverRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/Admin/Driver/StoreDriverRequest.php)
- [app/Http/Requests/Admin/Driver/UpdateDriverRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/Admin/Driver/UpdateDriverRequest.php)
- [app/Http/Requests/Admin/RepairRequest/StoreRepairRequestRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/Admin/RepairRequest/StoreRepairRequestRequest.php)
- [app/Http/Requests/Admin/RepairRequest/UpdateRepairRequestRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/Admin/RepairRequest/UpdateRepairRequestRequest.php)
- [app/Http/Requests/StoreVehicleMileageReadingRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/StoreVehicleMileageReadingRequest.php)
- [app/Http/Requests/UpdateVehicleMileageReadingRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/UpdateVehicleMileageReadingRequest.php)
- [app/Http/Requests/VehicleExpenseRequest.php](/home/lynx/projects/zenfleet/app/Http/Requests/VehicleExpenseRequest.php)

### Critere d'acceptation

- pattern d'autorisation documente et coherent
- absence de route critique reposant sur une logique non explicable

## Epic H - Durcissement multi-tenant

### Objectif

Elever Zenfleet au niveau SaaS multi-tenant haute exigence.

### Taches

1. Uniformiser le scoping des modeles sensibles.
2. Auditer les model bindings.
3. Auditer les queries de repository et services.
4. Verifier les jobs planifies et exports.
5. Formaliser la complementarite :
   - teams Spatie
   - scopes Eloquent
   - session PostgreSQL

### Fichiers cibles prioritaires

- [app/Models/Concerns/BelongsToOrganization.php](/home/lynx/projects/zenfleet/app/Models/Concerns/BelongsToOrganization.php)
- [app/Models/RepairRequest.php](/home/lynx/projects/zenfleet/app/Models/RepairRequest.php)
- [app/Models/VehicleMileageReading.php](/home/lynx/projects/zenfleet/app/Models/VehicleMileageReading.php)
- [app/Models/User.php](/home/lynx/projects/zenfleet/app/Models/User.php)
- [app/Repositories/Eloquent/VehicleRepository.php](/home/lynx/projects/zenfleet/app/Repositories/Eloquent/VehicleRepository.php)
- [app/Http/Middleware/SetTenantSession.php](/home/lynx/projects/zenfleet/app/Http/Middleware/SetTenantSession.php)
- [tests/Feature/MultiTenantRLSTest.php](/home/lynx/projects/zenfleet/tests/Feature/MultiTenantRLSTest.php)
- [tests/Feature/VehicleScopeTest.php](/home/lynx/projects/zenfleet/tests/Feature/VehicleScopeTest.php)
- [tests/Feature/VehicleStatusScopeTest.php](/home/lynx/projects/zenfleet/tests/Feature/VehicleStatusScopeTest.php)

### Critere d'acceptation

- aucune lecture cross-tenant possible
- aucune mutation cross-tenant possible

## Epic I - Observabilite et enforcement CI

### Objectif

Faire de la securite RBAC un systeme controle en continu.

### Taches

1. Enrichir les logs d'audit.
2. Introduire des tests d'autorisation par module.
3. Rendre les commandes d'audit bloquantes en CI.
4. Prevoir tableau de bord securite technique.

### Fichiers cibles

- [app/Console/Commands/AuditPermissions.php](/home/lynx/projects/zenfleet/app/Console/Commands/AuditPermissions.php)
- [app/Console/Commands/SecurityHealthCheck.php](/home/lynx/projects/zenfleet/app/Console/Commands/SecurityHealthCheck.php)
- [app/Http/Middleware/EnterprisePermissionMiddleware.php](/home/lynx/projects/zenfleet/app/Http/Middleware/EnterprisePermissionMiddleware.php)
- [app/Http/Middleware/PreventPrivilegeEscalation.php](/home/lynx/projects/zenfleet/app/Http/Middleware/PreventPrivilegeEscalation.php)
- [tests](/home/lynx/projects/zenfleet/tests)

### Critere d'acceptation

- CI rouge sur toute regression RBAC

## 14. Ordre de traitement par domaine fonctionnel

Pour limiter le risque, traiter les modules dans cet ordre :

1. coeur RBAC transversal
2. utilisateurs / roles / permissions
3. organisations
4. vehicules
5. chauffeurs
6. affectations
7. demandes de reparation
8. releves kilometriques
9. depenses
10. documents
11. fournisseurs
12. maintenance

### Justification

- les premiers lots portent les fondations
- les modules vehicules / chauffeurs / affectations supportent une grande partie des workflows
- `repair-requests`, `mileage-readings` et `expenses` sont plus sensibles sur la granularite contextuelle

## 15. Matrice fichiers -> phase

## Phase 0 / 1

- [app/Console/Commands/AuditPermissions.php](/home/lynx/projects/zenfleet/app/Console/Commands/AuditPermissions.php)
- [app/Console/Commands/SecurityHealthCheck.php](/home/lynx/projects/zenfleet/app/Console/Commands/SecurityHealthCheck.php)
- [app/Support/PermissionAliases.php](/home/lynx/projects/zenfleet/app/Support/PermissionAliases.php)
- [database/seeders](/home/lynx/projects/zenfleet/database/seeders)

## Phase 2 / 3

- [app/Enums/Permission.php](/home/lynx/projects/zenfleet/app/Enums/Permission.php)
- [config/enterprise_permissions.php](/home/lynx/projects/zenfleet/config/enterprise_permissions.php)
- [app/Models/User.php](/home/lynx/projects/zenfleet/app/Models/User.php)
- [app/Providers/AuthServiceProvider.php](/home/lynx/projects/zenfleet/app/Providers/AuthServiceProvider.php)
- [app/Http/Middleware/EnterprisePermissionMiddleware.php](/home/lynx/projects/zenfleet/app/Http/Middleware/EnterprisePermissionMiddleware.php)

## Phase 4

- [database/migrations/2026_02_03_000000_add_canonical_permissions.php](/home/lynx/projects/zenfleet/database/migrations/2026_02_03_000000_add_canonical_permissions.php)
- [database/migrations/2026_02_04_130000_normalize_legacy_permissions.php](/home/lynx/projects/zenfleet/database/migrations/2026_02_04_130000_normalize_legacy_permissions.php)
- nouvelle migration de purge definitive

## Phase 5

- [app/Http/Controllers/Admin/UserController.php](/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/UserController.php)
- [app/Policies/RoleAssignmentPolicy.php](/home/lynx/projects/zenfleet/app/Policies/RoleAssignmentPolicy.php)
- nouveau `RoleAssignmentService`

## Phase 6

- [app/Policies/VehiclePolicy.php](/home/lynx/projects/zenfleet/app/Policies/VehiclePolicy.php)
- [app/Policies/DriverPolicy.php](/home/lynx/projects/zenfleet/app/Policies/DriverPolicy.php)
- [app/Policies/AssignmentPolicy.php](/home/lynx/projects/zenfleet/app/Policies/AssignmentPolicy.php)
- [app/Policies/RepairRequestPolicy.php](/home/lynx/projects/zenfleet/app/Policies/RepairRequestPolicy.php)
- [app/Policies/VehicleMileageReadingPolicy.php](/home/lynx/projects/zenfleet/app/Policies/VehicleMileageReadingPolicy.php)
- [app/Policies/VehicleExpensePolicy.php](/home/lynx/projects/zenfleet/app/Policies/VehicleExpensePolicy.php)
- [app/Http/Requests](/home/lynx/projects/zenfleet/app/Http/Requests)

## Phase 7

- [app/Models/RepairRequest.php](/home/lynx/projects/zenfleet/app/Models/RepairRequest.php)
- [app/Models/VehicleMileageReading.php](/home/lynx/projects/zenfleet/app/Models/VehicleMileageReading.php)
- [app/Models/Concerns/BelongsToOrganization.php](/home/lynx/projects/zenfleet/app/Models/Concerns/BelongsToOrganization.php)
- [app/Http/Middleware/SetTenantSession.php](/home/lynx/projects/zenfleet/app/Http/Middleware/SetTenantSession.php)

## 16. Plan de test ultra detaille

## 16.1 Tests transverses RBAC

Ajouter ou renforcer les tests suivants :

- `tests/Feature/Security/RbacAuditCommandTest.php`
- `tests/Feature/Security/RoleAssignmentSecurityTest.php`
- `tests/Feature/Security/PermissionAliasCompatibilityTest.php`
- `tests/Feature/Security/TenantBoundaryAccessTest.php`
- `tests/Feature/Security/SuperAdminInvariantTest.php`

## 16.2 Tests par module

### Utilisateurs / roles

- creation utilisateur avec role du tenant
- refus d'assignation cross-tenant
- refus d'assignation `Super Admin` par non super admin
- refus d'auto-promotion

### Vehicules

- `Admin` du tenant voit ses vehicules
- admin d'un autre tenant ne voit pas
- `Chauffeur` n'accede qu'au perimetre autorise

### Affectations

- creation autorisee dans le tenant
- refus sur vehicule d'un autre tenant
- refus sur chauffeur d'un autre tenant

### Repair Requests

- `Driver` voit uniquement ses propres demandes si scope own
- `Supervisor` voit uniquement team
- `Fleet Manager` voit all org
- aucun acces cross-tenant

### Mileage Readings

- `own/team/all` respectes
- automatic/manual respectes
- refus de suppression hors perimetre

### Expenses

- vue selon scope metier
- restrictions sur depense approuvee
- refus cross-tenant

## 16.3 Tests de commandes

Verifier en CI :

```bash
docker compose exec -u zenfleet_user php php artisan permissions:audit --json
docker compose exec -u zenfleet_user php php artisan security:health-check --strict
```

## 17. Strategie de rollback ultra concrete

## 17.1 Avant chaque phase sensible

Executer :

1. snapshot structure + donnees tables RBAC
2. export JSON des audits
3. export matrice role -> permissions
4. export utilisateurs avec roles et permissions directes

## 17.2 Rollback par type de lot

### Lot catalogue

- revert code
- clear cache
- rerun audit

### Lot migration permissions

- restore snapshot tables RBAC
- clear cache Spatie
- rerun audits

### Lot role assignment

- revert service et controleur
- verifier pivots `model_has_roles`

### Lot policies / requests

- revert uniquement le module touche
- rejouer tests du module

## 18. Definition des jalons executifs

## Jalon 1 - Systeme sous controle

Conditions :

- freeze actif
- inventaire complet
- aucune nouvelle dette introduite

## Jalon 2 - Autorite canonique en place

Conditions :

- catalogue permissions
- catalogue roles
- resolver de transition actif

## Jalon 3 - Base normalisee

Conditions :

- `legacy_permissions = 0`
- audits structurels verts

## Jalon 4 - Moteur de droits industrialise

Conditions :

- plus de SQL manuel RBAC dans les controleurs
- pattern d'autorisation harmonise

## Jalon 5 - Niveau enterprise atteint

Conditions :

- tenant safety verifiee
- CI securite bloquante
- observabilite complete

## 19. Priorisation P0 / P1 / P2

## P0

- catalogue canonique
- compatibilite transitoire globale
- migration des `34` permissions legacy
- refactor `secureRoleAssignment`
- correction references `UserPolicy` / `OrganizationPolicy`
- blindage `RepairRequest` et `VehicleMileageReading`

## P1

- harmonisation middleware/policies
- reduction des role checks manuels
- audit trail complet
- CI securite stricte

## P2

- nettoyage des seeders historiques
- tableau de bord securite
- industrialisation supplementaire des exports et reporting RBAC

## 20. Recommendation finale d'execution

Si l'objectif est de preserver Zenfleet tout en l'elevant au plus haut niveau, l'ordre optimal est :

1. **observer**
2. **cataloguer**
3. **rendre compatible**
4. **migrer**
5. **durcir**
6. **retirer le legacy**

Le point le plus important de tout ce plan est le suivant :

> la refonte RBAC de Zenfleet doit etre conduite comme une migration de plateforme critique, pas comme un simple nettoyage de code.

Autrement dit :

- pilotage par invariants
- securite avant elegance
- compatibilite avant simplification
- preuves avant suppression
