<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeliveryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_deliveries');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        // Users can view their own deliveries or if they have view_all_deliveries permission
        return $user->can('view_deliveries') && 
               ($user->hasRole('admin') || 
                $user->hasRole('logistics_manager') ||
                $user->id === $delivery->batch->created_by);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_deliveries');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        // Only allow updates if the delivery is not yet completed
        $canUpdate = $delivery->delivery_status !== 'delivered' && $delivery->delivery_status !== 'cancelled';
        
        return $user->can('edit_deliveries') && $canUpdate;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        // Only allow deletion if the delivery is not yet completed
        $canDelete = $delivery->delivery_status !== 'delivered' && $delivery->delivery_status !== 'cancelled';
        
        return $user->can('delete_deliveries') && $canDelete;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Delivery $delivery): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Delivery $delivery): bool
    {
        return $user->hasRole('admin');
    }
    
    /**
     * Determine whether the user can update the delivery status.
     */
    public function updateStatus(User $user, Delivery $delivery): bool
    {
        return $user->can('update_delivery_status');
    }
    
    /**
     * Determine whether the user can submit feedback for the delivery.
     */
    public function submitFeedback(User $user, Delivery $delivery): bool
    {
        // Only allow feedback submission if the delivery is marked as delivered
        // and the feedback hasn't been submitted yet or is in 'pending' status
        $canSubmit = $delivery->delivery_status === 'delivered' && 
                    ($delivery->feedback_status === null || $delivery->feedback_status === 'pending');
                    
        return $user->can('submit_delivery_feedback') && $canSubmit;
    }
    
    /**
     * Determine whether the user can manage delivery feedback.
     */
    public function manageFeedback(User $user): bool
    {
        return $user->can('manage_delivery_feedback');
    }
}
