# Spec: Módulo de Compras v2 (descuentos en cascada)

Rama: `feature/compras-v2`
Estado: aprobado por el usuario vía entrevista (interview-me) el 2026-09-09, pendiente de plan/tasks.

## Objetivo

Reconstruir el registro y edición de compras (`compras/crear`, `compras/editar`, `compras/ver`, `compras/index`, `CompraController`) para que el cálculo de descuentos, IVA y total a pagar refleje el modelo de negocio real: los descuentos se aplican **en cascada, por producto, sobre el precio ya con IVA incluido**, no sobre el subtotal antes de impuestos. La versión anterior calculaba el IVA sobre la base ya descontada y trataba "Descuento Global"/"Extra" como un solo valor ambiguo (a veces tomado de la primera fila, a veces de la última), lo que producía totales que no coincidían con las facturas físicas de los proveedores.

Quién lo usa: el dueño/operador del taller, al capturar una compra de refacciones. Éxito = el número que el sistema muestra como "Total a Pagar" es exactamente lo que el proveedor cobra, verificable a mano con lápiz y papel contra la factura física.

## Modelo de negocio (fuente de verdad)

**Corregido el 2026-09-09** (segunda revisión): la primera versión de este spec fusionó "Descuento Global" (del proveedor, por producto) con "Descuento por Pronto Pago" (financiero, capturado a mano) en un solo concepto. Al reconstruir una factura real de DAPESA junto con su Nota de Crédito, el usuario detectó que son dos cosas distintas y que el sistema daba un total incorrecto. Quedan separados en dos etapas — ver "Cálculo por producto" y "Etapa 2" más abajo.

### Datos de proveedor (`proveedores`, sin cambios de esquema)
- `porcentaje_descuento_global` → se muestra en la UI como **"Descuento Global"**. Fijo por proveedor, no editable en la compra, cascada por producto.
- `porcentaje_descuento_extra` → valor por defecto sugerido para "Descuento Extra". Editable por fila, no se escribe de vuelta al proveedor.
- `dias_credito` → usado para calcular `fecha_vencimiento`.

### Datos generales de la compra
| Campo | Origen | Regla |
|---|---|---|
| Proveedor | select2 | obligatorio |
| Fecha de factura (`fecha_compra`) | input date | obligatorio |
| Folio de factura (`factura`) | input texto | opcional |
| Fecha de vencimiento (`fecha_vencimiento`) | calculada | `fecha_compra + proveedor.dias_credito`, readonly |

### Detalle de productos (una fila por producto, tabla `detalles_compra`)
| Campo | Editable | Regla |
|---|---|---|
| Cantidad | Sí | incrementa `producto.stock` al guardar |
| Producto | Sí (select2 AJAX sobre `productos.buscar`) | — |
| Precio de compra | Sí (autocompletado al elegir producto) | actualiza `producto.precio_compra` al guardar |
| Precio de venta | Sí (autocompletado) | actualiza `producto.precio_venta` al guardar, solo si > 0 |
| Descuento Global (%) | **No** | = `proveedor.porcentaje_descuento_global`, igual en todas las filas |
| Descuento Extra (%) | **Sí, por fila** | precargado con `proveedor.porcentaje_descuento_extra`, el usuario lo puede cambiar por producto |
| Descuento Interno (%) | **Sí, por fila** | promoción puntual del proveedor sobre ese producto, se captura a mano, sin catálogo |
| Subtotal de la fila | No (calculado) | `cantidad × precio_compra` |

### Etapa 1 — Cálculo comercial por producto (cascada, orden exacto)

**Corregido el 2026-09-09 (segunda vez)**: el usuario reportó que, aunque el Saldo Pendiente final ya coincidía con la factura real, el IVA, la suma de Descuentos y el Total de Factura NO coincidían. Causa: el IVA se calculaba PRIMERO (sobre el precio bruto) y los descuentos se cascadeaban después sobre el total-con-IVA. Esto da el mismo Total a Pagar final (la multiplicación es conmutativa), pero infla cada monto de descuento individual y el IVA reportado por el factor 1.16×, y no corresponde a cómo se calcula una factura CFDI real (el SAT calcula el IVA sobre el importe ya con el descuento comercial aplicado). Orden corregido: descuento primero (sobre el precio crudo), IVA al final (sobre la base ya neta):

```
monto_global        = subtotal_fila × pct_global / 100
resto1              = subtotal_fila - monto_global
monto_extra         = resto1 × pct_extra_fila / 100
resto2              = resto1 - monto_extra
monto_interno       = resto2 × pct_interno_fila / 100
base_gravable_fila  = resto2 - monto_interno
iva_fila            = base_gravable_fila × 0.16
total_fila_final    = base_gravable_fila + iva_fila
```

`pct_global` es el mismo para todas las filas de la compra (viene del proveedor). `pct_extra_fila` y `pct_interno_fila` son propios de cada fila. El `total_fila_final` de cada línea es idéntico al que daba el orden anterior (IVA-primero) — es solo el desglose intermedio (monto_global, monto_extra, monto_interno, IVA) el que cambia y ahora sí coincide con una factura real.

### Resumen de totales — Etapa 1 (agregado de todas las filas)

```
Subtotal          = Σ subtotal_fila                    (sin IVA)
IVA               = Σ iva_fila                          (cada línea sobre su propia base neta de descuentos)
Total de Factura  = Subtotal + IVA
Descuento Global  = Σ monto_global
Descuento Extra   = Σ monto_extra
Descuento Interno = Σ monto_interno
Total a Pagar = Σ total_fila_final (de TODAS las filas, incluyendo Maniobra/Seguro — ver abajo)
              = Total de Factura − (Descuento Global + Descuento Extra + Descuento Interno)
```

"Total a Pagar" en esta etapa es un **subtotal intermedio** — todavía no es el número final si la compra tiene Descuento por Pronto Pago (Etapa 2, abajo).

### Maniobra y Seguro (`monto_maniobra`, `aplica_descuento_maniobra`, `monto_seguro`, `aplica_descuento_seguro` — ya existen en `compras`, sin cambios de esquema)

Verificado contra una factura CFDI real de DAPESA (folio 1FC-2633354, UUID `1818531d-b3b6-4631-8424-f037c5940bc3`): un cargo como "Seguro por Envío" es una **línea de concepto más** en la factura real — participa del Subtotal y del IVA igual que un producto. Cada uno (si su monto es > 0) se trata como **una fila más** en la Etapa 1:

```
subtotal_fila      = monto (cantidad = 1)
monto_global       = aplica_descuento ? subtotal_fila × pct_global / 100 : 0
base_gravable_fila = subtotal_fila - monto_global
// Extra e Interno siempre son 0 para Maniobra/Seguro — nunca se les aplican
iva_fila           = base_gravable_fila × 0.16
total_fila_final   = base_gravable_fila + iva_fila
```

Decisión explícita (confirmada con el usuario): a Maniobra/Seguro **nunca** se les aplica Descuento Extra ni Interno, aunque su switch esté activo — porque Extra varía por producto y no hay un producto al que atribuírselo. Descuento Global sí les aplica, pero solo si su propio switch "Aplica descuento" está activo.

Maniobra y Seguro **sí suman** al Subtotal, al IVA y al Total de Factura agregados (igual que un producto), y su cascada de Descuento Global se suma al total de "Descuento Global" mostrado. Solo quedan fuera del desglose por producto (`detalles_compra`), porque no son productos.

### Etapa 2 — Descuento por Pronto Pago / Nota de Crédito (una sola vez, al final)

**Corregido el 2026-09-09**: el usuario reportó un total incorrecto al capturar una factura real de DAPESA junto con su Nota de Crédito. Se detectó que "Descuento Global" (por producto, del proveedor) y "Descuento por Pronto Pago" (financiero, de la Nota de Crédito) son dos cosas distintas — la primera cascada por producto ANTES del IVA de cada línea; la segunda es un **único porcentaje, capturado a mano para toda la compra**, que se aplica **una sola vez sobre el "Total de Factura" ya calculado en la Etapa 1** (nunca por producto, nunca antes del IVA de cada línea):

```
monto_pronto_pago = Total de Factura (Etapa 1) × pct_pronto_pago / 100
Saldo Pendiente   = Total de Factura (Etapa 1) − monto_pronto_pago
```

Si `pct_pronto_pago` es 0 (caso más común), Saldo Pendiente = Total de Factura sin cambio.

**"Saldo Pendiente" es el único número verdaderamente final** de la compra. Se guarda en `compra.total` (= Total de Factura, Etapa 1) y `compra.saldo_pendiente` (= Saldo Pendiente, Etapa 2) — `saldo_pendiente` seguirá bajando después con abonos registrados en Cuentas por Pagar, eso no cambia. Se muestra grande y prominente en la UI como **"Saldo Pendiente"**, con "Total de Factura" visible arriba como el subtotal intermedio.

**Corregido el 2026-09-09 (tercera vez) — orden de la UI**: se eliminó de la UI el antiguo renglón "Total de Factura" que mostraba `Subtotal + IVA` calculado sobre el precio BRUTO (sin descuento) — ese número no corresponde a nada en una factura real y solo generaba ruido/confusión junto al Total de Factura correcto. El renglón que antes se llamaba "Total a Pagar" (`Subtotal − Descuentos + IVA`, el único total intermedio correcto) se renombra a **"Total de Factura"**, y el orden de renglones en `crear`/`editar`/`ver`/`index` (Vista Rápida) ahora sigue el mismo orden que una factura CFDI real:

```
Subtotal
1. Descuento Global
2. Descuento Extra
3. Descuento Interno
IVA (16%)
Total de Factura   ← (antes "Total a Pagar"; el antiguo "Total de Factura" bruto ya no se muestra)
Desc. Financiero / Pronto Pago (Etapa 2)
Saldo Pendiente
```

No cambia ningún valor calculado ni el schema — es un cambio puro de qué se muestra y en qué orden, sobre los mismos campos que `CompraCalculator` ya devuelve (`subtotal`, `descuento_global`, `descuento_extra`, `descuento_interno`, `iva`, `total_a_pagar` — este último es el valor que ahora se etiqueta "Total de Factura" en la UI).

Campo de entrada: input `porcentaje_pronto_pago` a nivel de compra (no por producto, no ligado al proveedor) — existía en la versión original de este módulo, se había eliminado por error en la primera reconstrucción, y se restauró aquí.

### Caso de prueba de referencia (obligatorio en tests)

Un producto, cantidad 1, precio_compra 1000, pct_global 10%, pct_extra 5%, pct_interno 3% (sin Pronto Pago financiero):

| Paso | Valor |
|---|---|
| subtotal_fila | 1000.00 |
| tras Descuento Global (10%) | 900.00 |
| tras Extra (5%) | 855.00 |
| tras Interno (3%) → base_gravable_fila | 829.35 |
| iva_fila (16%) | 132.696 |
| total_fila_final | **962.046** (= Total a Pagar = Saldo Pendiente, sin Pronto Pago) |

### Casos de prueba adicionales requeridos
1. **Caso de referencia** de arriba (un producto, un nivel de cada descuento, Etapa 1 sola).
2. **Dos productos con Descuento Extra distinto** (ej. producto A 5%, producto B 0%) en la misma compra — confirmar que cada fila cascada con su propio % de Extra y que las sumas agregadas sean la suma simple de cada fila.
3. **Maniobra con switch activo** — confirmar `monto_con_iva × (1 - pct_global/100)`, sin restarle Extra.
4. **Maniobra con switch inactivo** — confirmar que solo lleva IVA, sin descuento.
5. **Seguro** — mismos dos casos que Maniobra (activo/inactivo), independiente de Maniobra.
6. **Compra sin Descuento Extra ni Interno** (ambos en 0%) — el Total a Pagar debe coincidir con Total de Factura menos solo el Descuento Global.
7. **Regresión de stock y precios**: al guardar, `producto.stock` incrementa por la cantidad comprada, `producto.precio_compra` y `producto.precio_venta` (si > 0) se actualizan — esto ya existe hoy, no debe romperse.
8. **Maniobra/Seguro integrados**: con producto + Maniobra + Seguro activos, el Subtotal/IVA/Total de Factura deben incluir sus montos (no solo el Total a Pagar).
9. **Descuento por Pronto Pago (Etapa 2)**: con el caso de referencia + `pct_pronto_pago` 10%, confirmar que se aplica una sola vez sobre el Total a Pagar (962.046 × 0.9 = 865.8414), no por producto.
10. **Regresión con factura real + Nota de Crédito** (`tests/Unit/CompraCalculatorTest::test_regresion_factura_real_dapesa_con_nota_de_credito`): 11 productos + 1 cargo de Seguro por Envío de la factura CFDI real de DAPESA (Total a Pagar debe reproducir el Total timbrado por el SAT, $6,549.01), más un 10% de Descuento por Pronto Pago (Nota de Crédito) aplicado una sola vez al final, reproduciendo el Saldo Pendiente verificado por el usuario a mano ($5,894.09) — ambos dentro de un margen de redondeo.

## Qué se elimina

- La lógica de "tomar el % de descuento de la primera/última fila" para determinar un valor global ambiguo — ya no aplica porque Descuento Global es un solo valor fijo (del proveedor) y Extra/Interno son explícitamente por fila.

## Qué se restaura (corregido el 2026-09-09)

La primera reconstrucción de este módulo eliminó por error el input independiente de "Descuento por Pronto Pago (%)" y el paso final de "Saldo Pendiente" distinto de "Total a Pagar", fusionándolos con "Descuento Global". Ambos se restauran (ver Etapa 2 arriba) porque representan conceptos financieros distintos: Global es una condición comercial fija del proveedor aplicada por producto; Pronto Pago/Nota de Crédito es un descuento financiero aplicado una sola vez al final.

Los campos de BD `porcentaje_pronto_pago` y `monto_pronto_pago` en `compras` **vuelven a usarse** (se habían dejado sin uso en la primera reconstrucción; ver "Qué se restaura" arriba) para el descuento financiero de la Etapa 2.

## Terminología (sin cambios de nombre en esta segunda revisión)

"Descuento Global" (columna en tabla de productos, línea en resumen, en `crear`/`editar`/`ver`/`index`) conserva su nombre original — es lo que representa: la condición comercial del proveedor, por producto. "Descuento por Pronto Pago" es el concepto distinto de la Etapa 2 (financiero, una sola vez, sobre toda la compra). `porcentaje_descuento`/`monto_descuento` en `Compra` guardan el Descuento Global (Etapa 1); `porcentaje_pronto_pago`/`monto_pronto_pago` guardan el descuento financiero (Etapa 2).

## Arquitectura (para evitar el bug original: fórmula duplicada e inconsistente)

- **Una sola clase de cálculo en PHP**, `App\Services\CompraCalculator`, con un método puro (sin DB, sin Request) que recibe: lista de filas `{cantidad, precio_compra, pct_extra, pct_interno}`, `pct_global` (de la compra/proveedor), `monto_maniobra` + `aplica_descuento_maniobra`, `monto_seguro` + `aplica_descuento_seguro`, `pct_pronto_pago` (financiero, Etapa 2); y devuelve un array con Subtotal, IVA, Total de Factura, los 3 descuentos agregados de Etapa 1, Total a Pagar, monto y Saldo Pendiente de Etapa 2. `store()` y `update()` del controlador usan **la misma instancia/método**, eliminando la duplicación que causó el bug original (una fórmula en `store()`, otra ligeramente distinta en `update()`).
- Esta clase es 100% unit-testeable sin Laravel HTTP/DB (recibe primitivos, devuelve primitivos) — ver Testing Strategy.
- El JS de `crear.blade.php`/`editar.blade.php` (`calculateTotal()`) replica la misma fórmula paso a paso, con nombres de variable que calcan los nombres de esta spec (`montoGlobal`, `resto1`, `montoExtra`, `resto2`, `montoInterno`, `baseGravableFila`, `ivaFila`, `totalFilaFinal`) para que sea trivial auditar que JS y PHP coinciden línea por línea.

## Alcance

**Sí (dentro de este trabajo):**
- `resources/views/compras/crear.blade.php`
- `resources/views/compras/editar.blade.php`
- `resources/views/compras/ver.blade.php`
- `resources/views/compras/index.blade.php`
- `app/Http/Controllers/CompraController.php` (`index`, `store`, `edit`, `update`, `show`)
- Nueva clase `app/Services/CompraCalculator.php` (o ubicación equivalente) + sus tests unitarios
- Mantener intactos: botón "Nuevo Producto", botón "Fila Manual", auto-creación de fila al seleccionar producto en la última fila

**No (fuera de alcance):**
- Módulo Cuentas por Pagar (solo se sigue alimentando de `compra.saldo_pendiente`/`compra.total`, sin tocar su lógica)
- Módulo Ventas
- Módulo Inventario/Productos (solo se actualizan sus campos vía los side-effects que ya existen)
- Cualquier migración de esquema (los campos necesarios ya existen en `compras` y `detalles_compra`)

## Boundaries

- **Siempre**: correr los tests unitarios de `CompraCalculator` antes de dar por terminada cualquier tarea de cálculo; verificar el caso de referencia (962.046) exacto.
- **Preguntar antes**: cualquier cambio de esquema (agregar/quitar columnas), incluida la limpieza de `porcentaje_pronto_pago`/`monto_pronto_pago` sin uso.
- **Nunca**: tocar el módulo de Cuentas por Pagar, Ventas o la lógica propia de Productos/Inventario; tocar datos de compras ya guardadas en producción sin pedirlo explícitamente.

## Testing Strategy

- **Unit** (`tests/Unit/CompraCalculatorTest.php`): todos los casos de prueba listados arriba, contra `App\Services\CompraCalculator` directamente, sin DB.
- **Feature** (`tests/Feature/CompraControllerTest.php`): al menos un test de `store()` end-to-end (request → DB) que verifique que `compras.total`, `compras.saldo_pendiente`, `compras.iva` y los `detalles_compra.descuento_*` quedan exactamente como predice el caso de referencia; y un test de que el stock/precio del producto se actualiza.
- Verificación manual en navegador (agent-browser) de que el resumen en pantalla (`crear`/`editar`) coincide con lo guardado, igual que se hizo en la iteración anterior.

## Success Criteria

1. El caso de referencia (962.046) se reproduce exacto en el test unitario y en la UI (capturando esos valores en el navegador).
2. `store()` y `update()` usan la misma clase de cálculo — cero fórmulas duplicadas entre ambos métodos.
3. Dos productos con Descuento Extra distinto en la misma compra dan el resultado correcto por fila (verificado en test + navegador).
4. Maniobra/Seguro con switch activo/inactivo dan los 4 resultados esperados (verificado en test).
5. La UI muestra el input "Descuento por Pronto Pago (%)" (a nivel de compra) y, cuando su valor es > 0, un paso "Saldo Pendiente (Final)" distinto y posterior a "Total a Pagar".
6. "Descuento Global" conserva su nombre en `crear`, `editar`, `ver` e `index` — no se renombra.
7. Botones "Nuevo Producto", "Fila Manual" y auto-creación de fila siguen funcionando igual que hoy.
8. El caso de la factura real de DAPESA + Nota de Crédito (10% sobre el Total a Pagar) reproduce el Saldo Pendiente verificado a mano por el usuario ($5,894.09), dentro de margen de redondeo.
9. `git diff main` limpio y explicable: nada fuera del alcance listado arriba se modifica.

## Open Questions

Ninguna pendiente — todas resueltas en la entrevista previa y en la segunda revisión motivada por la factura real + Nota de Crédito (Extra por fila, Maniobra/Seguro solo con Descuento Global, Descuento por Pronto Pago restaurado como concepto separado aplicado una sola vez al final).
