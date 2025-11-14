<?php

namespace App\Observers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Log;

/**
 * 👁️ OBSERVER ELOQUENT - ASSIGNMENT LIFECYCLE
 *
 * SYSTÈME ENTERPRISE-GRADE ULTRA-PRO SURPASSANT FLEETIO/SAMSARA
 *
 * Cet Observer orchestre automatiquement le cycle de vie des affectations
 * en interceptant les événements Eloquent pour garantir la cohérence des données.
 *
 * FONCTIONNALITÉS AVANCÉES :
 * ✅ Auto-persistance du statut calculé en DB
 * ✅ Détection automatique des transitions de statut
 * ✅ Validation des règles métier avant sauvegarde
 * ✅ Audit trail complet avec logs structurés
 * ✅ Prévention des incohérences statut/dates
 * ✅ Synchronisation automatique avec véhicules/chauffeurs
 *
 * CORRECTIONS PAR RAPPORT À L'ANCIENNE VERSION :
 * - Le statut n'était calculé que dynamiquement via l'accessor
 * - Les affectations expirées gardaient status='active' en DB
 * - Aucune détection des zombies (ended_at NULL avec end_datetime passée)
 *
 * AVEC CET OBSERVER :
 * ✅ Le statut est toujours persiste en DB (source of truth)
 * ✅ Détection automatique des incohérences
 * ✅ Correction auto-healing lors de la récupération
 * ✅ Logs pour monitoring et alertes
 *
 * @package App\Observers
 * @version 2.0.0-Enterprise
 * @since 2025-11-12
 */
class AssignmentObserver
{
    /**
     * Événement déclenché lors de la récupération d'une affectation
     *
     * STRATÉGIE ULTRA-PRO :
     * - Vérifie si le statut en DB correspond au statut calculé
     * - Si différence détectée = ZOMBIE → Auto-correction silencieuse
     * - Log pour monitoring sans bloquer l'application
     *
     * @param Assignment $assignment
     * @return void
     */
    public function retrieved(Assignment $assignment): void
    {
        // Calculer le statut réel basé sur les dates
        $calculatedStatus = $this->calculateActualStatus($assignment);

        // Récupérer le statut stocké en DB (sans passer par l'accessor)
        $storedStatus = $assignment->getAttributes()['status'] ?? null;

        // Détection d'incohérence = ZOMBIE
        if ($storedStatus !== $calculatedStatus) {
            Log::warning('[AssignmentObserver] 🧟 ZOMBIE DÉTECTÉ - Incohérence de statut', [
                'assignment_id' => $assignment->id,
                'stored_status' => $storedStatus,
                'calculated_status' => $calculatedStatus,
                'start_datetime' => $assignment->start_datetime?->toIso8601String(),
                'end_datetime' => $assignment->end_datetime?->toIso8601String(),
                'ended_at' => $assignment->ended_at?->toIso8601String(),
            ]);

            // Auto-healing : Correction immédiate sans passer par les événements
            // (pour éviter les boucles infinies)
            \DB::table('assignments')
                ->where('id', $assignment->id)
                ->update([
                    'status' => $calculatedStatus,
                    'updated_at' => now()
                ]);

            // Rafraîchir l'instance en mémoire
            $assignment->setRawAttributes(
                array_merge($assignment->getAttributes(), ['status' => $calculatedStatus]),
                true
            );
        }
    }

    /**
     * Événement déclenché avant la sauvegarde (création ou mise à jour)
     *
     * STRATÉGIE ULTRA-PRO :
     * - Recalcule et force le statut correct avant écriture en DB
     * - Garantit que la DB est toujours la source of truth
     * - Empêche les incohérences à la source
     *
     * @param Assignment $assignment
     * @return void
     */
    public function saving(Assignment $assignment): void
    {
        // Calculer le statut réel
        $correctStatus = $this->calculateActualStatus($assignment);

        // Forcer le statut correct (sauf si explicitement cancelled par l'utilisateur)
        if ($assignment->status !== Assignment::STATUS_CANCELLED) {
            $assignment->status = $correctStatus;
        }

        // Auto-complétion de ended_at si l'affectation est terminée
        if ($correctStatus === Assignment::STATUS_COMPLETED && $assignment->ended_at === null) {
            $assignment->ended_at = $assignment->end_datetime ?? now();

            Log::info('[AssignmentObserver] ✅ Auto-complétion de ended_at', [
                'assignment_id' => $assignment->id,
                'ended_at' => $assignment->ended_at->toIso8601String()
            ]);
        }

        // Validation des règles métier
        $this->validateBusinessRules($assignment);
    }

    /**
     * Événement déclenché après la création
     *
     * CORRECTION CRITIQUE V3 : Synchronisation des ressources lors de la création
     * - Si l'affectation est créée déjà terminée (dates passées), libérer les ressources
     * - Si l'affectation est active ou planifiée, verrouiller les ressources
     *
     * @param Assignment $assignment
     * @return void
     */
    public function created(Assignment $assignment): void
    {
        Log::info('[AssignmentObserver] 🆕 Nouvelle affectation créée', [
            'assignment_id' => $assignment->id,
            'vehicle_id' => $assignment->vehicle_id,
            'driver_id' => $assignment->driver_id,
            'status' => $assignment->status,
            'start_datetime' => $assignment->start_datetime->toIso8601String(),
            'end_datetime' => $assignment->end_datetime?->toIso8601String(),
        ]);
        
        // ✅ CORRECTION CRITIQUE: Synchroniser les ressources selon le statut initial
        switch ($assignment->status) {
            case Assignment::STATUS_COMPLETED:
                // Affectation créée déjà terminée (dates passées)
                $this->releaseResourcesIfNoOtherActiveAssignment($assignment);
                Log::info('[AssignmentObserver] 📦 Ressources auto-libérées (affectation historique)', [
                    'assignment_id' => $assignment->id,
                    'vehicle_id' => $assignment->vehicle_id,
                    'driver_id' => $assignment->driver_id
                ]);
                break;
                
            case Assignment::STATUS_ACTIVE:
            case Assignment::STATUS_SCHEDULED:
                // Affectation active ou planifiée - verrouiller les ressources
                $this->lockResources($assignment);
                Log::info('[AssignmentObserver] 🔒 Ressources verrouillées pour affectation ' . $assignment->status, [
                    'assignment_id' => $assignment->id,
                    'vehicle_id' => $assignment->vehicle_id,
                    'driver_id' => $assignment->driver_id
                ]);
                break;
                
            case Assignment::STATUS_CANCELLED:
                // Rien à faire pour une affectation annulée dès la création
                Log::info('[AssignmentObserver] ⚠️ Affectation créée avec statut annulé', [
                    'assignment_id' => $assignment->id
                ]);
                break;
                
            default:
                Log::warning('[AssignmentObserver] ⚠️ Statut inconnu lors de la création', [
                    'assignment_id' => $assignment->id,
                    'status' => $assignment->status
                ]);
        }
    }

    /**
     * Événement déclenché après la mise à jour
     *
     * STRATÉGIE ULTRA-PRO :
     * - Détecte les transitions de statut importantes
     * - Log pour audit trail et monitoring
     * - Déclenche des alertes si nécessaire
     *
     * @param Assignment $assignment
     * @return void
     */
    public function updated(Assignment $assignment): void
    {
        // Détecter les transitions de statut
        if ($assignment->wasChanged('status')) {
            $oldStatus = $assignment->getOriginal('status');
            $newStatus = $assignment->status;

            Log::info('[AssignmentObserver] 🔄 Transition de statut détectée', [
                'assignment_id' => $assignment->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => auth()->id(),
            ]);

            // SYNCHRONISATION AUTOMATIQUE DES RESSOURCES ENTERPRISE-GRADE
            $this->syncResourcesBasedOnStatus($assignment, $oldStatus, $newStatus);
        }
    }

    /**
     * Synchronise automatiquement les ressources selon le statut
     *
     * @param Assignment $assignment
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    private function syncResourcesBasedOnStatus(Assignment $assignment, string $oldStatus, string $newStatus): void
    {
        // Si passage à 'completed' ou 'cancelled', libérer les ressources
        if (in_array($newStatus, [Assignment::STATUS_COMPLETED, Assignment::STATUS_CANCELLED])) {
            $this->releaseResourcesIfNoOtherActiveAssignment($assignment);
        }

        // Si passage à 'active' ou 'scheduled', verrouiller les ressources
        if (in_array($newStatus, [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED]) &&
            !in_array($oldStatus, [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])) {
            $this->lockResources($assignment);
        }
    }

    /**
     * Libère les ressources si aucune autre affectation active
     *
     * @param Assignment $assignment
     * @return void
     */
    private function releaseResourcesIfNoOtherActiveAssignment(Assignment $assignment): void
    {
        // Vérifier le véhicule
        $hasOtherVehicleAssignment = Assignment::where('vehicle_id', $assignment->vehicle_id)
            ->where('id', '!=', $assignment->id)
            ->whereNull('deleted_at')
            ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
            ->exists();

        if (!$hasOtherVehicleAssignment && $assignment->vehicle) {
            $assignment->vehicle->update([
                'is_available' => true,
                'current_driver_id' => null,
                'assignment_status' => 'available',
                'last_assignment_end' => now()
            ]);

            // ✅ SYNCHRONISATION AUTOMATIQUE via le service
            app(\App\Services\ResourceStatusSynchronizer::class)->syncVehicleStatus($assignment->vehicle->fresh());

            Log::info('[AssignmentObserver] ✅ Véhicule libéré automatiquement avec synchronisation complète', [
                'vehicle_id' => $assignment->vehicle_id,
                'assignment_id' => $assignment->id
            ]);
        }

        // Vérifier le chauffeur
        $hasOtherDriverAssignment = Assignment::where('driver_id', $assignment->driver_id)
            ->where('id', '!=', $assignment->id)
            ->whereNull('deleted_at')
            ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
            ->exists();

        if (!$hasOtherDriverAssignment && $assignment->driver) {
            $assignment->driver->update([
                'is_available' => true,
                'current_vehicle_id' => null,
                'assignment_status' => 'available',
                'last_assignment_end' => now()
            ]);

            // ✅ SYNCHRONISATION AUTOMATIQUE via le service
            app(\App\Services\ResourceStatusSynchronizer::class)->syncDriverStatus($assignment->driver->fresh());

            Log::info('[AssignmentObserver] ✅ Chauffeur libéré automatiquement avec synchronisation complète', [
                'driver_id' => $assignment->driver_id,
                'assignment_id' => $assignment->id
            ]);
        }
    }

    /**
     * Verrouille les ressources pour une affectation active
     *
     * @param Assignment $assignment
     * @return void
     */
    private function lockResources(Assignment $assignment): void
    {
        if ($assignment->vehicle) {
            $assignment->vehicle->update([
                'is_available' => false,
                'current_driver_id' => $assignment->driver_id,
                'assignment_status' => 'assigned'
            ]);

            // ✅ SYNCHRONISATION AUTOMATIQUE via le service
            app(\App\Services\ResourceStatusSynchronizer::class)->syncVehicleStatus($assignment->vehicle->fresh());

            Log::info('[AssignmentObserver] 🔒 Véhicule verrouillé automatiquement avec synchronisation', [
                'vehicle_id' => $assignment->vehicle_id,
                'assignment_id' => $assignment->id
            ]);
        }

        if ($assignment->driver) {
            $assignment->driver->update([
                'is_available' => false,
                'current_vehicle_id' => $assignment->vehicle_id,
                'assignment_status' => 'assigned'
            ]);

            // ✅ SYNCHRONISATION AUTOMATIQUE via le service
            app(\App\Services\ResourceStatusSynchronizer::class)->syncDriverStatus($assignment->driver->fresh());

            Log::info('[AssignmentObserver] 🔒 Chauffeur verrouillé automatiquement avec synchronisation', [
                'driver_id' => $assignment->driver_id,
                'assignment_id' => $assignment->id
            ]);
        }
    }

    /**
     * Calcule le statut réel d'une affectation basé sur les dates
     *
     * LOGIQUE ENTERPRISE-GRADE IDENTIQUE AU MODÈLE :
     * - Annulée = reste annulée
     * - Start > now = scheduled
     * - Start <= now ET (end NULL OU end > now) = active
     * - End <= now = completed
     *
     * @param Assignment $assignment
     * @return string
     */
    private function calculateActualStatus(Assignment $assignment): string
    {
        // Récupérer le statut brut de la DB
        $rawStatus = $assignment->getAttributes()['status'] ?? null;

        // Si annulée, conserver cet état
        if ($rawStatus === Assignment::STATUS_CANCELLED) {
            return Assignment::STATUS_CANCELLED;
        }

        $now = now();
        $start = $assignment->start_datetime;
        $end = $assignment->end_datetime;

        // Programmée (pas encore commencée)
        if ($start && $start > $now) {
            return Assignment::STATUS_SCHEDULED;
        }

        // Active (commencée mais pas encore terminée)
        if ($end === null || $end > $now) {
            return Assignment::STATUS_ACTIVE;
        }

        // Terminée
        return Assignment::STATUS_COMPLETED;
    }

    /**
     * Valide les règles métier enterprise-grade
     *
     * @param Assignment $assignment
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateBusinessRules(Assignment $assignment): void
    {
        // Règle 1 : Date de fin après date de début
        if ($assignment->end_datetime && $assignment->start_datetime >= $assignment->end_datetime) {
            throw new \InvalidArgumentException(
                "La date de fin ({$assignment->end_datetime}) doit être postérieure " .
                "à la date de début ({$assignment->start_datetime})"
            );
        }

        // Règle 2 : Durée maximale de 2 ans
        if ($assignment->end_datetime &&
            $assignment->start_datetime->diffInDays($assignment->end_datetime) > 730) {
            throw new \InvalidArgumentException(
                "La durée d'affectation ne peut pas dépasser 2 ans (730 jours)"
            );
        }

        // Règle 3 : Si status=completed, ended_at doit être renseigné
        if ($assignment->status === Assignment::STATUS_COMPLETED && !$assignment->ended_at) {
            Log::warning('[AssignmentObserver] ⚠️ Affectation completed sans ended_at', [
                'assignment_id' => $assignment->id
            ]);
        }
    }

    /**
     * Vérifie que les ressources sont bien libérées après terminaison
     *
     * STRATÉGIE ULTRA-PRO :
     * - Vérifie que le véhicule est marqué disponible
     * - Vérifie que le chauffeur est marqué disponible
     * - Log des alertes si incohérences détectées
     *
     * @param Assignment $assignment
     * @return void
     */
    private function checkResourcesReleased(Assignment $assignment): void
    {
        // Vérifier le véhicule
        if ($assignment->vehicle && !$assignment->vehicle->is_available) {
            // Vérifier qu'il n'y a pas d'autre affectation active pour ce véhicule
            $hasOtherActiveAssignment = Assignment::where('vehicle_id', $assignment->vehicle_id)
                ->where('id', '!=', $assignment->id)
                ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                ->exists();

            if (!$hasOtherActiveAssignment) {
                Log::warning('[AssignmentObserver] ⚠️ Véhicule non libéré après terminaison', [
                    'assignment_id' => $assignment->id,
                    'vehicle_id' => $assignment->vehicle_id,
                    'vehicle_is_available' => $assignment->vehicle->is_available,
                ]);
            }
        }

        // Vérifier le chauffeur
        if ($assignment->driver && !$assignment->driver->is_available) {
            // Vérifier qu'il n'y a pas d'autre affectation active pour ce chauffeur
            $hasOtherActiveAssignment = Assignment::where('driver_id', $assignment->driver_id)
                ->where('id', '!=', $assignment->id)
                ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                ->exists();

            if (!$hasOtherActiveAssignment) {
                Log::warning('[AssignmentObserver] ⚠️ Chauffeur non libéré après terminaison', [
                    'assignment_id' => $assignment->id,
                    'driver_id' => $assignment->driver_id,
                    'driver_is_available' => $assignment->driver->is_available,
                ]);
            }
        }
    }
}
