<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\RepairRequest;
use App\Models\VehicleExpense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ====================================================================
 * 🎯 SUPPLIER SCORING SERVICE - ENTERPRISE ULTRA-PRO
 * ====================================================================
 * 
 * Service de scoring intelligent pour évaluation automatique des
 * fournisseurs basé sur des métriques de performance réelles.
 * 
 * MÉTRIQUES ANALYSÉES:
 * ✅ Qualité de service (taux de complétion, réclamations)
 * ✅ Fiabilité (ponctualité, temps de réponse)
 * ✅ Performance financière (coûts, respect des devis)
 * ✅ Satisfaction client (évaluations, feedback)
 * ✅ Conformité (certifications, documents à jour)
 * 
 * ALGORITHME DE SCORING:
 * - Quality Score: 0-100 (qualité du travail)
 * - Reliability Score: 0-100 (fiabilité et ponctualité)
 * - Rating: 0-5 étoiles (note globale)
 * 
 * @package App\Services
 * @version 1.0.0-Enterprise
 * @since 2025-10-28
 * ====================================================================
 */
class SupplierScoringService
{
    /**
     * Poids des différents facteurs dans le calcul
     */
    private const WEIGHTS = [
        'quality' => [
            'completion_rate' => 0.30,      // 30% - Taux de complétion
            'complaint_rate' => 0.25,       // 25% - Absence de réclamations
            'rework_rate' => 0.20,          // 20% - Absence de retravail
            'certification' => 0.15,        // 15% - Certifications
            'documentation' => 0.10,        // 10% - Documentation complète
        ],
        'reliability' => [
            'punctuality' => 0.35,          // 35% - Ponctualité
            'response_time' => 0.25,        // 25% - Temps de réponse
            'availability' => 0.20,         // 20% - Disponibilité
            'communication' => 0.10,        // 10% - Communication
            'flexibility' => 0.10,          // 10% - Flexibilité
        ],
        'overall' => [
            'quality' => 0.40,              // 40% - Score qualité
            'reliability' => 0.35,          // 35% - Score fiabilité
            'cost_efficiency' => 0.15,      // 15% - Efficacité coût
            'customer_satisfaction' => 0.10, // 10% - Satisfaction client
        ]
    ];

    /**
     * Calculer et mettre à jour tous les scores d'un fournisseur
     * 
     * @param Supplier $supplier
     * @return array Scores calculés
     */
    public function calculateScores(Supplier $supplier): array
    {
        // Ne calculer que si auto_score_enabled est activé
        if (!$supplier->auto_score_enabled) {
            return [
                'quality_score' => $supplier->quality_score,
                'reliability_score' => $supplier->reliability_score,
                'rating' => $supplier->rating,
                'details' => ['message' => 'Scoring automatique désactivé']
            ];
        }

        // Collecter les métriques
        $metrics = $this->collectMetrics($supplier);
        
        // Calculer les scores
        $qualityScore = $this->calculateQualityScore($metrics);
        $reliabilityScore = $this->calculateReliabilityScore($metrics);
        $overallRating = $this->calculateOverallRating($qualityScore, $reliabilityScore, $metrics);
        
        // Mettre à jour le fournisseur
        $supplier->update([
            'quality_score' => round($qualityScore, 2),
            'reliability_score' => round($reliabilityScore, 2),
            'rating' => round($overallRating, 2),
            'last_evaluation_date' => now(),
            'total_orders' => $metrics['total_orders'],
            'completed_orders' => $metrics['completed_orders'],
            'on_time_deliveries' => $metrics['on_time_deliveries'],
            'customer_complaints' => $metrics['complaints_count'],
            'avg_response_time_hours' => $metrics['avg_response_time'],
        ]);

        return [
            'quality_score' => round($qualityScore, 2),
            'reliability_score' => round($reliabilityScore, 2),
            'rating' => round($overallRating, 2),
            'details' => $this->generateScoreDetails($metrics, $qualityScore, $reliabilityScore)
        ];
    }

    /**
     * Collecter toutes les métriques du fournisseur
     * 
     * @param Supplier $supplier
     * @return array
     */
    private function collectMetrics(Supplier $supplier): array
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        
        // Métriques des demandes de réparation
        $repairStats = DB::table('repair_requests')
            ->where('assigned_supplier_id', $supplier->id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw('
                COUNT(*) as total_repairs,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_repairs,
                SUM(CASE WHEN completed_at <= estimated_completion_date THEN 1 ELSE 0 END) as on_time_repairs,
                AVG(EXTRACT(EPOCH FROM (first_response_at - created_at))/3600) as avg_response_hours,
                SUM(CASE WHEN requires_rework = true THEN 1 ELSE 0 END) as rework_count
            ', ['completed'])
            ->first();

        // Métriques des dépenses
        $expenseStats = DB::table('vehicle_expenses')
            ->where('supplier_id', $supplier->id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw('
                COUNT(*) as total_expenses,
                AVG(CASE 
                    WHEN estimated_amount > 0 
                    THEN ((total_ttc - estimated_amount) / estimated_amount * 100)
                    ELSE 0 
                END) as avg_cost_variance
            ')
            ->first();

        // Métriques des évaluations
        $ratingStats = DB::table('supplier_ratings')
            ->where('supplier_id', $supplier->id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw('
                COUNT(*) as total_ratings,
                AVG(overall_rating) as avg_rating,
                AVG(quality_rating) as avg_quality,
                AVG(timeliness_rating) as avg_timeliness,
                AVG(communication_rating) as avg_communication,
                AVG(price_rating) as avg_price
            ')
            ->first();

        // Compter les réclamations
        $complaintsCount = DB::table('supplier_complaints')
            ->where('supplier_id', $supplier->id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->count();

        return [
            // Ordres et complétions
            'total_orders' => ($repairStats->total_repairs ?? 0) + ($expenseStats->total_expenses ?? 0),
            'completed_orders' => $repairStats->completed_repairs ?? 0,
            'on_time_deliveries' => $repairStats->on_time_repairs ?? 0,
            
            // Temps et réactivité
            'avg_response_time' => $repairStats->avg_response_hours ?? 24,
            'response_time_hours' => $supplier->response_time_hours ?? 24,
            
            // Qualité
            'rework_count' => $repairStats->rework_count ?? 0,
            'complaints_count' => $complaintsCount,
            
            // Coûts
            'avg_cost_variance' => $expenseStats->avg_cost_variance ?? 0,
            
            // Évaluations
            'avg_rating' => $ratingStats->avg_rating ?? 3.75,
            'avg_quality' => $ratingStats->avg_quality ?? 75,
            'avg_timeliness' => $ratingStats->avg_timeliness ?? 75,
            'avg_communication' => $ratingStats->avg_communication ?? 75,
            'avg_price' => $ratingStats->avg_price ?? 75,
            'total_ratings' => $ratingStats->total_ratings ?? 0,
            
            // Certifications et conformité
            'is_certified' => $supplier->is_certified,
            'has_valid_documents' => $this->hasValidDocuments($supplier),
            'certifications_count' => count($supplier->certifications ?? []),
        ];
    }

    /**
     * Calculer le score de qualité (0-100)
     * 
     * @param array $metrics
     * @return float
     */
    private function calculateQualityScore(array $metrics): float
    {
        $scores = [];
        
        // Taux de complétion (30%)
        if ($metrics['total_orders'] > 0) {
            $completionRate = ($metrics['completed_orders'] / $metrics['total_orders']) * 100;
        } else {
            $completionRate = 75; // Valeur par défaut
        }
        $scores['completion_rate'] = min(100, $completionRate);
        
        // Absence de réclamations (25%)
        if ($metrics['total_orders'] > 0) {
            $complaintRate = 100 - min(100, ($metrics['complaints_count'] / $metrics['total_orders']) * 100);
        } else {
            $complaintRate = 95; // Peu de plaintes par défaut
        }
        $scores['complaint_rate'] = $complaintRate;
        
        // Absence de retravail (20%)
        if ($metrics['completed_orders'] > 0) {
            $reworkRate = 100 - min(100, ($metrics['rework_count'] / $metrics['completed_orders']) * 100);
        } else {
            $reworkRate = 90; // Peu de retravail par défaut
        }
        $scores['rework_rate'] = $reworkRate;
        
        // Certifications (15%)
        $certificationScore = $metrics['is_certified'] ? 100 : 50;
        $certificationScore = min(100, $certificationScore + ($metrics['certifications_count'] * 10));
        $scores['certification'] = $certificationScore;
        
        // Documentation (10%)
        $documentationScore = $metrics['has_valid_documents'] ? 100 : 50;
        $scores['documentation'] = $documentationScore;
        
        // Calculer le score pondéré
        $qualityScore = 0;
        foreach (self::WEIGHTS['quality'] as $factor => $weight) {
            $qualityScore += ($scores[$factor] ?? 75) * $weight;
        }
        
        return min(100, max(0, $qualityScore));
    }

    /**
     * Calculer le score de fiabilité (0-100)
     * 
     * @param array $metrics
     * @return float
     */
    private function calculateReliabilityScore(array $metrics): float
    {
        $scores = [];
        
        // Ponctualité (35%)
        if ($metrics['completed_orders'] > 0) {
            $punctualityRate = ($metrics['on_time_deliveries'] / $metrics['completed_orders']) * 100;
        } else {
            $punctualityRate = 75; // Valeur par défaut
        }
        $scores['punctuality'] = min(100, $punctualityRate);
        
        // Temps de réponse (25%)
        // Score max si réponse < 1h, diminue progressivement
        $responseScore = max(0, 100 - ($metrics['avg_response_time'] * 2));
        $scores['response_time'] = $responseScore;
        
        // Disponibilité (20%)
        // Basé sur le ratio réponse/temps promis
        if ($metrics['response_time_hours'] > 0) {
            $availabilityScore = min(100, ($metrics['response_time_hours'] / $metrics['avg_response_time']) * 100);
        } else {
            $availabilityScore = 75;
        }
        $scores['availability'] = $availabilityScore;
        
        // Communication (10%)
        $scores['communication'] = $metrics['avg_communication'] ?? 75;
        
        // Flexibilité (10%)
        // Basé sur la variance des coûts (moins de variance = plus flexible)
        $flexibilityScore = max(0, 100 - abs($metrics['avg_cost_variance']));
        $scores['flexibility'] = $flexibilityScore;
        
        // Calculer le score pondéré
        $reliabilityScore = 0;
        foreach (self::WEIGHTS['reliability'] as $factor => $weight) {
            $reliabilityScore += ($scores[$factor] ?? 75) * $weight;
        }
        
        return min(100, max(0, $reliabilityScore));
    }

    /**
     * Calculer la note globale (0-5 étoiles)
     * 
     * @param float $qualityScore
     * @param float $reliabilityScore
     * @param array $metrics
     * @return float
     */
    private function calculateOverallRating(float $qualityScore, float $reliabilityScore, array $metrics): float
    {
        // Efficacité coût (15%)
        $costEfficiency = max(0, 100 - abs($metrics['avg_cost_variance']));
        
        // Satisfaction client (10%)
        $customerSatisfaction = ($metrics['avg_rating'] ?? 3.75) * 20; // Convertir 0-5 en 0-100
        
        // Calculer le score global pondéré
        $overallScore = 
            ($qualityScore * self::WEIGHTS['overall']['quality']) +
            ($reliabilityScore * self::WEIGHTS['overall']['reliability']) +
            ($costEfficiency * self::WEIGHTS['overall']['cost_efficiency']) +
            ($customerSatisfaction * self::WEIGHTS['overall']['customer_satisfaction']);
        
        // Convertir en échelle 0-5
        $rating = ($overallScore / 100) * 5;
        
        return min(5, max(0, $rating));
    }

    /**
     * Vérifier si le fournisseur a des documents valides
     * 
     * @param Supplier $supplier
     * @return bool
     */
    private function hasValidDocuments(Supplier $supplier): bool
    {
        $requiredDocuments = ['rc', 'nif', 'rib']; // Documents requis
        
        // Vérifier les champs de base
        if (empty($supplier->trade_register) || empty($supplier->nif)) {
            return false;
        }
        
        // Vérifier les documents uploadés si applicable
        $documents = $supplier->documents ?? [];
        if (count($documents) < 1) {
            return false;
        }
        
        return true;
    }

    /**
     * Générer les détails du score pour affichage
     * 
     * @param array $metrics
     * @param float $qualityScore
     * @param float $reliabilityScore
     * @return array
     */
    private function generateScoreDetails(array $metrics, float $qualityScore, float $reliabilityScore): array
    {
        return [
            'metrics' => [
                'total_orders' => $metrics['total_orders'],
                'completion_rate' => $metrics['total_orders'] > 0 
                    ? round(($metrics['completed_orders'] / $metrics['total_orders']) * 100, 1) 
                    : 0,
                'punctuality_rate' => $metrics['completed_orders'] > 0
                    ? round(($metrics['on_time_deliveries'] / $metrics['completed_orders']) * 100, 1)
                    : 0,
                'avg_response_time' => round($metrics['avg_response_time'], 1) . ' heures',
                'complaints' => $metrics['complaints_count'],
                'rework_needed' => $metrics['rework_count'],
            ],
            'scores' => [
                'quality' => round($qualityScore, 1),
                'reliability' => round($reliabilityScore, 1),
                'cost_efficiency' => round(max(0, 100 - abs($metrics['avg_cost_variance'])), 1),
                'customer_satisfaction' => round($metrics['avg_rating'] * 20, 1),
            ],
            'recommendations' => $this->generateRecommendations($qualityScore, $reliabilityScore, $metrics)
        ];
    }

    /**
     * Générer des recommandations d'amélioration
     * 
     * @param float $qualityScore
     * @param float $reliabilityScore
     * @param array $metrics
     * @return array
     */
    private function generateRecommendations(float $qualityScore, float $reliabilityScore, array $metrics): array
    {
        $recommendations = [];
        
        if ($qualityScore < 60) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'Score de qualité faible. Envisagez une formation ou un audit qualité.'
            ];
        }
        
        if ($reliabilityScore < 60) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'Score de fiabilité faible. Améliorez les délais de réponse et la ponctualité.'
            ];
        }
        
        if ($metrics['avg_response_time'] > 48) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'Temps de réponse élevé. Considérez l\'amélioration de la réactivité.'
            ];
        }
        
        if ($metrics['complaints_count'] > 3) {
            $recommendations[] = [
                'type' => 'danger',
                'message' => 'Nombre élevé de réclamations. Une revue de la qualité de service est nécessaire.'
            ];
        }
        
        if (!$metrics['is_certified']) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'Encouragez le fournisseur à obtenir des certifications pour améliorer son score.'
            ];
        }
        
        if ($qualityScore >= 90 && $reliabilityScore >= 90) {
            $recommendations[] = [
                'type' => 'success',
                'message' => 'Excellent fournisseur! Considérez le statut privilégié.'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Recalculer les scores de tous les fournisseurs actifs
     * 
     * @param int $organizationId
     * @return array
     */
    public function recalculateAllScores(int $organizationId): array
    {
        $suppliers = Supplier::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->where('auto_score_enabled', true)
            ->get();
        
        $results = [
            'updated' => 0,
            'failed' => 0,
            'details' => []
        ];
        
        foreach ($suppliers as $supplier) {
            try {
                $scores = $this->calculateScores($supplier);
                $results['updated']++;
                $results['details'][] = [
                    'supplier' => $supplier->company_name,
                    'scores' => $scores
                ];
            } catch (\Exception $e) {
                $results['failed']++;
                \Log::error("Erreur calcul score fournisseur {$supplier->id}: " . $e->getMessage());
            }
        }
        
        return $results;
    }
}
