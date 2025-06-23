<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AdminPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions if they don't exist
        $permissions = [
            'manage_users',
            'manage_roles',
            'manage_permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Find or create admin role
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        
        // Assign all permissions to admin role
        $adminRole->syncPermissions(Permission::all());

        // Assign admin role to the admin user
        $adminUser = User::where('email', 'cemithu06@gmail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Admin');
            $this->command->info('Admin permissions have been set up successfully.');
        } else {
            $this->command->warn('Admin user not found. Please create an admin user first.');
        }
    }
}
