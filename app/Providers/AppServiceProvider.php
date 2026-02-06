<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Contracts\PermissionsTeamResolver;
use App\Services\OrganizationTeamResolver;
use App\Models\VehicleMileageReading;
use App\Observers\VehicleMileageReadingObserver;
use App\Models\VehicleDepot;
use App\Observers\VehicleDepotObserver;
use App\Models\Organization;
use App\Observers\OrganizationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Organization Team Resolver for Spatie Permissions
        $this->app->singleton(PermissionsTeamResolver::class, OrganizationTeamResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Register Observers Enterprise-Grade
        VehicleMileageReading::observe(VehicleMileageReadingObserver::class);

        // ✅ V2.0 - Observer pour invalidation automatique du cache des dépôts
        // Garantit que le filtre des dépôts se met à jour en temps réel
        VehicleDepot::observe(VehicleDepotObserver::class);

        // ✅ Provision roles on organization creation (enterprise guardrail)
        Organization::observe(OrganizationObserver::class);
    }
}
