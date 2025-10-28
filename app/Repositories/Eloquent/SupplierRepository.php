<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $query = Supplier::query();

        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('company_name', 'ILIKE', $searchTerm)
                  ->orWhere('contact_first_name', 'ILIKE', $searchTerm)
                  ->orWhere('contact_last_name', 'ILIKE', $searchTerm)
                  ->orWhere('contact_email', 'ILIKE', $searchTerm);
            });
        }

        return $query->orderBy('company_name', 'asc')->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Supplier
    {
        // Ajouter l'organization_id automatiquement si pas présent
        if (!isset($data['organization_id'])) {
            $data['organization_id'] = auth()->user()->organization_id ?? 1;
        }

        // ===============================================
        // VALEURS PAR DÉFAUT ENTERPRISE-GRADE
        // ===============================================
        
        // Gérer les scores avec valeurs par défaut intelligentes
        $data['quality_score'] = $data['quality_score'] ?? 75.00;
        $data['reliability_score'] = $data['reliability_score'] ?? 75.00;
        $data['rating'] = $data['rating'] ?? 3.75;
        
        // Initialiser les métriques de performance
        $data['total_orders'] = $data['total_orders'] ?? 0;
        $data['completed_orders'] = $data['completed_orders'] ?? 0;
        $data['on_time_deliveries'] = $data['on_time_deliveries'] ?? 0;
        $data['customer_complaints'] = $data['customer_complaints'] ?? 0;
        $data['auto_score_enabled'] = $data['auto_score_enabled'] ?? true;
        
        // Gérer le temps de réponse par défaut selon le type
        if (!isset($data['response_time_hours'])) {
            $data['response_time_hours'] = match($data['supplier_type'] ?? 'autre') {
                'mecanicien' => 24,        // 24h pour mécanicien
                'assureur' => 48,          // 48h pour assureur
                'station_service' => 1,     // 1h pour station service
                'controle_technique' => 72, // 72h pour contrôle technique
                default => 24               // 24h par défaut
            };
        }
        
        // Gérer les checkboxes
        $data['is_active'] = isset($data['is_active']) ? true : false;
        $data['is_preferred'] = isset($data['is_preferred']) ? true : false;
        $data['is_certified'] = isset($data['is_certified']) ? true : false;
        $data['blacklisted'] = isset($data['blacklisted']) ? true : false;
        
        // Gérer les valeurs numériques
        $data['credit_limit'] = $data['credit_limit'] ?? 0.00;
        $data['payment_terms'] = $data['payment_terms'] ?? 30;
        
        // Gérer les tableaux JSON
        $data['specialties'] = isset($data['specialties']) && is_array($data['specialties']) 
            ? $data['specialties'] 
            : [];
            
        $data['certifications'] = isset($data['certifications']) && is_array($data['certifications']) 
            ? $data['certifications'] 
            : [];
            
        $data['service_areas'] = isset($data['service_areas']) && is_array($data['service_areas']) 
            ? $data['service_areas'] 
            : [];
            
        $data['documents'] = isset($data['documents']) && is_array($data['documents']) 
            ? $data['documents'] 
            : [];

        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): bool
    {
        // Gérer les checkboxes
        $data['is_active'] = isset($data['is_active']) ? true : false;
        $data['is_preferred'] = isset($data['is_preferred']) ? true : false;
        $data['is_certified'] = isset($data['is_certified']) ? true : false;

        return $supplier->update($data);
    }

    public function archive(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}