<?php

namespace App\Providers;

use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // On lie l'interface du repository des véhicules à son implémentation Eloquent.
        $this->app->bind(
            VehicleRepositoryInterface::class,
            VehicleRepository::class
        );

        // Plus tard, nous ajouterons ici les autres repositories (Driver, etc.)
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
