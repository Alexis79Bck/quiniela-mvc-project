# Quiniela FIFA World Cup 2026

Proyecto base para una aplicación de quiniela del Mundial de Fútbol 2026, construida con Laravel y preparada para gestión de predicciones, puntuación automática, roles y auditoría.

## 📌 Descripción

Esta aplicación ofrece una plataforma de pronósticos en torno a la Copa Mundial 2026 con:
- registro y login de usuarios,
- control de acceso por roles y permisos,
- sistema de creación de predicciones,
- cálculo automático de puntos,
- tabla de clasificación,
- auditoría de acciones.

## 🚀 Características principales

- Autenticación API y web con **Laravel Sanctum** y **Laravel Fortify**
- Autorizar acciones con **Spatie Permission**
- Auditoría de acciones con **Spatie Activitylog**
- API RESTful para crear, consultar y administrar predicciones y partidos
- Estructura de proyecto pensada para escalabilidad con **Service**, **Repository**, **Strategy** y **Factory patterns**
- Modelo de datos preparado para equipos, grupos, etapas, partidos y predicciones

## 🧩 Roles y permisos

Roles iniciales:
- **SuperAdmin**: acceso total (modo Dios)
- **Administrador**: gestión de la aplicación y usuarios
- **Jugador**: usuario general registrado

Permisos granulares incluyen:
- creation, edición y eliminación de predicciones
- gestión de partidos y equipos
- gestión de usuarios
- visualización de leaderboard
- acceso administrativo

## 📦 Requisitos

- PHP 8.3+ (recomendado 8.4)
- Composer
- SQLite o MySQL
- Extensiones PHP: `pdo_sqlite` o `pdo_mysql`, `mbstring`, `json`, `openssl`

## ⚙️ Instalación local

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate

# Usando SQLite
copy nul database\database.sqlite

# O usando MySQL
# Crear base de datos y ajustar variables DB_ en .env

php artisan migrate
php artisan db:seed
php artisan serve
```

## 🌐 Variables de entorno recomendadas

```env
APP_NAME="Quiniela WC2026"
APP_ENV=local
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_DEBUG=true

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# O MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=quiniela
# DB_USERNAME=root
# DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:5173
FRONTEND_URL=http://localhost:5173
```

## 📂 Estructura del proyecto

```
app/
├── Actions/
├── Http/Controllers/
│   └── Api/
├── Models/
├── Providers/
├── Repositories/
├── Services/
├── Strategies/
└── Factories/

config/
database/
public/
resources/
routes/
tests/
```

## 🧠 Arquitectura

El proyecto sigue el patrón **MVC** de Laravel y adopta patrones adicionales:
- **Service Layer** para lógica de negocio
- **Repository Pattern** para acceso a datos
- **Strategy Pattern** para cálculo de puntajes
- **Factory Pattern** para creación de configuraciones complejas

## 📊 Modelo de datos

Entidades principales:
- `User`
- `Team`
- `Group`
- `Match`
- `Stage`
- `Prediction`
- `Role`
- `Permission`

Relaciones clave:
- Usuario tiene muchas predicciones
- Partido tiene muchas predicciones
- Partido pertenece a un stage y a dos equipos
- Equipo pertenece a un grupo
- Usuario se relaciona con roles y permisos mediante Spatie

## 🔧 API principal

### Autenticación
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout`
- `GET /api/user`

### Predicciones
- `POST /api/predictions`
- `GET /api/predictions`
- `GET /api/predictions/{id}`
- `PUT /api/predictions/{id}`

### Partidos y clasificación
- `GET /api/matches`
- `GET /api/matches/{id}`
- `GET /api/leaderboard`
- `GET /api/teams`

> El proyecto documenta un API completo para consumo de frontend y experiencias SPA.

## 🛠️ Comandos útiles

```bash
php artisan migrate
php artisan db:seed
php artisan test --compact
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

## 🧪 Testing

El proyecto usa **PHPUnit**. Ejecuta las pruebas con:

```bash
php artisan test --compact
```

## 📘 Documentación adicional

La carpeta `docs/` contiene información detallada sobre:
- `API.md`
- `ARQUITECTURA.md`
- `DESPLIEGUE.md`
- `MODELO-DE-DATOS.md`
- `PLAN-DE-DESARROLLO.md`
- `PUNTUACION.md`
- `ROLES-Y-PERMISOS.md`

## 📣 Estado actual

Aplicación base lista para desarrollo y prueba de funcionalidades de quiniela, autenticación, roles y auditoría. El frontend aún debe definirse y sincronizarse con la API.

## 📄 Licencia

MIT