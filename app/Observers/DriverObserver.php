<?php
namespace App\Observers;
use App\Models\Driver;
use Carbon\Carbon;

class DriverObserver
{
    /**
     * Gère l'événement "saving" du modèle Driver.
     */
    public function saving(Driver $driver): void
    {
        if ($driver->isDirty('license_issue_date') && !is_null($driver->license_issue_date)) {
            $driver->license_expiry_date = Carbon::parse($driver->license_issue_date)->addYears(10);
        }
    }
}
