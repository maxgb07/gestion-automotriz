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

