# Quiniela FIFA World Cup 2026

Sistema de predicciones deportivas estilo Quiniela para la Copa Mundial de Fútbol 2026.

## Características

- **Autenticación**: Laravel Sanctum + Fortify
- **Control de acceso**: Spatie Permission (roles y permisos)
- **Predicciones**: Pronóstico de resultados por partido
- **Puntuación automática**: Sistema de puntos por acierto
- **Clasificación**: Leaderboard después de cada jornada
- **API RESTful**: Endpoints para consumo frontend
- **Auditoría**: Registro de acciones

## Requisitos

- PHP 8.3+
- SQLite (base de datos) o MySQL/PostgreSQL
- Composer

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate

# SQLite
touch database/database.sqlite

# MySQL (opcional)
# Crear base de datos y configurar .env

php artisan migrate --seed
php artisan serve
```

## Estructura del Proyecto

```
app/
├── Http/Controllers/    # Controladores MVC
├── Models/              # Eloquent models
├── Services/            # Service Layer
├── Repositories/        # Repository pattern
├── Strategies/          # Strategy pattern
├── Factories/           # Factory pattern
└── Providers/          # Service providers
```

## Paquetes Instalados

- laravel/sanctum - Autenticación API
- laravel/fortify - Autenticación web
- spatie/laravel-permission - Roles y permisos

## Licencia

MIT