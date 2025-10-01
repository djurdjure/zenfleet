<?php

namespace App\Observers;

use App\Models\Driver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DriverObserver
{
    /**
     * Gère l'événement "saving" du modèle Driver.
     */
    public function saving(Driver $driver): void
    {
        if ($driver->isDirty('license_issue_date') && !is_null($driver->license_issue_date)) {
            $driver->driver_license_expiry_date = Carbon::parse($driver->license_issue_date)->addYears(10);
        }
    }

    /**
     * Gère l'événement "created" du modèle Driver.
     */
    public function created(Driver $driver): void
    {
        // Si le chauffeur a déjà un user_id, on ne fait rien
        if ($driver->user_id) {
            return;
        }

        // Si le chauffeur est créé sans utilisateur associé, on en crée un.
        $userEmail = $driver->personal_email;

        if (empty($userEmail)) {
            $firstNameLetter = Str::lower(substr($driver->first_name, 0, 1));
            $lastName = Str::lower($driver->last_name);
            $randomNumber = rand(100, 999);
            $userEmail = "{$firstNameLetter}.{$lastName}{$randomNumber}@zen.fleet";
        }

        // On vérifie si un utilisateur avec cet email n'existe pas déjà
        if (User::where('email', $userEmail)->exists()) {
            // Si l'email existe déjà, on ne crée pas de nouvel utilisateur
            // et on log une erreur ou une notification si nécessaire.
            // Pour l'instant, on ignore silencieusement pour éviter de bloquer la création du chauffeur.
            return;
        }

        $user = User::create([
            'name' => $driver->first_name . ' ' . $driver->last_name,
            'email' => $userEmail,
            'password' => Hash::make(Str::random(12)),
            'organization_id' => $driver->organization_id,
        ]);

        // On associe le nouvel utilisateur au chauffeur
        // On utilise une mise à jour silencieuse pour ne pas redéclencher d'événements
        $driver->user_id = $user->id;
        $driver->saveQuietly();
    }
}
