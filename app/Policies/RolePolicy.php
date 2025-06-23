<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('manage_roles');
    }

    public function view(User $user, Role $role)
    {
        return $user->can('manage_roles');
    }

    public function create(User $user)
    {
        return $user->can('manage_roles');
    }

    public function update(User $user, Role $role)
    {
        return $user->can('manage_roles');
    }

    public function delete(User $user, Role $role)
    {
        if ($role->name === 'admin') {
            return false;
        }
        return $user->can('manage_roles');
    }
}
