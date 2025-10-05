<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Contracts\PermissionsTeamResolver;
use App\Services\OrganizationTeamResolver;
use App\Models\VehicleMileageReading;
use App\Observers\VehicleMileageReadingObserver;

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
        // Register Observers
        VehicleMileageReading::observe(VehicleMileageReadingObserver::class);
    }
}
