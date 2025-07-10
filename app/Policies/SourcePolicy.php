<?php

namespace App\Policies;

use App\Models\Source;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SourcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_source');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Source $source): bool
    {
        // User can view if they have view_source permission and either:
        // 1. They are an admin (has manage_source permission), or
        // 2. They are the owner of the source
        return $user->can('view_source') && 
               ($user->can('manage_source') || 
                $user->id === $source->owner_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_source');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function edit(User $user, Source $source): bool
    {
        // User can update if they have edit_source permission and either:
        // 1. They are an admin (has manage_source permission), or
        // 2. They are the owner of the source
        return $user->can('edit_source') && 
               ($user->can('manage_source') || 
                $user->id === $source->owner_id);
    }
    
    /**
     * Alias for edit to maintain Laravel's naming convention
     */
    public function update(User $user, Source $source): bool
    {
        return $this->edit($user, $source);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Source $source): bool
    {
        // User can delete if they have delete_source permission and either:
        // 1. They are an admin (has manage_source permission), or
        // 2. They are the owner of the source
        return $user->can('delete_source') && 
               ($user->can('manage_source') || 
                $user->id === $source->owner_id);
    }
    
    /**
     * Determine whether the user can manage the source (admin only).
     */
    public function manage(User $user): bool
    {
        return $user->can('manage_source');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Source $source): bool
    {
        return $user->hasPermissionTo('edit_source');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Source $source): bool
    {
        return $user->hasPermissionTo('delete_source');
    }
}
