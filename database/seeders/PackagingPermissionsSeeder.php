<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PackagingPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for packaging
        $permissions = [
            'view_packaging',
            'create_packaging',
            'edit_packaging',
            'delete_packaging',
            'export_packaging',
            'import_packaging',
            'manage_packaging'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Packaging permissions have been created successfully.');
    }

    /**
     * Assign permissions to appropriate roles
     */
    private function assignPermissionsToRoles(): void
    {
        // Admin gets all permissions
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'view_packaging',
                'create_packaging',
                'edit_packaging',
                'delete_packaging',
                'export_packaging',
                'import_packaging'
            ]);
        }

        // Packaging Operator gets basic permissions
        $packagingRole = Role::where('name', 'Packaging Operator')->first();
        if ($packagingRole) {
            $packagingRole->givePermissionTo([
                'view_packaging',
                'create_packaging',
                'edit_packaging',
                'export_packaging'
            ]);
        }

        // Supervisor gets view and export permissions
        $supervisorRole = Role::where('name', 'Supervisor')->first();
        if ($supervisorRole) {
            $supervisorRole->givePermissionTo([
                'view_packaging',
                'export_packaging'
            ]);
        }

        // Quality Control Officer gets view and export permissions
        $qcRole = Role::where('name', 'Quality Control Officer')->first();
        if ($qcRole) {
            $qcRole->givePermissionTo([
                'view_packaging',
                'export_packaging'
            ]);
        }
    }
}
