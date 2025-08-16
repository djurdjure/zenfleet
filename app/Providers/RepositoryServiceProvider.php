<?php

namespace App\Providers;

use App\Repositories\Eloquent\DriverRepository;
use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Interfaces\DriverRepositoryInterface;
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\AssignmentRepositoryInterface;
use App\Repositories\Eloquent\AssignmentRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Liaison pour les Véhicules (déjà présente)
        $this->app->bind(
            VehicleRepositoryInterface::class,
            VehicleRepository::class
        );

        // CORRECTION : Ajout de la liaison pour les Chauffeurs
        $this->app->bind(
            DriverRepositoryInterface::class,
            DriverRepository::class
        );

        // --- AJOUT POUR LES AFFECTATION
        $this->app->bind(
            AssignmentRepositoryInterface::class,
            AssignmentRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
