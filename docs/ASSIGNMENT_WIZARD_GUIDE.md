# 🚀 ASSIGNMENT WIZARD - Guide Complet Enterprise-Grade

## Vue d'ensemble

Le **Assignment Wizard** est une interface révolutionnaire en page unique pour affecter des véhicules aux chauffeurs, surpassant les solutions leader du marché (Fleetio, Samsara) avec une approche ultra-professionnelle et une expérience utilisateur optimale.

---

## 📋 Table des matières

1. [Caractéristiques principales](#caractéristiques-principales)
2. [Architecture technique](#architecture-technique)
3. [Flux de travail](#flux-de-travail)
4. [Guide d'utilisation](#guide-dutilisation)
5. [Validation et sécurité](#validation-et-sécurité)
6. [Dépannage](#dépannage)

---

## ✨ Caractéristiques principales

### Interface Utilisateur
- **Page unique** sans étapes multiples (suppression du wizard multi-étapes)
- **Layout 2 colonnes** : véhicules à gauche, chauffeurs à droite
- **Recherche fuzzy temps réel** sur véhicules et chauffeurs
- **Cards visuelles** avec photos, badges de statut et informations détaillées
- **Responsive mobile-first** pour utilisation sur tablette et smartphone

### Filtrage Intelligent
- **Véhicules** : Affiche UNIQUEMENT les véhicules au statut `PARKING`
- **Chauffeurs** : Affiche UNIQUEMENT les chauffeurs au statut `DISPONIBLE`
- Filtres additionnels par type de véhicule et dépôt
- Pas de véhicules déjà affectés ou en panne
- Pas de chauffeurs en mission ou en congé

### Validation Temps Réel
- **Détection automatique des conflits** dès la sélection
- **Vérification de chevauchement** véhicule/chauffeur
- **Alertes visuelles** pour conflits de planning
- **Suggestions automatiques** de créneaux horaires libres
- **Timeline Gantt preview** (à venir)

### Changements de Statut Automatiques

#### Lors de la création d'une affectation :
```
Véhicule : PARKING → AFFECTÉ
Chauffeur : DISPONIBLE → EN_MISSION
```

#### Historique complet :
- Enregistrement dans `status_history` (table polymorphique)
- Métadonnées avec ID d'affectation
- Raison et utilisateur ayant effectué le changement

### Analytics Instantanées
- Compteur de véhicules disponibles (statut PARKING)
- Compteur de chauffeurs disponibles (statut DISPONIBLE)
- Nombre d'affectations actives en temps réel

---

## 🏗️ Architecture technique

### Composants

#### 1. **Composant Livewire**
**Fichier** : `app/Livewire/Admin/AssignmentWizard.php`

**Propriétés principales** :
```php
public ?int $selectedVehicleId = null;
public ?int $selectedDriverId = null;
public ?string $startDatetime = null;
public ?string $endDatetime = null;
public string $reason = '';
public string $notes = '';
public bool $isIndefinite = false;
```

**Méthodes clés** :
- `availableVehicles()` - Computed property pour véhicules PARKING
- `availableDrivers()` - Computed property pour chauffeurs DISPONIBLES
- `validateInRealTime()` - Vérification conflits en temps réel
- `createAssignment()` - Création affectation + changement statuts
- `suggestSlot()` - Suggestion créneaux libres

#### 2. **Services**

##### OverlapCheckService
**Fichier** : `app/Services/OverlapCheckService.php`

Responsabilités :
- Détection des conflits de planning
- Vérification chevauchement dates
- Suggestions de créneaux libres

##### StatusTransitionService
**Fichier** : `app/Services/StatusTransitionService.php`

Responsabilités :
- Changement de statut avec validation State Machine
- Enregistrement historique
- Dispatch events
- Vérification permissions

#### 3. **Enums**

##### VehicleStatusEnum
**Fichier** : `app/Enums/VehicleStatusEnum.php`

```php
enum VehicleStatusEnum: string {
    case PARKING = 'parking';
    case AFFECTE = 'affecte';
    case EN_PANNE = 'en_panne';
    case EN_MAINTENANCE = 'en_maintenance';
    case REFORME = 'reforme';
}
```

##### DriverStatusEnum
**Fichier** : `app/Enums/DriverStatusEnum.php`

```php
enum DriverStatusEnum: string {
    case DISPONIBLE = 'disponible';
    case EN_MISSION = 'en_mission';
    case EN_CONGE = 'en_conge';
    case AUTRE = 'autre';
}
```

#### 4. **Modèles**

- **Assignment** : Affectation véhicule-chauffeur
- **Vehicle** : Véhicule avec relation `vehicleStatus`
- **Driver** : Chauffeur avec relation `driverStatus`
- **StatusHistory** : Historique polymorphique des changements de statut

### Base de données

#### Statuts Véhicules (vehicle_statuses)
```sql
id | name         | slug           | is_active
8  | Parking      | parking        | true
9  | Affecté      | affecte        | true
10 | En panne     | en_panne       | true
11 | Maintenance  | en_maintenance | true
12 | Réformé      | reforme        | false
```

#### Statuts Chauffeurs (driver_statuses)
```sql
id | name        | slug        | is_active | is_available_for_work
7  | Disponible  | disponible  | true      | true
8  | En mission  | en_mission  | true      | false
9  | En congé    | en_conge    | true      | false
10 | Autre       | autre       | true      | false
```

---

## 🔄 Flux de travail

### 1. Accès au Wizard

**URL** : `/admin/assignments/wizard`

**Route** : `route('admin.assignments.wizard')`

**Permissions** : Accessible aux rôles avec accès aux affectations (Admin, Gestionnaire Flotte)

### 2. Sélection Véhicule

1. L'utilisateur voit la liste des véhicules avec statut **PARKING** uniquement
2. Recherche possible par :
   - Plaque d'immatriculation
   - Nom du véhicule
   - Marque
   - Modèle
3. Filtres additionnels par type et dépôt
4. Clic sur card → Véhicule sélectionné avec bordure bleue

### 3. Sélection Chauffeur

1. L'utilisateur voit la liste des chauffeurs avec statut **DISPONIBLE** uniquement
2. Recherche possible par :
   - Prénom
   - Nom
   - Numéro de permis
   - Matricule employé
3. Clic sur card → Chauffeur sélectionné avec bordure bleue

### 4. Configuration Dates

**Options** :
- **Date/heure début** (obligatoire) : Doit être dans le futur
- **Date/heure fin** (optionnel) : Doit être après date début
- **Toggle "Affectation indéterminée"** : Pas de date de fin

**Validation temps réel** :
- Dès qu'un véhicule + chauffeur + date début sont sélectionnés
- Vérification automatique des conflits
- Alerte visuelle si conflit détecté

### 5. Informations Additionnelles

- **Raison** (optionnel, 500 caractères max) : Contexte de l'affectation
- **Notes** (optionnel, 1000 caractères max) : Remarques diverses

### 6. Création

**Bouton "Créer l'affectation"** :
- Désactivé tant que formulaire invalide ou conflits existants
- Clic → Transaction DB :
  1. Création `Assignment`
  2. Changement statut véhicule : `PARKING` → `AFFECTÉ`
  3. Changement statut chauffeur : `DISPONIBLE` → `EN_MISSION`
  4. Enregistrement historique dans `status_history`
  5. Dispatch events (`VehicleStatusChanged`, `DriverStatusChanged`)
- Toast de succès
- Formulaire reset automatiquement

---

## ✅ Validation et sécurité

### Validation Laravel

```php
$this->validate([
    'selectedVehicleId' => 'required|exists:vehicles,id',
    'selectedDriverId' => 'required|exists:drivers,id',
    'startDatetime' => 'required|date|after:now',
    'endDatetime' => 'nullable|date|after:startDatetime',
    'reason' => 'nullable|string|max:500',
    'notes' => 'nullable|string|max:1000',
]);
```

### Vérification Conflits

Le service `OverlapCheckService` vérifie :
- Véhicule déjà affecté sur la période
- Chauffeur déjà en mission sur la période
- Chevauchement de dates (inclusive)

**Algorithme** :
```sql
SELECT * FROM assignments
WHERE (
    (vehicle_id = ? OR driver_id = ?)
    AND status = 'active'
    AND (
        (start_datetime BETWEEN ? AND ?)
        OR (end_datetime BETWEEN ? AND ?)
        OR (start_datetime <= ? AND (end_datetime >= ? OR end_datetime IS NULL))
    )
)
```

### State Machine

Le `StatusTransitionService` vérifie les transitions autorisées :

**Véhicules** :
- PARKING → AFFECTE ✅
- PARKING → EN_PANNE ✅
- AFFECTE → PARKING ✅
- EN_MAINTENANCE → PARKING ✅
- etc.

**Chauffeurs** :
- DISPONIBLE → EN_MISSION ✅
- EN_MISSION → DISPONIBLE ✅
- DISPONIBLE → EN_CONGE ✅
- etc.

---

## 🐛 Dépannage

### Problème : Liste de véhicules vide

**Causes possibles** :
1. Aucun véhicule au statut PARKING
2. Organisation_id incorrect
3. Tous les véhicules archivés

**Solution** :
```sql
-- Vérifier les statuts
SELECT status_id, COUNT(*)
FROM vehicles
WHERE organization_id = 1 AND is_archived = false
GROUP BY status_id;

-- Mettre des véhicules en PARKING
UPDATE vehicles
SET status_id = (SELECT id FROM vehicle_statuses WHERE slug = 'parking')
WHERE id IN (SELECT id FROM vehicles WHERE organization_id = 1 LIMIT 5);
```

### Problème : Liste de chauffeurs vide

**Causes possibles** :
1. Aucun chauffeur au statut DISPONIBLE
2. Tous les chauffeurs soft-deleted
3. Organization_id incorrect

**Solution** :
```sql
-- Vérifier les statuts
SELECT status_id, COUNT(*)
FROM drivers
WHERE organization_id = 1 AND deleted_at IS NULL
GROUP BY status_id;

-- Mettre des chauffeurs en DISPONIBLE
UPDATE drivers
SET status_id = (SELECT id FROM driver_statuses WHERE slug = 'disponible')
WHERE id IN (SELECT id FROM drivers WHERE organization_id = 1 AND deleted_at IS NULL);
```

### Problème : Erreur lors de la création

**Causes possibles** :
1. Conflit non détecté
2. Statuts inexistants en base
3. Permissions insuffisantes

**Solution** :
1. Vérifier logs Laravel : `storage/logs/laravel.log`
2. Vérifier migrations : `php artisan migrate:status`
3. Tester StatusTransitionService manuellement

---

## 📊 Analytics et Rapports

### Dashboard Analytics

**Route** : `/admin/analytics/statuts`

Visualisations disponibles :
- KPI cards : Total changements, manuels, automatiques
- Graphique changements quotidiens (ApexCharts Area)
- Distribution actuelle des statuts (ApexCharts Donut)
- Top 10 transitions
- Véhicules les plus actifs
- Timeline des changements récents

---

## 🎯 Roadmap (Évolutions futures)

### Phase 2 (Q1 2026)
- [ ] Timeline Gantt visuelle avec drag & drop
- [ ] Notifications push pour conflits
- [ ] Suggestion automatique véhicule optimal (IA)
- [ ] Multi-affectation en masse
- [ ] Templates d'affectation récurrentes

### Phase 3 (Q2 2026)
- [ ] Mobile app native (React Native)
- [ ] Voice commands (Alexa/Google)
- [ ] Prédiction ML des besoins futurs
- [ ] Intégration GPS temps réel
- [ ] Blockchain pour traçabilité

---

## 📞 Support

Pour toute question ou problème :
- **Email** : support@zenfleet.com
- **Documentation** : https://docs.zenfleet.com
- **GitHub Issues** : https://github.com/zenfleet/zenfleet/issues

---

**Version** : 2.0-Enterprise
**Date** : 08 Novembre 2025
**Auteur** : ZenFleet Enterprise Team
**Licence** : Propriétaire
