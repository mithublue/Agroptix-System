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
        
        // Assign all permissions to admin role
        $permissions = [
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'view_product', 'create_product', 'edit_product', 'delete_product',
            'view_batch', 'create_batch', 'edit_batch', 'delete_batch',
            'view_quality_test', 'create_quality_test', 'edit_quality_test', 'delete_quality_test',
            'view_shipment', 'create_shipment', 'edit_shipment', 'delete_shipment',
            'view_source', 'create_source', 'edit_source', 'delete_source'
        ];

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
