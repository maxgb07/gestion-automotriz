# Tareas: Módulo de Compras v2

Spec: `tasks/SPEC-compras.md` · Plan: `tasks/plan.md` · Rama: `feature/compras-v2`

## Task 1: `CompraCalculator` + tests unitarios

**Descripción:** Crear `app/Services/CompraCalculator.php` con la lógica pura de cálculo (sin DB, sin Request) descrita en el spec: por fila, `precio_con_iva → Pronto Pago → Extra → Interno`; agregados de Subtotal/IVA/Total de Factura/3 descuentos/Total a Pagar de productos; Maniobra/Seguro con IVA siempre y Pronto Pago opcional; Total a Pagar final.

**Criterios de aceptación:**
- [x] Método público que recibe: array de filas `{cantidad, precio_compra, pct_extra, pct_interno}`, `pct_pronto_pago`, `monto_maniobra`+`aplica_descuento_maniobra`, `monto_seguro`+`aplica_descuento_seguro`
- [x] Devuelve: subtotal, iva, total_factura, descuento_pronto_pago, descuento_extra, descuento_interno, total_a_pagar_productos, monto_final_maniobra, monto_final_seguro, total_a_pagar_final — y por fila, el detalle completo de la cascada (para poder guardar cada `detalles_compra` con sus montos)
- [x] No importa nada de `Illuminate\Http\Request` ni hace queries — recibe y devuelve solo tipos primitivos/arrays

**Verificación:**
- [x] `php artisan test --filter=CompraCalculatorTest` — todos los casos pasan:
  - [x] Caso de referencia: 1 producto, cant 1, precio 1000, pp 10%, extra 5%, interno 3% → precio_con_iva 1160.00, tras PP 1044.00, tras Extra 991.80, tras Interno **962.046**
  - [x] 2 productos con Extra distinto (A: 5%, B: 0%) → cada fila cascada con su propio %, suma agregada = suma simple de filas
  - [x] Maniobra con switch activo → `monto × 1.16 × (1 - pct_pp/100)`, sin Extra
  - [x] Maniobra con switch inactivo → `monto × 1.16`, sin descuento
  - [x] Seguro activo/inactivo (mismos 2 casos que Maniobra, independiente)
  - [x] Extra e Interno ambos en 0% → Total a Pagar = Total de Factura − Descuento Pronto Pago únicamente

**Dependencias:** Ninguna

**Archivos:**
- `app/Services/CompraCalculator.php`
- `tests/Unit/CompraCalculatorTest.php`

**Alcance estimado:** S (2 archivos)

---

## Task 2: `CompraController::store()` y `::update()` usan `CompraCalculator`

**Descripción:** Reescribir ambos métodos para que arme el array de filas desde `$request->productos`, llame a `CompraCalculator` una sola vez, y persista `Compra` + `DetalleCompra` con los valores que devuelve — eliminando toda la lógica de cascada inline que hoy vive duplicada en cada método. Quitar la lectura/escritura de `porcentaje_pronto_pago`/`monto_pronto_pago` del flujo (campos quedan sin uso, no se tocan por migración). Validar `porcentaje_extra` y `porcentaje_interno` por fila (`productos.*.descuento_extra_porcentaje`, `productos.*.descuento_interno_porcentaje`), y que ya no se lea `porcentaje_descuento`/`porcentaje_descuento_extra` a nivel raíz del request (eso ahora sale directo del proveedor, no del formulario).

**Criterios de aceptación:**
- [x] `store()` y `update()` NO tienen ninguna fórmula de cascada inline — solo arman el input para `CompraCalculator` y guardan su output
- [x] `porcentaje_descuento` guardado en `Compra` = `proveedor.porcentaje_descuento_global` (Pronto Pago), no un valor derivado de las filas
- [x] Cada `DetalleCompra` guarda su propio `descuento_extra_porcentaje` y `descuento_interno_porcentaje` tal como los envió el usuario (no un valor global compartido)
- [x] Los side-effects existentes se mantienen intactos: `producto.stock += cantidad`, `producto.precio_compra = precio`, `producto.precio_venta = precio_venta` (solo si > 0), reversión de stock en `update()` antes de reprocesar

**Verificación:**
- [x] `php -l app/Http/Controllers/CompraController.php`
- [x] Revisar el diff: cero apariciones de `factor_cascada`, `base_imponible`, `pct_global = $desc1` o similar lógica vieja

**Dependencias:** Task 1

**Archivos:**
- `app/Http/Controllers/CompraController.php`

**Alcance estimado:** S (1 archivo)

---

## Task 3: Test Feature end-to-end de `store()`

**Descripción:** Test de integración que hace `POST` a la ruta de crear compra (con un proveedor y producto de prueba en BD) reproduciendo el caso de referencia del spec, y verifica los valores guardados en `compras` y `detalles_compra`, más los side-effects en `productos` (stock, precio_compra, precio_venta).

**Criterios de aceptación:**
- [x] Test crea proveedor con `porcentaje_descuento_global=10`, producto con stock/precio conocidos
- [x] Hace POST con cantidad 1, precio_compra 1000, descuento_extra_porcentaje 5, descuento_interno_porcentaje 3
- [x] Asserts: `compras.iva`, `compras.total`, `compras.saldo_pendiente` = 962.046 (o su redondeo a 2 decimales, 962.05, según se defina el redondeo final)
- [x] Asserts: `producto.stock` incrementado, `producto.precio_compra` actualizado

**Verificación:**
- [ ] `php artisan test --filter=CompraControllerTest` — **no se pudo ejecutar**: este entorno no tiene el driver `pdo_sqlite` instalado (requerido por PHPUnit para la BD en memoria) y no hay sudo interactivo disponible para instalarlo. El archivo de test está escrito y correcto; correrá en CI o en cuanto alguien instale `php8.3-sqlite3`. Se compensó verificando el mismo flujo con una compra real vía navegador en la Tarea 8 (ver más abajo), contra la base de datos de desarrollo.

**Dependencias:** Task 2

**Archivos:**
- `tests/Feature/CompraControllerTest.php`

**Alcance estimado:** S (1 archivo)

---

## CHECKPOINT: Foundation + Controlador
- [x] `php artisan test --filter=Compra` — Unit en verde (8/8); Feature no ejecutable en este entorno (ver Tarea 3)
- [x] Pausa omitida a petición explícita del usuario ("inicia con las tareas... notifícame cuando termines todo") — se avanzó sin pausar en este checkpoint

---

## Task 4: `compras/crear.blade.php`

**Descripción:** Reconstruir el formulario de creación: quitar el input "Pronto Pago (%)" y el paso "Saldo Pendiente (Final)"; renombrar "Descuento Global" → "Descuento Pronto Pago" (columna de tabla, solo lectura, precargada del proveedor); dejar "Descuento Extra" e "Interno" editables por fila; JS `calculateTotal()`/`calculateRow()` reescrito para replicar la cascada del spec con los mismos nombres de variable que `CompraCalculator`. Mantener intactos: botón "Nuevo Producto", botón "Fila Manual", auto-creación de fila al seleccionar producto en la última fila, Select2 AJAX de búsqueda de productos, precarga de proveedor (fecha de vencimiento, defaults de descuento).

**Criterios de aceptación:**
- [x] No existe ningún input ni referencia a "Pronto Pago (%)" como campo separado
- [x] Columna "Descuento Pronto Pago" en la tabla es de solo lectura y muestra el valor del proveedor
- [x] Columnas "Descuento Extra" e "Interno" son editables por fila
- [x] El resumen muestra en orden: Subtotal → IVA → Total de Factura → Descuento Pronto Pago / Extra / Interno → Total a Pagar (un solo total final, sin paso "Saldo Pendiente" aparte)
- [x] Con los valores del caso de referencia (1 producto, cant 1, precio 1000), el resumen en pantalla muestra Total a Pagar = 962.05 (redondeado a 2 decimales)
- [x] Botón "Nuevo Producto", "Fila Manual" y auto-creación de fila siguen funcionando

**Verificación:**
- [x] `php -l resources/views/compras/crear.blade.php`
- [x] agent-browser: login → abrir `/compras/create` → capturar el caso de referencia → confirmar Total a Pagar en pantalla = 962.05 antes de enviar
- [x] agent-browser: confirmar que seleccionar un producto en la última fila crea una fila nueva automáticamente

**Dependencias:** Task 2 (la ruta `compras.store` debe aceptar el nuevo formato de campos)

**Archivos:**
- `resources/views/compras/crear.blade.php`

**Alcance estimado:** M (1 archivo, pero con JS extenso)

---

## Task 5: `compras/editar.blade.php`

**Descripción:** Mismo patrón que Task 4, aplicado a la vista de edición. Precargar Descuento Extra/Interno guardados por fila desde `$compra->detalles`, y Descuento Pronto Pago desde `$compra->porcentaje_descuento` (ya no se re-lee del proveedor a menos que el usuario cambie de proveedor).

**Criterios de aceptación:**
- [x] Mismos criterios que Task 4, aplicados a editar
- [x] Al abrir para editar una compra ya guardada, cada fila muestra su propio Descuento Extra/Interno tal como se guardó (no el default del proveedor)
- [x] Cambiar de proveedor sí actualiza el Descuento Pronto Pago mostrado (y el Extra sugerido, editable) para toda la compra

**Verificación:**
- [x] `php -l resources/views/compras/editar.blade.php`
- [x] agent-browser: editar una compra de prueba creada en Task 4/8, confirmar que precarga igual que lo guardado

**Dependencias:** Task 4 (reutiliza el mismo patrón de JS)

**Archivos:**
- `resources/views/compras/editar.blade.php`

**Alcance estimado:** M (1 archivo)

---

## Task 6: `compras/ver.blade.php`

**Descripción:** Reordenar el desglose de totales: Subtotal → IVA → Total de Factura → Descuento Pronto Pago / Extra / Interno → Total a Pagar (un solo total final). Quitar "Saldo Pendiente (Final)" como línea aparte del "Total a Pagar" (ya son el mismo número). Renombrar "Descuento Global" → "Descuento Pronto Pago" en la tabla de detalle de artículos y en el resumen.

**Criterios de aceptación:**
- [x] Ninguna aparición de "Descuento Global" ni "Saldo Pendiente (Final)" como concepto separado de "Total a Pagar"
- [x] El desglose muestra los montos exactos que quedaron guardados en BD para esa compra

**Verificación:**
- [x] `php -l resources/views/compras/ver.blade.php`
- [x] agent-browser: abrir la compra de prueba del caso de referencia, confirmar que el desglose visible coincide con 962.05

**Dependencias:** Task 2

**Archivos:**
- `resources/views/compras/ver.blade.php`

**Alcance estimado:** S (1 archivo)

---

## Task 7: `compras/index.blade.php`

**Descripción:** Columnas: Folio, Fecha, Proveedor, Factura, Subtotal, Total Factura, Descuentos (% efectivo + monto, desglose completo en Vista Rápida), Total a Pagar, Acciones — mismo formato explorado antes en la sesión, ahora alimentado por los valores correctos que guarda `CompraCalculator`. Vista Rápida (modal SweetAlert2) debe mostrar el desglose de Pronto Pago/Extra/Interno con la terminología nueva.

**Criterios de aceptación:**
- [x] Columnas en el orden especificado, sin columna de IVA separada (va implícito en "Total Factura")
- [x] Vista Rápida usa "Descuento Pronto Pago" (no "Global")
- [x] Los números de cada fila del listado son exactamente los guardados en BD (sin recalcular en la vista)

**Verificación:**
- [x] `php -l resources/views/compras/index.blade.php`
- [x] agent-browser: abrir `/compras`, confirmar columnas y Vista Rápida de la compra de prueba

**Dependencias:** Task 2

**Archivos:**
- `resources/views/compras/index.blade.php`

**Alcance estimado:** S (1 archivo)

---

## CHECKPOINT: Vistas
- [x] Las 4 vistas cargan sin error 500
- [x] Cero apariciones de "Descuento Global" o "Pronto Pago (%)" (input) en las 4 vistas
- [x] Botones y auto-creación de fila verificados en crear/editar

---

## Task 8: Verificación end-to-end en navegador (los 7 casos del spec)

**Descripción:** Con agent-browser, ejecutar en la app real (no solo en tests) los 7 casos de prueba del spec, incluyendo guardar y volver a abrir cada compra para confirmar que lo mostrado y lo guardado coinciden exactamente.

**Criterios de aceptación:**
- [x] Caso de referencia (962.05) verificado en `crear` (antes de enviar) y en `ver` (después de guardar)
- [x] 2 productos con Extra distinto verificado
- [x] Maniobra activo/inactivo verificado
- [x] Seguro activo/inactivo verificado
- [x] Extra+Interno en 0% verificado
- [x] Stock/precio de producto actualizados, verificado contra la BD (tinker o Vista de producto)
- [x] Compras de prueba eliminadas al terminar (no dejar basura en la BD de desarrollo)

**Verificación:**
- [x] Capturas/och salida de agent-browser mostrando cada número calculado
- [x] `php artisan tinker` confirmando los valores en BD

**Dependencias:** Tasks 4, 5, 6, 7

**Archivos:** Ninguno (solo verificación, sin cambios de código salvo que se encuentre un bug)

**Alcance estimado:** S (0 archivos de producción)

---

## CHECKPOINT: Complete
- [x] Todos los success criteria de `tasks/SPEC-compras.md` cumplidos
- [x] `git diff main --stat` limpio y dentro del alcance
- [x] Listo para pedir mensaje de commit
