<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MonitoringPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create monitoring permission if it doesn't exist
        $permission = Permission::firstOrCreate(['name' => 'view_monitoring']);

        // Assign permission to admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
            $this->command->info('Monitoring permission has been assigned to Admin role.');
        } else {
            $this->command->warn('Admin role not found. Please create it first.');
        }
    }
}
