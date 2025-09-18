<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia caché de permisos/roles
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ===== Admin (queda igual que tu ejemplo) =====
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'               => 'Ing Evelin Ramirez Cube',
                'email'              => 'admin@dio.gob.bo',
                'password'           => Hash::make('admin123Admin'),
                'avatar'             => 'default.png',
                'role'               => 'Administrador',
                'area'               => 'ADMIN',
                'zona'               => 'CENTRAL',
                'email_verified_at'  => now(),
                'celular'            => '72461667',
            ]
        );

        // ===== Resto de usuarios con clave 1234567 =====
        $rawPassword = '1234567';

        $usersData = [
            // DNA
            [
                'name'   => 'Lic. Juan Riveros',
                'username' => 'asistente',
                'role'   => 'Asistente',
                'avatar' => 'default.png',
                'email'  => 'asistente@dio.gob.bo',
                'area'   => 'DNA',
                'zona'   => 'CENTRAL',
                'celular'=> '72461667',
            ],
            [
                'name'   => 'Lic. Ana  Calle',
                'username' => 'psicologo',
                'role'   => 'Psicologo',
                'avatar' => 'default.png',
                'email'  => 'psocologo@dio.gob.bo', // (tal cual lo pasaste)
                'area'   => 'DNA',
                'zona'   => 'CENTRAL',
                'celular'=> '72461667',
            ],
            [
                'name'   => 'Abo. Anita Calle',
                'username' => 'abogado',
                'role'   => 'Abogado',
                'avatar' => 'default.png',
                'email'  => 'abogado@dio.gob.bo',
                'area'   => 'DNA',
                'zona'   => 'CENTRAL',
                'celular'=> '72461667',
            ],
            [
                'name'   => 'Lic. Tania Calizaya',
                'username' => 'social',
                'role'   => 'Social',
                'avatar' => 'default.png',
                'email'  => 'social@dio.go.bo',
                'area'   => 'DNA',
                'zona'   => 'CENTRAL',
                'celular'=> '72461667',
            ],

            // SLIM
            [
                'name'   => 'LIC. SUSANA LIZARASU',
                'username' => 'SUSY',
                'role'   => 'Asistente',
                'avatar' => 'default.png',
                'email'  => null,
                'area'   => 'SLIM',
                'zona'   => 'CENTRAL',
                'celular'=> '63248057',
            ],
            [
                'name'   => 'LIC. DORIS',
                'username' => 'DORIS',
                'role'   => 'Psicologo',
                'avatar' => 'default.png',
                'email'  => null,
                'area'   => 'SLIM',
                'zona'   => 'CENTRAL',
                'celular'=> '72348634',
            ],
            [
                'name'   => 'ABG. CAMILO SORIA',
                'username' => 'CAMILO',
                'role'   => 'Abogado',
                'avatar' => 'default.png',
                'email'  => null,
                'area'   => 'SLIM',
                'zona'   => 'CENTRAL',
                'celular'=> null,
            ],

            // DNA (adicionales)
            [
                'name'   => 'LIC. JULIO PUÑA',
                'username' => 'JULIO',
                'role'   => 'Asistente',
                'avatar' => 'default.png',
                'email'  => null,
                'area'   => 'DNA',
                'zona'   => 'CENTRAL',
                'celular'=> '73815349',
            ],
            [
                'name'   => 'DR. GARCIA',
                'username' => 'GARCIA',
                'role'   => 'Abogado',
                'avatar' => 'default.png',
                'email'  => null,
                'area'   => 'DNA',
                'zona'   => 'CENTRAL',
                'celular'=> '69598025',
            ],
            [
                'name'   => 'LIC. CARLOS BUSTILLOS',
                'username' => 'CARLOS',
                'role'   => 'Psicologo',
                'avatar' => 'default.png',
                'email'  => null,
                'area'   => 'DNA',
                'zona'   => 'CENTRAL',
                'celular'=> '67224354',
            ],
        ];

        // Creamos/actualizamos y guardamos referencias por username para permisos
        $created = [];
        foreach ($usersData as $u) {
            $payload = [
                'name'              => $u['name'],
                'email'             => $u['email'],
                'password'          => Hash::make($rawPassword),
                'avatar'            => $u['avatar'] ?? 'default.png',
                'role'              => $u['role'],
                'area'              => $u['area'] ?? null,
                'zona'              => $u['zona'] ?? null,
                'celular'           => $u['celular'] ?? null,
                'email_verified_at' => $u['email'] ? now() : null,
            ];

            $model = User::updateOrCreate(['username' => $u['username']], $payload);
            $created[$u['username']] = $model;
        }

        // ===== Permisos =====
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
        $permisosAll = Permission::all();

        // Admin todos los permisos
        $admin->syncPermissions($permisosAll);

        // Atajos a usuarios por username (si existen)
        $asistente = $created['asistente'] ?? null;
        $psicologo = $created['psicologo'] ?? null;
        $abogado   = $created['abogado']   ?? null;
        $social    = $created['social']    ?? null;

        // Asignación de permisos (igual que tu esquema)
        if ($asistente) {
            $asistente->syncPermissions(['Dashboard', 'Casos', 'Documentos', 'Lineas de Tiempo']);
        }
        if ($psicologo) {
            $psicologo->syncPermissions(['Dashboard', 'Casos', 'Documentos', 'Lineas de Tiempo', 'KPIs', 'Agenda']);
        }
        if ($abogado) {
            $abogado->syncPermissions(['Dashboard', 'Casos', 'Documentos', 'Lineas de Tiempo', 'KPIs']);
        }
        if ($social) {
            $social->syncPermissions(['Dashboard', 'Casos', 'Documentos', 'Lineas de Tiempo', 'KPIs']);
        }

        // Puedes seguir con otros seeders si corresponde
        // $this->call([CasoSeeder::class]);
    }
}
