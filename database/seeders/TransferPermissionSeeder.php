<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TransferPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['transfer.view', 'transfer.view_all', 'transfer.request', 'transfer.approve'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        // IMPORTANT: the keys must match the role names in YOUR database.
        // Check them with:  php artisan tinker  ->  Spatie\Permission\Models\Role::pluck('name');
        $map = [
            'Admin'                 => ['transfer.view', 'transfer.view_all'],
            'Supervisor'            => ['transfer.view', 'transfer.view_all', 'transfer.approve'],
            'Investigating Officer' => ['transfer.view', 'transfer.request'],
            'Forensic Analyst'      => ['transfer.view', 'transfer.request'],
            'Custody Officer'       => ['transfer.view', 'transfer.request'],
            'Prosecutor'            => ['transfer.view', 'transfer.view_all'],
        ];

        foreach ($map as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if (! $role) {
                $this->command?->warn("Role not found: {$roleName}. Fix the name in this seeder and run it again.");
                continue;
            }

            $role->givePermissionTo($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
