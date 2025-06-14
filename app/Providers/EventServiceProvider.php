<?php

namespace App\Providers;

use App\Models\User;                // <--- AJOUTEZ CETTE LIGNE
use App\Observers\UserObserver;    // <--- AJOUTEZ CETTE LIGNE
use App\Models\Driver; // <-- Ajouter
use App\Observers\DriverObserver; // <-- Ajouter
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class); // <--- AJOUTEZ CETTE LIGNE
        Driver::observe(DriverObserver::class); // <-- Ajouter cette ligne 
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
