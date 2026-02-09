# Passe de Verification UI Guidee - ZenFleet

Date: 2026-02-09
Perimetre: validation fonctionnelle UI multi-modules apres correctifs RBAC + dataset de validation
Organisation de test: `ZenFleet Validation Lab` (id `12`)

## 1) Preconditions

1. Build assets:
```bash
docker compose exec -u zenfleet_user node yarn build
```
2. Clear caches:
```bash
docker compose exec -u zenfleet_user php php artisan optimize:clear
docker compose exec -u zenfleet_user php php artisan permission:cache-reset
```
3. Dataset de validation (idempotent):
```bash
docker compose exec -u zenfleet_user php php artisan zenfleet:seed-validation-dataset
```
4. Monitoring logs (terminal separe):
```bash
docker compose exec -u zenfleet_user php sh -lc '> /var/www/html/storage/logs/laravel.log'
docker compose exec -u zenfleet_user php tail -f /var/www/html/storage/logs/laravel.log
```

## 2) Comptes de test

- Admin: `admin.validation@validation.zenfleet.local` / `Admin@123!`
- Gestionnaire Flotte: `flotte.validation@validation.zenfleet.local` / `Flotte@123!`
- Superviseur: `superviseur.validation@validation.zenfleet.local` / `Superviseur@123!`
- Chauffeur 1: `chauffeur1.validation@validation.zenfleet.local` / `Chauffeur1@123!`
- Chauffeur 2: `chauffeur2.validation@validation.zenfleet.local` / `Chauffeur2@123!`
- Chauffeur 3: `chauffeur3.validation@validation.zenfleet.local` / `Chauffeur3@123!`

## 3) Gates techniques valides avant UI

- `security:health-check`:
  - `Organizations missing roles = 0`
  - `Orphan role permissions = 0`
  - `Orphan user roles = 0`
- Route map compile correctement (`php artisan route:list`) apres correction namespace controllers maintenance.

## 4) Verification module par module

## Module A - Authentification / Navigation globale

URL: `http://localhost/login`

Checks:
- Login Admin reussi.
- Menu admin complet visible.
- Aucun 500 dans console/browser.
- Logout/Login de nouveau sans perte de session.

KO critique:
- 500 Livewire update
- erreur permission immediate sur dashboard admin

## Module B - Dashboard

URL: `http://localhost/admin/dashboard`

Checks:
- Les cartes KPI se chargent.
- Les sections analytics ne cassent pas le rendu.
- Pas de blank cards, pas de spinner infini.
- Coherence chiffres: vehicules/chauffeurs > 0 pour org de test.

KO critique:
- page blanche, 500, widgets non rendus

## Module C - Utilisateurs

URLs:
- `http://localhost/admin/users`
- `http://localhost/admin/users/create`

Checks:
- Liste affiche les 6 comptes de l'organisation 12.
- Creation user: select role affiche roles organisation (pas de doublon `Super Admin`).
- Edition user: changement role fonctionne.
- Validation formulaire: message explicite + champ en erreur.

KO critique:
- role super admin duplique
- autres roles absents
- 403 incoherent pour Admin org

## Module D - Roles & Permissions

URL: `http://localhost/admin/roles/3/edit` (adapter id selon environnement)

Checks:
- Matrice charge sans crash navigateur.
- Toggle permission applique et persiste apres refresh.
- Aucune erreur Livewire `Could not find Livewire component in DOM tree`.
- Log Laravel: presence event `Role permissions updated` quand sauvegarde.

KO critique:
- freeze navigateur
- boucle XHR 500

## Module E - Organisations

URLs:
- `http://localhost/admin/organizations`
- `http://localhost/admin/organizations/create`

Checks:
- Organisation `ZenFleet Validation Lab` visible.
- Stats organisation non nulles.
- Creation org test supplementaire OK.

KO critique:
- exception RBAC/teams lors creation

## Module F - Vehicules

URLs:
- `http://localhost/admin/vehicles`
- `http://localhost/admin/vehicles/create`

Checks:
- 10 vehicules visibles pour org 12.
- Statuts coherents (affecte, maintenance, panne, parking).
- Creation vehicule: validations UI appliquees.
- Liste/filtre/recherche fonctionnent.

KO critique:
- vehicules invisibles alors que DB > 0
- erreurs 500 sur index/create

## Module G - Chauffeurs

URLs:
- `http://localhost/admin/drivers`
- `http://localhost/admin/drivers/create`

Checks:
- 3 chauffeurs visibles.
- Date expiration permis calculee (10 ans - 1 jour) sur formulaire si regle active.
- Edition chauffeur preserve relation user/role.
- Validation unifiee visible sur champs invalides.

KO critique:
- chauffeur sans role ou role incoherent
- champs invalides acceptes sans feedback

## Module H - Affectations

URLs:
- `http://localhost/admin/assignments`
- `http://localhost/admin/assignments/create`

Checks:
- 3 affectations actives + historique terminee visible.
- Impossible de creer chevauchement chauffeur ou vehicule (instant T).
- Fin d'affectation libere correctement disponibilite.
- Conflits zero verifies en base pour org 12.

KO critique:
- chevauchement accepte
- ressources non liberees apres fin

## Module I - Depenses

URLs:
- `http://localhost/admin/expenses`
- `http://localhost/admin/expenses/analytics`

Checks:
- 20 depenses chargees.
- TVA/Total calcules correctement (colonnes generees PostgreSQL).
- Aucun crash trigger audit lors create/update depense.
- Exports/analytics se chargent sans 500.

KO critique:
- erreur SQL trigger `log_expense_changes`
- erreurs colonnes generees inserees manuellement

## Module J - Maintenance

URLs:
- `http://localhost/admin/maintenance`
- `http://localhost/admin/maintenance/operations`
- `http://localhost/admin/maintenance/types`
- `http://localhost/admin/maintenance/providers`

Checks:
- Pages accessibles (namespace controllers corrige).
- 3 operations seed visibles.
- Types de maintenance CRUD accessible.
- Providers accessibles sans erreur de classe controller.

KO critique:
- ReflectionException controller inexistant
- 500 sur routes types/providers

## Module K - Kilometrage

URL: `http://localhost/admin/mileage-readings`

Checks:
- 30 releves visibles.
- Dernier kilometrage coherent avec vehicules.
- Export CSV/PDF accessible.

KO critique:
- lecture impossible ou ecarts incoherents

## Module L - Analytics / Rapports

URLs:
- `http://localhost/admin/analytics/statuts`
- `http://localhost/admin/reports`

Checks:
- Dashboard status se charge.
- Export CSV/PDF status fonctionne.
- Cohérence visuelle des graphes sur periode filtree.

KO critique:
- export en erreur
- graphiques vides alors que donnees presentes

## 5) Criteres Go/No-Go

Go si:
- 0 erreur 500 sur parcours complet
- 0 erreur JS bloquante console
- validations formulaire homogenes visibles
- RBAC coherent par role
- depenses/maintenance/affectations coherentes fonctionnellement

No-Go si:
- boucle Livewire 500
- controller missing class
- violation d'isolation tenant
- incoherence majeure KPI vs donnees reelles

## 6) Observations techniques ouvertes

1. Routes maintenance dupliquees partiellement entre `routes/web.php` et `routes/maintenance.php`.
2. Noms de routes dupliques detectes (`admin.maintenance.dashboard`, `admin.maintenance.types.*`).
3. Recommandation: normaliser vers un seul fichier source pour le module maintenance afin d'eviter collisions futures.
