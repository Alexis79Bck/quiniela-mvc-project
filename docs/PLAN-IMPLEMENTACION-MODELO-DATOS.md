# Plan de Implementación: Modelado de Datos Optimizado

Este documento detalla un plan de implementación incremental para optimizar el modelo de datos de la quiniela FIFA 2026, basado en el análisis de alternativas en `ALTERNATIVAS-DISEÑO-BD.md`. El enfoque es mejorar el rendimiento, la trazabilidad y la escalabilidad sin rediseñar completamente la base de datos existente.

## 1. Estado Actual del Modelo de Datos

### Estructura Existente
- **Tablas principales**: `users`, `teams`, `groups`, `matches`, `stages`, `predictions`
- **Tablas de Spatie**: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`
- **Tablas de auditoría**: `activity_log` (Spatie Activitylog)
- **Relaciones**: Bien definidas y normalizadas según el documento `MODELO-DE-DATOS.md`

### Puntos Fuertes
- Modelo relacional limpio y normalizado.
- Separación clara de dominios (usuarios, equipos, partidos, predicciones).
- Integración con Spatie para roles/permisos y auditoría.

### Áreas de Mejora Identificadas
- Falta de campos calculados para acelerar consultas de leaderboard.
- Ausencia de historial de cambios en predicciones.
- Limitaciones en estadísticas acumuladas de equipos.
- Permisos podrían ser más granulares para roles administrativos.

## 2. Objetivos de Optimización

### Rendimiento
- Reducir consultas complejas en leaderboard y estadísticas.
- Almacenar resultados calculados para lecturas rápidas.

### Trazabilidad
- Registrar cambios en predicciones editables.
- Mantener historial de estados de partidos.

### Escalabilidad
- Preparar para crecimiento en usuarios y partidos.
- Facilitar reportes y análisis.

### Mantenibilidad
- Mantener compatibilidad con código existente.
- Implementar cambios de forma incremental.

## 3. Cambios Propuestos

### Fase 1: Campos Calculados y Caché de Resultados

#### Migración: Agregar campos calculados
```php
// database/migrations/xxxx_xx_xx_add_calculated_fields.php
Schema::table('predictions', function (Blueprint $table) {
    $table->integer('points_earned')->default(0)->change(); // Ya existe, asegurar default
    $table->json('scoring_details')->nullable(); // Detalle de cómo se calcularon los puntos
});

Schema::table('users', function (Blueprint $table) {
    $table->integer('total_predictions')->default(0);
    $table->integer('correct_predictions')->default(0);
    $table->decimal('accuracy_percentage', 5, 2)->default(0.00);
});

Schema::table('matches', function (Blueprint $table) {
    $table->json('result_details')->nullable(); // Cache del resultado final
});
```

#### Modelo: Actualizar fillable y casts
```php
// app/Models/Prediction.php
protected $fillable = [/* existente */, 'scoring_details'];
protected $casts = [/* existente */, 'scoring_details' => 'array'];

// app/Models/User.php
protected $fillable = [/* existente */, 'total_predictions', 'correct_predictions', 'accuracy_percentage'];

// app/Models/Match.php
protected $casts = [/* existente */, 'result_details' => 'array'];
```

### Fase 2: Historial de Predicciones

#### Nueva tabla: prediction_history
```php
// database/migrations/xxxx_xx_xx_create_prediction_history_table.php
Schema::create('prediction_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('prediction_id')->constrained()->onDelete('cascade');
    $table->integer('old_home_score');
    $table->integer('old_away_score');
    $table->integer('new_home_score');
    $table->integer('new_away_score');
    $table->string('changed_by')->nullable(); // user_id o 'system'
    $table->text('change_reason')->nullable();
    $table->timestamps();
});
```

#### Modelo: PredictionHistory
```php
// app/Models/PredictionHistory.php
class PredictionHistory extends Model
{
    protected $fillable = [
        'prediction_id', 'old_home_score', 'old_away_score',
        'new_home_score', 'new_away_score', 'changed_by', 'change_reason'
    ];

    public function prediction()
    {
        return $this->belongsTo(Prediction::class);
    }
}
```

### Fase 3: Estadísticas de Equipos

#### Nueva tabla: team_statistics
```php
// database/migrations/xxxx_xx_xx_create_team_statistics_table.php
Schema::create('team_statistics', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id')->constrained()->onDelete('cascade');
    $table->foreignId('stage_id')->constrained()->onDelete('cascade');
    $table->integer('played')->default(0);
    $table->integer('won')->default(0);
    $table->integer('drawn')->default(0);
    $table->integer('lost')->default(0);
    $table->integer('goals_for')->default(0);
    $table->integer('goals_against')->default(0);
    $table->integer('goal_difference')->default(0);
    $table->integer('points')->default(0);
    $table->timestamps();

    $table->unique(['team_id', 'stage_id']);
});
```

#### Modelo: TeamStatistic
```php
// app/Models/TeamStatistic.php
class TeamStatistic extends Model
{
    protected $fillable = [
        'team_id', 'stage_id', 'played', 'won', 'drawn', 'lost',
        'goals_for', 'goals_against', 'goal_difference', 'points'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}
```

### Fase 4: Permisos Más Granulares

#### Actualizar seeder: RolePermissionSeeder
```php
// database/seeders/RolePermissionSeeder.php
$permissions = [
    // Existentes...
    'prediction.view_all',
    'prediction.manage',
    'match.view',
    'stage.manage',
    'team.view',
    'leaderboard.manage',
    'audit.view',
];

// Actualizar asignaciones de roles...
```

## 4. Plan de Implementación por Fases

### Fase 1: Campos Calculados (1-2 días)
1. Crear migración para campos calculados.
2. Actualizar modelos con nuevos campos.
3. Crear job para recalcular valores existentes.
4. Actualizar lógica de puntuación para poblar campos.
5. Ejecutar tests y validar.

### Fase 2: Historial de Predicciones (1 día)
1. Crear migración y modelo para prediction_history.
2. Actualizar PredictionController para registrar cambios.
3. Crear observer o event listener para cambios.
4. Agregar tests para historial.

### Fase 3: Estadísticas de Equipos (2 días)
1. Crear migración y modelo para team_statistics.
2. Crear service para calcular estadísticas.
3. Integrar en MatchController o jobs post-partido.
4. Crear endpoint para consultar estadísticas.

### Fase 4: Permisos Granulares (1 día)
1. Actualizar RolePermissionSeeder con nuevos permisos.
2. Ejecutar seeder para actualizar permisos.
3. Revisar middleware si es necesario.

### Fase 5: Testing y Optimización (2 días)
1. Ejecutar suite completa de tests.
2. Optimizar consultas con índices si es necesario.
3. Crear benchmarks para leaderboard.
4. Documentar cambios en README y docs.

## 5. Riesgos y Mitigaciones

### Riesgos
- **Inconsistencia de datos**: Campos calculados pueden desactualizarse.
- **Rendimiento**: Nuevas tablas pueden ralentizar inserts.
- **Compatibilidad**: Cambios en modelos pueden afectar código existente.

### Mitigaciones
- Usar database transactions para actualizaciones.
- Implementar jobs en cola para cálculos pesados.
- Mantener versiones de API y gradual rollout.
- Tests exhaustivos antes de deploy.

## 6. Testing y Validación

### Tests Requeridos
- Unit tests para nuevos modelos.
- Feature tests para endpoints con nuevos campos.
- Tests de performance para leaderboard.
- Tests de integridad referencial.

### Comando de Validación
```bash
php artisan test --compact
php artisan migrate:status
php artisan db:seed --class=RolePermissionSeeder
```

## 7. Métricas de Éxito

- Reducción del 50% en tiempo de consulta de leaderboard.
- Cobertura de tests >90%.
- Sin downtime en producción durante implementación.
- Compatibilidad total con código frontend existente.

## 8. Conclusión

Este plan permite optimizar el modelo de datos de forma incremental, manteniendo la estabilidad del sistema actual. Las mejoras se centran en rendimiento de lectura, trazabilidad y preparación para escalabilidad, sin requerir un rediseño completo.

**Tiempo estimado total**: 7-8 días de desarrollo.
**Riesgo**: Bajo, con implementación por fases.
**Beneficio**: Mejor UX, mayor escalabilidad y preparación para crecimiento.