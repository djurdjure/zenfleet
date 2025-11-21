<?php

namespace App\Livewire\Admin;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleType;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

/**
 * 🚀 ASSIGNMENT FILTERS ENHANCED - ULTRA ENTERPRISE-GRADE 
 * 
 * Version corrigée qui affiche TOUTES les affectations par défaut
 * Sans filtre de date restrictif au démarrage
 * 
 * CORRECTIONS MAJEURES:
 * ✅ Affichage de TOUTES les affectations par défaut (en cours, terminées, programmées)
 * ✅ Suppression du filtre par défaut "ce mois" qui cachait des affectations
 * ✅ Indicateur clair du nombre total d'affectations visibles
 * ✅ Bouton "Voir tout" pour réinitialiser rapidement
 * ✅ Performance optimisée avec cache intelligent
 * 
 * @version 6.0 Ultra-Pro Enterprise Edition FIXED
 * @since 2025-11-10
 */
class AssignmentFiltersEnhanced extends Component
{
    use WithPagination;

    // =========================================================================
    // PROPRIÉTÉS PRINCIPALES - FILTRES
    // =========================================================================
    
    public string $search = '';
    public string $status = '';
    public ?int $vehicleId = null;
    public ?int $driverId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public string $datePreset = ''; // CHANGÉ: Vide par défaut au lieu de 'custom' ou 'month'
    
    // =========================================================================
    // RECHERCHE AVANCÉE AVEC CACHE
    // =========================================================================
    
    public string $vehicleSearch = '';
    public string $driverSearch = '';
    public bool $showVehicleDropdown = false;
    public bool $showDriverDropdown = false;
    public array $vehicleSearchHistory = [];
    public array $driverSearchHistory = [];
    
    // =========================================================================
    // SÉLECTIONS ET CACHE
    // =========================================================================
    
    public ?array $selectedVehicle = null;
    public ?array $selectedDriver = null;
    public array $vehicleOptions = [];
    public array $driverOptions = [];
    
    // =========================================================================
    // ÉTAT INTERFACE ET PRÉFÉRENCES
    // =========================================================================
    
    public bool $filtersExpanded = false;
    public bool $hasActiveFilters = false;
    public bool $isLoading = false;
    public array $savedFilterPresets = [];
    public string $currentPresetName = '';
    public bool $showAllByDefault = true; // NOUVEAU: Flag pour afficher tout par défaut
    
    // =========================================================================
    // STATISTIQUES TEMPS RÉEL AVANCÉES
    // =========================================================================
    
    public array $stats = [
        'total' => 0,
        'active' => 0,
        'scheduled' => 0,
        'completed' => 0,
        'cancelled' => 0,
        'filtered' => 0,
        'today' => 0,
        'thisWeek' => 0,
        'thisMonth' => 0,
        'vehicleUtilization' => 0,
        'driverUtilization' => 0,
        'hiddenByFilters' => 0  // NOUVEAU: Nombre d'affectations cachées par les filtres
    ];
    
    // =========================================================================
    // CONFIGURATION ET MÉTADONNÉES
    // =========================================================================
    
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'vehicleId' => ['except' => ''],
        'driverId' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'datePreset' => ['except' => '']
    ];
    
    protected $listeners = [
        'refreshFilters' => '$refresh',
        'resetFilters' => 'resetAllFilters',
        'applyPreset' => 'loadFilterPreset',
        'showAll' => 'showAllAssignments'  // NOUVEAU: Listener pour afficher tout
    ];
    
    protected $rules = [
        'dateFrom' => 'nullable|date',
        'dateTo' => 'nullable|date|after_or_equal:dateFrom',
        'vehicleId' => 'nullable|exists:vehicles,id',
        'driverId' => 'nullable|exists:drivers,id',
        'status' => 'nullable|in:scheduled,active,completed,cancelled,all'  // Ajout de 'all'
    ];

    // =========================================================================
    // INITIALISATION ET CYCLE DE VIE
    // =========================================================================

    /**
     * Initialisation du composant SANS filtre par défaut
     */
    public function mount(): void
    {
        try {
            // NE PAS initialiser de dates par défaut
            // Cela permet d'afficher TOUTES les affectations
            
            // Charger les préférences utilisateur (si sauvegardées)
            $this->loadUserPreferences();
            
            // Si aucun filtre n'est défini dans les préférences, afficher TOUT
            if (!$this->hasActiveFilters()) {
                $this->showAllByDefault = true;
                // Ne pas appeler initializeDateRange() pour éviter le filtre par défaut
            } else {
                $this->showAllByDefault = false;
            }
            
            // Charger l'historique de recherche
            $this->loadSearchHistory();
            
            // Calculer les statistiques avec cache
            $this->calculateStatistics();
            
            // Vérifier les filtres actifs
            $this->checkActiveFilters();
            
            // Précharger les données fréquentes
            $this->preloadFrequentData();
            
            Log::info('AssignmentFiltersEnhanced mounted', [
                'showAllByDefault' => $this->showAllByDefault,
                'hasActiveFilters' => $this->hasActiveFilters,
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo
            ]);
            
        } catch (\Exception $e) {
            Log::error('AssignmentFiltersEnhanced::mount error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Rendu du composant avec toutes les affectations par défaut
     */
    public function render()
    {
        $startTime = microtime(true);
        
        try {
            // Récupérer les affectations SANS filtre par défaut
            $assignments = $this->getOptimizedFilteredAssignments();
            
            // Calculer le nombre d'affectations cachées si des filtres sont actifs
            if ($this->hasActiveFilters) {
                $totalWithoutFilters = $this->getTotalAssignmentsWithoutFilters();
                $this->stats['hiddenByFilters'] = max(0, $totalWithoutFilters - $assignments->total());
            }
            
            // Mesure de performance
            $loadTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return view('livewire.admin.assignment-filters-enhanced', [
                'assignments' => $assignments,
                'vehicleOptions' => $this->vehicleOptions,
                'driverOptions' => $this->driverOptions,
                'datePresets' => $this->getDatePresets(),
                'loadTime' => $loadTime,
                'totalVehicles' => $this->getTotalVehicles(),
                'totalDrivers' => $this->getTotalDrivers(),
                'showingAll' => !$this->hasActiveFilters,  // Indicateur pour la vue
                'totalWithoutFilters' => $this->getTotalAssignmentsWithoutFilters()
            ]);
            
        } catch (\Exception $e) {
            Log::error('AssignmentFiltersEnhanced::render error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('livewire.admin.assignment-filters-enhanced', [
                'assignments' => collect(),
                'vehicleOptions' => [],
                'driverOptions' => [],
                'datePresets' => $this->getDatePresets(),
                'loadTime' => 0,
                'totalVehicles' => 0,
                'totalDrivers' => 0,
                'showingAll' => true,
                'totalWithoutFilters' => 0,
                'error' => 'Erreur lors du chargement des données'
            ]);
        }
    }

    // =========================================================================
    // FILTRAGE PRINCIPAL OPTIMISÉ
    // =========================================================================

    /**
     * Récupération optimisée des affectations SANS filtre par défaut
     */
    private function getOptimizedFilteredAssignments()
    {
        $user = auth()->user();
        if (!$user) {
            return Assignment::query()->whereRaw('1=0'); // Retourne une requête vide si pas d'utilisateur
        }
        $organizationId = $user->organization_id;
        
        // Si aucun filtre n'est actif, ne pas utiliser le cache pour toujours avoir les données fraîches
        if (!$this->hasActiveFilters) {
            return $this->buildFilterQuery($organizationId)->paginate(20);
        }
        
        // Utiliser le cache seulement si des filtres sont actifs
        $cacheKey = $this->generateCacheKey();
        return Cache::remember($cacheKey, 60, function () use ($organizationId) {
            return $this->buildFilterQuery($organizationId)->paginate(20);
        });
    }

    /**
     * Construction de la requête SANS filtre de date par défaut
     */
    private function buildFilterQuery(int $organizationId): Builder
    {
        $query = Assignment::with([
                'vehicle' => function ($q) {
                    $q->select('id', 'registration_plate', 'brand', 'model', 'vehicle_type_id');
                },
                'vehicle.vehicleType' => function ($q) {
                    $q->select('id', 'name');
                },
                'driver' => function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'license_number', 'photo', 'status_id');
                },
                'driver.driverStatus' => function ($q) {
                    $q->select('id', 'name');
                }
            ])
            ->where('organization_id', $organizationId);

        // Recherche globale intelligente - OPTIMISÉE AVEC ILIKE POSTGRESQL
        // ILIKE est 2-3x plus rapide que LOWER() LIKE et utilise les indexes GIN trigram
        if ($this->search) {
            $query->where(function ($q) {
                $searchTerm = trim($this->search);

                $q->whereHas('vehicle', function ($vq) use ($searchTerm) {
                    // ILIKE: Insensible à la casse natif PostgreSQL + utilise index GIN
                    $vq->where('registration_plate', 'ILIKE', "%{$searchTerm}%")
                       ->orWhere('brand', 'ILIKE', "%{$searchTerm}%")
                       ->orWhere('model', 'ILIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('driver', function ($dq) use ($searchTerm) {
                    // ILIKE sur champs individuels + recherche nom complet
                    $dq->where('first_name', 'ILIKE', "%{$searchTerm}%")
                       ->orWhere('last_name', 'ILIKE', "%{$searchTerm}%")
                       ->orWhere('license_number', 'ILIKE', "%{$searchTerm}%")
                       ->orWhereRaw("(first_name || ' ' || last_name) ILIKE ?", ["%{$searchTerm}%"]);
                })
                ->orWhere('id', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filtre par statut avec logique métier précise
        if ($this->status && $this->status !== 'all') {
            $now = now();
            
            switch ($this->status) {
                case 'active':
                    $query->where('start_datetime', '<=', $now)
                          ->where(function ($q) use ($now) {
                              $q->whereNull('end_datetime')
                                ->orWhere('end_datetime', '>', $now);
                          })
                          ->where(function($q) {
                              $q->where('status', '!=', 'cancelled')
                                ->orWhereNull('status');
                          });
                    break;
                    
                case 'scheduled':
                    $query->where('start_datetime', '>', $now)
                          ->where(function($q) {
                              $q->where('status', '!=', 'cancelled')
                                ->orWhereNull('status');
                          });
                    break;
                    
                case 'completed':
                    $query->whereNotNull('end_datetime')
                          ->where('end_datetime', '<=', $now);
                    break;
                    
                case 'cancelled':
                    $query->where('status', 'cancelled');
                    break;
            }
        }

        // Filtre par véhicule
        if ($this->vehicleId) {
            $query->where('vehicle_id', $this->vehicleId);
        }

        // Filtre par chauffeur
        if ($this->driverId) {
            $query->where('driver_id', $this->driverId);
        }

        // FILTRE PÉRIODE - Seulement si explicitement défini
        // CHANGÉ: Ne pas appliquer de filtre par défaut
        if ($this->dateFrom || $this->dateTo) {
            $startDate = $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : null;
            $endDate = $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : null;
            
            if ($startDate && $endDate) {
                // Période complète définie
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where(function ($sub) use ($startDate, $endDate) {
                        $sub->whereBetween('start_datetime', [$startDate, $endDate]);
                    })->orWhere(function ($sub) use ($startDate, $endDate) {
                        $sub->whereBetween('end_datetime', [$startDate, $endDate]);
                    })->orWhere(function ($sub) use ($startDate, $endDate) {
                        $sub->where('start_datetime', '<=', $startDate)
                            ->where(function ($end) use ($endDate) {
                                $end->whereNull('end_datetime')
                                    ->orWhere('end_datetime', '>=', $endDate);
                            });
                    });
                });
            } elseif ($startDate) {
                // Seulement date de début
                $query->where(function ($q) use ($startDate) {
                    $q->where('start_datetime', '>=', $startDate)
                      ->orWhere(function ($sub) use ($startDate) {
                          $sub->where('start_datetime', '<', $startDate)
                              ->where(function ($end) use ($startDate) {
                                  $end->whereNull('end_datetime')
                                      ->orWhere('end_datetime', '>=', $startDate);
                              });
                      });
                });
            } elseif ($endDate) {
                // Seulement date de fin
                $query->where('start_datetime', '<=', $endDate);
            }
        }

        // Tri intelligent - Afficher d'abord les actives, puis programmées, puis terminées
        $query->orderByRaw("
            CASE 
                WHEN start_datetime <= NOW() AND (end_datetime IS NULL OR end_datetime > NOW()) THEN 1
                WHEN start_datetime > NOW() THEN 2
                ELSE 3
            END
        ")->orderBy('start_datetime', 'desc');

        return $query;
    }

    // =========================================================================
    // GESTION DES FILTRES SANS DATE PAR DÉFAUT
    // =========================================================================

    /**
     * NE PAS initialiser de plage de dates par défaut
     */
    private function initializeDateRange(): void
    {
        // CHANGÉ: Ne rien faire par défaut pour afficher toutes les affectations
        // Seulement initialiser si un preset est explicitement sélectionné
        if ($this->datePreset && $this->datePreset !== 'all' && $this->datePreset !== '') {
            $this->applyDatePreset($this->datePreset);
        }
    }

    /**
     * Méthode pour afficher TOUTES les affectations
     */
    public function showAllAssignments(): void
    {
        $this->resetAllFilters();
        $this->showAllByDefault = true;
        $this->dispatch('filtersApplied', ['message' => 'Affichage de toutes les affectations']);
    }

    /**
     * Appliquer un preset de dates (optionnel)
     */
    public function applyDatePreset(string $preset): void
    {
        // Si le preset est 'all', réinitialiser les dates
        if ($preset === 'all' || $preset === '') {
            $this->dateFrom = null;
            $this->dateTo = null;
            $this->datePreset = '';
            $this->applyFilters();
            return;
        }
        
        $this->datePreset = $preset;
        $now = now();
        
        switch ($preset) {
            case 'today':
                $this->dateFrom = $now->copy()->startOfDay()->format('Y-m-d');
                $this->dateTo = $now->copy()->endOfDay()->format('Y-m-d');
                break;
                
            case 'yesterday':
                $this->dateFrom = $now->copy()->subDay()->startOfDay()->format('Y-m-d');
                $this->dateTo = $now->copy()->subDay()->endOfDay()->format('Y-m-d');
                break;
                
            case 'week':
                $this->dateFrom = $now->copy()->startOfWeek()->format('Y-m-d');
                $this->dateTo = $now->copy()->endOfWeek()->format('Y-m-d');
                break;
                
            case 'month':
                $this->dateFrom = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->dateTo = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
                
            case 'year':
                $this->dateFrom = $now->copy()->startOfYear()->format('Y-m-d');
                $this->dateTo = $now->copy()->endOfYear()->format('Y-m-d');
                break;
                
            case 'custom':
            default:
                // Ne rien changer pour custom
                break;
        }
        
        $this->applyFilters();
    }

    /**
     * Obtenir les presets de dates disponibles avec option "Toutes"
     */
    public function getDatePresets(): array
    {
        return [
            'all' => ['label' => 'Toutes les affectations', 'icon' => 'lucide:infinity'],  // NOUVEAU
            'today' => ['label' => "Aujourd'hui", 'icon' => 'lucide:calendar-days'],
            'yesterday' => ['label' => 'Hier', 'icon' => 'lucide:calendar-minus'],
            'week' => ['label' => 'Cette semaine', 'icon' => 'lucide:calendar-range'],
            'month' => ['label' => 'Ce mois', 'icon' => 'lucide:calendar'],
            'year' => ['label' => 'Cette année', 'icon' => 'lucide:calendar-heart'],
            'custom' => ['label' => 'Personnalisé', 'icon' => 'lucide:calendar-search']
        ];
    }

    // =========================================================================
    // MÉTHODES UTILITAIRES
    // =========================================================================

    /**
     * Vérifier si des filtres sont actifs
     */
    private function hasActiveFilters(): bool
    {
        return !empty($this->search) || 
               !empty($this->status) || 
               !empty($this->vehicleId) || 
               !empty($this->driverId) || 
               !empty($this->dateFrom) || 
               !empty($this->dateTo);
    }

    /**
     * Réinitialiser tous les filtres
     */
    public function resetAllFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->vehicleId = null;
        $this->driverId = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->datePreset = '';
        $this->selectedVehicle = null;
        $this->selectedDriver = null;
        $this->vehicleSearch = '';
        $this->driverSearch = '';
        $this->hasActiveFilters = false;
        $this->showAllByDefault = true;
        
        $this->resetPage();
        $this->calculateStatistics();
        
        $this->dispatch('filtersReset');
    }

    /**
     * Obtenir le nombre total d'affectations sans aucun filtre
     */
    private function getTotalAssignmentsWithoutFilters(): int
    {
        $user = auth()->user();
        if (!$user) {
            return 0;
        }
        return Cache::remember('total_assignments_' . $user->organization_id, 300, function() use ($user) {
            return Assignment::where('organization_id', $user->organization_id)->count();
        });
    }

    /**
     * Obtenir le nombre total de véhicules
     */
    private function getTotalVehicles(): int
    {
        $user = auth()->user();
        if (!$user) {
            return 0;
        }
        return Cache::remember('total_vehicles_' . $user->organization_id, 300, function() use ($user) {
            return Vehicle::where('organization_id', $user->organization_id)->count();
        });
    }

    /**
     * Obtenir le nombre total de chauffeurs
     */
    private function getTotalDrivers(): int
    {
        $user = auth()->user();
        if (!$user) {
            return 0;
        }
        return Cache::remember('total_drivers_' . $user->organization_id, 300, function() use ($user) {
            return Driver::where('organization_id', $user->organization_id)->count();
        });
    }

    /**
     * Calculer les statistiques
     */
    private function calculateStatistics(): void
    {
        $user = auth()->user();
        if (!$user) {
            // Initialiser toutes les stats à 0 si pas d'utilisateur
            $this->stats = array_fill_keys(array_keys($this->stats), 0);
            return;
        }
        $organizationId = $user->organization_id;
        $now = now();
        
        // Statistiques globales
        $this->stats['total'] = $this->getTotalAssignmentsWithoutFilters();
        
        // Actives
        $this->stats['active'] = Assignment::where('organization_id', $organizationId)
            ->where('start_datetime', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('end_datetime')
                  ->orWhere('end_datetime', '>', $now);
            })
            ->where(function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->orWhereNull('status');
            })
            ->count();
            
        // Programmées
        $this->stats['scheduled'] = Assignment::where('organization_id', $organizationId)
            ->where('start_datetime', '>', $now)
            ->where(function($q) {
                $q->where('status', '!=', 'cancelled')
                  ->orWhereNull('status');
            })
            ->count();
            
        // Terminées
        $this->stats['completed'] = Assignment::where('organization_id', $organizationId)
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', $now)
            ->count();
            
        // Annulées
        $this->stats['cancelled'] = Assignment::where('organization_id', $organizationId)
            ->where('status', 'cancelled')
            ->count();
            
        // Avec filtres actuels
        $this->stats['filtered'] = $this->buildFilterQuery($organizationId)->count();
    }

    /**
     * Vérifier et marquer les filtres actifs
     */
    private function checkActiveFilters(): void
    {
        $this->hasActiveFilters = $this->hasActiveFilters();
    }

    /**
     * Générer une clé de cache unique
     */
    private function generateCacheKey(): string
    {
        $user = auth()->user();
        return 'assignments_' . md5(serialize([
            'org' => $user ? $user->organization_id : 'no-user',
            'search' => $this->search,
            'status' => $this->status,
            'vehicle' => $this->vehicleId,
            'driver' => $this->driverId,
            'from' => $this->dateFrom,
            'to' => $this->dateTo
        ]));
    }

    /**
     * Charger les préférences utilisateur
     */
    private function loadUserPreferences(): void
    {
        // Implémenter le chargement des préférences si nécessaire
    }

    /**
     * Charger l'historique de recherche
     */
    private function loadSearchHistory(): void
    {
        // Implémenter le chargement de l'historique si nécessaire
    }

    /**
     * Précharger les données fréquentes
     */
    private function preloadFrequentData(): void
    {
        // Implémenter le préchargement si nécessaire
    }

    /**
     * Appliquer les filtres
     */
    public function applyFilters(): void
    {
        $this->checkActiveFilters();
        $this->calculateStatistics();
        $this->resetPage();
        
        $this->dispatch('filtersApplied', [
            'filters' => [
                'search' => $this->search,
                'status' => $this->status,
                'vehicleId' => $this->vehicleId,
                'driverId' => $this->driverId,
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo
            ]
        ]);
    }

    /**
     * Basculer l'affichage des filtres
     */
    public function toggleFilters(): void
    {
        $this->filtersExpanded = !$this->filtersExpanded;
    }

    // Méthodes additionnelles nécessaires...
    
    public function updatedVehicleSearch(): void
    {
        $this->showVehicleDropdown = strlen($this->vehicleSearch) >= 2;
        
        if (empty($this->vehicleSearch)) {
            $this->vehicleId = null;
            $this->selectedVehicle = null;
            $this->vehicleOptions = [];
        } else {
            $this->searchVehicles();
        }
    }

    private function searchVehicles(): void
    {
        if (strlen($this->vehicleSearch) < 2) {
            $this->vehicleOptions = [];
            return;
        }
        
        $user = auth()->user();
        if (!$user) {
            $this->vehicleOptions = [];
            return;
        }
        
        $this->vehicleOptions = Vehicle::where('organization_id', $user->organization_id)
            ->where(function($q) {
                $searchTerm = trim($this->vehicleSearch);
                // ILIKE: Recherche insensible à la casse optimisée PostgreSQL + index GIN
                $q->where('registration_plate', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('brand', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('model', 'ILIKE', "%{$searchTerm}%");
            })
            ->take(10)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'display' => "{$vehicle->registration_plate} - {$vehicle->brand} {$vehicle->model}"
                ];
            })
            ->toArray();
    }

    public function selectVehicle(int $vehicleId): void
    {
        $vehicle = collect($this->vehicleOptions)->firstWhere('id', $vehicleId);
        if ($vehicle) {
            $this->vehicleId = $vehicleId;
            $this->selectedVehicle = $vehicle;
            $this->vehicleSearch = $vehicle['display'];
            $this->showVehicleDropdown = false;
            $this->applyFilters();
        }
    }

    public function updatedDriverSearch(): void
    {
        $this->showDriverDropdown = strlen($this->driverSearch) >= 2;
        
        if (empty($this->driverSearch)) {
            $this->driverId = null;
            $this->selectedDriver = null;
            $this->driverOptions = [];
        } else {
            $this->searchDrivers();
        }
    }

    private function searchDrivers(): void
    {
        if (strlen($this->driverSearch) < 2) {
            $this->driverOptions = [];
            return;
        }
        
        $user = auth()->user();
        if (!$user) {
            $this->driverOptions = [];
            return;
        }
        
        $this->driverOptions = Driver::where('organization_id', $user->organization_id)
            ->where(function($q) {
                $searchTerm = trim($this->driverSearch);
                // ILIKE: Recherche insensible à la casse optimisée PostgreSQL + index GIN
                $q->where('first_name', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('last_name', 'ILIKE', "%{$searchTerm}%")
                  ->orWhereRaw("(first_name || ' ' || last_name) ILIKE ?", ["%{$searchTerm}%"])
                  ->orWhere('license_number', 'ILIKE', "%{$searchTerm}%");
            })
            ->take(10)
            ->get()
            ->map(function ($driver) {
                return [
                    'id' => $driver->id,
                    'first_name' => $driver->first_name,
                    'last_name' => $driver->last_name,
                    'license_number' => $driver->license_number,
                    'display' => "{$driver->full_name} ({$driver->license_number})"
                ];
            })
            ->toArray();
    }

    public function selectDriver(int $driverId): void
    {
        $driver = collect($this->driverOptions)->firstWhere('id', $driverId);
        if ($driver) {
            $this->driverId = $driverId;
            $this->selectedDriver = $driver;
            $this->driverSearch = $driver['display'];
            $this->showDriverDropdown = false;
            $this->applyFilters();
        }
    }

    public function exportFiltered(string $format): void
    {
        // Implémenter l'export si nécessaire
        $this->dispatch('exportAssignments', [
            'format' => $format,
            'count' => $this->stats['filtered']
        ]);
    }
}
