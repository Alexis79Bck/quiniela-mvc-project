<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_and_permissions_are_seeded_correctly()
    {
        // Ejecutar el seeder
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        // Verificar permisos
        $permissions = [
            'prediction.create',
            'prediction.edit_own',
            'prediction.edit_all',
            'prediction.delete_own',
            'prediction.delete_all',
            'match.manage',
            'team.manage',
            'user.manage',
            'leaderboard.view',
            'admin.access',
        ];

        foreach ($permissions as $permission) {
            $this->assertDatabaseHas('permissions', ['name' => $permission]);
        }

        // Verificar roles
        $this->assertDatabaseHas('roles', ['name' => 'SuperAdmin']);
        $this->assertDatabaseHas('roles', ['name' => 'Administrador']);
        $this->assertDatabaseHas('roles', ['name' => 'Jugador']);

        // Verificar permisos asignados a SuperAdmin (todos)
        $superAdmin = Role::where('name', 'SuperAdmin')->first();
        $this->assertCount(10, $superAdmin->permissions);

        // Verificar permisos asignados a Administrador
        $admin = Role::where('name', 'Administrador')->first();
        $this->assertCount(10, $admin->permissions); // Todos menos algunos, pero en este caso 10

        // Verificar permisos asignados a Jugador
        $jugador = Role::where('name', 'Jugador')->first();
        $this->assertCount(4, $jugador->permissions); // prediction.create, edit_own, delete_own, leaderboard.view
    }
}
