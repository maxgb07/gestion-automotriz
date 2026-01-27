<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        //
        /* //if (app()->environment('production')) {
            URL::forceScheme('https');
       // } */

        // Compartir alertas con el navbar
        \Illuminate\Support\Facades\View::composer('partials.navbar', function ($view) {
            $view->with('alerts', app(\App\Services\NotificationService::class)->getAlerts());
        });
    }
}
