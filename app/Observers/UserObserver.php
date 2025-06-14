<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "saving" event.
     *
     * This method is called before a user is created or updated.
     * We use it to automatically populate the 'name' field
     * from 'first_name' and 'last_name'.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function saving(User $user): void
    {
        // Vérifie si first_name ou last_name ont été modifiés ou sont en cours de définition
        if ($user->isDirty('first_name') || $user->isDirty('last_name') || !$user->exists) {
            $firstName = $user->first_name ?? '';
            $lastName = $user->last_name ?? '';

            $user->name = trim($firstName . ' ' . $lastName);

            // Si le nom est vide après le trim (cas où first_name et last_name sont vides ou null),
            // et que la colonne 'name' ne peut être null, on pourrait lui assigner une valeur par défaut
            // ou s'appuyer sur la validation pour que first_name/last_name soient requis.
            // Breeze s'attend à ce que 'name' ne soit pas null.
            if (empty($user->name) && !empty($user->email)) {
                // Fallback simple si le nom est vide mais que l'email existe (ex: pour les anciens utilisateurs sans first/last name)
                // Ou vous pourriez choisir de lever une exception ou de mettre une valeur placeholder.
                // Pour l'instant, on s'attend à ce que first_name/last_name soient fournis à la création.
            }
        }
    }

    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
