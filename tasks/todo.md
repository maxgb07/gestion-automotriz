# TODO — Cobro Masivo y Facturación Condicionada

## 🎯 Objetivos
- [x] **MODULO PAGO LOTE**: Selección múltiple de deudas y pago con distribución FIFO.
- [x] **ESTABILIDAD UI**: Asegurar que los montos manuales no se sobrescriban.
- [x] **FACTURACIÓN INTELIGENTE**: Campo `requiere_factura` solo se activa en liquidación total.

# TODO — Módulo de Préstamos de Material

## 🎯 Objetivos
- [x] **DB UPDATE**: Enums actualizados para soportar `PRESTAMO` y `DEVUELTO`.
- [x] **VALIDACIÓN**: Bloqueo de préstamos para "Público General".
- [x] **PDF ESPECIALIZADO**: Formato sin precios para vales de préstamo.
- [x] **LOGICA DE DEVOLUCIÓN**: Incremento automático de stock al devolver.

## 📋 Tareas Realizadas

### 1. Base de Datos
- [x] Crear y ejecutar migración `add_prestamo_to_ventas_enums`.

### 2. Backend (Lógica de Negocio)
- [x] Actualizar `VentaController@store` para asignar estado `PRESTAMO`.
- [x] Implementar `VentaController@devolverPrestamo` con lógica de stock.
- [x] Configurar conmutación de vistas PDF en `downloadPDF`.

### 3. Frontend (Interfaz)
- [x] Añadir opción "PRÉSTAMO" en el selector de métodos de pago.
- [x] Implementar validación JS cruzada (Cliente vs Método).
- [x] Añadir botón de devolución en el historial de ventas.
- [x] Implementar AJAX para el botón de devolución.

### 4. Formatos
- [x] Crear plantilla `pdf_media_carta_prestamo.blade.php`.
- [x] Incluir insignias de estado (EN PRÉSTAMO / DEVUELTO) en la tabla principal.

## 🏁 Revisión Final
- [ ] Autocomprobar con Tinker (Opcional).
- [ ] Presentar Walkthrough al usuario.
