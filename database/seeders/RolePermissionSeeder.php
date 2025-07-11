<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = [
            'Supplier',
            'Field Inspector',
            'Processing Technician',
            'Supervisor',
            'Quality Control Officer',
            'Lab Technician',
            'Packaging Operator'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $this->command->info('Roles created successfully!');
        $this->command->info('Available roles: ' . implode(', ', $roles));
    }
}
