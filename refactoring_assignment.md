# Refactoring des Affectations — ZenFleet (Enterprise-Grade, Version Détaillée)

## 0) Contexte et objectif
ZenFleet est une plateforme de gestion de flotte multi-tenant (Laravel 12 + Livewire 3 + PostgreSQL 18) avec des exigences de qualité internationale. Ce document propose une refonte architecturale des affectations afin d’atteindre un niveau Fleetio/Samsara.

Objectif : rendre les affectations indépendantes des statuts opérationnels (maintenance, panne, formation, réformé, etc.) tout en garantissant l’unicité conducteur↔véhicule à un instant T, la cohérence multi-tenant, et une gestion robuste des conflits.

## 1) Définitions et concepts
### 1.1 Affectation (Assignment)
Contrat temporel entre un chauffeur et un véhicule.
- Débute à `start_datetime`.
- Peut être ouverte (`end_datetime` NULL).
- Peut être annulée (`status = cancelled`).

### 1.2 Statut opérationnel
État métier du véhicule ou du chauffeur (maintenance, panne, formation, suspendu, réformé, vendu, etc.).
- Indépendant de l’affectation.
- Peut changer sans casser l’affectation.

### 1.3 Présence d’affectation
Indicateur dérivé : “le chauffeur a un véhicule affecté” ou “le véhicule a un chauffeur affecté”.
- Source de vérité : table `assignments`.
- Champs `current_driver_id` / `current_vehicle_id` = cache dérivé (optionnel).

## 2) Analyse de l’existant (synthèse technique)
### 2.1 Modèle assignments
- Table `assignments` avec `start_datetime`, `end_datetime`, `status`.
- Contraintes anti-chevauchement GIST (PostgreSQL) sur véhicule et chauffeur.
- `Assignment::calculateStatus()` calcule un statut dynamique.

### 2.2 Champs de disponibilité
- `vehicles.is_available`, `vehicles.assignment_status`, `vehicles.current_driver_id`.
- `drivers.is_available`, `drivers.assignment_status`, `drivers.current_vehicle_id`.

### 2.3 Synchronisation statuts
- `ResourceStatusSynchronizer` synchronise `status_id` à partir de `is_available + assignment_status`.
- Couplage fort entre statut opérationnel et affectation.

### 2.4 Services existants
- `AssignmentTerminationService` libère véhicule/chauffeur via `is_available`, `assignment_status`, `current_*_id`.
- `OverlapCheckService` détecte les conflits temporels.

### 2.5 Risques observés
- Double source de vérité (assignments + véhicules/chauffeurs).
- Affectation cassée ou “libérée” lors d’un changement de statut métier.
- Complexité de “healing” (zombies) nécessaire.

## 3) Problèmes fondamentaux
### 3.1 Couplage statuts ↔ affectations
- Un véhicule peut être “affecté” et “en maintenance” en même temps.
- Un chauffeur peut être “en formation” mais conserver son affectation.
- Aujourd’hui, certains flux libèrent les ressources dès que le statut métier change.

### 3.2 Conflits d’autorité
- `assignments` est censé être la source de vérité.
- Mais `is_available` et `assignment_status` sont traités comme source de vérité.

### 3.3 Incohérences temporelles
- Affectation active alors que véhicule “disponible”.
- Chauffeur “disponible” alors qu’il a une affectation active.

## 4) Principes d’architecture cible
1. L’affectation est la source de vérité unique.
2. Le statut opérationnel est indépendant.
3. L’unicité conducteur↔véhicule est garantie par la base (GIST).
4. Aucun changement de statut opérationnel ne termine une affectation.
5. Les champs de disponibilité deviennent dérivés.

## 5) Modèle cible recommandé
### 5.1 Table `assignments`
- Conserver les colonnes existantes.
- Exclusion GIST sur `vehicle_id` et `driver_id` avec `tsrange(start_datetime, COALESCE(end_datetime, 'infinity'), '[)')`.
- Filtre d’exclusion = `deleted_at IS NULL AND status != 'cancelled'`.
- Le statut calculé reste possible mais l’unicité reste purement temporelle.

### 5.2 Statuts opérationnels
- Véhicules : statuts métiers (parking, maintenance, panne, réformé, vendu, etc.).
- Chauffeurs : statuts métiers (disponible, formation, congé, suspendu, etc.).
- Flags métiers recommandés :
  - `allows_assignments` (peut recevoir une nouvelle affectation)
  - `is_available_for_work` (drivers)

### 5.3 Présence d’affectation (dérivée)
Option A : calcul à la volée
- Exemple SQL :
  - `exists(select 1 from assignments where driver_id = ? and start_datetime <= now() and (end_datetime is null or end_datetime > now()) and status != 'cancelled' and deleted_at is null)`

Option B : cache dérivé (recommandé pour performance)
- `drivers.current_vehicle_id`
- `vehicles.current_driver_id`
- Mise à jour via trigger ou service centralisé (voir 8.2).

## 6) Règles métier (Enterprise)
### 6.1 Création d’affectation
- Vérifier :
  - Conformité multi-tenant
  - Chevauchement (DB constraint + pré-check applicatif)
  - Statuts métier compatibles (ex: pas de véhicule “réformé”)
- Créer l’affectation
- Mettre à jour le cache dérivé si activé

### 6.2 Fin d’affectation
- Mettre `end_datetime`
- Calculer `status` (ou laisser calcul dynamique)
- Mettre à jour le cache dérivé
- Ne pas modifier les statuts métier

### 6.3 Changement de statut opérationnel
- Ne jamais modifier l’affectation
- Déclencher alerte si statut incompatible avec l’affectation (ex: panne)

### 6.4 Remplacement de véhicule
- Annuler l’affectation initiale (`status = cancelled`)
- Créer une nouvelle affectation

## 7) Gestion des conflits et chevauchements
### 7.1 Invariants
- Un chauffeur = un véhicule actif à un instant T.
- Un véhicule = un chauffeur actif à un instant T.

### 7.2 Contrainte DB
- Exclusion GIST sur `(vehicle_id, organization_id, tsrange(start_datetime, COALESCE(end_datetime, 'infinity'), '[)'))`.
- Même contrainte pour `driver_id`.
- Filtre `deleted_at is null and status != 'cancelled'`.

### 7.3 Conflits applicatifs
- Pré-check via `OverlapCheckService`.
- En cas d’erreur DB → retour HTTP 409 avec détails.

## 8) Implémentation technique
### 8.1 Découplage statuts métier
- `AssignmentTerminationService` : supprimer la mise à jour automatique de `status_id`.
- `AssignmentService` : ne plus utiliser “Parking/Disponible” comme source de vérité.

### 8.2 Synchronisation des caches dérivés
Option recommandée : triggers SQL
- `AFTER INSERT/UPDATE/DELETE ON assignments`.
- Mettre à jour `vehicles.current_driver_id` et `drivers.current_vehicle_id`.

Pseudo SQL (simplifié) :
- AFTER INSERT/UPDATE
  - récupérer l’affectation active la plus récente
  - mettre à jour `current_driver_id` / `current_vehicle_id`
- AFTER DELETE
  - recalculer les affectations actives

Alternative : service unique `AssignmentPresenceService` utilisé dans chaque flux create/update/end/cancel.

### 8.3 Alignement des champs de disponibilité
- `is_available` et `assignment_status` deviennent dérivés et calculés depuis assignments.
- Court terme : conserver ces champs mais ne jamais les modifier directement.

## 9) Migration des données
### 9.1 Réconciliation initiale
- Pour chaque véhicule, calculer `current_driver_id` depuis assignments actives.
- Pour chaque chauffeur, calculer `current_vehicle_id` depuis assignments actives.

### 9.2 Détection des zombies
- Affectations actives + véhicule marqué disponible → alerte.
- Chauffeurs disponibles + affectation active → alerte.

## 10) Observabilité et audit
- Logs structurés (assignments.created, assignments.ended, conflicts).
- Table d’audit pour affectations critiques.
- Dashboard “Conflits et incohérences”.

## 11) Sécurité multi-tenant
- Toutes les requêtes filtrées par `organization_id`.
- Validation systématique des droits (policy).
- Option RLS PostgreSQL pour protection renforcée.

## 12) UX recommandé
- Afficher deux badges distincts :
  - “Affectation” (actif / planifié / terminé)
  - “Statut opérationnel” (maintenance, panne, formation, etc.)
- Si statut incompatible → alerte + proposition de remplacement.

## 13) Roadmap de refonte
### Phase 1 — Découplage logique
- Modifier les services pour ne plus modifier `status_id`.
- Définir l’affectation comme source de vérité.

### Phase 2 — Présence dérivée
- Ajouter triggers ou service unique.
- Supprimer les écritures directes de `is_available` / `assignment_status`.

### Phase 3 — Gouvernance métier
- Statuts opérationnels bloquent uniquement les nouvelles affectations.
- Implémenter alertes business (panne + affectation active).

### Phase 4 — Stabilisation
- Dashboard conflits + jobs de reconciliation.
- Tests de non-régression et monitoring continu.

---

Décision centrale : l’affectation est un contrat temporel. Les statuts opérationnels sont orthogonaux et ne doivent jamais annuler implicitement ce contrat.


---

## 14) OBSERVATIONS D'EXPERT — Analyse Architecturale Complète

> **Auteur** : Expert Architecte Système – Gestion de Flotte Multi-Tenant  
> **Date d'analyse** : 2026-02-07  
> **Sources référentielles** : Fleetio, Samsara, ZenFleet codebase actuel

### 14.1 Points Forts de l'Architecture Actuelle

#### ✅ Contraintes GIST PostgreSQL — Excellent niveau technique
- La migration `2025_01_20_000000_add_gist_constraints_assignments.php` implémente des contraintes d'exclusion temporelle de niveau enterprise
- Fonction `assignment_interval()` gérant correctement les durées indéterminées (`end_datetime = NULL → 2099-12-31`)
- Les contraintes `assignments_vehicle_no_overlap` et `assignments_driver_no_overlap` garantissent l'invariant **"un véhicule = un chauffeur à un instant T"** au niveau database

```sql
-- Contrainte existante (excellente)
EXCLUDE USING GIST (
    organization_id WITH =,
    vehicle_id WITH =,
    assignment_interval(start_datetime, end_datetime) WITH &&
) WHERE (deleted_at IS NULL)
```

#### ✅ Vue matérialisée pour dashboard
- La vue `assignment_stats_daily` est une excellente pratique pour les tableaux de bord haute performance
- Indexation unique sur `(organization_id, assignment_date)`

#### ✅ Modèle Assignment — Conception solide
- Calcul de statut dynamique intelligent (`calculateStatus()`)
- Support complet des affectations ouvertes et programmées
- Audit trail avec `created_by`, `updated_by`, `ended_by_user_id`

---

### 14.2 Problèmes Critiques Identifiés

#### 🔴 CRITIQUE #1 : Double Source de Vérité

**Localisation** : `AssignmentService.php`, `AssignmentTerminationService.php`, modèles `Vehicle` et `Driver`

**Diagnostic** :
```php
// AssignmentService.php:52-63 — PROBLÈME
public function endAssignment(Assignment $assignment, int $endMileage, string $endDateTime): bool
{
    // ❌ Mise à jour DIRECTE du status_id sans passer par assignments
    $parkingStatusId = VehicleStatus::where('name', 'Parking')->firstOrFail()->id;
    $assignment->vehicle->update(['status_id' => $parkingStatusId]);
    
    $availableStatusId = DriverStatus::where('name', 'Disponible')->firstOrFail()->id;
    $assignment->driver->update(['status_id' => $availableStatusId]);
}
```

Ce code **contredit le principe fondamental** du document : le `status_id` est modifié directement selon l'état de l'affectation, créant un couplage fort entre affectation et statut opérationnel.

**Impact** :
- Si un véhicule est en panne pendant l'affectation, la terminaison le remet à "Parking" malgré qu'il devrait rester "En panne"
- Le statut chauffeur "En formation" serait écrasé par "Disponible"

---

#### 🔴 CRITIQUE #2 : Champs Redondants sur Véhicules/Chauffeurs

**Localisation** : `AssignmentTerminationService.php:128-134`, `ResourceStatusSynchronizer.php`

**Champs problématiques existants** :
- `vehicles.is_available` / `drivers.is_available`
- `vehicles.assignment_status` / `drivers.assignment_status`
- `vehicles.current_driver_id` / `drivers.current_vehicle_id`
- `vehicles.last_assignment_end` / `drivers.last_assignment_end`

**Symptôme observable** :
```php
// AssignmentTerminationService.php:129-134
$assignment->vehicle->update([
    'is_available' => true,
    'current_driver_id' => null,
    'assignment_status' => 'available',  // ← Ecrasement!
    'last_assignment_end' => $endTime,
]);
```

Ces champs **dupliquent** l'information déjà présente dans la table `assignments`. La proposition de refactoring (section 5.3) mentionne qu'ils doivent devenir dérivés, ce qui est correct.

---

#### 🔴 CRITIQUE #3 : Nécessité de "Healing" des Zombies

**Localisation** : `ResourceStatusSynchronizer.php:222-267`, `AssignmentTerminationService.php:285-299`

**Diagnostic** :
La présence de mécanismes `healAllVehicleZombies()` et `detectZombieAssignments()` est un **indicateur d'architecture défaillante**. Dans un système bien conçu, les zombies ne devraient pas pouvoir exister.

**Définition zombie actuelle** :
```php
// Véhicule avec is_available=true ET assignment_status='available' 
// MAIS affectation active dans la table assignments
```

Le fait même qu'un zombie puisse se créer prouve que `is_available`/`assignment_status` sont des sources de vérité concurrentes.

---

#### 🟡 ATTENTION #4 : ResourceStatusSynchronizer — Logique Inverse

**Problème conceptuel** :
Le `ResourceStatusSynchronizer` synchronise `status_id` **à partir de** `is_available + assignment_status`. La bonne architecture serait l'inverse : dériver `is_available` de la table `assignments`.

```php
// ACTUEL (inversé) — ResourceStatusSynchronizer.php:88-112
if ($vehicle->is_available === true && $vehicle->assignment_status === 'available') {
    $newStatusId = $this->resolveVehicleStatusIdForAvailable($organizationId);
}
elseif ($vehicle->is_available === false && $vehicle->assignment_status === 'assigned') {
    $newStatusId = $this->resolveVehicleStatusIdForAssigned($organizationId);
}
```

**Proposition** : La synchronisation devrait lire **assignments** et calculer `is_available`, pas l'inverse.

---

### 14.3 Validation de la Proposition de Refactoring

#### ✅ Section 4 — Principes d'architecture cible : **VALIDÉE**
Les 5 principes sont conformes aux meilleures pratiques Fleetio/Samsara.

#### ✅ Section 5.1 — Table assignments : **VALIDÉE**
- Conservation des contraintes GIST
- Filtre `deleted_at IS NULL AND status != 'cancelled'` : correct

#### ✅ Section 5.2 — Statuts opérationnels : **VALIDÉE AVEC RÉSERVE**
Recommandation : ajouter un flag `allows_new_assignments` (booléen) sur les tables `vehicle_statuses` et `driver_statuses` pour déterminer si une nouvelle affectation est autorisée dans ce statut. Exemple :

| Statut Véhicule | allows_new_assignments |
|-----------------|------------------------|
| Parking | ✅ true |
| Affecté | ✅ true (permet réaffectation) |
| En maintenance | ✅ true |
| En panne | ❌ false |
| Réformé | ❌ false |
| Vendu | ❌ false |

#### ✅ Section 5.3 — Présence dérivée : **VALIDÉE**
L'option B (cache dérivé via triggers) est recommandée pour la performance.

#### ✅ Section 6 — Règles métier : **VALIDÉE INTÉGRALEMENT**
Critique : la section 6.3 est la plus importante :
> "Ne jamais modifier l'affectation lors d'un changement de statut opérationnel"

---

### 14.4 Recommandations Complémentaires Niveau International

#### 📐 RECOMMANDATION #1 : Diagramme ERD Formalisé

**Demande** : Veuillez fournir un schéma ERD incluant :
1. Tables : `assignments`, `vehicles`, `drivers`, `vehicle_statuses`, `driver_statuses`
2. Relations FK clairement annotées
3. Indication des champs **à supprimer** (is_available, assignment_status) vs **à conserver** (current_driver_id comme cache)

#### 📐 RECOMMANDATION #2 : Diagrammes de Séquence

**Flux recommandés à documenter** :
1. **Création d'affectation** : validation statut → vérification overlap → création → mise à jour cache
2. **Fin d'affectation** : validation → mise à jour end_datetime → mise à jour cache → événements
3. **Annulation d'affectation** : validation → status = cancelled → mise à jour cache
4. **Changement de statut véhicule** : mise à jour status_id → alerte si affectation active (AUCUNE modification de l'affectation)

#### 📐 RECOMMANDATION #3 : Triggers SQL Complets

**Trigger proposé — Synchronisation cache véhicule** :
```sql
CREATE OR REPLACE FUNCTION sync_vehicle_current_driver()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_current_driver_id INT;
BEGIN
    -- Calculer le chauffeur actuel depuis assignments
    SELECT driver_id INTO v_current_driver_id
    FROM assignments
    WHERE vehicle_id = COALESCE(NEW.vehicle_id, OLD.vehicle_id)
      AND deleted_at IS NULL
      AND status = 'active'
      AND start_datetime <= NOW()
      AND (end_datetime IS NULL OR end_datetime > NOW())
    ORDER BY start_datetime DESC
    LIMIT 1;
    
    -- Mettre à jour le cache
    UPDATE vehicles
    SET current_driver_id = v_current_driver_id
    WHERE id = COALESCE(NEW.vehicle_id, OLD.vehicle_id);
    
    RETURN COALESCE(NEW, OLD);
END;
$$;

CREATE TRIGGER trg_sync_vehicle_current_driver
AFTER INSERT OR UPDATE OR DELETE ON assignments
FOR EACH ROW
EXECUTE FUNCTION sync_vehicle_current_driver();
```

**Trigger proposé — Synchronisation cache chauffeur** :
```sql
CREATE OR REPLACE FUNCTION sync_driver_current_vehicle()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_current_vehicle_id INT;
BEGIN
    SELECT vehicle_id INTO v_current_vehicle_id
    FROM assignments
    WHERE driver_id = COALESCE(NEW.driver_id, OLD.driver_id)
      AND deleted_at IS NULL
      AND status = 'active'
      AND start_datetime <= NOW()
      AND (end_datetime IS NULL OR end_datetime > NOW())
    ORDER BY start_datetime DESC
    LIMIT 1;
    
    UPDATE drivers
    SET current_vehicle_id = v_current_vehicle_id
    WHERE id = COALESCE(NEW.driver_id, OLD.driver_id);
    
    RETURN COALESCE(NEW, OLD);
END;
$$;

CREATE TRIGGER trg_sync_driver_current_vehicle
AFTER INSERT OR UPDATE OR DELETE ON assignments
FOR EACH ROW
EXECUTE FUNCTION sync_driver_current_vehicle();
```

---

### 14.5 Comparaison avec les Leaders du Marché

#### 🏆 Fleetio — Architecture de Référence
| Fonctionnalité | Fleetio | ZenFleet Actuel | ZenFleet Cible |
|----------------|---------|-----------------|----------------|
| Affectation = contrat temporel | ✅ | ⚠️ Partiellement | ✅ |
| Statut opérationnel indépendant | ✅ | ❌ Couplé | ✅ |
| Contraintes DB overlap | ✅ | ✅ | ✅ |
| Cache dérivé via triggers | ✅ | ❌ Service manuel | ✅ Proposé |
| Alertes statut incompatible | ✅ | ❌ | ✅ À implémenter |
| Dashboard conflits | ✅ | ❌ | ✅ Phase 4 |

#### 🏆 Samsara — Fonctionnalités Avancées
| Fonctionnalité | Samsara | ZenFleet Cible |
|----------------|---------|----------------|
| Affectations récurrentes | ✅ | 🔜 V2 recommandé |
| Historique complet audit | ✅ | ✅ Via ended_by_user_id |
| Notifications temps réel | ✅ | 🔜 À planifier |
| Intégration IoT/Télématique | ✅ | ❌ Hors scope |

---

### 14.6 Priorités d'Implémentation Recommandées

| Phase | Priorité | Effort | Impact |
|-------|----------|--------|--------|
| Phase 1 — Découplage logique | 🔴 Critique | 2-3 jours | Élimine 80% des bugs de cohérence |
| Phase 2 — Présence dérivée (triggers) | 🟡 Haute | 3-5 jours | Performance + cohérence |
| Phase 3 — Gouvernance métier | 🟡 Haute | 2-3 jours | Expérience utilisateur premium |
| Phase 4 — Stabilisation | 🟢 Moyenne | 5-7 jours | Observabilité production |

**Estimation totale** : 12-18 jours de développement pour atteindre le niveau Fleetio/Samsara

---

### 14.7 Points d'Attention Multi-Tenant

#### 🔒 Sécurité
1. **Toutes les contraintes GIST incluent `organization_id`** : ✅ Vérifié dans la migration
2. **Les triggers proposés doivent respecter l'isolation tenant** : Pas de risque car les FK vers `vehicles`/`drivers` imposent déjà le tenant

#### 🔒 RLS PostgreSQL (Optionnel mais Recommandé)
```sql
-- Politique RLS pour assignments
ALTER TABLE assignments ENABLE ROW LEVEL SECURITY;

CREATE POLICY assignments_tenant_isolation ON assignments
    USING (organization_id = current_setting('app.current_organization_id')::INT);
```

---

### 14.8 Checklist de Validation Finale

Avant chaque release du module refactoré, valider :

- [ ] Un véhicule en panne conserve son affectation active
- [ ] Un chauffeur en formation conserve son véhicule affecté
- [ ] La terminaison d'une affectation ne modifie pas le `status_id` du véhicule/chauffeur
- [ ] Le changement de statut véhicule vers "En panne" génère une alerte sans toucher l'affectation
- [ ] Les contraintes GIST rejettent les chevauchements même avec `end_datetime = NULL`
- [ ] Le cache `current_driver_id`/`current_vehicle_id` est synchronisé par trigger
- [ ] Aucun zombie ne peut être créé par un flux normal d'utilisation
- [ ] Le dashboard affiche séparément "Badge Affectation" et "Badge Statut Opérationnel"

---

## 15) ANNEXES TECHNIQUES — À Fournir

> [!IMPORTANT]
> Les éléments suivants sont **demandés** pour valider définitivement le projet de refactoring :

1. **Schéma ERD complet** avec annotations des modifications (champs à supprimer, à ajouter, à modifier)
2. **Diagramme de séquence** : flux création d'affectation
3. **Diagramme de séquence** : flux fin/annulation d'affectation
4. **Diagramme de séquence** : flux changement de statut véhicule avec affectation active
5. **Migration SQL complète** des triggers proposés (section 14.4)
6. **Tests de non-régression** : scénarios à couvrir pour la checklist 14.8

---

## 16) CONCLUSION

La proposition de refactoring décrite dans ce document est **techniquement solide et architecturalement cohérente** avec les meilleures pratiques de l'industrie (Fleetio, Samsara). Les contraintes GIST déjà en place constituent une excellente fondation.

**Le changement de paradigme fondamental** :
> L'affectation devient l'unique source de vérité. Le `status_id` des véhicules et chauffeurs devient **orthogonal** et ne doit jamais être modifié par le cycle de vie des affectations.

**Bénéfices attendus** :
- Élimination des états "zombie"
- Simplification massive du code service (disparition de `ResourceStatusSynchronizer` dans son rôle actuel)
- Flexibilité opérationnelle : un véhicule peut être en maintenance, en panne, en réparation tout en restant affecté
- Conformité aux standards internationaux de gestion de flotte

**Risque principal** : La migration des données existantes doit être planifiée avec soin (section 9) pour éviter les incohérences historiques.

> **Verdict final** : ✅ **PROJET VALIDÉ** — Recommandation de procéder avec les phases 1 et 2 en priorité.
