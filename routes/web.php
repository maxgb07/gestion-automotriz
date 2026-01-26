<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PagoVentaController;
use App\Http\Controllers\ReporteController;

// Ruta principal - redirige según autenticación
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Gestión de Clientes
    Route::post('clientes/{id}/restore', [ClienteController::class, 'restore'])->name('clientes.restore');
    Route::resource('clientes', ClienteController::class);
    
    // Gestión de Vehículos
    Route::post('vehiculos/{id}/restore', [VehiculoController::class, 'restore'])->name('vehiculos.restore');
    Route::get('clientes/{cliente}/vehiculos/crear', [VehiculoController::class, 'create'])->name('vehiculos.create');
    Route::post('clientes/{cliente}/vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::get('vehiculos/{vehiculo}/editar', [VehiculoController::class, 'edit'])->name('vehiculos.edit');
    Route::put('vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

    // Inventario - Productos
    Route::get('productos/pedimento', [ProductoController::class, 'pedimento'])->name('productos.pedimento');
    Route::resource('productos', ProductoController::class);

    // Proveedores
    Route::resource('proveedores', ProveedorController::class)->parameters([
        'proveedores' => 'proveedor'
    ]);

    // Compras
    Route::resource('compras', CompraController::class);
    Route::resource('servicios', ServicioController::class);

    // Ventas
    Route::get('ventas/{venta}/pdf', [VentaController::class, 'downloadPDF'])->name('ventas.pdf');
    Route::resource('ventas', VentaController::class);
    Route::post('ventas/{venta}/pagos', [PagoVentaController::class, 'store'])->name('ventas.pagos.store');

    // Órdenes de Servicio
    Route::get('clientes-buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    Route::get('vehiculos-buscar', [VehiculoController::class, 'buscar'])->name('vehiculos.buscar');
    Route::resource('ordenes', \App\Http\Controllers\OrdenServicioController::class);
    Route::post('ordenes/{orden}/pagos', [\App\Http\Controllers\OrdenServicioController::class, 'registrarPago'])->name('ordenes.pagos.store');
    Route::get('ordenes/{orden}/pdf', [\App\Http\Controllers\OrdenServicioController::class, 'descargarPDF'])->name('ordenes.pdf');
    Route::get('ordenes/{orden}/cotizacion/pdf', [\App\Http\Controllers\OrdenServicioController::class, 'descargarCotizacionPDF'])->name('ordenes.cotizacion.pdf');
    Route::post('ordenes/{orden}/detalles', [\App\Http\Controllers\OrdenServicioController::class, 'agregarDetalle'])->name('ordenes.detalles.store');
    Route::delete('ordenes/{orden}/detalles/{detalle}', [\App\Http\Controllers\OrdenServicioController::class, 'eliminarDetalle'])->name('ordenes.detalles.destroy');
    Route::post('ordenes/{orden}/imagenes', [\App\Http\Controllers\OrdenServicioController::class, 'subirImagen'])->name('ordenes.imagenes.store');
    Route::delete('ordenes/{orden}/imagenes/{imagen}', [\App\Http\Controllers\OrdenServicioController::class, 'eliminarImagen'])->name('ordenes.imagenes.destroy');

    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/corte', [ReporteController::class, 'corteDia'])->name('corte');
        Route::get('/corte/pdf', [ReporteController::class, 'cortePDF'])->name('corte.pdf');
        Route::get('/ventas', [ReporteController::class, 'ventas'])->name('ventas');
        Route::get('/ventas/pdf', [ReporteController::class, 'ventasPDF'])->name('ventas.pdf');
        Route::get('/ordenes', [ReporteController::class, 'ordenes'])->name('ordenes');
        Route::get('/ordenes/pdf', [ReporteController::class, 'ordenesPDF'])->name('ordenes.pdf');
    });
});
