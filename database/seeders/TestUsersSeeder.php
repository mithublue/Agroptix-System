<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all roles
        $roles = Role::all();
        
        // Common password for all test users
        $password = '11111111';
        
        foreach ($roles as $role) {
            // Skip admin role as it's already handled in DatabaseSeeder
            if ($role->name === 'admin') {
                continue;
            }
            
            // Create username by converting role name to lowercase and replacing spaces with underscores
            $username = strtolower(str_replace(' ', '_', $role->name));
            $email = $username . '@example.com';
            
            // Create user if not exists
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => ucwords(str_replace('_', ' ', $username)),
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]
            );
            
            // Assign role to user
            $user->syncRoles([$role->name]);
            
            $this->command->info("Created {$role->name} user with email: {$email} and password: {$password}");
        }
        
        $this->command->info('Test users created successfully!');
    }
}
