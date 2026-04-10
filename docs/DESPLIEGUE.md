# Guía de Despliegue

## Requisitos del Servidor

### Servidor Local (Desarrollo)

```bash
# PHP 8.3+
php -v

# Composer
composer --version

# Extensiones PHP requeridas
- pdo_sqlite (o pdo_mysql)
- mbstring
- json
- openssl
```

### Servidor de Producción

| Recurso | Mínimo | Recomendado |
|---------|--------|-------------|
| PHP | 8.3 | 8.4 |
| RAM | 512MB | 1GB |
| CPU | 1 core | 2 cores |
| Disco | 500MB | 1GB |

## Configuración de Entorno

### Variables Requeridas

```env
# Application
APP_NAME="Quiniela WC2026"
APP_ENV=local
APP_KEY=base64:xxxxxxxxxxxxxxx
APP_DEBUG=true

# Database (SQLite)
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

# O MYSQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=quiniela
# DB_USERNAME=root
# DB_PASSWORD=

# Auth
SANCTUM_STATEFUL_DOMAINS=localhost:5173
FRONTEND_URL=http://localhost:5173
```

## Comandos de Instalación

```bash
# 1. Instalar dependencias
composer install --no-dev --optimize-autoloader

# 2. Generar clave
php artisan key:generate

# 3. Ejecutar migraciones
php artisan migrate --force

# 4. Opcional: Semillar datos iniciales
php artisan db:seed --class=StageSeeder
php artisan db:seed --class=TeamSeeder

# 5. Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 6. Optimizar
php artisan optimize
```

## Comandos Útiles

### Gestión de Predicciones

```bash
# Ver todas las predicciones
php artisan predictions:list

# Recalcular puntos
php artisan predictions:recalculate

# ver Leaderboard
php artisan leaderboard:show
```

### Gestión de Partidos

```bash
# Cerrar etapa (finalizar todos los partidos)
php artisan matches:close --stage=group_stage
```

### Limpieza

```bash
# Limpiar prediciones pendientes
php artisan predictions:clear --days=30
```

## Servidor de Producción

### Usando Laravel Forge (Recomendado)

1. Conectar repositorio Git
2. Seleccionar servidor (AWS, DigitalOcean, etc.)
3. Forge configurará automáticamente:
   - PHP 8.3+
   - Nginx
   - MySQL/SQLite
   - SSL (Let's Encrypt)

### Manual

```bash
# Servidor con Nginx

# 1. Instalar PHP
sudo apt install php8.3 php8.3-fpm php8.3-mbstring php8.3-xml

# 2. Configurar Nginx
server {
    listen 80;
    server_name quiniela.example.com;
    root /path/to/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Configuración de Frontend

### Endpoints

| Ambiente | URL |
|----------|-----|
| Desarrollo | http://localhost:8000/api |
| Producción | https://tu-dominio.com/api |

### Token de Acceso

1. Registrar usuario
2. Obtener token vía login
3. Incluir en header:
   ```
   Authorization: Bearer {token}
   ```

## Mantenimiento

### backup

```bash
# Exportar base de datos SQLite
cp database/database.sqlite database/backup_$(date +%Y%m%d).sqlite

# Exportar base de datos MySQL
mysqldump -u root -p quiniela > backup_$(date +%Y%m%d).sql
```

### Restaurar

```bash
# SQLite
cp database/backup_20260101.sqlite database/database.sqlite

# MySQL
mysql -u root -p quiniela < backup_20260101.sql
```

## Troubleshooting

### Error 500

```bash
# Ver logs
tail -f storage/logs/laravel.log
```

### Error de autenticación

```bash
# Regenerar keys
php artisan key:generate
php artisan jwt:secret
```

### Base de datos SQLite no writable

```bash
chmod 666 database/database.sqlite
```