<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Interfaces
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use App\Repositories\Interfaces\DriverRepositoryInterface;
use App\Repositories\Interfaces\AssignmentRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
// Implémentations
use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Eloquent\DriverRepository;
use App\Repositories\Eloquent\AssignmentRepository;
use App\Repositories\Eloquent\SupplierRepository;

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
	
	// ----- AJOUT POUR SUPPLIER
	$this->app->bind(
		SupplierRepositoryInterface::class, 
		SupplierRepository::class
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
