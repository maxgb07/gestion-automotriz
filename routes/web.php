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
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\DashboardBetaController;

// Ruta principal - redirige según autenticación
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta pública para compartir orden de servicio (PDF)
Route::get('orden/compartir/{token}', [\App\Http\Controllers\OrdenServicioController::class, 'verPDFCompartido'])->name('ordenes.compartir');

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/estadisticas', [DashboardBetaController::class, 'index'])->name('estadisticas.index');
    
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

    Route::get('productos/inventario', [ProductoController::class, 'inventario'])->name('productos.inventario');
    Route::get('productos/inventario/captura-rapida', [ProductoController::class, 'capturaRapida'])->name('productos.inventario.captura_rapida');
    Route::get('productos/inventario/crear-lote', [ProductoController::class, 'crearLote'])->name('productos.crear_lote');
    Route::post('productos/inventario/guardar-lote-nuevos', [ProductoController::class, 'guardarLoteNuevos'])->name('productos.guardar_lote_nuevos');
    Route::post('productos/inventario/update-lote', [ProductoController::class, 'guardarLoteInventario'])->name('productos.inventario.update_lote');
    Route::post('productos/inventario/update', [ProductoController::class, 'updateInventario'])->name('productos.inventario.update');
    Route::get('productos/pedimento', [ProductoController::class, 'pedimento'])->name('productos.pedimento');
    Route::get('productos/inventario/pdf', [ProductoController::class, 'exportarInventarioPDF'])->name('productos.inventario.pdf');
    Route::get('productos-buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    Route::get('productos/{producto}/ultimas-ventas', [ProductoController::class, 'ultimasVentas'])->name('productos.ultimas_ventas');
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
    Route::get('ventas/{venta}/ticket', [VentaController::class, 'showTicket'])->name('ventas.ticket');
    Route::post('ventas/{venta}/facturar', [VentaController::class, 'registrarFactura'])->name('ventas.factura.store');
    Route::post('ventas/{venta}/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar');
    Route::post('ventas/{venta}/resolver', [VentaController::class, 'resolverPrestamo'])->name('ventas.resolver_prestamo');
    Route::resource('ventas', VentaController::class);
    Route::post('ventas/{venta}/pagos', [PagoVentaController::class, 'store'])->name('ventas.pagos.store');
    Route::post('ventas/{venta}/items', [VentaController::class, 'storeItems'])->name('ventas.items.store');
    Route::put('ventas/{venta}/detalles/{detalle}', [VentaController::class, 'updateDetalle'])->name('ventas.detalles.update');
    Route::delete('ventas/{venta}/detalles/{detalle}', [VentaController::class, 'destroyDetalle'])->name('ventas.detalles.destroy');

    // Órdenes de Servicio
    Route::get('clientes-buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    Route::get('vehiculos-buscar', [VehiculoController::class, 'buscar'])->name('vehiculos.buscar');
    Route::resource('ordenes', \App\Http\Controllers\OrdenServicioController::class);
    Route::post('ordenes/{orden}/revertir', [\App\Http\Controllers\OrdenServicioController::class, 'revertirAReparacion'])->name('ordenes.revertir');
    Route::post('ordenes/{orden}/pagos', [\App\Http\Controllers\OrdenServicioController::class, 'registrarPago'])->name('ordenes.pagos.store');
    Route::post('ordenes/{orden}/facturar', [\App\Http\Controllers\OrdenServicioController::class, 'registrarFactura'])->name('ordenes.factura.store');
    Route::get('ordenes/{orden}/pdf', [\App\Http\Controllers\OrdenServicioController::class, 'descargarPDF'])->name('ordenes.pdf');
    Route::get('ordenes/{orden}/cotizacion/pdf', [\App\Http\Controllers\OrdenServicioController::class, 'descargarCotizacionPDF'])->name('ordenes.cotizacion.pdf');
    Route::post('ordenes/{orden}/detalles', [\App\Http\Controllers\OrdenServicioController::class, 'agregarDetalle'])->name('ordenes.detalles.store');
    Route::put('ordenes/{orden}/detalles/{detalle}', [\App\Http\Controllers\OrdenServicioController::class, 'actualizarDetalle'])->name('ordenes.detalles.update');
    Route::delete('ordenes/{orden}/detalles/{detalle}', [\App\Http\Controllers\OrdenServicioController::class, 'eliminarDetalle'])->name('ordenes.detalles.destroy');
    Route::post('ordenes/{orden}/imagenes', [\App\Http\Controllers\OrdenServicioController::class, 'subirImagen'])->name('ordenes.imagenes.store');
    Route::post('ordenes/{orden}/datos-vehiculo', [\App\Http\Controllers\OrdenServicioController::class, 'actualizarDatosVehiculo'])->name('ordenes.datos-vehiculo.update');
    Route::delete('ordenes/{orden}/imagenes/{imagen}', [\App\Http\Controllers\OrdenServicioController::class, 'eliminarImagen'])->name('ordenes.imagenes.destroy');

    // Cuentas por Pagar
    Route::prefix('cuentas-por-pagar')->name('cuentas_por_pagar.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CuentasPorPagarController::class, 'index'])->name('index');
        Route::get('/calendario-eventos', [\App\Http\Controllers\CuentasPorPagarController::class, 'eventosCalendario'])->name('calendario_eventos');
        Route::get('/pdf-global', [\App\Http\Controllers\CuentasPorPagarController::class, 'descargarPDFGlobal'])->name('pdf_global');
        Route::get('/{proveedor}', [\App\Http\Controllers\CuentasPorPagarController::class, 'show'])->name('show');
        Route::get('/{proveedor}/pdf', [\App\Http\Controllers\CuentasPorPagarController::class, 'descargarPDF'])->name('pdf');
        Route::post('/pagos', [\App\Http\Controllers\CuentasPorPagarController::class, 'registrarPago'])->name('pagos.store');
        Route::get('/pagos/{grupo_pago_id}/facturas', [\App\Http\Controllers\CuentasPorPagarController::class, 'getFacturasPagadas'])->name('pagos.facturas');
        Route::post('/complementos', [\App\Http\Controllers\CuentasPorPagarController::class, 'registrarComplemento'])->name('complementos.store');
        Route::post('/notas-credito', [\App\Http\Controllers\CuentasPorPagarController::class, 'registrarNotaCredito'])->name('notas_credito.store');
        Route::put('/notas-credito/{notaCredito}', [\App\Http\Controllers\CuentasPorPagarController::class, 'actualizarNotaCredito'])->name('notas_credito.update');
        Route::delete('/notas-credito/{notaCredito}', [\App\Http\Controllers\CuentasPorPagarController::class, 'eliminarNotaCredito'])->name('notas_credito.destroy');
    });

    // Cuentas por Cobrar
    Route::prefix('creditos')->name('creditos.')->group(function () {
        Route::get('/', [CreditoController::class, 'index'])->name('index');
        Route::get('/reporte-cobranza', [CreditoController::class, 'reporteGeneral'])->name('reporte_cobranza');
        Route::get('/{cliente}', [CreditoController::class, 'show'])->name('show');
        Route::post('/{cliente}/comentario', [CreditoController::class, 'storeComentario'])->name('comentario.store');
        Route::get('/{cliente}/historial', [CreditoController::class, 'historialComentarios'])->name('historial');
        Route::post('/pago-lote', [CreditoController::class, 'registrarPagoLote'])->name('pago_lote');
        Route::get('/{cliente}/pdf', [CreditoController::class, 'generarEstadoCuenta'])->name('pdf');
    });

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
