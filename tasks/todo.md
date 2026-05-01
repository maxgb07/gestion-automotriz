# TODO — Fix Error VentaController::registrarFactura

## 🎯 Objetivos
- [x] **SOLUCIONAR ERROR**: Resolver el error `Call to undefined method App\Http\Controllers\VentaController::registrarFactura()`.

## 📋 Tareas de Implementación
- [x] **VentaController.php**: Renombrar el método `facturar` existente a `registrarFactura` para que coincida con la ruta definida en `web.php`.
- [x] **Mejora**: Asegurar que al registrar la factura se actualice el campo `requiere_factura` a 'SI' (consistente con `OrdenServicioController`).

## 🏁 Revisión y Verificación
- [x] Validar que al enviar el POST a `/ventas/{venta}/facturar` desde `ventas.index`, se registre correctamente el folio sin arrojar el error 500.

---

# TODO — Formato Xprinter 80mm y Auto-Impresión (Tareas Anteriores)

## 🎯 Objetivos
- [ ] **NUEVA VISTA**: Crear `ticket_80mm.blade.php` optimizada para impresoras térmicas.
- [ ] **RUTA**: Añadir endpoint para visualizar el ticket HTML.
- [ ] **LÓGICA CONTROLADOR**: Implementar `showTicket` en `VentaController`.
- [ ] **AUTO-IMPRESIÓN**: Modificar frontend para disparar impresión automática en ventas no-crédito/no-préstamo.

## 📋 Tareas de Implementación

### 1. Estructura y Estilos
- [ ] Diseñar el layout de 80mm con CSS `@media print`.
- [ ] Asegurar que el ticket incluya todos los datos legales y comerciales necesarios.

### 2. Backend (Laravel)
- [ ] Registrar ruta `ventas.ticket` en `web.php`.
- [ ] Agregar método `showTicket` en `VentaController.php`.
- [ ] Asegurar que el método `store` devuelva la `ticket_url` cuando corresponda.

### 3. Frontend (JavaScript)
- [ ] Actualizar `ventas/crear.blade.php`:
    - [ ] Detectar `metodo_pago` al finalizar.
    - [ ] Implementar apertura de ventana de impresión para tickets.
- [ ] (Opcional) Replicar lógica en `resolverPrestamo`.

## 🏁 Revisión y Verificación
- [ ] Verificar renderizado de ticket en `/ventas/{id}/ticket`.
- [ ] Probar flujo completo de venta con efectivo.
- [ ] Confirmar que ventas a crédito siguen usando PDF.
