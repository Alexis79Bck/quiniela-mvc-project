# Modelo de Datos

## Entidades Principales

### Users
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| fullname | string | Nombre completo |
| username | string | Nombre de usuario único |
| email | string | Email único |
| password | string | Hash bcrypt |
| points | integer | Puntos acumulados |
| created_at | timestamp | |
| updated_at | timestamp | |

### Teams
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| name | string | Nombre del equipo |
| fifa_code | string | Código FIFA (ARG, BRA...) |
| flag | string | URL del escudo |
| group_id | bigint | FK group_id |
| created_at | timestamp | |

### Groups
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| name | string | Grupo (A, B, C...) |
| created_at | timestamp | |

### Matches
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| home_team_id | bigint | FK teams(id) |
| away_team_id | bigint | FK teams(id) |
| stage_id | bigint | FK stages(id) |
| match_date | datetime | Fecha partido |
| home_score | integer | Goles local (null si no jugado) |
| away_score | integer | Goles visitante |
| status | enum | scheduled, in_progress, finished |
| created_at | timestamp | |

### Stages
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| name | string | group_stage, round_16, quarter... |
| order | integer | Orden de eliminación |
| is_active | boolean | Etapa actual |
| created_at | timestamp | |

### Predictions
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| user_id | bigint | FK users(id) |
| match_id | bigint | FK matches(id) |
| home_score | integer | Predicción local |
| away_score | integer | Predicción visitante |
| points_earned | integer | Puntos obtenidos |
| status | enum | pending, correct, incorrect |
| created_at | timestamp | |
| updated_at | timestamp | |

### Permissions (Spatie)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| name | string | prediction.create, admin... |
| guard_name | string | web/api |
| created_at | timestamp | |

### Role (Spatie)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | PK |
| name | string | admin, user |
| guard_name | string | web |
| created_at | timestamp | |

### Model Has Permissions (Spatie)
Tabla pivote para permisos de usuarios.

### Model Has Roles (Spatie)
Tabla pivote para roles de usuarios.

## Relaciones

```
User 1───* Prediction
Match 1───* Prediction
Match *───1 Team (home)
Match *───1 Team (away)
Match *───1 Stage
Team *───1 Group
Stage 1───* Match
User *───* Role (via model_has_roles)
User *───* Permission (via model_has_permissions)
```

## Esquema de Eliminación

### Copa Mundial 2026 (48 equipos)

**Fase de Grupos**
- 8 grupos × 6 equipos = 48 equipos
- 8 × 6 × 5/2 = 72 partidos = 48 theoretical matches per group = 288

**16avos de Final**
- 16 partidos (ganadores de grupos + 2do mejor 3ro)

**Octavos**
- 8 partidos

**Cuartos**
- 4 partidos

**Semifinales**
- 2 partidos

**Final**
- 1 partido

**Total**: 48 + 16 + 8 + 4 + 2 + 1 = 79 partidos