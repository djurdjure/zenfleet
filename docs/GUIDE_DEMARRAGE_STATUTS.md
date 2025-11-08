# 🚀 Guide de Démarrage - Système de Gestion des Statuts

## Version 2.0 - Enterprise Grade

Ce guide vous permet de démarrer rapidement avec le nouveau système de gestion des statuts et types pour véhicules et chauffeurs.

---

## 📋 Prérequis

- PHP 8.2 ou supérieur (pour les Enums natifs)
- Laravel 10.x
- PostgreSQL 15+ (configuré dans votre `.env`)
- Tailwind CSS (pour les badges UI)

---

## ⚡ Installation Rapide

### Étape 1 : Exécuter les migrations

```bash
# Exécuter toutes les migrations
php artisan migrate

# Ou exécuter uniquement les nouvelles migrations
php artisan migrate --path=database/migrations/2025_11_08_000001_update_vehicle_statuses_with_new_enum_values.php
php artisan migrate --path=database/migrations/2025_11_08_000002_update_vehicle_types_with_new_enum_values.php
php artisan migrate --path=database/migrations/2025_11_08_000003_create_status_history_table.php
```

Les migrations sont **idempotentes** : elles peuvent être exécutées plusieurs fois en toute sécurité.

### Étape 2 : Vérifier les données insérées

Connectez-vous à PostgreSQL et vérifiez :

```sql
-- Vérifier les statuts véhicules (5 statuts attendus)
SELECT name, slug, color, sort_order FROM vehicle_statuses ORDER BY sort_order;

-- Vérifier les types de véhicules (9 types attendus)
SELECT name, slug, required_license_category FROM vehicle_types ORDER BY sort_order;

-- Vérifier la table d'historique
\d status_history
```

### Étape 3 : Ajouter les permissions (si vous utilisez Spatie Permission)

```php
// Dans DatabaseSeeder.php ou un seeder dédié
use Spatie\Permission\Models\Permission;

Permission::create(['name' => 'update-vehicle-status']);
Permission::create(['name' => 'update-driver-status']);
Permission::create(['name' => 'view-status-history']);

// Assigner aux rôles appropriés
$adminRole = Role::findByName('admin');
$adminRole->givePermissionTo(['update-vehicle-status', 'update-driver-status', 'view-status-history']);
```

---

## 🎯 Utilisation Basique

### 1. Afficher le badge de statut dans une vue

```blade
{{-- Dans une vue Blade (ex: vehicles/index.blade.php) --}}
@foreach($vehicles as $vehicle)
    <div class="flex items-center space-x-2">
        <span class="font-medium">{{ $vehicle->registration_plate }}</span>
        {!! $vehicle->statusBadge() !!}
        {!! $vehicle->typeBadge() !!}
    </div>
@endforeach
```

### 2. Changer le statut d'un véhicule

```php
// Dans un Controller
use App\Services\StatusTransitionService;
use App\Enums\VehicleStatusEnum;

public function updateStatus(Request $request, Vehicle $vehicle)
{
    $service = app(StatusTransitionService::class);

    try {
        $service->changeVehicleStatus(
            $vehicle,
            VehicleStatusEnum::from($request->input('status')),
            [
                'reason' => $request->input('reason'),
                'metadata' => $request->input('metadata', []),
            ]
        );

        return back()->with('success', 'Statut mis à jour avec succès.');
    } catch (\InvalidArgumentException $e) {
        return back()->withErrors(['status' => $e->getMessage()]);
    }
}
```

### 3. Utiliser le composant Livewire (exemple fourni)

```blade
{{-- Dans votre vue --}}
@livewire('admin.change-vehicle-status', ['vehicle' => $vehicle])
```

---

## 📊 Exemples de Cas d'Usage

### Cas 1 : Envoyer un véhicule en maintenance

```php
use App\Services\StatusTransitionService;
use App\Enums\VehicleStatusEnum;

$vehicle = Vehicle::find(1);
$service = app(StatusTransitionService::class);

$service->changeVehicleStatus(
    $vehicle,
    VehicleStatusEnum::EN_MAINTENANCE,
    [
        'reason' => 'Réparation du moteur suite à panne détectée',
        'metadata' => [
            'repair_request_id' => 42,
            'estimated_duration_days' => 7,
            'workshop' => 'Garage Central',
        ],
    ]
);
```

### Cas 2 : Mettre un chauffeur en congé

```php
use App\Enums\DriverStatusEnum;

$driver = Driver::find(5);

$service->changeDriverStatus(
    $driver,
    DriverStatusEnum::EN_CONGE,
    [
        'reason' => 'Congé annuel',
        'metadata' => [
            'leave_type' => 'annual',
            'leave_start_date' => '2025-12-01',
            'leave_end_date' => '2025-12-15',
        ],
    ]
);
```

### Cas 3 : Affectation automatique (avec changement de statuts)

```php
// Dans AssignmentService.php
public function createAssignment(Vehicle $vehicle, Driver $driver, array $data)
{
    DB::transaction(function () use ($vehicle, $driver, $data) {
        // 1. Créer l'affectation
        $assignment = Assignment::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'start_datetime' => $data['start_datetime'],
            'status' => 'active',
        ]);

        // 2. Changer les statuts automatiquement
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

        return $assignment;
    });
}
```

### Cas 4 : Consulter l'historique

```php
// Historique complet d'un véhicule
$history = $vehicle->statusHistory()
    ->with('changedBy')
    ->get();

foreach ($history as $change) {
    echo "{$change->from_status} → {$change->to_status} ";
    echo "par {$change->changedBy->name} ";
    echo "le {$change->changed_at->format('d/m/Y H:i')}\n";
}

// Historique récent (30 derniers jours)
$recentChanges = $vehicle->recentStatusHistory;
```

---

## 🎨 Personnalisation des Badges

### Tailles disponibles

```blade
{!! $vehicle->statusBadge(['size' => 'sm']) !!}   {{-- Petit --}}
{!! $vehicle->statusBadge(['size' => 'default']) !!}  {{-- Normal (défaut) --}}
{!! $vehicle->statusBadge(['size' => 'lg']) !!}   {{-- Grand --}}
```

### Avec ou sans icône

```blade
{!! $vehicle->statusBadge(['icon' => true]) !!}   {{-- Avec icône (défaut) --}}
{!! $vehicle->statusBadge(['icon' => false]) !!}  {{-- Sans icône --}}
```

### Badge avec tooltip (Alpine.js)

```blade
{!! $vehicle->statusBadgeWithTooltip() !!}
```

### Récupérer uniquement les classes CSS

```blade
<span class="{{ $vehicle->statusTailwindClasses() }}">
    {{ $vehicle->statusLabel() }}
</span>
```

---

## 🔍 Requêtes Utiles

### Filtrer les véhicules par statut

```php
use App\Models\VehicleStatus;

// Méthode 1 : Via la relation
$parkingVehicles = Vehicle::whereHas('vehicleStatus', function($query) {
    $query->where('slug', 'parking');
})->get();

// Méthode 2 : Via les scopes existants (si implémentés)
$activeVehicles = Vehicle::active()->get();
$inMaintenanceVehicles = Vehicle::inMaintenance()->get();
```

### Statistiques de statuts

```php
// Nombre de véhicules par statut
$stats = Vehicle::with('vehicleStatus')
    ->get()
    ->groupBy('vehicleStatus.slug')
    ->map(function($vehicles, $status) {
        return [
            'status' => $status,
            'count' => $vehicles->count(),
            'vehicles' => $vehicles->pluck('registration_plate'),
        ];
    });

// Temps moyen en maintenance
use App\Models\StatusHistory;

$avgDays = StatusHistory::getAverageDurationInStatus(
    'Vehicle',
    'en_maintenance',
    auth()->user()->organization_id
);
```

---

## ⚠️ Erreurs Courantes et Solutions

### Erreur : "Transition impossible de X vers Y"

**Cause** : La transition demandée n'est pas autorisée selon les règles métier.

**Solution** : Vérifiez les transitions autorisées dans la documentation ou :

```php
$currentStatus = VehicleStatusEnum::PARKING;
$allowedTransitions = $currentStatus->allowedTransitions();
// Retourne : [VehicleStatusEnum::AFFECTE, VehicleStatusEnum::EN_PANNE]
```

### Erreur : "Statut 'xxx' non trouvé en base de données"

**Cause** : Le slug de l'Enum ne correspond à aucun enregistrement dans `vehicle_statuses` ou `driver_statuses`.

**Solution** : Ré-exécutez les migrations pour insérer les statuts par défaut :

```bash
php artisan migrate:refresh --path=database/migrations/2025_11_08_000001_update_vehicle_statuses_with_new_enum_values.php
```

### Erreur : Permission denied (403)

**Cause** : L'utilisateur n'a pas la permission requise.

**Solution** : Vérifiez et assignez les permissions :

```php
// Vérifier si l'utilisateur a la permission
auth()->user()->can('update-vehicle-status');

// Assigner la permission
$user->givePermissionTo('update-vehicle-status');
```

---

## 📈 Dashboard et Analytics

### Créer un widget de statuts

```blade
{{-- Dans un dashboard --}}
<div class="grid grid-cols-3 gap-4">
    @foreach(['parking', 'affecte', 'en_maintenance'] as $statusSlug)
        @php
            $count = Vehicle::whereHas('vehicleStatus', fn($q) => $q->where('slug', $statusSlug))->count();
            $status = \App\Enums\VehicleStatusEnum::from($statusSlug);
        @endphp
        <div class="p-4 bg-white rounded-lg shadow">
            <div class="text-2xl font-bold">{{ $count }}</div>
            <div>{!! (new \App\Models\Vehicle)->statusBadge() !!}</div>
        </div>
    @endforeach
</div>
```

### Graphique de transitions (exemple avec Chart.js)

```php
// Dans un Controller
$transitions = StatusHistory::getTransitionStats('Vehicle', auth()->user()->organization_id);

return view('dashboard.stats', [
    'transitions' => $transitions,
]);
```

```javascript
// Dans la vue
const transitionData = @json($transitions);

// Créer un graphique avec Chart.js
const ctx = document.getElementById('transitionChart').getContext('2d');
new Chart(ctx, {
    type: 'sankey',
    data: transitionData,
    // ...
});
```

---

## 🧪 Tests et Validation

### Tester une transition

```php
// Tinker
php artisan tinker

>>> $vehicle = Vehicle::first();
>>> $service = app(StatusTransitionService::class);
>>> $service->changeVehicleStatus($vehicle, VehicleStatusEnum::EN_MAINTENANCE, ['reason' => 'Test']);

// Vérifier l'historique
>>> $vehicle->statusHistory->first()->toArray();
```

### Vérifier les Enums

```php
>>> VehicleStatusEnum::cases();
// Retourne tous les statuts disponibles

>>> VehicleStatusEnum::PARKING->allowedTransitions();
// Retourne [AFFECTE, EN_PANNE]
```

---

## 📚 Ressources

- **Documentation complète** : `docs/GESTION_STATUTS_VEHICULES_CHAUFFEURS.md`
- **Code source Enums** : `app/Enums/`
- **Service de transition** : `app/Services/StatusTransitionService.php`
- **Composant Livewire exemple** : `app/Livewire/Admin/ChangeVehicleStatus.php`

---

## 💡 Conseils Pro

1. **Utilisez toujours le Service** : Ne modifiez JAMAIS directement `status_id` sans passer par `StatusTransitionService` pour garantir la cohérence et l'historisation.

2. **Privilégiez les Enums** : Utilisez `VehicleStatusEnum::PARKING` au lieu de chaînes hardcodées pour éviter les erreurs de typo.

3. **Exploitez l'historique** : Utilisez `StatusHistory` pour des analytics avancés et la conformité RGPD.

4. **Testez les transitions** : Avant de déployer une nouvelle fonctionnalité, testez toutes les transitions possibles.

5. **Personnalisez les hooks** : Étendez `StatusTransitionService::executeVehiclePostTransitionHook()` pour vos besoins métier spécifiques.

---

**Version** : 2.0-Enterprise
**Dernière mise à jour** : 2025-11-08

