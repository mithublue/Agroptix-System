<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'create sources']);
        Permission::create(['name' => 'edit sources']);
        Permission::create(['name' => 'delete sources']);
        Permission::create(['name' => 'view sources']);
        // Add more permissions for products, batches, etc.

        // Create roles and assign existing permissions
        $role = Role::create(['name' => 'Supplier']);
        $role->givePermissionTo(['create sources', 'view sources', 'edit sources']);

        $role = Role::create(['name' => 'Inspector']);
        $role->givePermissionTo('view sources');

        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo(Permission::all());
    }
}
