<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Predicciones
            'prediction.create',
            'prediction.edit_own',
            'prediction.edit_all',
            'prediction.delete_own',
            'prediction.delete_all',
            // Partidos y equipos
            'match.manage',
            'team.manage',
            // Usuarios y leaderboard
            'user.manage',
            'leaderboard.view',
            // Admin
            'admin.access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $superAdmin->syncPermissions($permissions); // Todos los permisos

        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions([
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
        ]);

        $jugador = Role::firstOrCreate(['name' => 'Jugador']);
        $jugador->syncPermissions([
            'prediction.create',
            'prediction.edit_own',
            'prediction.delete_own',
            'leaderboard.view',
        ]);
    }
}
