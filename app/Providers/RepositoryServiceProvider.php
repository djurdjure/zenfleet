<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use App\Repositories\Eloquent\VehicleRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            VehicleRepositoryInterface::class,
            VehicleRepository::class
        );
    }
}
