<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckUserPermissions extends Command
{
    protected $signature = 'user:permissions {email} {--permission=} {--role=}';
    protected $description = 'Check user permissions and roles';

    public function handle()
    {
        $email = $this->argument('email');
        $permission = $this->option('permission');
        $role = $this->option('role');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $this->info("User: {$user->name} <{$user->email}>");
        
        // Get all roles
        $roles = $user->getRoleNames();
        $this->info("\nRoles: " . $roles->implode(', '));

        // Get all permissions
        $permissions = $user->getAllPermissions()->pluck('name');
        $this->info("Permissions: " . $permissions->implode(', '));

        // Check specific permission if provided
        if ($permission) {
            $hasPermission = $user->can($permission);
            $this->info("\nCan {$permission}: " . ($hasPermission ? 'YES' : 'NO'));
        }

        // Check specific role if provided
        if ($role) {
            $hasRole = $user->hasRole($role);
            $this->info("Has role '{$role}': " . ($hasRole ? 'YES' : 'NO'));
        }

        return 0;
    }
}
