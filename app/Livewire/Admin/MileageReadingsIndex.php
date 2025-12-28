<?php

namespace App\Livewire\Admin;

use App\Models\VehicleMileageReading;
use App\Models\Vehicle;
use App\Models\User;
use App\Services\MileageReadingService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MileageReadingsExport;
use Illuminate\Support\Facades\Log;

/**
 * MileageReadingsIndex - Vue globale des relevés kilométriques
 *
 * Features:
 * - Affichage de tous les relevés de l'organisation
 * - Filtrage par véhicule, méthode, auteur, dates
 * - Tri multi-colonnes
 * - Accès rapide à l'historique d'un véhicule
 * - Permission-based scoping (own/team/all)
 *
 * @version 1.0-Enterprise
 */
class MileageReadingsIndex extends Component
{
    use WithPagination;

    /**
     * 💼 SERVICE LAYER
     */
    protected MileageReadingService $service;

    /**
     * 🔍 PROPRIÉTÉS DE RECHERCHE ET FILTRES
     */
    public string $search = '';
    public string $vehicleFilter = '';
    public string $methodFilter = '';
    public string $authorFilter = '';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public string $mileageMin = ''; // NOUVEAU: Filtre kilométrage minimum
    public string $mileageMax = ''; // NOUVEAU: Filtre kilométrage maximum

    /**
     * 📊 PROPRIÉTÉS DE TRI ET PAGINATION
     */
    public string $sortField = 'recorded_at';
    public string $sortDirection = 'desc';
    public int $perPage = 25; // Défaut 25 pour pagination entreprise

    /**
     * 🗑️ PROPRIÉTÉS DE SUPPRESSION
     */
    public ?int $deleteId = null;
    public bool $showDeleteModal = false;

    /**
     * 🎛️ LISTENERS
     */
    protected $listeners = [
        'refresh-readings' => '$refresh',
    ];

    /**
     * 🔄 RESET PAGINATION QUAND FILTRES CHANGENT
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingVehicleFilter(): void
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

    public function updatingMileageMin(): void
    {
        $this->resetPage();
    }

    public function updatingMileageMax(): void
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
     * 🔄 RESET FILTRES
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'vehicleFilter',
            'methodFilter',
            'authorFilter',
            'dateFrom',
            'dateTo',
            'mileageMin',
            'mileageMax',
            'sortField',
            'sortDirection',
        ]);
        $this->resetPage();
    }

    /**
     * 📋 RÉCUPÉRATION DES RELEVÉS
     */
    public function getReadingsProperty()
    {
        $user = auth()->user();

        // 🏗️ BASE QUERY
        // On sélectionne explicitement toutes les colonnes de la table principale
        // pour éviter les conflits lors des joins ou addSelect ultérieurs.
        $query = VehicleMileageReading::query()
            ->select('vehicle_mileage_readings.*')
            ->with([
                'vehicle',
                'recordedBy',
            ])
            ->where('organization_id', $user->organization_id);

        // 🔐 PERMISSION-BASED SCOPING
        if ($user->can('view all mileage readings')) {
            // Tous les relevés de l'organisation
        } elseif ($user->can('view team mileage readings')) {
            // Relevés de l'équipe/dépôt
            $query->whereHas('vehicle', function ($q) use ($user) {
                if ($user->depot_id) {
                    $q->where('depot_id', $user->depot_id);
                }
            });
        } else {
            // Seulement les relevés créés par l'utilisateur
            $query->where('recorded_by_id', $user->id);
        }

        // 🔍 RECHERCHE GLOBALE
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('mileage', 'ilike', $search)
                    ->orWhere('notes', 'ilike', $search)
                    ->orWhereHas('vehicle', function ($q) use ($search) {
                        $q->where('registration_plate', 'ilike', $search)
                            ->orWhere('brand', 'ilike', $search)
                            ->orWhere('model', 'ilike', $search);
                    })
                    ->orWhereHas('recordedBy', function ($q) use ($search) {
                        $q->where('name', 'ilike', $search);
                    });
            });
        }

        // 📊 FILTRES SPÉCIFIQUES
        if (!empty($this->vehicleFilter)) {
            $query->where('vehicle_id', $this->vehicleFilter);
        }

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

        if (!empty($this->mileageMin)) {
            $query->where('mileage', '>=', $this->mileageMin);
        }

        if (!empty($this->mileageMax)) {
            $query->where('mileage', '<=', $this->mileageMax);
        }

        // 📊 TRI
        if ($this->sortField === 'vehicle') {
            $query->join('vehicles', 'vehicle_mileage_readings.vehicle_id', '=', 'vehicles.id')
                ->orderBy('vehicles.registration_plate', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        // 🧠 CALCUL INTELLIGENT DU KILOMÉTRAGE PRÉCÉDENT
        // Utilisation du Scope défini dans le modèle pour encapsulation et réutilisabilité.
        $query->withPreviousMileage();

        return $query->paginate($this->perPage);
    }

    /**
     * 🚗 LISTE DES VÉHICULES (POUR FILTRE)
     *
     * Récupère les véhicules de l'organisation pour le filtre dropdown.
     *
     * Optimisations Enterprise-Grade:
     * - Select minimal (id, registration_plate, brand, model)
     * - Filtre multi-tenant strict (organization_id)
     * - Tri alphabétique par plaque
     * - Exclude soft deleted vehicles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesProperty()
    {
        $user = auth()->user();

        return Vehicle::where('organization_id', $user->organization_id)
            ->select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();
    }

    /**
     * 👥 LISTE DES AUTEURS (POUR FILTRE)
     *
     * Récupère les utilisateurs ET les chauffeurs qui ont créé au moins un relevé
     * kilométrique dans l'organisation courante.
     *
     * Enterprise-Grade:
     * - Union Users + Drivers (chauffeurs peuvent aussi enregistrer relevés)
     * - Filtre multi-tenant strict (organization_id)
     * - Indication du type (user/driver) pour distinction visuelle
     * - Tri alphabétique unique
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAuthorsProperty()
    {
        $user = auth()->user();

        // IDs des utilisateurs/chauffeurs ayant enregistré au moins un relevé
        $authorIds = VehicleMileageReading::where('organization_id', $user->organization_id)
            ->whereNotNull('recorded_by_id')
            ->distinct()
            ->pluck('recorded_by_id');

        if ($authorIds->isEmpty()) {
            return collect([]);
        }

        // Récupérer les utilisateurs (table users)
        $users = User::whereIn('id', $authorIds)
            ->where('organization_id', $user->organization_id)
            ->select('id', 'name', DB::raw("'user' as type"))
            ->get();

        // Récupérer les chauffeurs (table drivers)
        $drivers = \App\Models\Driver::whereIn('id', $authorIds)
            ->where('organization_id', $user->organization_id)
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"), DB::raw("'driver' as type"))
            ->get();

        // Fusionner et trier par nom
        return $users->concat($drivers)->sortBy('name')->values();
    }

    /**
     * 📊 ANALYTICS ENTERPRISE - VIA SERVICE LAYER
     * 
     * Utilise le MileageReadingService pour obtenir 20+ KPIs avancés
     * avec caching intelligent (5 minutes).
     */
    public function getAnalyticsProperty(): array
    {
        if (!isset($this->service)) {
            $this->service = app(MileageReadingService::class);
        }

        return $this->service->getAnalytics(auth()->user()->organization_id);
    }

    /**
     * 📊 STATISTIQUES GLOBALES (Compatibilité legacy)
     */
    public function getStatsProperty(): array
    {
        $analytics = $this->analytics;

        return [
            'total_readings' => $analytics['total_readings'] ?? 0,
            'manual_count' => $analytics['manual_count'] ?? 0,
            'automatic_count' => $analytics['automatic_count'] ?? 0,
            'vehicles_tracked' => $analytics['vehicles_tracked'] ?? 0,
            'last_reading_date' => $analytics['last_reading_date'] ?? null,
            'total_mileage_covered' => $analytics['total_mileage_covered'] ?? 0,
            'avg_daily_mileage' => $analytics['avg_daily_mileage'] ?? 0,
            'readings_last_7_days' => $analytics['readings_last_7_days'] ?? 0,
            'readings_last_30_days' => $analytics['readings_last_30_days'] ?? 0,
        ];
    }

    /**
     * 🗑️ CONFIRMER LA SUPPRESSION
     * 
     * Affiche la popup de confirmation avant suppression
     * 
     * @param int $id ID du relevé à supprimer
     */
    public function confirmDelete(int $id): void
    {
        // Vérifier que le relevé existe et appartient à l'organisation
        $reading = VehicleMileageReading::where('organization_id', auth()->user()->organization_id)
            ->findOrFail($id);

        // Vérifier les permissions
        if (!auth()->user()->can('delete mileage readings')) {
            session()->flash('error', 'Vous n\'avez pas la permission de supprimer des relevés.');
            return;
        }

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * 🗑️ SUPPRIMER LE RELEVÉ
     * 
     * Supprime définitivement le relevé kilométrique
     * Recalcule automatiquement le kilométrage actuel du véhicule
     */
    public function delete(): void
    {
        if (!$this->deleteId) {
            session()->flash('error', 'Aucun relevé sélectionné pour la suppression.');
            return;
        }

        try {
            // Récupérer le relevé
            $reading = VehicleMileageReading::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($this->deleteId);

            // Vérifier les permissions
            if (!auth()->user()->can('delete mileage readings')) {
                session()->flash('error', 'Vous n\'avez pas la permission de supprimer des relevés.');
                return;
            }

            $vehicleId = $reading->vehicle_id;
            $deletedMileage = $reading->mileage;

            DB::beginTransaction();

            // Supprimer le relevé
            $reading->delete();

            // Recalculer le kilométrage actuel du véhicule
            // Prendre le dernier relevé restant
            $lastReading = VehicleMileageReading::where('vehicle_id', $vehicleId)
                ->orderBy('recorded_at', 'desc')
                ->first();

            if ($lastReading) {
                Vehicle::where('id', $vehicleId)->update([
                    'current_mileage' => $lastReading->mileage,
                ]);
            }

            DB::commit();

            session()->flash('success', "Relevé de " . number_format($deletedMileage) . " km supprimé avec succès.");

            // Émettre un événement
            $this->dispatch('reading-deleted', vehicleId: $vehicleId);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        } finally {
            $this->deleteId = null;
            $this->showDeleteModal = false;
            $this->resetPage();
        }
    }

    /**
     * ❌ ANNULER LA SUPPRESSION
     */
    public function cancelDelete(): void
    {
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }

    /**
     * 📤 GESTION DES EXPORTS ENTERPRISE
     */

    protected function getFilters(): array
    {
        return [
            'search' => $this->search,
            'vehicle_id' => $this->vehicleFilter,
            'method' => $this->methodFilter,
            'recorded_by' => $this->authorFilter,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'mileage_min' => $this->mileageMin,
            'mileage_max' => $this->mileageMax,
            'sort_by' => $this->sortField,
            'sort_direction' => $this->sortDirection,
        ];
    }

    public function exportExcel()
    {
        try {
            return Excel::download(
                new MileageReadingsExport($this->getFilters()),
                'releves_kilometriques_' . date('Y-m-d_H-i') . '.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Erreur export Excel: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export Excel: ' . $e->getMessage()
            ]);
        }
    }

    public function exportPdf()
    {
        try {
            session(['mileage_export_filters' => $this->getFilters()]);
            return redirect()->route('admin.mileage-readings.export.pdf');
        } catch (\Exception $e) {
            Log::error('Erreur export PDF: ' . $e->getMessage());
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export PDF: ' . $e->getMessage()
            ]);
        }
    }

    public function exportCsv()
    {
        try {
            session(['mileage_export_filters' => $this->getFilters()]);
            return redirect()->route('admin.mileage-readings.export.csv');
        } catch (\Exception $e) {
            Log::error('Erreur export CSV: ' . $e->getMessage());
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export CSV: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🎨 RENDER
     */
    public function render(): View
    {
        return view('livewire.admin.mileage-readings-index', [
            'readings' => $this->readings,
            'vehicles' => $this->vehicles,
            'authors' => $this->authors,
            'stats' => $this->stats,
            'analytics' => $this->analytics, // Analytics complètes 20+ KPIs
        ]);
    }
}
