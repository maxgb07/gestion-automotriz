# Lecciones Aprendidas — gestion-automotriz

> Se actualiza después de cada corrección del usuario. Revisar al inicio de cada sesión.

---

## Formato

```
### [YYYY-MM-DD] Título del patrón aprendido
**Error cometido:** descripción breve
**Causa raíz:** por qué ocurrió
**Regla a seguir:** instrucción concreta para evitar repetirlo
```

---

<!-- Las lecciones se irán añadiendo aquí -->

### [2026-04-04] Usar mb_strtoupper para mayúsculas con acentos
**Error cometido:** Usar `strtoupper()` para convertir a mayúsculas texto con caracteres acentuados (é, á, ó, etc.).
**Causa raíz:** `strtoupper()` solo opera en ASCII; no convierte caracteres multibyte, dejando 'é' como 'é' en lugar de 'É'.
**Regla a seguir:** En este proyecto (Laravel + UTF-8) siempre usar `mb_strtoupper($str, 'UTF-8')` para cualquier conversión a mayúsculas de texto en español.

### [2026-04-07] Colisiones de IDs en SweetAlert2
**Error cometido:** Lectura de valores incorrectos (saldos totales en vez de montos manuales) usando `document.getElementById`.
**Causa raíz:** SweetAlert2 re-renderiza el HTML en el DOM. Si hay colisiones de IDs con elementos ocultos o previos, `getElementById` devuelve el primero que encuentra, ignorando el input activo.
**Regla a seguir:** Dentro de `preConfirm` o eventos de un modal, usar `Swal.getPopup().querySelector('#id')` para garantizar la selección del elemento activo.

### [2026-04-07] Atributos dinámicos en Eloquent durante bucles
**Error cometido:** Error de SQL "Column not found" al guardar modelos en el controlador de crédito.
**Causa raíz:** Se agregaron propiedades temporales (`tipo_doc`, `fecha_doc`) a los modelos para ordenamiento global y Eloquent intentó persistirlas al llamar a `save()`.
**Regla a seguir:** Siempre ejecutar `unset($model->atributo)` para cualquier propiedad virtual antes de llamar a `save()` si el modelo fue manipulado dinámicamente.

### [2026-04-07] Sincronización de Selección en el DOM
**Error cometido:** Desfase entre los elementos marcados visualmente y los datos enviados al servidor.
**Causa raíz:** El uso de una variable global (`docsSeleccionadosLote`) que se actualizaba en `onchange` fallaba si el usuario refrescaba parcialmente la página o si el estado del DOM cambiaba sin disparar el evento.
**Regla a seguir:** Re-escanear el DOM mediante `querySelectorAll(':checked')` en el momento exacto en que se dispara la acción (ej: clic en botón de pago) para obtener la verdad actual.

### [2026-05-01] Consistencia de Rutas y Métodos en Controladores
**Error cometido:** `Call to undefined method App\Http\Controllers\VentaController::registrarFactura()` al intentar facturar una venta.
**Causa raíz:** En `web.php` se apuntaba la ruta `ventas.factura.store` al método `registrarFactura` del `VentaController`, pero en el controlador el método se llamaba `facturar`.
**Regla a seguir:** Mantener consistencia en los nombres de los métodos entre las definiciones de rutas (`web.php`), las llamadas AJAX en el frontend, y la implementación en el controlador. Al implementar o copiar lógicas de otros controladores (ej. `OrdenServicioController`), asegurarse de renombrar y estandarizar los métodos correspondientes.
