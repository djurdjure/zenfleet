<?php

namespace App\Traits;

use App\Models\Driver;
use App\Models\Vehicle;
use App\Services\AssignmentPresenceService;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait Enterprise-Grade pour la gestion intelligente des statuts de ressources.
 * Assure qu'une ressource n'est libérée que si elle n'a plus aucune affectation active ou planifiée.
 */
trait ManagesResourceStatus
{
    /**
     * Libère la ressource (Driver ou Vehicle) si elle n'a plus d'affectation active/planifiée.
     */
    protected function releaseResource(Model $resource, ?\Carbon\Carbon $referenceTime = null, ?\Carbon\Carbon $lastAssignmentEnd = null): void
    {
        $presence = app(AssignmentPresenceService::class);
        $referenceTime = $referenceTime ?? now();
        $lastAssignmentEnd = $lastAssignmentEnd ?? $referenceTime;

        if ($resource instanceof Vehicle) {
            $presence->syncVehicle($resource->id, $referenceTime, $lastAssignmentEnd);
            return;
        }

        if ($resource instanceof Driver) {
            $presence->syncDriver($resource->id, $referenceTime, $lastAssignmentEnd);
        }
    }
}
