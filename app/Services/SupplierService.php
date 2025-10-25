<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 🏢 SERVICE FOURNISSEURS - ENTERPRISE GRADE
 * 
 * Service Layer pour la gestion des fournisseurs
 * Pattern: Service Layer + Repository + Caching
 * 
 * @version 2.0 Enterprise
 * @author ZenFleet Architecture Team
 */
class SupplierService
{
    protected SupplierRepositoryInterface $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Récupère les fournisseurs pour la page d'index via le repository.
     */
    public function getFilteredSuppliers(array $filters): LengthAwarePaginator
    {
        return $this->supplierRepository->getFiltered($filters);
    }

    /**
     * Récupère les données nécessaires pour les formulaires de création/modification.
     */
    /**
     * Récupère les données nécessaires pour les formulaires de création/modification.
     */
    public function getDataForCreateForm(): array
    {
        // CORRECTION : On retourne un simple tableau avec les données.
        return [
            'categories' => SupplierCategory::orderBy('name')->get(),
        ];
    }

    /**
     * Gère la création d'un fournisseur.
     */
    public function createSupplier(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    /**
     * Gère la mise à jour d'un fournisseur.
     */
    public function updateSupplier(Supplier $supplier, array $data): bool
    {
        return $this->supplierRepository->update($supplier, $data);
    }

    /**
     * Gère l'archivage d'un fournisseur.
     */
    public function archiveSupplier(Supplier $supplier): bool
    {
        return $this->supplierRepository->archive($supplier);
    }

    /**
     * ═══════════════════════════════════════════════════════════════
     * ANALYTICS & MÉTRIQUES - ENTERPRISE GRADE
     * ═══════════════════════════════════════════════════════════════
     */

    /**
     * Récupère les analytics complètes des fournisseurs
     * 
     * @param array $filters Filtres optionnels
     * @return array Analytics avec KPIs
     */
    public function getAnalytics(array $filters = []): array
    {
        $cacheKey = 'suppliers_analytics_' . auth()->id() . '_' . md5(json_encode($filters));
        
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $query = Supplier::query();

            // Total fournisseurs
            $total = $query->count();
            
            // Actifs vs Inactifs
            $active = Supplier::where('is_active', true)->count();
            $inactive = $total - $active;
            
            // Préférés et Certifiés
            $preferred = Supplier::where('is_preferred', true)->count();
            $certified = Supplier::where('is_certified', true)->count();
            $blacklisted = Supplier::where('blacklisted', true)->count();
            
            // Ratings et Scores
            $avgRating = Supplier::avg('rating') ?? 0;
            $avgQuality = Supplier::avg('quality_score') ?? 0;
            $avgReliability = Supplier::avg('reliability_score') ?? 0;
            
            // Distribution par type
            $byType = Supplier::select('supplier_type', DB::raw('count(*) as count'))
                ->groupBy('supplier_type')
                ->get()
                ->pluck('count', 'supplier_type')
                ->toArray();
            
            // Distribution géographique (Top 5 wilayas)
            $byWilaya = Supplier::select('wilaya', DB::raw('count(*) as count'))
                ->whereNotNull('wilaya')
                ->groupBy('wilaya')
                ->orderByDesc('count')
                ->limit(5)
                ->get();
            
            // Top performers
            $topRated = Supplier::where('is_active', true)
                ->whereNotNull('rating')
                ->orderByDesc('rating')
                ->limit(5)
                ->get();
            
            $topQuality = Supplier::where('is_active', true)
                ->whereNotNull('quality_score')
                ->orderByDesc('quality_score')
                ->limit(5)
                ->get();
            
            return [
                // Métriques principales
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'preferred' => $preferred,
                'certified' => $certified,
                'blacklisted' => $blacklisted,
                
                // Scores moyens
                'avg_rating' => round($avgRating, 2),
                'avg_quality' => round($avgQuality, 2),
                'avg_reliability' => round($avgReliability, 2),
                
                // Distributions
                'by_type' => $byType,
                'by_wilaya' => $byWilaya,
                
                // Top performers
                'top_rated' => $topRated,
                'top_quality' => $topQuality,
                
                // Calculés
                'active_percent' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
                'preferred_percent' => $total > 0 ? round(($preferred / $total) * 100, 1) : 0,
                'certified_percent' => $total > 0 ? round(($certified / $total) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Récupère les fournisseurs avec filtres avancés
     * 
     * @param array $filters Filtres multiples
     * @param int $perPage Nombre par page
     * @return LengthAwarePaginator
     */
    public function getFilteredSuppliersAdvanced(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Supplier::with(['category']);

        // Recherche textuelle
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'ilike', "%{$search}%")
                  ->orWhere('contact_first_name', 'ilike', "%{$search}%")
                  ->orWhere('contact_last_name', 'ilike', "%{$search}%")
                  ->orWhere('contact_email', 'ilike', "%{$search}%")
                  ->orWhere('contact_phone', 'ilike', "%{$search}%");
            });
        }

        // Filtres avancés
        if (!empty($filters['supplier_type'])) {
            $query->where('supplier_type', $filters['supplier_type']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('supplier_category_id', $filters['category_id']);
        }

        if (!empty($filters['wilaya'])) {
            $query->where('wilaya', $filters['wilaya']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === '1');
        }

        if (isset($filters['is_preferred'])) {
            $query->where('is_preferred', $filters['is_preferred'] === '1');
        }

        if (isset($filters['is_certified'])) {
            $query->where('is_certified', $filters['is_certified'] === '1');
        }

        if (!empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        // Tri
        $sortField = $filters['sort_by'] ?? 'company_name';
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage)->appends($filters);
    }
}