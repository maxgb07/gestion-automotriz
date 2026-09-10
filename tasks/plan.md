# Implementation Plan: Módulo de Compras v2 (descuentos en cascada)

Spec de referencia: `tasks/SPEC-compras.md`. Rama: `feature/compras-v2`.

## Overview

Extraer el cálculo de descuentos/IVA/totales de una compra a una clase de servicio pura (`App\Services\CompraCalculator`), usarla desde `CompraController::store()` y `::update()` (hoy tienen la fórmula duplicada e inconsistente entre sí — la causa raíz del bug original), y reconstruir las 4 vistas de Compras para reflejar la cascada correcta (IVA primero, luego Pronto Pago → Extra por fila → Interno por fila) y la nueva terminología ("Descuento Pronto Pago" en vez de "Descuento Global", sin input separado de Pronto Pago ni paso de "Saldo Pendiente" aparte).

## Architecture Decisions

- **Cálculo puro y único**: `CompraCalculator` no toca DB ni `Request` — recibe primitivos (array de filas + parámetros), devuelve un array/DTO con todos los subtotales. Esto es lo que hace testeable el caso de referencia (962.046) sin levantar Laravel completo, y es lo que garantiza que `store()` y `update()` nunca más diverjan.
- **JS espejo, no fuente de verdad**: el JS de `crear`/`editar` replica la misma fórrmula para el resumen en vivo, con los mismos nombres de variable que el PHP (`precioConIva`, `montoPP`, `resto1`, `montoExtra`, `resto2`, `montoInterno`, `totalFilaFinal`). El valor que manda siempre es el que calcula PHP al guardar; el JS es solo para que el usuario vea el total antes de enviar el formulario.
- **Sin migraciones**: todos los campos de BD necesarios ya existen en `compras` y `detalles_compra` (confirmado en el spec). `porcentaje_pronto_pago`/`monto_pronto_pago` quedan sin uso pero no se eliminan en este trabajo (requiere aprobación aparte por ser cambio de esquema).
- **Orden de construcción**: de adentro hacia afuera — primero la lógica pura y testeada, después el controlador que la consume, después las vistas que dependen del controlador. Cada vista se hace en un task separado porque cada una tiene su propio riesgo de UI (crear/editar tienen JS complejo con Select2 y auto-creación de filas; ver e index son solo lectura/display).

## Task List

### Phase 1: Foundation — cálculo puro

- [ ] Task 1: `CompraCalculator` + tests unitarios

### Checkpoint: Foundation
- [ ] `php artisan test --filter=CompraCalculatorTest` pasa, incluyendo el caso de referencia 962.046
- [ ] Ningún archivo de `resources/views` ni `CompraController` tocado todavía

### Phase 2: Controlador

- [ ] Task 2: `CompraController::store()` y `::update()` usan `CompraCalculator`
- [ ] Task 3: Test Feature end-to-end de `store()`

### Checkpoint: Controlador
- [ ] `php artisan test --filter=Compra` pasa completo (unit + feature)
- [ ] Una compra creada por HTTP (test Feature) tiene en BD los mismos números que el test unitario del caso de referencia

### Phase 3: Vistas

- [ ] Task 4: `compras/crear.blade.php`
- [ ] Task 5: `compras/editar.blade.php`
- [ ] Task 6: `compras/ver.blade.php`
- [ ] Task 7: `compras/index.blade.php`

### Checkpoint: Vistas
- [ ] Las 4 vistas cargan sin error 500 (verificar con agent-browser o `curl -I` autenticado)
- [ ] Ningún texto "Descuento Global" ni "Pronto Pago (%)" (input) queda visible en ninguna de las 4 vistas
- [ ] Botón "Nuevo Producto", "Fila Manual" y auto-creación de fila siguen funcionando en `crear` y `editar`

### Phase 4: Verificación end-to-end

- [ ] Task 8: Verificación en navegador de los 7 casos de prueba del spec

### Checkpoint: Complete
- [ ] Todos los success criteria del spec cumplidos
- [ ] `git diff main --stat` muestra solo los archivos listados en el spec (alcance limpio)
- [ ] Listo para pedir mensaje de commit / PR

## Risks and Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| JS y PHP divergen otra vez (mismo bug original) | Alto | Nombres de variable idénticos entre JS y PHP (ver Architecture Decisions); Task 8 verifica en navegador que el resumen en pantalla coincide con lo guardado en BD |
| Romper la auto-creación de fila / Select2 al reescribir crear/editar | Medio | Task 4/5 explícitamente listan "no romper" como criterio de aceptación, se prueba manualmente en navegador antes de cerrar la tarea |
| Redondeo de decimales (962.046 tiene 3 decimales) causando diferencias de centavos entre PHP y lo mostrado en UI | Medio | El test unitario usa el valor completo sin redondear intermedio; solo se redondea a 2 decimales al mostrar/guardar el total final, nunca en pasos intermedios de la cascada |
| Migrar compras ya existentes en producción con la fórmula vieja | Bajo (fuera de alcance) | El spec es explícito: fórmula nueva solo aplica a compras nuevas/editadas de aquí en adelante, no se migra histórico |

## Open Questions

Ninguna — resueltas en el spec.
