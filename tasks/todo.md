# TODO — Formato Xprinter 80mm y Auto-Impresión

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

## 📝 Lecciones Aprendidas
*(Se actualizará después de la implementación)*
