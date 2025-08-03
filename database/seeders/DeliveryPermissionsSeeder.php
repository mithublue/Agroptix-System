<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DeliveryPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delivery Permissions
        $permissions = [
            'view_deliveries' => 'View Deliveries',
            'create_deliveries' => 'Create Deliveries',
            'edit_deliveries' => 'Edit Deliveries',
            'delete_deliveries' => 'Delete Deliveries',
            'update_delivery_status' => 'Update Delivery Status',
            'submit_delivery_feedback' => 'Submit Delivery Feedback',
            'manage_delivery_feedback' => 'Manage Delivery Feedback',
            'export_deliveries' => 'Export Deliveries',
            'import_deliveries' => 'Import Deliveries',
        ];

        // Create permissions
        foreach ($permissions as $code => $name) {
            Permission::updateOrCreate(
                ['name' => $code],
                ['name' => $code, 'guard_name' => 'web']
            );
        }

        // Assign permissions to roles
        $admin = Role::where('name', 'admin')->firstOrFail();
        $admin->givePermissionTo(array_keys($permissions));

        $logisticsManager = Role::updateOrCreate(
            ['name' => 'logistics_manager'],
            ['name' => 'logistics_manager', 'guard_name' => 'web']
        );
        
        $logisticsManager->givePermissionTo([
            'view_deliveries',
            'create_deliveries',
            'edit_deliveries',
            'update_delivery_status',
            'manage_delivery_feedback',
            'export_deliveries',
        ]);

        $customer = Role::updateOrCreate(
            ['name' => 'customer'],
            ['name' => 'customer', 'guard_name' => 'web']
        );
        
        $customer->givePermissionTo([
            'view_deliveries',
            'submit_delivery_feedback',
        ]);
        
        // Field staff role (for delivery personnel)
        $fieldStaff = Role::updateOrCreate(
            ['name' => 'field_staff'],
            ['name' => 'field_staff', 'guard_name' => 'web']
        );
        
        $fieldStaff->givePermissionTo([
            'view_deliveries',
            'update_delivery_status',
        ]);
    }
}
