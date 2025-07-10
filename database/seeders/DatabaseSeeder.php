<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'cemithu06@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('11111111'),
                'email_verified_at' => now(),
            ]
        );

        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Define modules and their permissions
        $modules = ['source', 'product', 'batch', 'quality_test', 'shipment'];
        $actions = ['create', 'view', 'edit', 'delete'];
        
        // Initialize permissions array with admin specific permissions
        $permissions = [
            'manage_users',
            'manage_roles',
            'manage_permissions',
        ];
        
        // Add manage_{module} permissions for each module
        foreach ($modules as $module) {
            $permissions[] = 'manage_' . $module;
            
            // Add CRUD permissions for each module
            foreach ($actions as $action) {
                $permissions[] = $action . '_' . $module;
            }
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole->syncPermissions(Permission::all());
        $adminUser->assignRole($adminRole);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: cemi...@gmail.com');
        $this->command->info('Password: 11111111');
    }
}
