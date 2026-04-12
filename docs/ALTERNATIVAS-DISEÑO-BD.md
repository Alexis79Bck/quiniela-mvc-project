# Alternativas de Diseño de Base de Datos

Este documento analiza el modelo de datos propuesto en `docs/MODELO-DE-DATOS.md` y propone alternativas de diseño para una implementación robusta, escalable y alineada con las necesidades de una quiniela de la Copa Mundial 2026.

## 1. Diseño actual sugerido

El modelo de datos actual propone las siguientes entidades principales:

- `users`
- `teams`
- `groups`
- `matches`
- `stages`
- `predictions`
- `roles` / `permissions` y las tablas pivote de Spatie

### Relaciones clave

- `User` 1–* `Prediction`
- `Match` 1–* `Prediction`
- `Match` *–1 `Team` (home)
- `Match` *–1 `Team` (away)
- `Match` *–1 `Stage`
- `Team` *–1 `Group`
- `Stage` 1–* `Match`
- `User` *–* `Role`
- `User` *–* `Permission`

### Observaciones del diseño actual

- Es un buen modelo relacional clásico y normalizado.
- Se separan bien los dominios de juego (`teams`, `groups`, `matches`, `stages`, `predictions`).
- El uso de Spatie para roles y permisos es estándar y adecuado.
- El privilegio de la auditoría puede implementarse con `activity_log` sin cambios en el modelo principal.

## 2. Alternativa 1: Normalización completa con detalles de partido

### Tablas adicionales recomendadas

- `team_statistics`: estadísticas acumuladas por equipo en la competencia.
- `stage_team`: relación entre `stage` y `team` para saber qué equipos avanzan.
- `match_events`: registro de eventos de partido (gol, tarjeta, cambio) si se requiere detalle histórico.
- `prediction_history`: versiones de predicción cuando se permite edición antes del cierre.

### Ventajas

- Mejor trazabilidad de cambios y auditoría interna.
- Flexibilidad para reportes de estadísticas y seguimiento de fases.
- Evita almacenamiento redundante de información derivada.

### Desventajas

- Mayor complejidad en consultas y joins.
- Más tablas que manejar en migraciones y seeders.

## 3. Alternativa 2: Diseño orientado a reporting / analítica

### Esquema estrella básico

- `fact_predictions`
- `dim_users`
- `dim_matches`
- `dim_teams`
- `dim_stages`
- `dim_groups`

### Uso sugerido

- Ideal para paneles de clasificación y análisis de rendimiento.
- Se puede cargar desde el modelo transaccional mediante jobs programados.

### Ventajas

- Consultas agregadas y dashboards muy rápidas.
- Separación entre datos transaccionales y datos analíticos.

### Desventajas

- Requiere proceso ETL adicional.
- Duplicación de datos y mayor mantenimiento.

## 4. Alternativa 3: Diseño híbrido con caché de resultados y puntuación

### Columnas adicionales en tablas transaccionales

- `matches`.`result` o `final_score`
- `predictions`.`points_earned`
- `users`.`points` (ya presente)
- `stages`.`is_active`

### Recomendación

Mantener el diseño relacional, pero almacenar campos calculados en la tabla `predictions` y `users` para acelerar leaderboard y consultas de ranking.

### Ventajas

- Lecturas más rápidas para la UI.
- Menos recalculación en cada request.

### Desventajas

- Debe garantizarse la coherencia mediante jobs o eventos.
- Riesgo de datos desactualizados si no se sincroniza correctamente.

## 5. Alternativa 4: Extensión de permisos y roles del dominio de quiniela

### Permisos adicionales sugeridos

- `prediction.view_all`
- `prediction.manage`
- `match.view`
- `stage.manage`
- `team.view`
- `leaderboard.manage`

### Estructura de roles

- `SuperAdmin`: todos los permisos.
- `Administrador`: gestión de usuarios, partidos, equipos, predicciones y etapas.
- `Jugador`: predicciones y visualización de leaderboard.
- `Auditor`: solo lectura de logs y resultados.

### Ventajas

- Mayor granularidad en el control de acceso.
- Facilita que el backend soporte perfilamientos futuros.

## 6. Alternativa 5: Diseño para la fase de grupos con equipos de 6 y 48 partidos

### Ajuste de `groups` y `teams`

- `groups`: 8 grupos.
- `teams`: 48 equipos.
- `matches`: 72 partidos de fase de grupos.

### Consideración para el cálculo de eliminatorias

- `stage` puede contener `order` y `is_active`.
- Se recomienda una tabla intermedia `stage_matches` si se requiere gestión dinámica de llaves.

### Ventaja

- Claridad en la generación de fase de grupos y transición a eliminación.

## 7. Recomendación final

### Mejor alternativa para este proyecto

Mantener el modelo relacional actual y aplicar una extensión gradual con:

1. `prediction_history` si se permite edición de pronósticos.
2. `team_statistics` si se requiere métricas históricas de equipos.
3. Campos calculados (`points_earned`, `users.points`) para acelerar leaderboards.
4. Permisos y roles más finos si el producto se amplía a roles administrativos específicos.

### Por qué

- El modelo actual es limpio y bien normalizado.
- La mayoría de los requerimientos de una quiniela se resuelven con el diagrama existente.
- Las mejoras sugeridas agregan valor sin cambiar drásticamente la base actual.

## 8. Ejemplo de esquema relacional resumido

```
users
teams
groups
stages
matches
predictions
roles
permissions
model_has_roles
model_has_permissions

# Opcional
prediction_history
team_statistics
match_events
stage_team
```

## 9. Conclusión

El diseño propuesto en `docs/MODELO-DE-DATOS.md` es una base sólida. Las alternativas presentadas permiten elegir entre rendimiento de lectura, auditabilidad y trazabilidad, sin sacrificar la claridad de los datos de partido.

La mejor ruta para este proyecto es mantener el diseño relacional y ampliar con una capa de resultados calculados y registros históricos solo si la complejidad del producto lo requiere.
