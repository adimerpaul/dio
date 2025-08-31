<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrador DIO',
                'email' => 'admin@dio.gob.bo',
                'password' => Hash::make('Admin123!*'),
                'avatar' => 'default.png',
                'role' => 'Administrador',
                'email_verified_at' => now(),
            ]
        );
        $permisos = [
            'Dashboard',
            'Usuarios',
            'Casos',
            'Documentos',
            'Lineas de Tiempo',
            'KPIs',
            'Auditorias',
        ];
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }
        $permisos = Permission::all();
//        $admin->assignPermisos

        $admin->syncPermissions($permisos);
    }
}
