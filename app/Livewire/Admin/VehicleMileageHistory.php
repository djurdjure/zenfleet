<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

/**
 * VehicleMileageHistory - Historique des relevés kilométriques d'un véhicule
 *
 * Features:
 * - Affichage paginé de l'historique des relevés
 * - Filtrage par date, méthode, auteur
 * - Ajout de nouveaux relevés (modal)
 * - Export CSV/Excel
 * - Multi-tenant scoping
 * - Permission-based access
 *
 * @version 1.0-Enterprise
 */
class VehicleMileageHistory extends Component
{
    use WithPagination;

    /**
     * 🚗 PROPRIÉTÉS DU VÉHICULE
     */
    public Vehicle $vehicle;
    public int $vehicleId;

    /**
     * 🔍 PROPRIÉTÉS DE RECHERCHE ET FILTRES
     */
    public string $search = '';
    public string $methodFilter = '';
    public string $authorFilter = '';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    /**
     * 📊 PROPRIÉTÉS DE TRI ET PAGINATION
     */
    public string $sortField = 'recorded_at';
    public string $sortDirection = 'desc';
    public int $perPage = 15;

    /**
     * 📝 PROPRIÉTÉS DU FORMULAIRE DE NOUVEAU RELEVÉ
     */
    public bool $showAddModal = false;
    public int $newMileage = 0;
    public string $newRecordedAt = '';
    public string $newRecordingMethod = 'manual';
    public string $newNotes = '';

    /**
     * 🎛️ LISTENERS POUR ÉVÉNEMENTS
     */
    protected $listeners = [
        'mileage-reading-created' => '$refresh',
        'refresh-history' => '$refresh',
    ];

    /**
     * 📋 RÈGLES DE VALIDATION
     */
    protected function rules(): array
    {
        return [
            'newMileage' => [
                'required',
                'integer',
                'min:0',
                'max:9999999',
                function ($attribute, $value, $fail) {
                    if ($value < $this->vehicle->current_mileage) {
                        $fail("Le kilométrage ({$value} km) ne peut pas être inférieur au kilométrage actuel du véhicule (" . number_format($this->vehicle->current_mileage) . " km).");
                    }
                },
            ],
            'newRecordedAt' => [
                'required',
                'date',
                'before_or_equal:now',
                'after_or_equal:' . now()->subYears(1)->toDateString(),
            ],
            'newRecordingMethod' => [
                'required',
                'in:manual,automatic',
            ],
            'newNotes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * 🔧 MESSAGES DE VALIDATION PERSONNALISÉS
     */
    protected $messages = [
        'newMileage.required' => 'Le kilométrage est obligatoire.',
        'newMileage.integer' => 'Le kilométrage doit être un nombre entier.',
        'newMileage.min' => 'Le kilométrage ne peut pas être négatif.',
        'newMileage.max' => 'Le kilométrage ne peut pas dépasser 9 999 999 km.',
        'newRecordedAt.required' => 'La date d\'enregistrement est obligatoire.',
        'newRecordedAt.date' => 'La date d\'enregistrement doit être une date valide.',
        'newRecordedAt.before_or_equal' => 'La date d\'enregistrement ne peut pas être dans le futur.',
        'newRecordedAt.after_or_equal' => 'La date d\'enregistrement ne peut pas dépasser 1 an dans le passé.',
        'newRecordingMethod.required' => 'La méthode d\'enregistrement est obligatoire.',
        'newRecordingMethod.in' => 'La méthode d\'enregistrement doit être "manual" ou "automatic".',
        'newNotes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.',
    ];

    /**
     * 🏗️ INITIALISATION DU COMPOSANT
     */
    public function mount(int $vehicleId): void
    {
        $user = auth()->user();

        // Récupérer le véhicule avec multi-tenant scoping
        $this->vehicle = Vehicle::where('organization_id', $user->organization_id)
            ->findOrFail($vehicleId);

        $this->vehicleId = $vehicleId;

        // Initialiser la date d'enregistrement à maintenant
        $this->newRecordedAt = now()->format('Y-m-d\TH:i');
    }

    /**
     * 🔄 RESET PAGINATION QUAND FILTRES CHANGENT
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAuthorFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * 📊 TRI DES COLONNES
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * 🔄 RESET TOUS LES FILTRES
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'methodFilter',
            'authorFilter',
            'dateFrom',
            'dateTo',
            'sortField',
            'sortDirection',
        ]);
        $this->resetPage();
    }

    /**
     * 📋 RÉCUPÉRATION DES RELEVÉS AVEC FILTRES
     */
    public function getReadingsProperty()
    {
        $user = auth()->user();

        $query = VehicleMileageReading::with([
            'recordedBy',
            'vehicle',
        ])
            ->where('vehicle_id', $this->vehicleId)
            ->where('organization_id', $user->organization_id);

        // 🔍 RECHERCHE GLOBALE
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('mileage', 'ilike', $search)
                    ->orWhere('notes', 'ilike', $search)
                    ->orWhereHas('recordedBy', function ($q) use ($search) {
                        $q->where('name', 'ilike', $search);
                    });
            });
        }

        // 📊 FILTRES SPÉCIFIQUES
        if (!empty($this->methodFilter)) {
            $query->where('recording_method', $this->methodFilter);
        }

        if (!empty($this->authorFilter)) {
            $query->where('recorded_by_id', $this->authorFilter);
        }

        if (!empty($this->dateFrom)) {
            $query->whereDate('recorded_at', '>=', $this->dateFrom);
        }

        if (!empty($this->dateTo)) {
            $query->whereDate('recorded_at', '<=', $this->dateTo);
        }

        // 📊 TRI
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * 📊 STATISTIQUES DU VÉHICULE
     */
    public function getStatsProperty(): array
    {
        $readings = VehicleMileageReading::where('vehicle_id', $this->vehicleId)
            ->where('organization_id', auth()->user()->organization_id)
            ->get();

        $manualCount = $readings->where('recording_method', 'manual')->count();
        $automaticCount = $readings->where('recording_method', 'automatic')->count();

        $totalDistance = $this->vehicle->current_mileage - ($this->vehicle->initial_mileage ?? 0);

        $lastReading = $readings->sortByDesc('recorded_at')->first();

        return [
            'total_readings' => $readings->count(),
            'manual_count' => $manualCount,
            'automatic_count' => $automaticCount,
            'total_distance' => $totalDistance,
            'average_mileage' => $readings->count() > 0 ? $readings->avg('mileage') : 0,
            'last_reading_date' => $lastReading ? $lastReading->recorded_at->format('d/m/Y H:i') : 'N/A',
        ];
    }

    /**
     * 👥 LISTE DES AUTEURS DISPONIBLES (POUR FILTRE)
     */
    public function getAuthorsProperty()
    {
        return User::where('organization_id', auth()->user()->organization_id)
            ->whereHas('mileageReadings', function ($q) {
                $q->where('vehicle_id', $this->vehicleId);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * 🆕 OUVRIR LE MODAL D'AJOUT
     */
    public function openAddModal(): void
    {
        // Vérifier la permission
        if (!auth()->user()->can('create mileage readings')) {
            session()->flash('error', 'Vous n\'avez pas la permission de créer des relevés kilométriques.');
            return;
        }

        $this->resetAddForm();
        $this->showAddModal = true;
    }

    /**
     * ❌ FERMER LE MODAL D'AJOUT
     */
    public function closeAddModal(): void
    {
        $this->showAddModal = false;
        $this->resetAddForm();
        $this->resetValidation();
    }

    /**
     * 🔄 RESET DU FORMULAIRE D'AJOUT
     */
    private function resetAddForm(): void
    {
        $this->newMileage = $this->vehicle->current_mileage;
        $this->newRecordedAt = now()->format('Y-m-d\TH:i');
        $this->newRecordingMethod = 'manual';
        $this->newNotes = '';
    }

    /**
     * 💾 CRÉER UN NOUVEAU RELEVÉ
     */
    public function saveReading(): void
    {
        // Vérifier la permission
        if (!auth()->user()->can('create mileage readings')) {
            session()->flash('error', 'Vous n\'avez pas la permission de créer des relevés kilométriques.');
            $this->closeAddModal();
            return;
        }

        // Valider les données
        $validated = $this->validate();

        DB::beginTransaction();
        try {
            // Créer le relevé
            $reading = VehicleMileageReading::create([
                'vehicle_id' => $this->vehicleId,
                'mileage' => $this->newMileage,
                'recorded_at' => $this->newRecordedAt,
                'recording_method' => $this->newRecordingMethod,
                'notes' => $this->newNotes,
                'recorded_by_id' => $this->newRecordingMethod === 'manual' ? auth()->id() : null,
                'organization_id' => auth()->user()->organization_id,
            ]);

            // L'Observer met à jour automatiquement vehicle.current_mileage

            DB::commit();

            // Rafraîchir le véhicule
            $this->vehicle->refresh();

            // Message de succès
            session()->flash('success', 'Relevé kilométrique ajouté avec succès. Kilométrage actuel: ' . number_format($this->vehicle->current_mileage) . ' km');

            // Fermer le modal
            $this->closeAddModal();

            // Émettre un événement pour rafraîchir
            $this->dispatch('mileage-reading-created');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'ajout du relevé: ' . $e->getMessage());
        }
    }

    /**
     * 📤 EXPORTER LES RELEVÉS (CSV)
     */
    public function exportCsv(): void
    {
        // Vérifier la permission
        if (!auth()->user()->can('export mileage readings')) {
            session()->flash('error', 'Vous n\'avez pas la permission d\'exporter les relevés kilométriques.');
            return;
        }

        session()->flash('info', 'Fonctionnalité d\'export CSV en cours de développement.');
    }

    /**
     * 📤 EXPORTER LES RELEVÉS (Excel)
     */
    public function exportExcel(): void
    {
        // Vérifier la permission
        if (!auth()->user()->can('export mileage readings')) {
            session()->flash('error', 'Vous n\'avez pas la permission d\'exporter les relevés kilométriques.');
            return;
        }

        session()->flash('info', 'Fonctionnalité d\'export Excel en cours de développement.');
    }

    /**
     * 🎨 RENDER
     */
    public function render(): View
    {
        return view('livewire.admin.vehicle-mileage-history', [
            'readings' => $this->readings,
            'stats' => $this->stats,
            'authors' => $this->authors,
        ])->layout('layouts.admin.catalyst');
    }
}
