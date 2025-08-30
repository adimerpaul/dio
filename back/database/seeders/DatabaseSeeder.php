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
        // 0) Limpiar cache de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web'); // 'web' | 'sanctum' | 'api'

        // 1) Usuario admin inicial
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

        // 2) Permisos (con guard_name)
        $permissions = [
            'cases.create','cases.view','cases.view_sensitive','cases.update','cases.delete','cases.restore',
            'cases.assign','cases.transfer','cases.escalate','cases.close','cases.reopen','cases.add_note','cases.link_cud',
            'documents.upload','documents.view','documents.download','documents.delete','documents.templates_manage',
            'timeline.view','timeline.add_event','timeline.revert_event',
            'kpis.view','kpis.export','kpis.configure',
            'reports.view','reports.export','reports.schedule',
            'users.view','users.create','users.update','users.delete','users.reset_password',
            'roles.view','roles.create','roles.update','roles.delete','roles.assign',
            'integrations.justicia_libre.read','integrations.justicia_libre.link','integrations.justicia_libre.unlink','integrations.justicia_libre.sync',
            'security.audit_logs_view','security.audit_logs_export','security.pii_mask',
            'security.backup_run','security.backup_restore',
            'config.catalogs_manage','config.numbering_manage','config.mail_templates_manage',
            'training.content_manage','training.attendance_register','training.issue_certificates',
            'support.ticket_create','support.ticket_manage',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => $guard]);
        }

        // 3) Roles (con guard_name) + mapeo
        $roles = [
            'Operador DIO' => [
                'cases.create','cases.view','cases.update','cases.assign','cases.add_note','cases.link_cud',
                'documents.upload','documents.view','documents.download',
                'timeline.view','timeline.add_event',
                'reports.view',
                'support.ticket_create',
            ],
            'Analista DIO' => [
                'cases.view','cases.view_sensitive','cases.update','cases.escalate','cases.close','cases.reopen','cases.add_note',
                'documents.upload','documents.view','documents.download','documents.delete',
                'timeline.view','timeline.add_event',
                'kpis.view','reports.view','reports.export',
                'support.ticket_create',
            ],
            'Supervisor DIO' => [
                'cases.view','cases.view_sensitive','cases.update','cases.assign','cases.transfer','cases.escalate','cases.close','cases.reopen',
                'documents.upload','documents.view','documents.download','documents.delete',
                'timeline.view','timeline.add_event','timeline.revert_event',
                'kpis.view','kpis.export',
                'reports.view','reports.export',
                'support.ticket_manage',
            ],
            'Administrador DIO' => [
                'cases.*','documents.*','timeline.*','kpis.*','reports.*',
                'users.*','roles.*','config.*','support.ticket_manage',
            ],
            'TI GAMO' => [
                'integrations.justicia_libre.*',
                'security.audit_logs_view','security.audit_logs_export','security.pii_mask',
                'security.backup_run','security.backup_restore',
                'kpis.view','reports.view','reports.export',
            ],
            'Auditor' => [
                'cases.view','cases.view_sensitive',
                'documents.view','documents.download',
                'timeline.view',
                'kpis.view',
                'reports.view','reports.export',
                'security.audit_logs_view',
            ],
            'Invitado' => [
                'kpis.view','reports.view',
            ],
        ];

        // Permisos del guard actual
        $all = Permission::where('guard_name', $guard)->pluck('name')->all();

        // Expandir comodines tipo 'cases.*'
        $expand = function(array $list) use ($all) {
            $grant = [];
            foreach ($list as $item) {
                if (str_ends_with($item, '.*')) {
                    $prefix = rtrim($item, '.*');
                    foreach ($all as $p) {
                        if (str_starts_with($p, $prefix.'.')) $grant[] = $p;
                    }
                } else {
                    $grant[] = $item;
                }
            }
            return array_values(array_unique($grant));
        };

        foreach ($roles as $roleName => $permList) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            $role->syncPermissions($expand($permList));
        }

        // 4) Asignar rol Admin al usuario inicial
        $adminRole = Role::where(['name' => 'Administrador DIO', 'guard_name' => $guard])->first();
        if ($adminRole) {
            $admin->syncRoles([$adminRole]);
        }

        // 5) (Opcional) Conceder TODOS los permisos directo al admin también
        $admin->givePermissionTo($all);

        // 6) Volver a cachear
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
