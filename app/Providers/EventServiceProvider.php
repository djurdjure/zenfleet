<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Models\Driver;
use App\Observers\DriverObserver;
use App\Events\RepairRequestStatusChanged;
use App\Listeners\SendRepairRequestNotifications;
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

        // 🔧 REPAIR REQUEST EVENTS
        RepairRequestStatusChanged::class => [
            SendRepairRequestNotifications::class,
        ],

        // 🚗 ASSIGNMENT EVENTS - Enterprise-Grade Auto-Release
        \App\Events\AssignmentEnded::class => [
            \App\Listeners\ReleaseVehicleAndDriver::class,
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
