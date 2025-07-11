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
        // Create roles and basic permissions
        $this->call([
            RolePermissionSeeder::class,
            TestUsersSeeder::class,
        ]);

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'cemithu06@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('11111111'),
                'email_verified_at' => now(),
            ]
        );

        // Get or create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Assign admin role to admin user
        $adminUser->assignRole($adminRole);
        
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

        // Seed test data in non-production environments
        if (!app()->environment('production')) {
            $this->call([
                TestDataSeeder::class,
            ]);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: cemi...@gmail.com');
        $this->command->info('Password: 11111111');
        $this->command->info('Test data has been seeded.');
    }
}
