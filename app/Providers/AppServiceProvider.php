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

        // Compartir alerta de fin de mes y de mes anterior con el dashboard y el index de órdenes
        \Illuminate\Support\Facades\View::composer(['dashboard', 'ordenes.index'], function ($view) {
            $notificationService = app(\App\Services\NotificationService::class);
            $view->with('eomAlert', $notificationService->getEndOfMonthRepairs());
            $view->with('prevMonthAlert', $notificationService->getPreviousMonthRepairs());
        });
    }
}
