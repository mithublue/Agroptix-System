<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Option;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all modules
        $modules = ['source', 'product', 'batch', 'quality_test', 'shipment'];
        $actions = ['create', 'view', 'edit', 'delete'];
        
        // Admin specific permissions
        $adminPermissions = [
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'manage_options',
        ];
        
        // Add manage_{module} permissions for each module
        foreach ($modules as $module) {
            $adminPermissions[] = 'manage_' . $module;
        }
        
        // Create admin permissions
        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create permissions for each module and action
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$module}"]);
            }
        }

        // Create or get roles and assign permissions
        $supplier = Role::firstOrCreate(['name' => 'Supplier']);
        $supplier->syncPermissions([
            'create_source', 'view_source', 'edit_source',
            'create_product', 'view_product', 'edit_product',
            'view_batch', 'view_quality_test', 'view_shipment'
        ]);

        $inspector = Role::firstOrCreate(['name' => 'Inspector']);
        $inspector->syncPermissions([
            'view_source', 'view_product', 'view_batch',
            'create_quality_test', 'view_quality_test', 'edit_quality_test',
            'view_shipment'
        ]);

        $logistics = Role::firstOrCreate(['name' => 'Logistics']);
        $logistics->syncPermissions([
            'view_source', 'view_product', 'view_batch',
            'view_quality_test',
            'create_shipment', 'view_shipment', 'edit_shipment'
        ]);

        // Create or get Admin role with all permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $allPermissions = Permission::all();
        $admin->syncPermissions($allPermissions);

        // Find or create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'cemithu06@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('11111111'), // Make sure to change this in production
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role to the user
        $adminUser->assignRole('Admin');

        // Default site options
        Option::set('users_need_activation', 'yes'); // yes or no
        Option::set('users_activation_method', 'email'); // email or phone
        Option::set('users_need_admin_approval', 'no'); // yes or no
    }
}
