<?php

namespace App\Modules\Auth\ServiceProviders;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as LaravelRouteServiceProvider;

class RouteServiceProvider extends LaravelRouteServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            Route::prefix('api/v1/auth')
                ->name('auth.')
                ->middleware(['api'])
                ->group(__DIR__ . '/../Routes/api.php');
        });
    }
}