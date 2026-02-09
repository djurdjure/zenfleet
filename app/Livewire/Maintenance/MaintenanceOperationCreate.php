<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceOperation;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🔧 Composant Formulaire Création Opération de Maintenance - Enterprise Grade V1
 *
 * Architecture enterprise-grade suivant les standards ZenFleet:
 * - Validation temps réel avec feedback visuel
 * - Auto-complétion intelligente depuis maintenance_types
 * - SlimSelect pour sélecteurs professionnels
 * - Kilométrage auto-chargé depuis véhicule
 * - Support Suppliers génériques (architecture unifiée)
 * - UX optimisée avec statut visuel
 *
 * @version 1.0-Enterprise
 * @since 2025-11-23
 */
class MaintenanceOperationCreate extends Component
{
    use AuthorizesRequests;

    // ====== PROPRIÉTÉS DU FORMULAIRE ======

    #[Validate('required|exists:vehicles,id')]
    public string $vehicle_id = '';

    #[Validate('required|exists:maintenance_types,id')]
    public string $maintenance_type_id = '';

    #[Validate('nullable|exists:suppliers,id')]
    public string $provider_id = '';

    #[Validate('required|in:planned,in_progress,completed')]
    public string $status = 'planned';

    #[Validate('required|date')]
    public string $scheduled_date = '';

    #[Validate('nullable|date|after_or_equal:scheduled_date')]
    public string $completed_date = '';

    #[Validate('nullable|integer|min:0')]
    public ?int $mileage_at_maintenance = null;

    // ✅ Durée saisie EN HEURES dans l'UI (converti en minutes pour DB)
    public ?float $duration_hours = null;

    #[Validate('nullable|integer|min:1')]
    public ?int $duration_minutes = null;

    #[Validate('nullable|numeric|min:0|max:999999.99')]
    public ?float $total_cost = null;

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('nullable|string|max:2000')]
    public string $notes = '';

    // ====== DONNÉES CONTEXTUELLES ======

    // Kilométrage actuel du véhicule sélectionné
    public ?int $current_vehicle_mileage = null;

    // Données du type de maintenance sélectionné (pour auto-complétion)
    public ?array $selectedMaintenanceType = null;

    // Options pour les selects
    public $vehicleOptions = [];
    public $maintenanceTypeOptions = [];
    public $providerOptions = [];

    // État du formulaire
    public bool $isSubmitting = false;
    public array $errors_list = [];

    /**
     * Initialisation du composant
     */
    public function mount()
    {
        Log::info('[MaintenanceOperationCreate] Initialisation du composant');

        // Autorisation create
        $this->authorize('create', MaintenanceOperation::class);

        // Initialiser la date planifiée à aujourd'hui
        $this->scheduled_date = Carbon::today()->format('Y-m-d');

        // Charger les options pour les selects
        $this->loadOptions();

        Log::info('[MaintenanceOperationCreate] Initialisation terminée avec succès');
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.maintenance.maintenance-operation-create', [
            'vehicleOptions' => $this->vehicleOptions,
            'maintenanceTypeOptions' => $this->maintenanceTypeOptions,
            'providerOptions' => $this->providerOptions,
            'statusOptions' => MaintenanceOperation::STATUSES,
        ]);
    }

    /**
     * ✅ Charger les options pour les selects - ENTERPRISE EDITION
     *
     * Utilise les colonnes RÉELLES de la base de données:
     * - vehicles: registration_plate, brand, model, current_mileage
     * - maintenance_types: name, category, estimated_duration_minutes, estimated_cost
     * - suppliers: company_name, supplier_type, city, wilaya, rating
     */
    protected function loadOptions()
    {
        Log::info('[MaintenanceOperationCreate] Chargement des options pour les selects');

        // ✅ VÉHICULES: Charger avec kilométrage actuel
        $this->vehicleOptions = Vehicle::select('id', 'registration_plate', 'brand', 'model', 'current_mileage')
            ->whereDoesntHave('vehicleStatus', function ($query) {
                $query->whereIn('slug', ['reforme', 'decommissioned', 'archived', 'sold', 'vendu'])
                    ->orWhereIn('name', ['Réformé', 'Decommissioned', 'Archived', 'Sold', 'Vendu']);
            })
            ->orderBy('registration_plate')
            ->get()
            ->map(function ($vehicle) {
                $vehicle->display_text = sprintf(
                    '%s - %s %s (%s km)',
                    $vehicle->registration_plate,
                    $vehicle->brand,
                    $vehicle->model,
                    number_format($vehicle->current_mileage ?? 0, 0, ',', ' ')
                );
                return $vehicle;
            });

        // ✅ TYPES DE MAINTENANCE: Charger avec métadonnées
        // Note: La table n'a QUE 'estimated_duration_minutes', pas 'estimated_duration_hours'
        $this->maintenanceTypeOptions = MaintenanceType::select(
                'id',
                'name',
                'category',
                'description',
                'estimated_duration_minutes', // ✅ Seule colonne de durée existante
                'estimated_cost'
            )
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(function ($type) {
                // Emojis pour catégories visuelles
                $categoryEmojis = [
                    'preventive' => '🔧',
                    'corrective' => '⚠️',
                    'inspection' => '🔍',
                    'revision' => '📋',
                ];

                $emoji = $categoryEmojis[$type->category] ?? '🔧';
                $type->display_text = sprintf(
                    '%s %s (%s)',
                    $emoji,
                    $type->name,
                    ucfirst($type->category)
                );

                // ✅ CALCUL: Convertir minutes en heures pour l'auto-complétion
                if ($type->estimated_duration_minutes) {
                    $type->estimated_duration_hours = round($type->estimated_duration_minutes / 60, 2);
                } else {
                    $type->estimated_duration_hours = null;
                }

                return $type;
            });

        // ✅ FOURNISSEURS (Suppliers génériques): Charger avec localisation DZ
        // Note: La table 'suppliers' utilise 'company_name', PAS 'name'
        $this->providerOptions = Supplier::select(
                'id',
                'company_name',        // ✅ Nom entreprise
                'supplier_type',
                'city',
                'wilaya',              // ✅ Wilaya algérienne
                'rating'
            )
            ->where('is_active', true)
            ->orderBy('company_name')
            ->get()
            ->map(function ($provider) {
                // Display text enrichi: "Entreprise - Ville, Wilaya ⭐⭐⭐⭐"
                $provider->display_text = $provider->company_name;

                if ($provider->city || $provider->wilaya) {
                    $location = [];
                    if ($provider->city) {
                        $location[] = $provider->city;
                    }
                    if ($provider->wilaya) {
                        $wilayaLabel = Supplier::WILAYAS[$provider->wilaya] ?? $provider->wilaya;
                        $location[] = $wilayaLabel;
                    }
                    $provider->display_text .= ' - ' . implode(', ', $location);
                }

                // Rating 0-10 → 0-5 étoiles
                if ($provider->rating && $provider->rating > 0) {
                    $stars = min(5, max(0, round($provider->rating / 2)));
                    if ($stars > 0) {
                        $provider->display_text .= ' ' . str_repeat('⭐', (int) $stars);
                    }
                }

                return $provider;
            });

        Log::info('[MaintenanceOperationCreate] Options chargées', [
            'vehicles_count' => $this->vehicleOptions->count(),
            'types_count' => $this->maintenanceTypeOptions->count(),
            'providers_count' => $this->providerOptions->count(),
        ]);
    }

    /**
     * Listener: Mise à jour lors de la sélection d'un véhicule
     * Auto-charge le kilométrage actuel pour pré-remplir le formulaire
     */
    public function updatedVehicleId()
    {
        if ($this->vehicle_id) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle) {
                $this->current_vehicle_mileage = $vehicle->current_mileage;

                // Pré-remplir le kilométrage si vide
                if ($this->mileage_at_maintenance === null && $vehicle->current_mileage) {
                    $this->mileage_at_maintenance = $vehicle->current_mileage;
                }

                Log::info('[MaintenanceOperationCreate] Véhicule sélectionné', [
                    'vehicle_id' => $this->vehicle_id,
                    'current_mileage' => $this->current_vehicle_mileage,
                ]);
            }
        } else {
            $this->current_vehicle_mileage = null;
        }
    }

    /**
     * Listener: Mise à jour lors de la sélection d'un type de maintenance
     * Auto-complète durée et coût estimés
     */
    public function updatedMaintenanceTypeId()
    {
        if ($this->maintenance_type_id) {
            $type = MaintenanceType::find($this->maintenance_type_id);
            if ($type) {
                // Stocker les données du type pour auto-complétion
                $this->selectedMaintenanceType = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'category' => $type->category,
                    'estimated_duration_minutes' => $type->estimated_duration_minutes,
                    'estimated_cost' => $type->estimated_cost,
                    'description' => $type->description,
                ];

                // ✅ Auto-remplir durée EN HEURES (convertir minutes → heures)
                if ($this->duration_hours === null && $type->estimated_duration_minutes) {
                    $this->duration_hours = round($type->estimated_duration_minutes / 60, 2);
                    $this->duration_minutes = $type->estimated_duration_minutes;
                }

                if ($this->total_cost === null && $type->estimated_cost) {
                    $this->total_cost = $type->estimated_cost;
                }

                if (empty($this->description) && $type->description) {
                    $this->description = $type->description;
                }

                Log::info('[MaintenanceOperationCreate] Type de maintenance sélectionné', [
                    'type_id' => $this->maintenance_type_id,
                    'auto_filled' => [
                        'duration' => $this->duration_minutes,
                        'cost' => $this->total_cost,
                    ],
                ]);
            }
        } else {
            $this->selectedMaintenanceType = null;
        }
    }

    /**
     * ✅ Listener: Mise à jour lors de la saisie de la durée en heures
     * Conversion automatique heures → minutes pour la DB
     */
    public function updatedDurationHours()
    {
        if ($this->duration_hours !== null && $this->duration_hours > 0) {
            $this->duration_minutes = (int) round($this->duration_hours * 60);
            Log::info('[MaintenanceOperationCreate] Durée convertie', [
                'hours' => $this->duration_hours,
                'minutes' => $this->duration_minutes,
            ]);
        } else {
            $this->duration_minutes = null;
        }
    }

    /**
     * Soumission du formulaire - Création de l'opération
     */
    public function save()
    {
        Log::info('[MaintenanceOperationCreate] Début de la sauvegarde');

        $this->isSubmitting = true;
        $this->errors_list = [];

        try {
            // ✅ CONVERSION FINALE: Heures → Minutes avant validation
            if ($this->duration_hours !== null && $this->duration_hours > 0) {
                $this->duration_minutes = (int) round($this->duration_hours * 60);
            }

            // Validation
            $validated = $this->validate();

            Log::info('[MaintenanceOperationCreate] Validation réussie', $validated);

            // Création de l'opération dans une transaction
            DB::beginTransaction();

            try {
                $operation = MaintenanceOperation::create([
                    'organization_id' => auth()->user()->organization_id,
                    'vehicle_id' => $this->vehicle_id,
                    'maintenance_type_id' => $this->maintenance_type_id,
                    'provider_id' => $this->provider_id ?: null,
                    'status' => $this->status,
                    'scheduled_date' => $this->scheduled_date,
                    'completed_date' => $this->completed_date ?: null,
                    'mileage_at_maintenance' => $this->mileage_at_maintenance,
                    'duration_minutes' => $this->duration_minutes,
                    'total_cost' => $this->total_cost,
                    'description' => $this->description,
                    'notes' => $this->notes,
                    'created_by' => auth()->id(),
                ]);

                // ✅ Mise à jour du kilométrage du véhicule si nécessaire
                if ($this->mileage_at_maintenance && $this->status === 'completed') {
                    $vehicle = Vehicle::find($this->vehicle_id);
                    if ($vehicle && $this->mileage_at_maintenance > $vehicle->current_mileage) {
                        $vehicle->update(['current_mileage' => $this->mileage_at_maintenance]);
                        Log::info('[MaintenanceOperationCreate] Kilométrage véhicule mis à jour', [
                            'vehicle_id' => $this->vehicle_id,
                            'new_mileage' => $this->mileage_at_maintenance,
                        ]);
                    }
                }

                DB::commit();

                Log::info('[MaintenanceOperationCreate] Opération créée avec succès', [
                    'operation_id' => $operation->id,
                ]);

                // Message de succès
                session()->flash('success', 'Opération de maintenance créée avec succès !');

                // Émettre événement pour redirection
                $this->dispatch('operation-created', operation_id: $operation->id);

                // Redirection vers la liste
                return redirect()->route('admin.maintenance.operations.index');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errors_list = $e->validator->errors()->all();
            Log::warning('[MaintenanceOperationCreate] Erreur de validation', [
                'errors' => $this->errors_list,
            ]);

            session()->flash('error', 'Veuillez corriger les erreurs dans le formulaire.');

        } catch (\Exception $e) {
            Log::error('[MaintenanceOperationCreate] Erreur lors de la sauvegarde', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Une erreur est survenue lors de la création de l\'opération.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Annuler et retourner à la liste
     */
    public function cancel()
    {
        return redirect()->route('admin.maintenance.operations.index');
    }

    /**
     * Règles de validation personnalisées
     */
    protected function rules()
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'provider_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|in:planned,in_progress,completed,cancelled',
            'scheduled_date' => 'required|date',
            'completed_date' => 'nullable|date|after_or_equal:scheduled_date',
            'mileage_at_maintenance' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1|max:14400',
            'total_cost' => 'nullable|numeric|min:0|max:999999.99',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages()
    {
        return [
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'maintenance_type_id.required' => 'Le type de maintenance est obligatoire.',
            'maintenance_type_id.exists' => 'Le type de maintenance sélectionné n\'existe pas.',
            'provider_id.exists' => 'Le fournisseur sélectionné n\'existe pas.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'scheduled_date.required' => 'La date planifiée est obligatoire.',
            'scheduled_date.date' => 'La date planifiée doit être une date valide.',
            'completed_date.date' => 'La date de completion doit être une date valide.',
            'completed_date.after_or_equal' => 'La date de completion ne peut pas être antérieure à la date planifiée.',
            'mileage_at_maintenance.integer' => 'Le kilométrage doit être un nombre entier.',
            'mileage_at_maintenance.min' => 'Le kilométrage ne peut pas être négatif.',
            'duration_minutes.integer' => 'La durée doit être un nombre entier de minutes.',
            'duration_minutes.min' => 'La durée doit être d\'au moins 1 minute (0.02 heure).',
            'duration_minutes.max' => 'La durée ne peut pas dépasser 10 jours (240 heures ou 14400 minutes).',
            'total_cost.numeric' => 'Le coût total doit être un nombre valide.',
            'total_cost.min' => 'Le coût total ne peut pas être négatif.',
            'total_cost.max' => 'Le coût total ne peut pas dépasser 999 999,99 DA.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 2000 caractères.',
        ];
    }
}
