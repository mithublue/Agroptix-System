<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('manage_permissions');
    }

    public function view(User $user, Permission $permission)
    {
        return $user->can('manage_permissions');
    }

    public function create(User $user)
    {
        return $user->can('manage_permissions');
    }

    public function update(User $user, Permission $permission)
    {
        return $user->can('manage_permissions');
    }

    public function delete(User $user, Permission $permission)
    {
        return $user->can('manage_permissions');
    }
}
