# Plan de Desarrollo e Implementación

## Descripción General del Proyecto

Plataforma de quinielas para la Copa Mundial 2026 que permite a usuarios registrados predecir resultados de partidos, acumular puntos y competir en clasificaciones.

---

## FASE 1: Configuración del Entorno y Base de Datos

### 1.1 Instalación de Dependencias

```bash
# Instalar dependencias Composer
composer install

# Generar clave de aplicación
php artisan key:generate

# Limpiar caché
php artisan config:clear
```

### 1.2 Configuración de Base de Datos

1. Configurar `.env` para SQLite o MySQL
2. Ejecutar migraciones:
   ```bash
   php artisan migrate
   ```

### 1.3 Migraciones Requeridas

| Orden | Archivo | Descripción |
|-------|---------|-------------|
| 1 | `create_users_table` | Tabla de usuarios con points |
| 2 | `create_groups_table` | Grupos del torneo |
| 3 | `create_teams_table` | Equipos con relación a grupos |
| 4 | `create_stages_table` | Etapas del torneo |
| 5 | `create_matches_table` | Partidos con relación a equipos y etapas |
| 6 | `create_predictions_table` | Predicciones de usuarios |
| 7 | Permisos Spatie | Migration de roles y permisos |

### 1.4 Seeders Iniciales

```bash
php artisan db:seed --class=StageSeeder
php artisan db:seed --class=GroupSeeder
php artisan db:seed --class=TeamSeeder
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
```

### Tests: Fase 1

- **Unit Test**: Verificar que las migraciones se ejecutan correctamente
- **Feature Test**: Verificar que los seeders cargan datos iniciales

---

## FASE 2: Modelos y Relaciones Eloquent

### 2.1 Modelos a Crear

| Modelo | Ubicación | Relaciones |
|--------|-----------|------------|
| User | `app/Models/User.php` | hasMany(Prediction), HasRoles |
| Team | `app/Models/Team.php` | belongsTo(Group), belongsToMany(Match) |
| Group | `app/Models/Group.php` | hasMany(Team) |
| Stage | `app/Models/Stage.php` | hasMany(Match) |
| Match | `app/Models/Match.php` | belongsTo(Team, home/away), belongsTo(Stage), hasMany(Prediction) |
| Prediction | `app/Models/Prediction.php` | belongsTo(User), belongsTo(Match) |

### 2.2 Traits Requeridos

- `Spatie\Permission\Traits\HasRoles` en User

### Tests: Fase 2

- **Unit Test**: Probar relaciones Eloquent entre modelos
- **Unit Test**: Verificar que `User::hasRole()` funciona correctamente

---

## FASE 3: Autenticación (API)

### 3.1 endpoints de Auth

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/register` | Registrar nuevo usuario |
| POST | `/api/login` | Iniciar sesión |
| POST | `/api/logout` | Cerrar sesión |

### 3.2 Componentes Requeridos

- **Controller**: `AuthController`
- **Request**: `RegisterRequest`, `LoginRequest`
- **Service**: `AuthService`
- **Route**: Middleware `auth:sanctum`

### 3.3 Configuración de Sanctum

1. Publicar configuración: `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
2. Agregar `HasApiTokens` trait al modelo User

### Tests: Fase 3

- **Feature Test**: `test_user_can_register`
- **Feature Test**: `test_user_can_login`
- **Feature Test**: `test_user_can_logout`
- **Feature Test**: `test_unauthenticated_user_cannot_access_protected_routes`

---

## FASE 4: Gestión de Equipos (API)

### 4.1 Endpoints

| Método | Endpoint | Permiso Requerido |
|--------|----------|------------------|
| GET | `/api/teams` | - |
| GET | `/api/teams?group=A` | - |
| GET | `/api/teams/{id}` | - |
| POST | `/api/teams` | team.manage |
| PUT | `/api/teams/{id}` | team.manage |
| DELETE | `/api/teams/{id}` | team.manage |

### 4.2 Componentes

- **Controller**: `TeamController`
- **Request**: `StoreTeamRequest`, `UpdateTeamRequest`
- **Service**: `TeamService`
- **Resource**: `TeamResource`

### Tests: Fase 4

- **Feature Test**: `test_admin_can_create_team`
- **Feature Test**: `test_user_cannot_create_team`
- **Feature Test**: `test_can_list_teams_by_group`
- **Feature Test**: `test_team_validation_fails_with_invalid_data`

---

## FASE 5: Gestión de Partidos (API)

### 5.1 Endpoints

| Método | Endpoint | Permiso Requerido |
|--------|----------|------------------|
| GET | `/api/matches` | - |
| GET | `/api/matches?stage=group_stage` | - |
| GET | `/api/matches/{id}` | - |
| POST | `/api/matches` | match.manage |
| PUT | `/api/matches/{id}` | match.manage |
| PUT | `/api/matches/{id}/result` | match.manage (registrar resultado) |

### 5.2 Estados de Partido

- `scheduled` - Programado
- `in_progress` - En juego
- `finished` - Finalizado

### 5.3 Componentes

- **Controller**: `MatchController`
- **Request**: `StoreMatchRequest`, `UpdateMatchRequest`
- **Service**: `MatchService`
- **Resource**: `MatchResource`

### Tests: Fase 5

- **Feature Test**: `test_admin_can_create_match`
- **Feature Test**: `test_admin_can_register_match_result`
- **Feature Test**: `test_cannot_register_result_for_non_existent_match`
- **Feature Test**: `test_matches_are_filterable_by_stage`

---

## FASE 6: Sistema de Predicciones (API)

### 6.1 Endpoints

| Método | Endpoint | Permiso Requerido |
|--------|----------|------------------|
| POST | `/api/predictions` | prediction.create |
| GET | `/api/predictions` | - |
| GET | `/api/predictions/{id}` | - |
| PUT | `/api/predictions/{id}` | prediction.edit_own |

### 6.2 Reglas de Negocio

- Una predicción por partido por usuario
- No se puede predecir partidos con estado "finished"
- Hora límite: 1 hora antes del partido
- Solo el propietario puede editar su predicción

### 6.3 Componentes

- **Controller**: `PredictionController`
- **Request**: `StorePredictionRequest`, `UpdatePredictionRequest`
- **Service**: `PredictionService`
- **Repository**: `PredictionRepository`
- **Resource**: `PredictionResource`

### Tests: Fase 6

- **Feature Test**: `test_user_can_create_prediction`
- **Feature Test**: `test_user_cannot_create_two_predictions_for_same_match`
- **Feature Test**: `test_user_cannot_predict_finished_match`
- **Feature Test**: `test_prediction_must_be_made_1_hour_before_match`
- **Feature Test**: `test_user_can_update_own_prediction`
- **Feature Test**: `test_user_cannot_update_other_user_prediction`

---

## FASE 7: Sistema de Puntuación

### 7.1 Lógica de Puntos

| Acierto | Puntos |
|---------|--------|
| Marcador exacto (fase grupos) | 10 |
| Marcador exacto (eliminación) | 15 |
| Ganador/Empate correcto | 5 (grupos) / 7-8 (eliminación) |
| Goles de equipo (no exacto) | 1 |

### 7.2 Componentes

- **Service**: `ScoringService`
- **Strategy**: `ExactScoreStrategy`, `ResultOnlyStrategy`, `BonusPointsStrategy`
- **Comando**: `php artisan predictions:recalculate`

### 7.3 Cálculo Automático

El cálculo se ejecuta cuando:
1. Un partido cambia a estado "finished"
2. Se ejecuta el comando `predictions:recalculate`

### Tests: Fase 7

- **Unit Test**: `test_exact_score_returns_10_points`
- **Unit Test**: `test_correct_winner_returns_5_points`
- **Unit Test**: `test_incorrect_prediction_returns_0_points`
- **Unit Test**: `test_bonus_points_for_team_goals`

---

## FASE 8: Sistema de Clasificación (Leaderboard)

### 8.1 Endpoints

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/leaderboard` | Clasificación general |
| GET | `/api/leaderboard?stage=round_16` | Clasificación por etapa |

### 8.2 Datos de Respuesta

- Posición del usuario
- Puntuación total
- Número de aciertos (totales)
- Comparación con top 3

### 8.3 Componentes

- **Controller**: `LeaderboardController`
- **Service**: `LeaderboardService`
- **Resource**: `LeaderboardResource`

### Tests: Fase 8

- **Feature Test**: `test_leaderboard_returns_ordered_users`
- **Feature Test**: `test_leaderboard_includes_user_rank`
- **Feature Test**: `test_leaderboard_can_filter_by_stage`

---

## FASE 9: Roles y Permisos (Admin)

### 9.1 Permisos del Sistema

| Permiso | Descripción |
|---------|-------------|
| prediction.create | Crear predicciones |
| prediction.edit_own | Editar predicciones propias |
| prediction.view_all | Ver predicciones de otros |
| match.manage | Gestionar partidos |
| team.manage | Gestionar equipos |
| user.manage | Gestionar usuarios |
| leaderboard.view | Ver clasificación |
| admin.access | Panel de admin |

### 9.2 Roles

| Rol | Permisos |
|-----|----------|
| admin | Todos |
| user | prediction.create, prediction.edit_own, leaderboard.view |

### 9.3 Endpoints Admin

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/admin/users` | Listar usuarios |
| PUT | `/api/admin/users/{id}/role` | Asignar rol |
| GET | `/api/admin/permissions` | Listar permisos |

### Tests: Fase 9

- **Feature Test**: `test_admin_can_assign_role_to_user`
- **Feature Test**: `test_middleware_blocks_unauthorized_access`

---

## FASE 10: Tests de Integración

### 10.1 Flujo Completo de Usuario

1. Registrarse → Obtener token
2. Listar partidos → Seleccionar partido
3. Crear predicción
4. Ver predicción creada
5. Ver posición en leaderboard

### 10.2 Flujo Completo de Admin

1. Iniciar sesión como admin
2. Crear equipo
3. Crear partido
4. Asignar rol a usuario
5. Registrar resultado de partido

### 10.3 Tests de Integración

- **Feature Test**: `test_full_user_prediction_workflow`
- **Feature Test**: `test_full_admin_workflow`
- **Feature Test**: `test_points_are_calculated_on_match_completion`

---

## Resumen de Ejecución por Fases

| Fase | Descripción | Tests Mínimos |
|------|-------------|---------------|
| 1 | Entorno y Base de Datos | 2 |
| 2 | Modelos Eloquent | 2 |
| 3 | Autenticación | 4 |
| 4 | Equipos | 4 |
| 5 | Partidos | 4 |
| 6 | Predicciones | 6 |
| 7 | Puntuación | 4 |
| 8 | Leaderboard | 3 |
| 9 | Roles/Permisos | 2 |
| 10 | Integración | 3 |
| **Total** | | **34 tests** |

## Comandos de Ejecución de Tests

```bash
# Ejecutar todos los tests
php artisan test --compact

# Ejecutar tests de una fase específica
php artisan test --filter="Phase3"

# Ejecutar un test específico
php artisan test --filter="test_user_can_register"
```