# 📋 DOCUMENTATION TECHNIQUE - Gestion des Statuts et Types de Véhicules/Chauffeurs

## Version 2.0 - Enterprise Grade

### 🎯 Vue d'ensemble

Cette documentation décrit l'implémentation complète du système de gestion des statuts et types pour les véhicules et chauffeurs dans ZenFleet, basée sur une architecture enterprise-grade avec :

- **Enums PHP 8.2+** pour la sécurité de type
- **State Machine Pattern** pour la validation des transitions
- **Event Sourcing léger** pour l'historique complet
- **Architecture modulaire** (DDD, Services, Repository)

---

## 📊 Architecture Globale

```
┌─────────────────────────────────────────────────────────────┐
│                   ARCHITECTURE LAYERS                        │
└─────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│  PRESENTATION LAYER (Livewire Components)                    │
│  - ChangeVehicleStatus.php                                   │
│  - Badges UI (HasStatusBadge Trait)                          │
└────────────────────┬─────────────────────────────────────────┘
                     │
┌────────────────────▼─────────────────────────────────────────┐
│  APPLICATION LAYER (Services & Form Requests)                │
│  - StatusTransitionService.php                               │
│  - ChangeVehicleStatusRequest.php                            │
│  - ChangeDriverStatusRequest.php                             │
└────────────────────┬─────────────────────────────────────────┘
                     │
┌────────────────────▼─────────────────────────────────────────┐
│  DOMAIN LAYER (Enums, Business Logic)                        │
│  - VehicleStatusEnum.php (State Machine)                     │
│  - DriverStatusEnum.php (State Machine)                      │
│  - VehicleTypeEnum.php                                       │
└────────────────────┬─────────────────────────────────────────┘
                     │
┌────────────────────▼─────────────────────────────────────────┐
│  INFRASTRUCTURE LAYER (Models, Database)                     │
│  - Vehicle Model (+ HasStatusBadge)                          │
│  - Driver Model (+ HasStatusBadge)                           │
│  - StatusHistory Model (Audit Trail)                         │
│  - Migrations (status_history, vehicle_statuses, etc.)       │
└──────────────────────────────────────────────────────────────┘
```

---

## 🗂️ Structure des fichiers créés/modifiés

### ✅ Fichiers créés

```
app/
├── Enums/
│   ├── VehicleStatusEnum.php        ← Enum des statuts véhicules
│   ├── DriverStatusEnum.php         ← Enum des statuts chauffeurs
│   └── VehicleTypeEnum.php          ← Enum des types de véhicules
│
├── Models/
│   ├── StatusHistory.php            ← Modèle d'historique (polymorphic)
│   └── Concerns/
│       └── HasStatusBadge.php       ← Trait pour badges Tailwind
│
├── Services/
│   └── StatusTransitionService.php  ← Service de gestion des transitions
│
├── Http/Requests/
│   ├── ChangeVehicleStatusRequest.php  ← Validation changement statut véhicule
│   └── ChangeDriverStatusRequest.php   ← Validation changement statut chauffeur
│
└── Livewire/Admin/
    └── ChangeVehicleStatus.php      ← Composant Livewire exemple

database/migrations/
├── 2025_11_08_000001_update_vehicle_statuses_with_new_enum_values.php
├── 2025_11_08_000002_update_vehicle_types_with_new_enum_values.php
└── 2025_11_08_000003_create_status_history_table.php

resources/views/livewire/admin/
└── change-vehicle-status.blade.php  ← Vue Blade du composant
```

### ✏️ Fichiers modifiés

```
app/Models/
├── Vehicle.php                      ← Ajout du trait HasStatusBadge + relations
└── Driver.php                       ← Ajout du trait HasStatusBadge + relations
```

---

## 🚗 Statuts des Véhicules

### Énumération VehicleStatusEnum

| Statut | Valeur | Description | Transitions autorisées |
|--------|--------|-------------|------------------------|
| **PARKING** | `parking` | Véhicule disponible au parking, non affecté | → AFFECTÉ, EN_PANNE |
| **AFFECTÉ** | `affecte` | Véhicule affecté à un chauffeur | → PARKING, EN_PANNE |
| **EN_PANNE** | `en_panne` | Véhicule en panne, nécessite intervention | → EN_MAINTENANCE, PARKING |
| **EN_MAINTENANCE** | `en_maintenance` | Véhicule chez le réparateur | → PARKING, REFORMÉ |
| **REFORMÉ** | `reforme` | Véhicule réformé (état terminal) | ∅ (aucune transition) |

### Règles métier

1. **PARKING → AFFECTÉ** : Possible uniquement si un chauffeur disponible existe
2. **AFFECTÉ → EN_PANNE** : Automatique lors de la création d'une `RepairRequest`
3. **EN_PANNE → EN_MAINTENANCE** : Nécessite une intervention planifiée
4. **EN_MAINTENANCE → REFORMÉ** : État terminal, aucune transition sortante possible
5. **REFORMÉ** : Le véhicule est automatiquement désaffecté

### Propriétés métier des statuts

```php
// Exemple d'utilisation
$status = VehicleStatusEnum::PARKING;

$status->label();              // "Parking"
$status->description();        // "Véhicule disponible au parking..."
$status->color();              // "blue"
$status->hexColor();           // "#3b82f6"
$status->icon();               // "parking"
$status->badgeClasses();       // "inline-flex items-center px-2.5 py-0.5..."
$status->canBeAssigned();      // true (seulement pour PARKING)
$status->isOperational();      // true (PARKING, AFFECTÉ)
$status->requiresMaintenance();// false (true pour EN_PANNE, EN_MAINTENANCE)
$status->isTerminal();         // false (true pour REFORMÉ)
$status->allowedTransitions(); // [AFFECTÉ, EN_PANNE]
```

---

## 👤 Statuts des Chauffeurs

### Énumération DriverStatusEnum

| Statut | Valeur | Description | Transitions autorisées |
|--------|--------|-------------|------------------------|
| **DISPONIBLE** | `disponible` | Chauffeur disponible, peut recevoir affectation | → EN_MISSION, EN_CONGE, AUTRE |
| **EN_MISSION** | `en_mission` | Chauffeur actuellement en mission | → DISPONIBLE |
| **EN_CONGE** | `en_conge` | Chauffeur en congé | → DISPONIBLE, AUTRE |
| **AUTRE** | `autre` | Statut spécial (sanction, maladie, formation) | → DISPONIBLE, EN_CONGE |

### Règles métier

1. **DISPONIBLE → EN_MISSION** : Possible uniquement si véhicule disponible (PARKING)
2. **EN_MISSION → DISPONIBLE** : Termine automatiquement l'affectation active
3. **DISPONIBLE → EN_CONGE** : Nécessite métadonnées (type de congé, dates)
4. **DISPONIBLE → AUTRE** : Nécessite une raison (sanction, maladie, formation)

### Métadonnées spécifiques

```php
// Statut EN_CONGE - Métadonnées obligatoires
[
    'leave_type' => 'annual',           // annual, sick, maternity, paternity, unpaid, exceptional
    'leave_start_date' => '2025-11-10',
    'leave_end_date' => '2025-11-20',
]

// Statut AUTRE - Métadonnées obligatoires
[
    'other_reason' => 'sanction',  // sanction, maladie, formation, accident, administrative, other
    'details' => 'Sanction suite à...',
]
```

---

## 🚙 Types de Véhicules

### Énumération VehicleTypeEnum

| Type | Valeur | Permis requis | Niveau coût maintenance | Capacité moyenne (tonnes) |
|------|--------|---------------|-------------------------|---------------------------|
| **VOITURE** | `voiture` | B | 2/5 | 0.5 |
| **CAMION** | `camion` | C | 4/5 | 12.0 |
| **MOTO** | `moto` | A | 2/5 | 0.2 |
| **ENGIN** | `engin` | CACES | 5/5 | null |
| **FOURGONNETTE** | `fourgonnette` | B | 3/5 | 2.0 |
| **BUS** | `bus` | D | 4/5 | null (passagers) |
| **VUL** | `vul` | B | 3/5 | 1.5 |
| **SEMI_REMORQUE** | `semi_remorque` | CE | 5/5 | 24.0 |
| **AUTRE** | `autre` | null | 3/5 | null |

### Propriétés métier

```php
$type = VehicleTypeEnum::CAMION;

$type->label();                     // "Camion"
$type->requiresSpecialLicense();    // true
$type->requiredLicenseCategory();   // "C"
$type->maintenanceCostLevel();      // 4
$type->averageCapacityTons();       // 12.0
$type->isCargoTransport();          // true
$type->isPassengerTransport();      // false
$type->requiresSpecializedTraining();// false
```

---

## 📊 Historique des Statuts (StatusHistory)

### Table `status_history`

Système d'audit trail complet avec Event Sourcing léger.

#### Colonnes principales

| Colonne | Type | Description |
|---------|------|-------------|
| `statusable_type` | string | Type d'entité (Vehicle, Driver) |
| `statusable_id` | bigint | ID de l'entité |
| `from_status` | string | Statut précédent (null si création) |
| `to_status` | string | Nouveau statut |
| `reason` | text | Raison du changement |
| `metadata` | json | Métadonnées additionnelles |
| `changed_by_user_id` | bigint | Utilisateur ayant effectué le changement |
| `change_type` | enum | manual / automatic / system |
| `ip_address` | string | Adresse IP (audit) |
| `user_agent` | string | User-Agent (audit) |
| `organization_id` | bigint | Organisation (multi-tenant) |
| `changed_at` | timestamp | Date et heure du changement |

#### Index de performance

```sql
-- Index polymorphique
CREATE INDEX idx_statusable_changed ON status_history (statusable_type, statusable_id, changed_at);

-- Index pour analytics
CREATE INDEX idx_to_status_changed ON status_history (to_status, changed_at);
CREATE INDEX idx_dashboard_analytics ON status_history (statusable_type, to_status, organization_id, changed_at);
```

### Utilisation du modèle StatusHistory

```php
// Récupérer l'historique d'un véhicule
$vehicle->statusHistory;  // Tous les changements
$vehicle->recentStatusHistory;  // 30 derniers jours

// Filtres et scopes
StatusHistory::forType('Vehicle')
    ->forEntity($vehicleId)
    ->betweenDates('2025-01-01', '2025-12-31')
    ->manual()
    ->recent()
    ->get();

// Analytics
$avgDuration = StatusHistory::getAverageDurationInStatus('Vehicle', 'en_maintenance', $orgId);
$transitions = StatusHistory::getTransitionStats('Vehicle', $orgId);
```

---

## 🔧 Service de Transition (StatusTransitionService)

### Responsabilités

1. Validation des transitions (State Machine)
2. Mise à jour atomique en base de données
3. Enregistrement automatique dans l'historique
4. Exécution de hooks post-transition
5. Gestion des erreurs et rollback

### Méthodes principales

#### changeVehicleStatus()

```php
use App\Services\StatusTransitionService;
use App\Enums\VehicleStatusEnum;

$service = app(StatusTransitionService::class);

$service->changeVehicleStatus(
    $vehicle,
    VehicleStatusEnum::EN_MAINTENANCE,
    [
        'reason' => 'Panne moteur détectée lors de la mission',
        'metadata' => [
            'repair_request_id' => 123,
            'estimated_duration_days' => 7,
            'cost_estimate' => 1500.50,
        ],
        'change_type' => 'manual',  // manual, automatic, system
    ]
);
```

#### changeDriverStatus()

```php
$service->changeDriverStatus(
    $driver,
    DriverStatusEnum::EN_CONGE,
    [
        'reason' => 'Congé annuel',
        'metadata' => [
            'leave_type' => 'annual',
            'leave_start_date' => '2025-11-10',
            'leave_end_date' => '2025-11-20',
        ],
    ]
);
```

#### bulkChangeVehicleStatus()

```php
$result = $service->bulkChangeVehicleStatus(
    [1, 2, 3, 4],  // IDs des véhicules
    VehicleStatusEnum::PARKING,
    ['reason' => 'Fin de maintenance collective']
);

// $result = [
//     'success' => 3,
//     'failed' => 1,
//     'errors' => [2 => "Transition impossible de 'réformé' vers 'parking'..."]
// ]
```

### Hooks post-transition

Les hooks sont exécutés automatiquement après une transition réussie :

```php
// Exemples de hooks implémentés dans le service

// VEHICULE: EN_PANNE → EN_MAINTENANCE
- Vérifie qu'une MaintenanceOperation est planifiée

// VEHICULE: → REFORMÉ
- Termine automatiquement toutes les affectations actives

// CHAUFFEUR: EN_MISSION → DISPONIBLE
- Termine l'affectation de véhicule active
```

---

## 🎨 Affichage des Badges (HasStatusBadge Trait)

### Usage dans les vues Blade

```blade
{{-- Badge de statut --}}
{!! $vehicle->statusBadge() !!}
{!! $driver->statusBadge() !!}

{{-- Badge de type --}}
{!! $vehicle->typeBadge() !!}

{{-- Badge combiné --}}
{!! $vehicle->statusAndTypeBadges() !!}

{{-- Badge avec tooltip Alpine.js --}}
{!! $vehicle->statusBadgeWithTooltip() !!}

{{-- Personnalisation --}}
{!! $vehicle->statusBadge(['size' => 'lg', 'icon' => true]) !!}

{{-- Texte seul (sans HTML) --}}
{{ $vehicle->statusLabel() }}    // "Parking"
{{ $vehicle->typeLabel() }}      // "Camion"

{{-- Couleurs pour graphiques --}}
<div style="background-color: {{ $vehicle->statusColor() }}">
```

### Classes CSS générées (Tailwind)

```html
<!-- Exemple de badge généré -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
    <i class="fas fa-parking mr-1"></i>Parking
</span>
```

---

## ✅ Form Requests - Validation

### ChangeVehicleStatusRequest

```php
// Règles de validation
[
    'status' => ['required', new Enum(VehicleStatusEnum::class)],
    'reason' => ['nullable', 'string', 'max:1000'],  // Obligatoire pour EN_PANNE, EN_MAINTENANCE, REFORMÉ
    'metadata' => ['nullable', 'array'],
    'metadata.reform_reason' => ['required_if:status,reforme'],  // Obligatoire pour REFORMÉ
]

// Permissions
$this->user()->can('update-vehicle-status')
```

### ChangeDriverStatusRequest

```php
// Règles de validation
[
    'status' => ['required', new Enum(DriverStatusEnum::class)],
    'reason' => ['nullable', 'string', 'max:1000'],  // Obligatoire pour AUTRE
    'metadata.leave_type' => ['required_if:status,en_conge', 'in:annual,sick,maternity,...'],
    'metadata.leave_start_date' => ['required_if:status,en_conge', 'date'],
    'metadata.leave_end_date' => ['required_if:status,en_conge', 'date', 'after_or_equal:metadata.leave_start_date'],
]

// Permissions
$this->user()->can('update-driver-status')
```

---

## 🔄 Workflow Complet - Exemple d'utilisation

### Scénario : Passer un véhicule en maintenance

```php
// 1. Dans un controller ou Livewire component
use App\Services\StatusTransitionService;
use App\Enums\VehicleStatusEnum;

public function sendToMaintenance(Vehicle $vehicle, ChangeVehicleStatusRequest $request)
{
    $service = app(StatusTransitionService::class);

    try {
        // Le service valide, update, et historise automatiquement
        $service->changeVehicleStatus(
            $vehicle,
            VehicleStatusEnum::EN_MAINTENANCE,
            [
                'reason' => $request->input('reason'),
                'metadata' => [
                    'maintenance_operation_id' => $maintenanceOp->id,
                    'scheduled_date' => $request->input('scheduled_date'),
                    'estimated_duration_days' => 5,
                ],
            ]
        );

        session()->flash('success', 'Véhicule envoyé en maintenance avec succès.');
        return redirect()->back();

    } catch (\InvalidArgumentException $e) {
        // Erreur de validation de transition
        return back()->withErrors(['status' => $e->getMessage()]);
    }
}
```

### Scénario : Changement automatique lors d'une affectation

```php
// Dans AssignmentService
public function assignVehicleToDriver(Vehicle $vehicle, Driver $driver)
{
    DB::transaction(function () use ($vehicle, $driver) {
        // 1. Créer l'affectation
        $assignment = Assignment::create([...]);

        // 2. Changer automatiquement les statuts
        $statusService = app(StatusTransitionService::class);

        $statusService->changeVehicleStatus(
            $vehicle,
            VehicleStatusEnum::AFFECTE,
            [
                'reason' => "Affecté au chauffeur {$driver->full_name}",
                'metadata' => ['assignment_id' => $assignment->id],
                'change_type' => 'automatic',
            ]
        );

        $statusService->changeDriverStatus(
            $driver,
            DriverStatusEnum::EN_MISSION,
            [
                'reason' => "Affectation du véhicule {$vehicle->registration_plate}",
                'metadata' => ['assignment_id' => $assignment->id],
                'change_type' => 'automatic',
            ]
        );
    });
}
```

---

## 🚀 Migration et Déploiement

### Étape 1 : Exécuter les migrations

```bash
php artisan migrate
```

Les migrations sont **idempotentes** : elles peuvent être exécutées plusieurs fois sans duplication de données.

### Étape 2 : Vérifier les données insérées

```sql
-- Vérifier les statuts véhicules
SELECT * FROM vehicle_statuses ORDER BY sort_order;

-- Vérifier les types de véhicules
SELECT * FROM vehicle_types ORDER BY sort_order;

-- Vérifier la table d'historique
DESCRIBE status_history;
```

### Étape 3 : Mettre à jour les permissions

```php
// Ajouter les permissions dans votre seeder
Permission::create(['name' => 'update-vehicle-status']);
Permission::create(['name' => 'update-driver-status']);
Permission::create(['name' => 'view-status-history']);
```

---

## 📈 Analytics et Reporting

### Requêtes courantes

```php
// 1. Temps moyen d'un véhicule en maintenance
$avgDays = StatusHistory::getAverageDurationInStatus('Vehicle', 'en_maintenance', $orgId);

// 2. Statistiques de transitions
$transitions = StatusHistory::getTransitionStats('Vehicle', $orgId);
// [
//     ['from' => 'parking', 'to' => 'affecte', 'count' => 150],
//     ['from' => 'affecte', 'to' => 'en_panne', 'count' => 23],
//     ...
// ]

// 3. Véhicules par statut (dashboard)
$vehiclesByStatus = Vehicle::with('vehicleStatus')
    ->get()
    ->groupBy('vehicleStatus.slug')
    ->map->count();

// 4. Historique complet d'un véhicule
$history = $vehicle->statusHistory()
    ->with('changedBy')
    ->get();
```

### Requêtes SQL optimisées

```sql
-- Véhicules en maintenance depuis plus de 30 jours
SELECT v.id, v.registration_plate, h.changed_at
FROM vehicles v
INNER JOIN (
    SELECT DISTINCT ON (statusable_id) *
    FROM status_history
    WHERE statusable_type = 'App\Models\Vehicle'
      AND to_status = 'en_maintenance'
    ORDER BY statusable_id, changed_at DESC
) h ON h.statusable_id = v.id
WHERE h.changed_at < NOW() - INTERVAL '30 days';
```

---

## 🔐 Sécurité et Bonnes Pratiques

### 1. Validation des permissions

Toujours vérifier les permissions avant un changement de statut :

```php
if (!auth()->user()->can('update-vehicle-status')) {
    abort(403);
}
```

### 2. Audit trail complet

L'IP et le User-Agent sont enregistrés automatiquement pour traçabilité RGPD.

### 3. Transactions DB

Toutes les opérations critiques sont dans des transactions pour garantir la cohérence.

### 4. Validation stricte

Les Form Requests empêchent les données invalides d'atteindre le service.

---

## 🧪 Tests

### Tests unitaires recommandés

```php
// VehicleStatusEnumTest.php
public function test_parking_can_transition_to_affecte()
{
    $status = VehicleStatusEnum::PARKING;
    $this->assertTrue($status->canTransitionTo(VehicleStatusEnum::AFFECTE));
}

public function test_reforme_is_terminal_state()
{
    $status = VehicleStatusEnum::REFORME;
    $this->assertTrue($status->isTerminal());
    $this->assertEmpty($status->allowedTransitions());
}

// StatusTransitionServiceTest.php
public function test_changing_vehicle_status_creates_history()
{
    $vehicle = Vehicle::factory()->create();
    $service = app(StatusTransitionService::class);

    $service->changeVehicleStatus($vehicle, VehicleStatusEnum::EN_MAINTENANCE, [
        'reason' => 'Test'
    ]);

    $this->assertDatabaseHas('status_history', [
        'statusable_type' => Vehicle::class,
        'statusable_id' => $vehicle->id,
        'to_status' => 'en_maintenance',
    ]);
}
```

---

## 📞 Support et Contact

Pour toute question sur cette implémentation :

- **Architecture** : Consultez les commentaires dans les fichiers Enum
- **Base de données** : Voir les migrations dans `database/migrations/`
- **Business Logic** : `StatusTransitionService.php`

---

**Version** : 2.0-Enterprise
**Date de création** : 2025-11-08
**Auteur** : ZenFleet Engineering Team
**License** : Propriétaire

