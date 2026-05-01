# TODO — Módulo Cuentas por Pagar (CXP) y Compras

## Fase 1: Actualización de Proveedores
- [x] **Migración**: Crear migración `add_cuentas_pagar_fields_to_proveedores_table` para añadir `dias_credito` (int) y `porcentaje_descuento_global` (decimal).
- [x] **Modelo**: Actualizar `$fillable` en `App\Models\Proveedor`.
- [x] **Vistas**: Agregar los nuevos campos en `resources/views/proveedores/crear.blade.php` y `editar.blade.php`.
- [x] **Controlador**: Actualizar `ProveedorController@store` y `update` con las nuevas reglas de validación.

## Fase 2: Actualización de Compras (Estructura Base y Descuentos)
- [x] **Migración Compras**: Crear migración `modify_compras_table_for_cuentas_pagar`.
    - Campos: `subtotal`, `porcentaje_descuento`, `monto_descuento`, `iva`, `fecha_vencimiento`, `saldo_pendiente`, `estado_pago` (PENDIENTE, PARCIAL, PAGADA), `estado_complemento` (PENDIENTE, RECIBIDO, NO_APLICA).
- [x] **Migración Detalles**: Crear migración `modify_detalles_compra_for_cuentas_pagar`.
    - Campos: `descuento_porcentaje`, `descuento_extra_porcentaje`, `subtotal` (antes de IVA, después de descuentos en cascada).
- [x] **Modelo**: Actualizar `$fillable` en `Compra` y `DetalleCompra`.
- [x] **Vistas Frontend**:
    - Rediseñar `compras/crear.blade.php` (Agregar fechas, inputs para descuento extra, cálculo de IVA estricto al 16% y descuentos en cascada).
- [x] **Controlador Backend**:
    - Lógica de guardado en `CompraController@store` respetando cascada e IVA.
    - Implementar acción de **Edición de Compra** con reversión de inventario.

## Fase 3: Motor de Pagos y Notas de Crédito
- [x] **Migraciones**:
    - `create_notas_credito_proveedores_table` (folio, proveedor_id, monto_original, saldo_disponible, fecha).
    - `create_pagos_compras_table` (compra_id, monto, fecha_pago, forma_pago, referencia, tipo, nota_credito_id).
- [x] **Modelos**: Crear `NotaCreditoProveedor` y `PagoCompra`. Relaciones con `Compra` y `Proveedor`.
- [x] **Controladores y Vistas**: Interfaz para registrar una NC.

## Fase 4: Módulo de Cuentas por Pagar y Reportes
- [x] **Dashboard CXP**: Listado de compras (facturas) por proveedor con estado de pago y alertas de vencimiento.
- [x] **Pagos Múltiples**: Modal para seleccionar varias facturas de un proveedor y aplicar pago (o usar Nota de Crédito).
- [x] **Reportes**: Generación de reportes de saldos por proveedor (individual/múltiple) en PDF/Excel.

## Fase 5: Complementos de Pago (REP) y Expedientes
- [x] **Migraciones**: Agregar `grupo_pago_id` y campos de REP a pagos. Agregar `ncs_informativas`.
- [x] **Agrupación**: Refactorizar historial para agrupar transacciones liquidadas con varias formas de pago.
- [x] **Expedientes Fiscales**: UI para registrar y cerrar expedientes ligando un pago, un REP y múltiples NCs.
- [x] **Paginación y UX**: Paginación dinámica en modal de visualización de facturas pagadas y reordenamiento global por folio.
