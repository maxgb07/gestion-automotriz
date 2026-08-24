<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// El comando 'inventario:clasificar' se ejecuta directamente desde el cron de Hostinger
// cada domingo a las 2:00 AM. No se requiere el scheduler de Laravel.
// Cron: 0 2 * * 0 cd /ruta/proyecto && php artisan inventario:clasificar >> /dev/null 2>&1
