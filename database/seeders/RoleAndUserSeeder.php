<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'case.view',
            'case.create',
            'case.edit',
            'case.assign',
            'evidence.view',
            'evidence.register',
            'evidence.edit',
            'evidence.verify',
            'evidence.transfer',
            'evidence.approve_transfer',
            'report.generate',
            'audit.view',
            'user.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'Admin' => $permissions,

            'Supervisor' => [
                'case.view', 'case.assign',
                'evidence.view', 'evidence.approve_transfer',
                'report.generate', 'audit.view',
            ],

            'Investigating Officer' => [
                'case.view', 'case.create', 'case.edit',
                'evidence.view', 'evidence.register', 'evidence.edit',
                'evidence.transfer',
            ],

            'Forensic Analyst' => [
                'case.view',
                'evidence.view', 'evidence.verify', 'evidence.transfer',
            ],

            'Custody Officer' => [
                'evidence.view', 'evidence.transfer',
            ],

            // Read only. Can never change an evidence record.
            'Prosecutor' => [
                'case.view', 'evidence.view', 'report.generate',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            Role::firstOrCreate(['name' => $roleName])
                ->syncPermissions($rolePermissions);
        }

        // One test account per role. Password for all: password
        $users = [
            ['Admin User',        'admin@dfems.test',      'Admin'],
            ['Supervisor Karim',  'supervisor@dfems.test', 'Supervisor'],
            ['Officer Rahim',     'officer@dfems.test',    'Investigating Officer'],
            ['Analyst Nadia',     'analyst@dfems.test',    'Forensic Analyst'],
            ['Custody Officer Jamal', 'custody@dfems.test', 'Custody Officer'],
            ['Prosecutor Zoha',   'prosecutor@dfems.test', 'Prosecutor'],
        ];

        foreach ($users as [$name, $email, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$role]);
        }
    }
}
