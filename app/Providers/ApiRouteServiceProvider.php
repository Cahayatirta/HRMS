<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class ApiRouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
        
        // Override route middleware untuk API Filament
        Route::middleware(['api', 'auth:sanctum'])
            ->prefix('api')
            ->group(function () {
                // Routes akan di-handle oleh plugin tapi dengan middleware kita
            });
    }
}