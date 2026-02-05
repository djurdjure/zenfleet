<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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
    use AuthorizesRequests;

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
    public int $perPage = 25;

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

        $this->authorize('mileageHistory', $this->vehicle);

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
     * 📊 STATISTIQUES ENRICHIES DU VÉHICULE (ENTERPRISE-GRADE)
     */
    public function getStatsProperty(): array
    {
        $allReadings = VehicleMileageReading::where('vehicle_id', $this->vehicleId)
            ->where('organization_id', auth()->user()->organization_id)
            ->orderBy('recorded_at', 'asc')
            ->get();

        $manualCount = $allReadings->where('recording_method', 'manual')->count();
        $automaticCount = $allReadings->where('recording_method', 'automatic')->count();
        $totalCount = $allReadings->count();

        // Distance totale parcourue
        $totalDistance = $totalCount > 1
            ? ($allReadings->last()->mileage - $allReadings->first()->mileage)
            : 0;

        // Dernier relevé
        $lastReading = $allReadings->last();

        // Relevés du mois en cours
        $monthlyCount = $allReadings->where('recorded_at', '>=', now()->startOfMonth())->count();

        // Relevés des 7 derniers jours
        $last7Days = $allReadings->where('recorded_at', '>=', now()->subDays(7));
        $last7DaysKm = $last7Days->count() > 1
            ? ($last7Days->last()->mileage - $last7Days->first()->mileage)
            : 0;

        // Moyenne journalière (basée sur 30 derniers jours)
        $avgDaily = $totalCount > 1 && $allReadings->first()->recorded_at
            ? round($totalDistance / max($allReadings->first()->recorded_at->diffInDays(now()), 1), 2)
            : 0;

        // Tendance 7 jours
        $trend7Days = $last7DaysKm;

        // Premier relevé date
        $firstReadingDate = $allReadings->first()?->recorded_at->format('d/m/Y');

        return [
            'total_readings' => $totalCount,
            'manual_count' => $manualCount,
            'automatic_count' => $automaticCount,
            'manual_percentage' => $totalCount > 0 ? round(($manualCount / $totalCount) * 100, 1) : 0,
            'automatic_percentage' => $totalCount > 0 ? round(($automaticCount / $totalCount) * 100, 1) : 0,
            'total_distance' => $totalDistance,
            'last_reading' => $lastReading?->recorded_at,
            'first_reading_date' => $firstReadingDate,
            'monthly_count' => $monthlyCount,
            'avg_daily' => $avgDaily,
            'last_7_days_km' => $last7DaysKm,
            'trend_7_days' => $trend7Days,
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
        $this->authorize('create', VehicleMileageReading::class);

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
        $this->authorize('create', VehicleMileageReading::class);

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
        $this->authorize('export', VehicleMileageReading::class);

        session()->flash('info', 'Fonctionnalité d\'export CSV en cours de développement.');
    }

    /**
     * 📤 EXPORTER LES RELEVÉS (Excel)
     */
    public function exportExcel(): void
    {
        $this->authorize('export', VehicleMileageReading::class);

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
