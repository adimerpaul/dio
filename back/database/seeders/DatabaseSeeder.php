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
                'name' => 'Ing Evelin Ramirez Cube',
                'email' => 'admin@dio.gob.bo',
                'password' => Hash::make('admin123Admin'),
                'avatar' => 'default.png',
                'role' => 'Administrador',
                'area' => 'DNA',
                'zona' => 'CENTRAL',
                'email_verified_at' => now(),
                'celular' => '72461667',
            ]
        );
//        roles: ['Administrador', 'Asistente', 'Psicologo', 'Abogado','Social'],
        $asistente = User::firstOrCreate(
            ['username' => 'asistente'],
            [
                'name' => 'Lic. Juan Riveros',
                'email' => 'asistente@dio.gob.bo',
                'password' => Hash::make('123456'),
                'avatar' => 'default.png',
                'role' => 'Asistente',
                'area' => 'DNA',
                'zona' => 'CENTRAL',
                'email_verified_at' => now(),
                'celular' => '72461667',
            ]
        );

        $psicologo = User::firstOrCreate(
            ['username' => 'psicologo'],
            [
                //                un nombre falslo
                'name' => 'Lic. Ana  Calle',
                'email' => 'psocologo@dio.gob.bo',
                'password' => Hash::make('123456'),
                'avatar' => 'default.png',
                'role' => 'Psicologo',
                'area' => 'DNA',
                'zona' => 'CENTRAL',
                'email_verified_at' => now(),
                'celular' => '72461667',
            ]
        );
        $abogado = User::firstOrCreate(
            ['username' => 'abogado'],
            [
                'name' => 'Abo. Anita Calle',
                'email' => 'abogado@dio.gob.bo',
                'password' => Hash::make('123456'),
                'avatar' => 'default.png',
                'role' => 'Abogado',
                'area' => 'DNA',
                'zona' => 'CENTRAL',
                'email_verified_at' => now(),
                'celular' => '72461667',
            ]
        );
        $social = User::firstOrCreate(
            ['username' => 'social'],
            [
                'name' => 'Lic. Tania Calizaya',
                'email' => 'social@dio.go.bo',
                'password' => Hash::make('123456'),
                'avatar' => 'default.png',
                'role' => 'Social',
                'area' => 'DNA',
                'zona' => 'CENTRAL',
                'email_verified_at' => now(),
                'celular' => '72461667',
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
            'Agenda',
        ];
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }
        $permisos = Permission::all();
//        $admin->assignPermisos

        $admin->syncPermissions($permisos);
        $asistente->syncPermissions(['Dashboard', 'Casos', 'Documentos', 'Lineas de Tiempo']);
        $psicologo->syncPermissions(['Dashboard', 'Casos', 'Documentos', 'Lineas de Tiempo', 'KPIs','Agenda']);
        $abogado->syncPermissions(['Dashboard', 'Casos', 'Documentos', 'Lineas de Tiempo', 'KPIs']);
        $social->syncPermissions(['Dashboard', 'Casos', 'Documentos', 'Lineas de Tiempo', 'KPIs']);
//        $this->call([
//                CasoSeeder::class,
//        ]);
    }
}
