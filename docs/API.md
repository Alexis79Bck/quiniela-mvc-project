# API RESTful

## Autenticación

### Registrarse
```
POST /api/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@email.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Iniciar Sesión
```
POST /api/login
Content-Type: application/json

{
  "email": "juan@email.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Juan Pérez",
      "email": "juan@email.com"
    },
    "token": "1|a1b2c3d4..."
  }
}
```

### Cerrar Sesión
```
POST /api/logout
Authorization: Bearer {token}
```

## Partidos

### Listar Partidos
```
GET /api/matches?stage=group_stage
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "home_team": {
        "id": 1,
        "name": "Argentina",
        "code": "ARG"
      },
      "away_team": {
        "id": 2,
        "name": "Brasil",
        "code": "BRA"
      },
      "match_date": "2026-06-11 16:00:00",
      "status": "scheduled"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 48
  }
}
```

### Ver Partido
```
GET /api/matches/{id}
Authorization: Bearer {token}
```

## Predicciones

### Crear Predicción
```
POST /api/predictions
Authorization: Bearer {token}
Content-Type: application/json

{
  "match_id": 1,
  "home_score": 2,
  "away_score": 1
}
```

**Reglas:**
- Solo una predicción por partido por usuario
- No se puede predecir partidos con estado "finished"
- Hora límite: 1 hora antes del partido

**Response (201):**
```json
{
  "message": "Prediction created",
  "data": {
    "id": 1,
    "match_id": 1,
    "home_score": 2,
    "away_score": 1,
    "points_earned": null,
    "status": "pending"
  }
}
```

### Listar Mis Predicciones
```
GET /api/predictions
Authorization: Bearer {token}
```

### Ver Predicción
```
GET /api/predictions/{id}
Authorization: Bearer {token}
```

### Actualizar Predicción
```
PUT /api/predictions/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "home_score": 3,
  "away_score": 1
}
```

## Clasificación

### Leaderboard General
```
GET /api/leaderboard
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "rank": 1,
      "user": {
        "id": 1,
        "name": "Juan Pérez"
      },
      "points": 45,
      "correct_predictions": 9
    },
    {
      "rank": 2,
      "user": {
        "id": 2,
        "name": "María García"
      },
      "points": 42,
      "correct_predictions": 8
    }
  ],
  "meta": {
    "user_rank": 5,
    "user_points": 28
  }
}
```

### Clasificación por Etapa
```
GET /api/leaderboard?stage=round_16
Authorization: Bearer {token}
```

## Equipos

### Listar Equipos
```
GET /api/teams
Authorization: Bearer {token}
```

### Equipos por Grupo
```
GET /api/teams?group=A
Authorization: Bearer {token}
```

## Códigos de Estado

| Código | Descripción |
|--------|-------------|
| 200 | OK |
| 201 | Creado |
| 400 | Error de validación |
| 401 | No autenticado |
| 403 | No autorizado |
| 404 | No encontrado |
| 422 | Entidad no procesable |
| 500 | Error del servidor |

## Formato de Errores

```json
{
  "message": "Validation failed",
  "errors": {
    "match_id": ["El partido ya terminó"],
    "home_score": ["Debe ser mayor a 0"]
  }
}
```