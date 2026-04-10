# Roles y Permisos

## Resumen de Acceso

| Acción | Administrador | Usuario |
|--------|---------------|---------|
| Ver partidos | ✓ | ✓ |
| Crear predicción | ✓ | ✓ |
| Editar predicción propia | ✓ | ✓ |
| Ver leaderboard | ✓ | ✓ |
| Gestionar equipos | ✓ | ✗ |
| Gestionar partidos | ✓ | ✗ |
| Registrar resultados | ✓ | ✗ |
| asignar roles | ✓ | ✗ |

## Roles

### Administrador

Usuario con control total del sistema:
- Gestionar equipos (crear, editar, eliminar)
- Gestionar partidos (crear, editar, resultado)
- Gestionar usuarios (asignar roles)
- Ver todos los datos
- Reiniciar puntuaciones
- Cerrar/abrir etapa

### Usuario

Participante estándar:
- Crear predicciones
- Editar predicciones propias
- Ver leaderboard
- Ver tabla de clasificación personal

## Permisos

### prediction.create
- Crear nuevas predicciones

### prediction.edit_own
- Editar predicciones propias (hasta 1h antes)

### prediction.view_all
- Ver predicciones de otros usuarios

### match.manage
- Crear/editar/eliminar partidos
- Registrar resultados

### team.manage
- Crear/editar/eliminar equipos
- Asignar equipos a grupos

### user.manage
- Asignar/revocar roles
- Gestionar usuarios

### leaderboard.view
- Ver clasificación general

### admin.access
- Panel de admin

## Implementación con Spatie

### En Modelo User

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = ['name', 'email', 'password', 'points'];
}
```

### Migración de Permisos

```php
use Spatie\Permission\Models\Permission;

Permission::firstOrCreate(['name' => 'prediction.create']);
Permission::firstOrCreate(['name' => 'prediction.edit_own']);
Permission::firstOrCreate(['name' => 'match.manage']);
Permission::firstOrCreate(['name' => 'team.manage']);
```

### Asignar Roles

```php
// Crear rol admin
$adminRole = Role::firstOrCreate(['name' => 'admin']);

// Asignar permisos
$adminRole->givePermissionTo([
    'prediction.create',
    'prediction.edit_own',
    'match.manage',
    'team.manage',
    'user.manage',
    'leaderboard.view',
    'admin.access',
]);

// Asignar rol a usuario
$user = User::find(1);
$user->assignRole('admin');
```

### Middleware de Acceso

```php
// En routes/api.php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('matches', MatchController::class);
});
```

```php
// Verificación en controlador
public function store(Request $request)
{
    $this->authorize('match.manage');

    // ...
}
```

## Permisos por Etapa

### Fase de Grupos
- Todos los usuarios pueden predecir
- Sin restricciones adicionales

### Eliminación
- Si hay 2+ usuarios con puntuación idéntica, priorizar por cantidad de marcador exacto

## Auditoría de Acciones

Todas las acciones sensitive se registran:

```php
// Ejemplo de logging
activity()
    ->causedBy($user)
    ->performedOn($prediction)
    ->withProperties(['action' => 'created'])
    ->log('Prediction created');
```

| Acción | Logueado |
|--------|----------|
| Create prediction | Sí |
| Update prediction | Sí |
| Register match result | Sí |
| Assign role | Sí |
| Delete prediction (admin) | Sí |