<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all modules
        $modules = ['source', 'product', 'batch', 'quality_test', 'shipment'];
        $actions = ['create', 'view', 'edit', 'delete'];

        // Create permissions for each module and action
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create(['name' => "{$action}_{$module}"]);
            }
        }

        // Create roles and assign permissions
        $supplierPermissions = [
            'create_source', 'view_source', 'edit_source',
            'create_product', 'view_product', 'edit_product',
            'view_batch', 'view_quality_test', 'view_shipment'
        ];

        $inspectorPermissions = [
            'view_source', 'view_product', 'view_batch',
            'create_quality_test', 'view_quality_test', 'edit_quality_test',
            'view_shipment'
        ];

        $logisticsPermissions = [
            'view_source', 'view_product', 'view_batch',
            'view_quality_test',
            'create_shipment', 'view_shipment', 'edit_shipment'
        ];

        // Create roles
        $supplier = Role::create(['name' => 'Supplier']);
        $supplier->givePermissionTo($supplierPermissions);

        $inspector = Role::create(['name' => 'Inspector']);
        $inspector->givePermissionTo($inspectorPermissions);

        $logistics = Role::create(['name' => 'Logistics']);
        $logistics->givePermissionTo($logisticsPermissions);

        // Create Admin role with all permissions
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());

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
    }
}
