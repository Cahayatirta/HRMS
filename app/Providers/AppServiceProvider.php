<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Rupadana\ApiService\ApiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Override API middleware setelah plugin loaded
        // if (class_exists(\Rupadana\ApiService\ApiServicePlugin::class)) {
        //     config([
        //         'filament-api-service.middlewares' => [
        //             'auth:sanctum'
        //         ]
        //     ]);
        // }
        Paginator::useBootstrap();
    }
}